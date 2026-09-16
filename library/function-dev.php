<?php

/**
 * New dev site (2026/27) — Vite assets, templates, components.
 *
 * @package Foundry
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Editor and block editor theme supports for the new dev site.
 */
function ea_setup()
{
	add_theme_support('disable-custom-colors');

	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => __('Black', 'foundry'),
				'slug'  => 'black',
				'color' => '#000000',
			),
			array(
				'name'  => __('White', 'foundry'),
				'slug'  => 'white',
				'color' => '#FFFFFF',
			),
			array(
				'name'  => __('Yellow', 'foundry'),
				'slug'  => 'yellow',
				'color' => '#F59D19',
			),
		)
	);
}
add_action('after_setup_theme', 'ea_setup');

/**
 * Page templates that use ACF only (no Gutenberg / classic content editor).
 *
 * @return string[]
 */
function fdry_acf_only_page_templates(): array
{
	return array(
		'template-home.php',
		'template-service.php',
		'template-service-child.php',
		'template-service-inner.php',
		'template-work.php',
	);
}

/**
 * Whether a page uses an ACF-only template (no block or classic editor).
 *
 * @param int $post_id Page post ID.
 */
function fdry_page_uses_acf_only_template(int $post_id): bool
{
	if ($post_id <= 0 || get_post_type($post_id) !== 'page') {
		return false;
	}

	return in_array(get_page_template_slug($post_id), fdry_acf_only_page_templates(), true);
}

/**
 * Disable Gutenberg for ACF-only page templates.
 *
 * @param bool    $use_block_editor Whether the block editor is enabled.
 * @param WP_Post $post             Current post object.
 */
function fdry_disable_block_editor_for_acf_templates(bool $use_block_editor, WP_Post $post): bool
{
	if (fdry_page_uses_acf_only_template((int) $post->ID)) {
		return false;
	}

	return $use_block_editor;
}
add_filter('use_block_editor_for_post', 'fdry_disable_block_editor_for_acf_templates', 10, 2);

/**
 * Hide the classic content editor on ACF-only page templates.
 */
function fdry_hide_classic_editor_for_acf_templates(): void
{
	if (! is_admin()) {
		return;
	}

	$post_id = 0;

	if (isset($_GET['post'])) {
		$post_id = (int) $_GET['post'];
	} elseif (isset($_POST['post_ID'])) {
		$post_id = (int) $_POST['post_ID'];
	}

	if (! fdry_page_uses_acf_only_template($post_id)) {
		return;
	}

	remove_post_type_support('page', 'editor');
}
add_action('admin_init', 'fdry_hide_classic_editor_for_acf_templates');

/**
 * Register nav menu locations for the new dev site.
 */
function fdry_register_theme_menus()
{
	register_nav_menus(
		array(
			'mainmenu'     => __('Main menu 2026', 'foundry'),
			'secondarymenu' => __('Secondary menu 2026', 'foundry'),
			'footer'       => __('Footer 2026', 'foundry'),
			'footer-secondary' => __('Footer secondary 2026', 'foundry'),
		)
	);
}
add_action('init', 'fdry_register_theme_menus');

/**
 * Build a parent/children tree from a theme menu location.
 *
 * @param string $location Registered menu location slug.
 * @return array<int, array{item: WP_Post, children: WP_Post[]}>
 */
function fdry_get_nav_menu_tree(string $location): array
{
	$locations = get_nav_menu_locations();

	if (empty($locations[$location])) {
		return array();
	}

	$items = wp_get_nav_menu_items((int) $locations[$location]);

	if (! is_array($items) || $items === array()) {
		return array();
	}

	$children_map = array();

	foreach ($items as $item) {
		$parent_id = (int) $item->menu_item_parent;

		if (! isset($children_map[$parent_id])) {
			$children_map[$parent_id] = array();
		}

		$children_map[$parent_id][] = $item;
	}

	$tree = array();

	foreach ($children_map[0] ?? array() as $parent) {
		$tree[] = array(
			'item'     => $parent,
			'children' => $children_map[(int) $parent->ID] ?? array(),
		);
	}

	return $tree;
}

/**
 * Secondary menu 2026 grouped as parent items with optional children.
 *
 * @return array<int, array{item: WP_Post, children: WP_Post[]}>
 */
function fdry_get_secondary_menu_tree(): array
{
	return fdry_get_nav_menu_tree('secondarymenu');
}

/**
 * Nav menu term ID assigned to a theme location.
 *
 * @param string $location Registered menu location slug.
 */
function fdry_get_nav_menu_term_id(string $location): int
{
	$locations = get_nav_menu_locations();

	if (empty($locations[$location])) {
		return 0;
	}

	return (int) $locations[$location];
}

/**
 * Read an ACF field stored on a nav menu term (menu-level, not menu item).
 *
 * @param string $field_name ACF field name.
 * @param string $location   Registered menu location slug.
 * @return mixed Field value or null when unset / menu missing.
 */
function fdry_get_nav_menu_acf_field(string $field_name, string $location)
{
	$menu_id = fdry_get_nav_menu_term_id($location);

	if ($menu_id <= 0) {
		return null;
	}

	return get_field($field_name, 'nav_menu_' . $menu_id);
}

/**
 * Read hashed asset URLs from the Vite manifest (dist/.vite/manifest.json).
 *
 * @return array{css: string, js: string}|null
 */
function fdry_get_vite_assets(): ?array
{
	static $assets = null;

	if (null !== $assets) {
		return $assets;
	}

	$manifest_path = get_template_directory() . '/dist/.vite/manifest.json';
	$entry_key     = 'src/scripts/main.js';

	if (! file_exists($manifest_path)) {
		$assets = null;
		return null;
	}

	$manifest = json_decode((string) file_get_contents($manifest_path), true);

	if (! is_array($manifest) || empty($manifest[$entry_key])) {
		$assets = null;
		return null;
	}

	$entry     = $manifest[$entry_key];
	$js_file   = $entry['file'] ?? '';
	$css_files = $entry['css'] ?? array();
	$css_file  = is_array($css_files) && $css_files !== array() ? $css_files[0] : '';

	if (! $js_file && ! $css_file) {
		$assets = null;
		return null;
	}

	$base_uri = get_stylesheet_directory_uri() . '/dist/';

	$assets = array(
		'js'  => $js_file ? $base_uri . $js_file : '',
		'css' => $css_file ? $base_uri . $css_file : '',
	);

	return $assets;
}

/**
 * Enqueue fdry assets built from src/ via Vite.
 *
 * Filenames are content-hashed; paths come from dist/.vite/manifest.json.
 */
function fdry_enqueue_assets()
{
	wp_enqueue_style(
		'foundry-typekit',
		'https://use.typekit.net/rdq4arx.css',
		array(),
		null
	);

	$assets = fdry_get_vite_assets();

	if (! $assets) {
		return;
	}

	if ($assets['css']) {
		wp_enqueue_style(
			'fdry-overrides',
			$assets['css'],
			array('understrap-styles', 'foundry-typekit'),
			null
		);
	}

	if ($assets['js']) {
		wp_enqueue_script(
			'fdry-scripts',
			$assets['js'],
			array('jquery'),
			null,
			true
		);
	}
}
add_action('wp_enqueue_scripts', 'fdry_enqueue_assets', 11);

/**
 * Normalise an ACF link field to url + target parts.
 *
 * @param array|string|false|null $link ACF link field value.
 * @return array{url: string, target: string}
 */
function fdry_acf_link_parts($link): array
{
	$url    = '#';
	$target = '';

	if (is_array($link)) {
		$url    = is_string($link['url'] ?? null) && $link['url'] !== '' ? $link['url'] : '#';
		$target = is_string($link['target'] ?? null) ? $link['target'] : '';
	} elseif (is_string($link) && $link !== '') {
		$url = $link;
	}

	return array(
		'url'    => $url,
		'target' => $target,
	);
}

/**
 * Normalise an ACF url/file field value to a URL string.
 *
 * @param array|string|false|null $value ACF url or file field value.
 */
function fdry_media_url($value): string
{
	if (! $value) {
		return '';
	}

	if (is_string($value)) {
		return trim($value);
	}

	if (is_array($value) && ! empty($value['url']) && is_string($value['url'])) {
		return trim($value['url']);
	}

	return '';
}

/**
 * Normalise an ACF image field value.
 *
 * @param array|string|false|null $image ACF image field value.
 * @return array{url: string, alt: string, width: int, height: int}
 */
function fdry_image_parts($image): array
{
	$empty = array(
		'url'    => '',
		'alt'    => '',
		'width'  => 0,
		'height' => 0,
	);

	if (! $image) {
		return $empty;
	}

	if (is_string($image) && $image !== '') {
		return array(
			'url'    => $image,
			'alt'    => '',
			'width'  => 0,
			'height' => 0,
		);
	}

	if (! is_array($image) || empty($image['url'])) {
		return $empty;
	}

	return array(
		'url'    => is_string($image['url']) ? $image['url'] : '',
		'alt'    => is_string($image['alt'] ?? null) ? $image['alt'] : '',
		'width'  => isset($image['width']) ? (int) $image['width'] : 0,
		'height' => isset($image['height']) ? (int) $image['height'] : 0,
	);
}

/**
 * Resolve hero / showreel media fields into one normalised array.
 *
 * The homepage hero and the service page showreel share markup but use
 * different ACF field names, so the names are mapped per prefix rather than
 * built by concatenation.
 *
 * @param int                  $post_id Post to read ACF fields from.
 * @param string               $prefix  Field set to read: hero or showreel.
 * @param array<string, mixed> $args    Optional overrides, keyed as the return array.
 * @return array{
 *     mp4: string,
 *     webm: string,
 *     poster: array{url: string, alt: string, width: int, height: int},
 *     full_video: string,
 *     label: string,
 *     thumb: array{url: string, alt: string, width: int, height: int}
 * }
 */
function fdry_hero_media(int $post_id, string $prefix = 'hero', array $args = array()): array
{
	$names = $prefix === 'showreel'
		? array(
			'mp4'        => 'showreel_autoplay_video',
			'webm'       => 'showreel_autoplay_video_webm',
			'poster'     => 'showreel_poster',
			'full_video' => 'showreel_full_video',
			'label'      => 'showreel_label',
			'thumb'      => 'showreel_thumb',
		)
		: array(
			'mp4'        => 'hero_autoplay_video',
			'webm'       => 'hero_autoplay_video_webm',
			'poster'     => 'hero_poster',
			'full_video' => 'hero_full_video',
			'label'      => 'hero_showreel_label',
			'thumb'      => 'hero_showreel_thumb',
		);

	$get = static function (string $key) use ($names, $post_id, $args) {
		if (array_key_exists($key, $args) && $args[$key] !== null) {
			return $args[$key];
		}

		if ($post_id <= 0 || ! function_exists('get_field')) {
			return null;
		}

		return get_field($names[$key], $post_id);
	};

	$label = $get('label');
	$label = is_string($label) && $label !== '' ? $label : 'SEE FULL SHOWREEL';

	return array(
		'mp4'        => fdry_media_url($get('mp4')),
		'webm'       => fdry_media_url($get('webm')),
		'poster'     => fdry_image_parts($get('poster')),
		'full_video' => fdry_media_url($get('full_video')),
		'label'      => $label,
		'thumb'      => fdry_image_parts($get('thumb')),
	);
}

/**
 * Preload the homepage hero poster so it paints as the LCP element.
 *
 * Homepage only: the service showreel sits below the fold, where a
 * high priority preload would compete with that page's real LCP element.
 */
function fdry_preload_hero_poster(): void
{
	if (! is_singular('page') || get_page_template_slug() !== 'template-home.php') {
		return;
	}

	$media = fdry_hero_media((int) get_queried_object_id(), 'hero');

	if ($media['poster']['url'] === '' || $media['mp4'] === '') {
		return;
	}

	printf(
		'<link rel="preload" as="image" href="%1$s" fetchpriority="high" />' . "\n",
		esc_url($media['poster']['url'])
	);
}
add_action('wp_head', 'fdry_preload_hero_poster', 1);

/**
 * Upload guard thresholds for background video fields.
 *
 * A muted looping hero has no business above roughly 5 Mbps; the ceiling is
 * set well clear of that so only master-grade exports trip it.
 */
const FDRY_HERO_MAX_BITRATE_MBPS = 10.0;
const FDRY_HERO_MAX_BYTES        = 30000000;

/**
 * Read the duration of an MP4 from the mvhd box inside moov.
 *
 * @param resource $handle Open file handle.
 * @param int      $start  First byte of moov payload.
 * @param int      $end    Byte after the moov box.
 */
function fdry_mp4_duration($handle, int $start, int $end): float
{
	$offset = $start;

	while ($offset < $end - 8) {
		fseek($handle, $offset);
		$header = fread($handle, 8);

		if ($header === false || strlen($header) < 8) {
			return 0.0;
		}

		$box = unpack('Nsize/a4type', $header);

		if ($box['size'] < 8) {
			return 0.0;
		}

		if ($box['type'] === 'mvhd') {
			$data = fread($handle, min($box['size'] - 8, 32));

			if ($data === false || strlen($data) < 20) {
				return 0.0;
			}

			// mvhd: version(1) flags(3), then times. Field offsets differ by version.
			if (ord($data[0]) === 1) {
				if (strlen($data) < 32) {
					return 0.0;
				}

				$timescale = unpack('N', substr($data, 20, 4))[1];
				$high      = unpack('N', substr($data, 24, 4))[1];
				$low       = unpack('N', substr($data, 28, 4))[1];
				$duration  = ($high << 32) | $low;
			} else {
				$timescale = unpack('N', substr($data, 12, 4))[1];
				$duration  = unpack('N', substr($data, 16, 4))[1];
			}

			return $timescale > 0 ? $duration / $timescale : 0.0;
		}

		$offset += $box['size'];
	}

	return 0.0;
}

/**
 * Inspect a local MP4 for the two delivery faults that matter: an index
 * written after the media data (no faststart) and a master-grade bitrate.
 *
 * Parses the container in plain PHP because the host has no ffmpeg, and
 * deliberately fails open — anything unparseable returns null so a valid
 * upload is never blocked by a limitation of this reader.
 *
 * @return array{faststart: bool|null, duration: float, bitrate: float, size: int}|null
 */
function fdry_inspect_mp4(string $path): ?array
{
	if (! is_readable($path)) {
		return null;
	}

	$size = filesize($path);

	if ($size === false || $size < 16) {
		return null;
	}

	$handle = fopen($path, 'rb');

	if (! $handle) {
		return null;
	}

	$order    = array();
	$duration = 0.0;
	$offset   = 0;

	while ($offset < $size && count($order) < 32) {
		fseek($handle, $offset);
		$header = fread($handle, 8);

		if ($header === false || strlen($header) < 8) {
			break;
		}

		$box         = unpack('Nsize/a4type', $header);
		$box_size    = $box['size'];
		$header_size = 8;

		if ($box_size === 1) {
			$extended = fread($handle, 8);

			if ($extended === false || strlen($extended) < 8) {
				break;
			}

			$box_size    = (unpack('N', substr($extended, 0, 4))[1] << 32) | unpack('N', substr($extended, 4, 4))[1];
			$header_size = 16;
		} elseif ($box_size === 0) {
			$box_size = $size - $offset;
		}

		if ($box_size < $header_size) {
			break;
		}

		$order[] = $box['type'];

		if ($box['type'] === 'moov') {
			$duration = fdry_mp4_duration($handle, $offset + $header_size, $offset + $box_size);
		}

		$offset += $box_size;
	}

	fclose($handle);

	$moov_at = array_search('moov', $order, true);
	$mdat_at = array_search('mdat', $order, true);

	return array(
		'faststart' => ($moov_at !== false && $mdat_at !== false) ? $moov_at < $mdat_at : null,
		'duration'  => $duration,
		'bitrate'   => $duration > 0 ? ($size * 8) / $duration / 1000000 : 0.0,
		'size'      => (int) $size,
	);
}

/**
 * Copy-pasteable brief for whoever re-encodes the file.
 *
 * Deliberately a raw ffmpeg command rather than a reference to this repo's
 * "pnpm encode": the person receiving it may be a video editor or an agency
 * with no access to the theme, and ffmpeg runs anywhere.
 */
function fdry_video_encode_instructions(): string
{
	return __(
		"\n\nPlease contact the developer and ask for the following:\n\n"
			. "\"Re-encode this video as a muted background loop for the web, keeping the same resolution and length:\n\n"
			. "ffmpeg -i INPUT -an -c:v libx264 -profile:v high -level 4.2 -preset veryslow -crf 18 -pix_fmt yuv420p -movflags +faststart hero.mp4\n\n"
			. "And export the first frame as a poster image:\n\n"
			. "ffmpeg -i INPUT -frames:v 1 -q:v 1 poster.png\n\n"
			. "Send back hero.mp4 and poster.png. The -movflags +faststart part is essential — without it the video will not start playing until it has fully downloaded.\"",
		'foundry'
	);
}

/**
 * Block background video uploads that would undo the hero's load behaviour.
 *
 * Content editors have no access to the encoder, so the two regressions that
 * silently cost the most are caught here with instructions attached.
 *
 * @param bool|string $valid Current validity.
 * @param mixed       $value Attachment ID.
 * @param array       $field ACF field array.
 * @return bool|string
 */
function fdry_validate_background_video($valid, $value, $field)
{
	if ($valid !== true || ! $value) {
		return $valid;
	}

	$path = get_attached_file((int) $value);

	if (! $path || ! file_exists($path)) {
		return $valid;
	}

	$size = filesize($path);

	if ($size !== false && $size > FDRY_HERO_MAX_BYTES) {
		return sprintf(
			/* translators: %s: file size in MB. */
			__('This file is %s MB, which is far too large for a background loop.', 'foundry') . fdry_video_encode_instructions(),
			number_format($size / 1000000, 1)
		);
	}

	if (strtolower((string) pathinfo($path, PATHINFO_EXTENSION)) !== 'mp4') {
		return $valid;
	}

	$info = fdry_inspect_mp4($path);

	if ($info === null) {
		return $valid;
	}

	if ($info['faststart'] === false) {
		return __('This MP4 is not web-optimised: its index sits after the video data, so nothing plays until the whole file has downloaded.', 'foundry') . fdry_video_encode_instructions();
	}

	if ($info['bitrate'] > FDRY_HERO_MAX_BITRATE_MBPS) {
		return sprintf(
			/* translators: %s: bitrate in Mbps. */
			__('This is a %s Mbps master export rather than a web encode, so visitors would download many times more data than they need.', 'foundry') . fdry_video_encode_instructions(),
			number_format($info['bitrate'], 1)
		);
	}

	return $valid;
}
add_filter('acf/validate_value/name=hero_autoplay_video', 'fdry_validate_background_video', 10, 3);
add_filter('acf/validate_value/name=hero_autoplay_video_webm', 'fdry_validate_background_video', 10, 3);
add_filter('acf/validate_value/name=showreel_autoplay_video', 'fdry_validate_background_video', 10, 3);
add_filter('acf/validate_value/name=showreel_autoplay_video_webm', 'fdry_validate_background_video', 10, 3);

/**
 * Require a poster wherever a background video is set.
 *
 * Cross-field, so it runs on save rather than per field. The poster is what
 * paints immediately; without it the hero is black until the video decodes.
 */
function fdry_validate_poster_present(): void
{
	if (! function_exists('acf_add_validation_error') || empty($_POST['acf']) || ! is_array($_POST['acf'])) {
		return;
	}

	$pairs = array(
		'field_6a971hero0001' => 'field_6a971hero0005',
		'field_6aa185527a011' => 'field_6aa185527a015',
	);

	foreach ($pairs as $video_key => $poster_key) {
		if (! isset($_POST['acf'][$video_key]) || ! isset($_POST['acf'][$poster_key])) {
			continue;
		}

		if (! empty($_POST['acf'][$video_key]) && empty($_POST['acf'][$poster_key])) {
			acf_add_validation_error(
				'acf[' . $poster_key . ']',
				__('Add a poster frame. It is what visitors see instantly while the video loads — without it the hero stays black until the video decodes. Use the generated -poster.webp.', 'foundry')
			);
		}
	}
}
add_action('acf/validate_save_post', 'fdry_validate_poster_present', 20);

/**
 * Build work parallax card data from the homepage ACF repeater.
 *
 * @return array<int, array{
 *     id: int,
 *     title: string,
 *     permalink: string,
 *     image_url: string,
 *     image_width: int,
 *     image_height: int,
 *     image_alt: string,
 *     tagline: string,
 *     categories: string[]
 * }>
 */
function fdry_get_work_parallax_cards(int $post_id): array
{
	if ($post_id <= 0 || ! function_exists('have_rows') || ! have_rows('work_parallax', $post_id)) {
		return array();
	}

	$cards = array();

	while (have_rows('work_parallax', $post_id)) {
		the_row();

		$work = get_sub_field('work');

		if (! $work instanceof WP_Post) {
			continue;
		}

		$work_id = (int) $work->ID;

		if (! has_post_thumbnail($work_id)) {
			continue;
		}

		$thumbnail_id = (int) get_post_thumbnail_id($work_id);
		$image        = wp_get_attachment_image_src($thumbnail_id, 'large');

		if (! is_array($image) || empty($image[0])) {
			continue;
		}

		$tagline = get_sub_field('work_tagline');
		$tagline = is_string($tagline) ? trim($tagline) : '';

		$categories = array();

		foreach (get_the_category($work_id) as $category) {
			if (! $category instanceof WP_Term) {
				continue;
			}

			if ($category->slug === 'uncategorized') {
				continue;
			}

			$categories[] = $category->name;
		}

		$cards[] = array(
			'id'           => $work_id,
			'title'        => get_the_title($work_id),
			'permalink'    => (string) get_permalink($work_id),
			'image_url'    => $image[0],
			'image_width'  => isset($image[1]) ? (int) $image[1] : 0,
			'image_height' => isset($image[2]) ? (int) $image[2] : 0,
			'image_alt'    => (string) get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true),
			'tagline'      => $tagline,
			'categories'   => $categories,
		);
	}

	return $cards;
}

/**
 * Preload work parallax card images on the homepage template.
 */
function fdry_preload_work_parallax_images(): void
{
	if (! is_singular('page') || get_page_template_slug() !== 'template-home.php') {
		return;
	}

	$post_id = (int) get_queried_object_id();
	$cards   = fdry_get_work_parallax_cards($post_id);

	if ($cards === array()) {
		return;
	}

	foreach ($cards as $index => $card) {
		if ($card['image_url'] === '') {
			continue;
		}

		$fetchpriority = $index === 0 ? ' fetchpriority="high"' : '';

		printf(
			'<link rel="preload" as="image" href="%1$s"%2$s />' . "\n",
			esc_url($card['image_url']),
			$fetchpriority
		);
	}
}
add_action('wp_head', 'fdry_preload_work_parallax_images', 1);

/**
 * Return SVG markup from an ACF file field (URL or path).
 *
 * Uses the local filesystem when possible to avoid slow HTTP loopback requests.
 *
 * @param string|array|false $file ACF file field value (URL, path, or array with url).
 * @return string SVG file contents, or empty string.
 */
function acfFile_toSvg($file)
{
	if (! $file) {
		return '';
	}

	if (is_array($file) && ! empty($file['url'])) {
		$file = $file['url'];
	}

	static $cache = array();

	if (isset($cache[$file])) {
		return $cache[$file];
	}

	$path = $file;

	// ACF file fields return URLs — convert to local filesystem path.
	if (filter_var($file, FILTER_VALIDATE_URL)) {
		$upload_dir = wp_get_upload_dir();
		$base_url   = $upload_dir['baseurl'];

		if (strpos($file, $base_url) === 0) {
			$path = str_replace($base_url, $upload_dir['basedir'], $file);
		} else {
			$attachment_id = attachment_url_to_postid($file);

			if ($attachment_id) {
				$path = get_attached_file($attachment_id);
			}
		}
	}

	if ($path && file_exists($path)) {
		$cache[$file] = file_get_contents($path);
	} else {
		$cache[$file] = '';
	}

	return $cache[$file];
}
