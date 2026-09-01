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
