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
function fdry_work_query(string $category, int $paged, int $per_page = 9): WP_Query
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
 * Render one work archive card.
 */
function fdry_render_work_card(int $post_id): string
{
	if ($post_id <= 0 || get_post_type($post_id) !== 'works_post') {
		return '';
	}

	if (! has_post_thumbnail($post_id)) {
		return '';
	}

	$thumbnail_id = (int) get_post_thumbnail_id($post_id);
	$image        = wp_get_attachment_image_src($thumbnail_id, 'large');

	if (! is_array($image) || empty($image[0])) {
		return '';
	}

	$title = get_the_title($post_id);
	$title = is_string($title) ? $title : '';

	$permalink = (string) get_permalink($post_id);

	$description = get_field('description', $post_id);
	$description = is_string($description) ? trim($description) : '';

	$categories = fdry_work_card_categories($post_id);
	$image_alt  = (string) get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
	$image_alt  = $image_alt !== '' ? $image_alt : $title;

	ob_start();
	?>
	<article class="work-card" data-post-id="<?= esc_attr((string) $post_id); ?>">
		<a
			class="work-card__link"
			href="<?= esc_url($permalink); ?>"
			aria-label="<?= esc_attr(sprintf(__('View %s', 'foundry'), $title)); ?>">
			<div class="work-card__media">
				<img
					class="work-card__image"
					src="<?= esc_url($image[0]); ?>"
					alt="<?= esc_attr($image_alt); ?>"
					loading="lazy"
					decoding="async"
					<?php if (! empty($image[1])) : ?>
						width="<?= esc_attr((string) (int) $image[1]); ?>"
					<?php endif; ?>
					<?php if (! empty($image[2])) : ?>
						height="<?= esc_attr((string) (int) $image[2]); ?>"
					<?php endif; ?>>
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
 */
function fdry_render_work_cards(WP_Query $query): string
{
	$html = '';

	foreach ($query->posts as $post_id) {
		$html .= fdry_render_work_card((int) $post_id);
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
