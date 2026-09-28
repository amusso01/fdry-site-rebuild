<?php

/**
 * Insight archive — tagline, title, and WYSIWYG intro, then the post grid
 *
 * Each card has three links to the post: image, Read more, and title. The
 * article itself is not wrapped in <a>. The image link is a duplicate, so it
 * is kept out of the tab order and hidden from assistive tech.
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

$tagline = $post_id ? get_field('insight_tagline', $post_id) : '';
$tagline = is_string($tagline) ? trim($tagline) : '';

$title = $post_id ? get_field('insight_title', $post_id) : '';
$title = is_string($title) ? trim($title) : '';

$content = $post_id ? get_field('insight_content', $post_id) : '';
$content = is_string($content) ? trim($content) : '';

$has_intro = $tagline !== '' || $title !== '' || $content !== '';

// The legacy loop asked for -8 per page, which WordPress treats as "all",
// so its pagination never printed. Same output, without the pagination.
$insights = new WP_Query(
	array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	)
);
?>

<section class="insight-archive">
	<div class="content-block">
		<div class="content-max">
			<?php if ($has_intro) : ?>
				<div class="insight-archive__intro">
					<?php if ($tagline !== '' || $title !== '') : ?>
						<div class="insight-archive__header">
							<?php if ($tagline !== '') : ?>
								<h1 class="insight-archive__tagline" data-fade-up><?= esc_html($tagline); ?></h1>
							<?php endif; ?>

							<?php if ($title !== '') : ?>
								<p class="insight-archive__title" data-fade-up data-fade-up-delay="0.2"><?= esc_html($title); ?></p>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ($content !== '') : ?>
						<div class="insight-archive__body wysiwyg" data-fade-up-group>
							<?= wp_kses_post($content); ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ($insights->have_posts()) : ?>
				<div class="insight-archive__grid">
					<?php while ($insights->have_posts()) : ?>
						<?php
						$insights->the_post();

						$card_title = get_the_title();
						$permalink  = (string) get_permalink();

						$category = '';

						foreach (get_the_category() as $term) {
							if ($term instanceof WP_Term && $term->slug !== 'uncategorized') {
								$category = $term->name;
								break;
							}
						}

						$thumbnail_id = (int) get_post_thumbnail_id();
						$image_alt    = $thumbnail_id ? (string) get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) : '';
						$image_alt    = $image_alt !== '' ? $image_alt : wp_strip_all_tags($card_title);
						$image_html   = $thumbnail_id ? wp_get_attachment_image(
							$thumbnail_id,
							'large',
							false,
							array(
								'class'    => 'insight-card__image',
								'alt'      => $image_alt,
								'loading'  => 'lazy',
								'decoding' => 'async',
								'sizes'    => '(min-width: 768px) 50vw, 100vw',
							)
						) : '';
						?>
						<article class="insight-card" data-fade-up>
							<?php if ($image_html !== '') : ?>
								<a class="insight-card__media" href="<?= esc_url($permalink); ?>" tabindex="-1" aria-hidden="true">
									<?= $image_html; ?>
								</a>
							<?php endif; ?>

							<div class="insight-card__meta">
								<?php if ($category !== '') : ?>
									<p class="insight-card__category"><?= esc_html($category); ?></p>
								<?php endif; ?>

								<a
									class="insight-card__more"
									href="<?= esc_url($permalink); ?>"
									aria-label="<?= esc_attr(sprintf(__('Read more about %s', 'foundry'), wp_strip_all_tags($card_title))); ?>"><?= esc_html__('Read more', 'foundry'); ?></a>
							</div>

							<h2 class="insight-card__title">
								<a class="insight-card__title-link" href="<?= esc_url($permalink); ?>"><?= esc_html($card_title); ?></a>
							</h2>
						</article>
					<?php endwhile; ?>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
