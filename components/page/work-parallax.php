<?php

/**
 * Work parallax stack
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

if (! $post_id || ! have_rows('work_parallax', $post_id)) {
	return;
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
		'permalink'    => get_permalink($work_id),
		'image_url'    => $image[0],
		'image_width'  => isset($image[1]) ? (int) $image[1] : 0,
		'image_height' => isset($image[2]) ? (int) $image[2] : 0,
		'image_alt'    => (string) get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true),
		'tagline'      => $tagline,
		'categories'   => $categories,
	);
}

if ($cards === array()) {
	return;
}
?>

<section class="work-parallax" aria-label="<?php esc_attr_e('Featured work', 'foundry'); ?>">
	<div class="content-block">
		<div class="content-max">
			<div class="work-parallax__stack">
				<?php foreach ($cards as $index => $card) : ?>
					<article
						class="work-parallax__card"
						style="z-index: <?= esc_attr((string) ($index + 1)); ?>;">
						<a
							class="work-parallax__link"
							href="<?= esc_url($card['permalink']); ?>"
							aria-label="<?= esc_attr(sprintf(__('View %s', 'foundry'), $card['title'])); ?>">
							<div class="work-parallax__media">
								<img
									class="work-parallax__image"
									src="<?= esc_url($card['image_url']); ?>"
									alt="<?= esc_attr($card['image_alt'] !== '' ? $card['image_alt'] : $card['title']); ?>"
									loading="<?= $index === 0 ? 'eager' : 'lazy'; ?>"
									decoding="async"
									<?php if ($card['image_width'] > 0) : ?>
										width="<?= esc_attr((string) $card['image_width']); ?>"
									<?php endif; ?>
									<?php if ($card['image_height'] > 0) : ?>
										height="<?= esc_attr((string) $card['image_height']); ?>"
									<?php endif; ?>>
							</div>

							<div class="work-parallax__overlay">
								<div class="work-parallax__meta">
									<?php if ($card['title'] !== '') : ?>
										<p class="work-parallax__title"><?= esc_html($card['title']); ?></p>
									<?php endif; ?>

									<?php if ($card['tagline'] !== '') : ?>
										<p class="work-parallax__tagline"><?= esc_html($card['tagline']); ?></p>
									<?php endif; ?>

									<span class="work-parallax__arrow" aria-hidden="true">
										<?php get_template_part('svg-template/svg-arrow'); ?>
									</span>
								</div>

								<?php if ($card['categories'] !== array()) : ?>
									<ul class="work-parallax__categories" aria-label="<?php esc_attr_e('Categories', 'foundry'); ?>">
										<?php foreach ($card['categories'] as $category_name) : ?>
											<li class="work-parallax__category"><?= esc_html($category_name); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
