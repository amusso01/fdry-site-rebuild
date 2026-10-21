<?php

/**
 * Work archive (2026/27) — query, cards, AJAX.
 *
 * @package Foundry
 */

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Query published works, optionally filtered by category slug.
 *
 * @param string $category Category slug, or "all".
 * @param int    $paged    1-based page number.
 * @param int    $per_page Posts per page.
 */
function fdry_work_query(string $category, int $paged, int $per_page = 18): WP_Query
{
	$paged    = max(1, $paged);
	$per_page = max(1, $per_page);
	$category = sanitize_title($category);

	$args = array(
		'post_type'              => 'works_post',
		'post_status'            => 'publish',
		'posts_per_page'         => $per_page,
		'paged'                  => $paged,
		'orderby'                => 'date',
		'order'                  => 'DESC',
		'fields'                 => 'ids',
		'no_found_rows'          => false,
		'update_post_meta_cache' => true,
		'update_post_term_cache' => true,
		'meta_query'             => array(
			array(
				'key'     => '_thumbnail_id',
				'compare' => 'EXISTS',
			),
		),
	);

	if ($category !== '' && $category !== 'all') {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field'    => 'slug',
				'terms'    => $category,
			),
		);
	}

	return new WP_Query($args);
}

/**
 * Category terms assigned to at least one published work.
 *
 * @return WP_Term[]
 */
function fdry_work_categories(): array
{
	$work_ids = get_posts(
		array(
			'post_type'      => 'works_post',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	if (! is_array($work_ids) || $work_ids === array()) {
		return array();
	}

	$terms = wp_get_object_terms(
		$work_ids,
		'category',
		array(
			'orderby' => 'name',
			'order'   => 'ASC',
		)
	);

	if (! is_array($terms) || $terms === array()) {
		return array();
	}

	$categories = array();

	foreach ($terms as $term) {
		if (! $term instanceof WP_Term) {
			continue;
		}

		if (in_array($term->slug, array('uncategorized', 'agencylife'), true)) {
			continue;
		}

		$categories[] = $term;
	}

	return $categories;
}

/**
 * Up to two category names for a work card (skips Uncategorized).
 *
 * @return string[]
 */
function fdry_work_card_categories(int $post_id): array
{
	if ($post_id <= 0) {
		return array();
	}

	$categories = array();

	foreach (get_the_category($post_id) as $category) {
		if (! $category instanceof WP_Term) {
			continue;
		}

		if ($category->slug === 'uncategorized') {
			continue;
		}

		$categories[] = $category->name;

		if (count($categories) >= 2) {
			break;
		}
	}

	return $categories;
}

/**
 * A work image (card, row or parallax) with srcset, capped on phones.
 *
 * Wraps wp_get_attachment_image() at `large`, so the WebP swap and
 * WordPress's `sizes="auto"` for lazy images apply as usual. Pass `sizes`
 * and the loading attributes in $attr.
 *
 * Phones never get a file wider than `large`, which is what they loaded
 * before srcset (PERFORMANCE-PLAN.md #7): at 340px a 768w card is already
 * 2.3×, so the 1152w candidate would cost bytes nobody sees. Below the
 * `phone` breakpoint (640px, as in image-banner.php) a <source> lists only
 * the candidates up to `large`. Wider screens get them all, so retina
 * screens get a 2× file.
 *
 * @param array  $attr        wp_get_attachment_image() attributes.
 * @param string $phone_sizes `sizes` for the phone <source>. Pass the width the
 *                            image renders at when object-fit: cover crops it by
 *                            height, as on the square parallax cards.
 */
function fdry_work_image(int $attachment_id, array $attr, string $phone_sizes = '100vw'): string
{
	$image = wp_get_attachment_image($attachment_id, 'large', false, $attr + array('decoding' => 'async'));

	if ($image === '') {
		return '';
	}

	$meta   = wp_get_attachment_metadata($attachment_id);
	$cap    = is_array($meta) ? (int) ($meta['sizes']['large']['width'] ?? $meta['width'] ?? 0) : 0;
	$srcset = (string) wp_get_attachment_image_srcset($attachment_id, 'large');

	if ($cap <= 0 || $srcset === '') {
		return $image;
	}

	// Upload names can't contain commas (sanitize_file_name()), so ", " only
	// separates candidates.
	$phone   = array();
	$dropped = false;

	foreach (explode(', ', $srcset) as $candidate) {
		if (preg_match('/\s(\d+)w$/', $candidate, $match) && (int) $match[1] > $cap) {
			$dropped = true;
			continue;
		}

		$phone[] = $candidate;
	}

	if (! $dropped || $phone === array()) {
		return $image;
	}

	return sprintf(
		'<picture><source media="(max-width: 639px)" srcset="%s" sizes="%s">%s</picture>',
		esc_attr(implode(', ', $phone)),
		esc_attr($phone_sizes),
		$image
	);
}

/**
 * Render one work archive card.
 *
 * $is_lcp: the archive's first card, the LCP on phones (one column). It loads
 * at once with high priority instead of waiting for layout like a lazy image.
 */
function fdry_render_work_card(int $post_id, bool $is_lcp = false): string
{
	if ($post_id <= 0 || get_post_type($post_id) !== 'works_post') {
		return '';
	}

	if (! has_post_thumbnail($post_id)) {
		return '';
	}

	$title = get_the_title($post_id);
	$title = is_string($title) ? $title : '';

	$thumbnail_id = (int) get_post_thumbnail_id($post_id);
	$image_alt    = (string) get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
	$image_attr   = array(
		'class'   => 'work-card__image',
		'alt'     => $image_alt !== '' ? $image_alt : $title,
		'loading' => $is_lcp ? 'eager' : 'lazy',
		// 3, 2 and 1 columns (_work-archive.scss); content-max stops growing at 1920px.
		'sizes'   => '(max-width: 639px) 100vw, (max-width: 1139px) 50vw, (max-width: 1919px) 33vw, 624px',
	);

	if ($is_lcp) {
		$image_attr['fetchpriority'] = 'high';
	}

	$image_html = fdry_work_image($thumbnail_id, $image_attr);

	if ($image_html === '') {
		return '';
	}

	$permalink = (string) get_permalink($post_id);

	$description = get_field('description', $post_id);
	$description = is_string($description) ? trim($description) : '';

	$categories = fdry_work_card_categories($post_id);

	ob_start();
	?>
	<article class="work-card" data-post-id="<?= esc_attr((string) $post_id); ?>">
		<a
			class="work-card__link"
			href="<?= esc_url($permalink); ?>"
			aria-label="<?= esc_attr(sprintf(__('View %s', 'foundry'), $title)); ?>">
			<div class="work-card__media">
				<?= $image_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built by wp_get_attachment_image() ?>
			</div>

			<div class="work-card__overlay">
				<div class="work-card__meta">
					<?php if ($title !== '') : ?>
						<p class="work-card__title"><?= esc_html($title); ?></p>
					<?php endif; ?>

					<?php if ($description !== '' || $categories !== array()) : ?>
						<div class="work-card__details">
							<div class="work-card__details-inner">
								<?php if ($description !== '') : ?>
									<p class="work-card__description"><?= esc_html($description); ?></p>
								<?php endif; ?>

								<?php if ($categories !== array()) : ?>
									<ul class="work-card__categories" aria-label="<?php esc_attr_e('Categories', 'foundry'); ?>">
										<?php foreach ($categories as $category_name) : ?>
											<li class="work-card__category"><?= esc_html($category_name); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</a>
	</article>
	<?php

	return (string) ob_get_clean();
}

/**
 * Render work cards for a query.
 *
 * $first_is_lcp: true for the first page load only, not for AJAX pages.
 */
function fdry_render_work_cards(WP_Query $query, bool $first_is_lcp = false): string
{
	$html = '';

	foreach ($query->posts as $index => $post_id) {
		$html .= fdry_render_work_card((int) $post_id, $first_is_lcp && $index === 0);
	}

	return $html;
}

/**
 * AJAX: filter or load more works.
 */
function fdry_load_more_works(): void
{
	check_ajax_referer('fdry_work_archive', 'nonce');

	$page     = isset($_POST['page']) ? (int) $_POST['page'] : 1;
	$category = isset($_POST['category']) ? sanitize_title(wp_unslash((string) $_POST['category'])) : 'all';

	if ($page < 1) {
		$page = 1;
	}

	if ($category === '') {
		$category = 'all';
	}

	$query = fdry_work_query($category, $page);

	wp_send_json_success(
		array(
			'html'     => fdry_render_work_cards($query),
			'has_more' => $query->max_num_pages > $page,
		)
	);
}
add_action('wp_ajax_fdry_load_more_works', 'fdry_load_more_works');
add_action('wp_ajax_nopriv_fdry_load_more_works', 'fdry_load_more_works');

/**
 * Pass AJAX URL and nonce to the work archive script.
 */
function fdry_localize_work_archive(): void
{
	if (! is_page_template('template-work.php')) {
		return;
	}

	if (! wp_script_is('fdry-scripts', 'enqueued')) {
		return;
	}

	wp_localize_script(
		'fdry-scripts',
		'fdryWorkArchive',
		array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
			'nonce'   => wp_create_nonce('fdry_work_archive'),
		)
	);
}
add_action('wp_enqueue_scripts', 'fdry_localize_work_archive', 12);

/**
 * Put the Works page in the case-study breadcrumb, after Home.
 *
 * works_post has no archive, so Yoast adds no post-type crumb and would go
 * straight from Home to the case study. Yoast's BreadcrumbList schema is
 * built from the same crumbs.
 *
 * @param array $crumbs Yoast breadcrumb links.
 */
function fdry_works_breadcrumb(array $crumbs): array
{
	if (! is_singular('works_post')) {
		return $crumbs;
	}

	array_splice(
		$crumbs,
		1,
		0,
		array(
			array(
				'url'  => fdry_template_page_url('template-work.php', '/works/'),
				'text' => __('Work', 'foundry'),
			),
		)
	);

	return $crumbs;
}
add_filter('wpseo_breadcrumb_links', 'fdry_works_breadcrumb');

/**
 * The single work meta bar: platform, industries, strategies and website.
 *
 * Strategy URLs come from each term's Service link (a Page Link, so the
 * page's current URL); a strategy with no page has an empty url.
 *
 * @return array{
 *     platform: string,
 *     industries: string[],
 *     strategies: array<int, array{name: string, url: string}>,
 *     website: string
 * }
 */
function fdry_work_meta(int $post_id): array
{
	$platform = get_field('work_platform', $post_id);
	$website  = get_field('work_website', $post_id);

	$industries = array();
	$terms      = get_the_terms($post_id, 'industry');

	foreach (is_array($terms) ? $terms : array() as $term) {
		$industries[] = $term->name;
	}

	$strategies = array();
	$terms      = get_the_terms($post_id, 'strategy');

	foreach (is_array($terms) ? $terms : array() as $term) {
		$url = get_field('service_link', $term);

		$strategies[] = array(
			'name' => $term->name,
			'url'  => is_string($url) ? $url : '',
		);
	}

	return array(
		'platform'   => is_string($platform) ? trim($platform) : '',
		'industries' => $industries,
		'strategies' => $strategies,
		'website'    => is_string($website) ? trim($website) : '',
	);
}

/**
 * One media slot of a single work image row (media_1 / media_2 group).
 *
 * A video slot needs an MP4 or a WebM; its still is optional. A slot with
 * nothing usable comes back with an empty type.
 *
 * @param mixed $group ACF group field value.
 * @return array{type: string, image_id: int, mp4: string, webm: string, still_id: int}
 */
function fdry_work_row_media($group): array
{
	$media = array(
		'type'     => '',
		'image_id' => 0,
		'mp4'      => '',
		'webm'     => '',
		'still_id' => 0,
	);

	if (! is_array($group)) {
		return $media;
	}

	if (($group['type'] ?? 'image') === 'video') {
		$media['mp4']      = fdry_media_url($group['video_mp4'] ?? null);
		$media['webm']     = fdry_media_url($group['video_webm'] ?? null);
		$media['still_id'] = (int) ($group['still'] ?? 0);

		if ($media['mp4'] !== '' || $media['webm'] !== '') {
			$media['type'] = 'video';
		}

		return $media;
	}

	$media['image_id'] = (int) ($group['image'] ?? 0);

	if ($media['image_id'] > 0) {
		$media['type'] = 'image';
	}

	return $media;
}

/**
 * Hide the Categories box on works using a new template, which use the
 * Industry and Strategy taxonomies instead.
 *
 * Only the box goes. When it isn't submitted, wp_update_post() keeps the
 * categories a work already has, so the work stays in the /works/ category
 * filters and its cards keep their pills.
 */
function fdry_hide_work_categories_box(WP_Post $post): void
{
	if (! in_array(get_page_template_slug($post), fdry_acf_only_work_templates(), true)) {
		return;
	}

	remove_meta_box('categorydiv', 'works_post', 'side');
}
add_action('add_meta_boxes_works_post', 'fdry_hide_work_categories_box');

/**
 * Strategies list (Works → Strategies): a Service link column in place of
 * Description.
 *
 * @param string[] $columns Column labels, keyed by column name.
 * @return string[]
 */
function fdry_strategy_columns(array $columns): array
{
	$updated = array();

	foreach ($columns as $name => $label) {
		if ($name === 'description') {
			$updated['service_link'] = __('Service link', 'foundry');
			continue;
		}

		$updated[$name] = $label;
	}

	return $updated;
}
add_filter('manage_edit-strategy_columns', 'fdry_strategy_columns');

/**
 * The page picked in a strategy's Service link field, linked to that page.
 *
 * Also runs for the row the Add New Strategy form inserts over AJAX; ACF has
 * saved the field by then.
 *
 * @param string $content     Column content.
 * @param string $column_name Column name.
 * @param int    $term_id     Strategy term ID.
 */
function fdry_strategy_service_link_column(string $content, string $column_name, int $term_id): string
{
	if ($column_name !== 'service_link') {
		return $content;
	}

	// Unformatted, the field is the page ID.
	$page_id = (int) get_field('service_link', 'term_' . $term_id, false);

	if ($page_id <= 0 || ! get_post($page_id)) {
		return '<span aria-hidden="true">&#8212;</span><span class="screen-reader-text">' . esc_html__('No service link', 'foundry') . '</span>';
	}

	return sprintf(
		'<a href="%s">%s</a>',
		esc_url(get_permalink($page_id)),
		esc_html(get_the_title($page_id))
	);
}
add_filter('manage_strategy_custom_column', 'fdry_strategy_service_link_column', 10, 3);
