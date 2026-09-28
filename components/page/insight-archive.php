<?php

/**
 * Insight archive — tagline, title, and WYSIWYG intro, then the post grid
 *
 * The cards reuse the legacy markup from loop-templates/content-insight.php
 * (.grid-insight / .insight-grid), so css/theme.css and mainstyle.css still
 * style them until they are redesigned.
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

$spinner_url = get_template_directory_uri() . '/img/Spinner.gif';
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
					<div class="row grid-insight">
						<?php while ($insights->have_posts()) : ?>
							<?php
							$insights->the_post();

							$categories = get_the_category();
							$category   = $categories[0]->name ?? '';

							$thumbnail_id  = (int) get_post_thumbnail_id();
							$thumbnail_alt = $thumbnail_id ? (string) get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true) : '';
							$image         = $thumbnail_id ? wp_get_attachment_image_src($thumbnail_id, 'full') : false;

							$permalink   = get_permalink();
							$description = get_field('description_insight');
							?>
							<article class="col-md-6 insight-grid" data-fade-up>
								<a href="<?= esc_url($permalink); ?>">
									<?php if ($image) : ?>
										<img src="<?= esc_url($spinner_url); ?>" data-src="<?= esc_url($image[0]); ?>" alt="<?= esc_attr($thumbnail_alt); ?>" class="img-fluid lozad" />
										<noscript><img src="<?= esc_url($image[0]); ?>" alt="<?= esc_attr($thumbnail_alt); ?>" class="img-fluid lozad" /></noscript>
									<?php endif; ?>
								</a>
								<div class="insight-info">
									<p class="insight-cat"><?= esc_html($category); ?></p>
									<a class="see-more see-more__insight" href="<?= esc_url($permalink); ?>">READ MORE <i class="fa fa-chevron-right"></i></a>
								</div>
								<div class="grey-line"></div>
								<div class="insight-title">
									<a href="<?= esc_url($permalink); ?>">
										<?php the_title('<h4 style="color: #1a1c22;">', '</h4>'); ?>
									</a>
									<?php if (is_string($description) && $description !== '') : ?>
										<?= wp_kses_post($description); ?>
									<?php endif; ?>
								</div>
							</article>
						<?php endwhile; ?>
					</div>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
