<?php

/**
 * Work archive — intro, category filters, and paginated work grid
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type int $post_id Post ID for ACF fallback. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();

$tagline = $post_id ? get_field('work_tagline', $post_id) : '';
$tagline = is_string($tagline) ? trim($tagline) : '';

if ($tagline === '') {
	$tagline = __('WORK', 'foundry');
}

$content = $post_id ? get_field('work_content', $post_id) : '';
$content = is_string($content) ? trim($content) : '';

$categories = fdry_work_categories();
$query      = fdry_work_query('all', 1);
$cards_html = fdry_render_work_cards($query);
$has_more   = $query->max_num_pages > 1;
?>

<section class="work-archive" aria-label="<?php esc_attr_e('Work', 'foundry'); ?>" data-category="all" data-page="1">
	<div class="content-block">
		<div class="content-max">
			<div class="work-archive__intro">
				<p class="work-archive__tagline"><?= esc_html($tagline); ?></p>

				<?php if ($content !== '') : ?>
					<div class="work-archive__content">
						<?= wp_kses_post($content); ?>
					</div>
				<?php endif; ?>
			</div>

			<nav class="work-archive__nav" aria-label="<?php esc_attr_e('Work categories', 'foundry'); ?>">
				<ul class="work-archive__filters">
					<li>
						<button
							type="button"
							class="work-archive__filter is-active"
							data-category="all"
							aria-pressed="true">
							<?php esc_html_e('Featured', 'foundry'); ?>
						</button>
					</li>
					<?php foreach ($categories as $category) : ?>
						<li>
							<button
								type="button"
								class="work-archive__filter"
								data-category="<?= esc_attr($category->slug); ?>"
								aria-pressed="false">
								<?= esc_html($category->name); ?>
							</button>
						</li>
					<?php endforeach; ?>
				</ul>
			</nav>

			<div class="work-archive__grid" aria-live="polite">
				<?= $cards_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cards are escaped in fdry_render_work_card() ?>
			</div>

			<div class="work-archive__more"<?= $has_more ? '' : ' hidden'; ?>>
				<button type="button" class="work-archive__load-more">
					<?php esc_html_e('Load more', 'foundry'); ?>
				</button>
			</div>
		</div>
	</div>
</section>
