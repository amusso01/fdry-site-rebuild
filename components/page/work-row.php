<?php

/**
 * Work row — three-column hover expand grid
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type string $field          ACF repeater field name. Default work_row.
 *     @type bool   $show_more_work When true, show a More work CTA below the grid. Default false.
 *     @type string $variant        Visual variant: default or inner. Default default.
 *     @type bool   $slider         When false, stack cards on mobile instead of Swiper. Default true.
 *     @type int    $post_id        Post ID for ACF fallback. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();

$field = $args['field'] ?? 'work_row';
$field = is_string($field) && $field !== '' ? $field : 'work_row';

$show_more_work = ! empty($args['show_more_work']);

$allowed_variants = array('default', 'inner');
$variant          = $args['variant'] ?? 'default';
$variant          = is_string($variant) && in_array($variant, $allowed_variants, true) ? $variant : 'default';
$is_inner         = $variant === 'inner';
$enable_slider    = ! array_key_exists('slider', $args) || (bool) $args['slider'];

if (! $post_id || ! have_rows($field, $post_id)) {
	return;
}

$cards = array();

while (have_rows($field, $post_id)) {
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

	$description = '';
	$categories  = array();

	if (! $is_inner) {
		$description = get_field('description', $work_id);
		$description = is_string($description) ? trim($description) : '';

		foreach (get_the_category($work_id) as $category) {
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
	}

	$cards[] = array(
		'id'           => $work_id,
		'title'        => get_the_title($work_id),
		'permalink'    => get_permalink($work_id),
		'description'  => $description,
		'image_url'    => $image[0],
		'image_width'  => isset($image[1]) ? (int) $image[1] : 0,
		'image_height' => isset($image[2]) ? (int) $image[2] : 0,
		'image_alt'    => (string) get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true),
		'categories'   => $categories,
	);
}

if ($cards === array()) {
	return;
}
?>

<section class="work-row<?= $is_inner ? ' work-row--inner' : ''; ?><?= $enable_slider ? '' : ' work-row--stack'; ?>" aria-label="<?php esc_attr_e('Featured work', 'foundry'); ?>">
	<?php if ($enable_slider) : ?>
		<div class="swiper work-row__slider">
	<?php endif; ?>
		<div class="work-row__grid<?= $enable_slider ? ' swiper-wrapper' : ''; ?>">
			<?php foreach ($cards as $card) : ?>
				<article class="work-row__card<?= $enable_slider ? ' swiper-slide' : ''; ?>">
					<a
						class="work-row__link"
						href="<?= esc_url($card['permalink']); ?>"
						aria-label="<?= esc_attr(sprintf(__('View %s', 'foundry'), $card['title'])); ?>">
						<div class="work-row__media">
							<img
								class="work-row__image"
								src="<?= esc_url($card['image_url']); ?>"
								alt="<?= esc_attr($card['image_alt'] !== '' ? $card['image_alt'] : $card['title']); ?>"
								loading="eager"
								decoding="async"
								<?php if ($card['image_width'] > 0) : ?>
									width="<?= esc_attr((string) $card['image_width']); ?>"
								<?php endif; ?>
								<?php if ($card['image_height'] > 0) : ?>
									height="<?= esc_attr((string) $card['image_height']); ?>"
								<?php endif; ?>>
						</div>

						<div class="work-row__overlay">
							<div class="work-row__meta">
								<?php if ($card['title'] !== '') : ?>
									<p class="work-row__title"><?= esc_html($card['title']); ?></p>
								<?php endif; ?>

								<?php if ($is_inner) : ?>
									<span class="work-row__arrow" aria-hidden="true">
										<?php get_template_part('svg-template/svg-arrow'); ?>
									</span>
								<?php else : ?>
									<?php if ($card['description'] !== '') : ?>
										<p class="work-row__description"><?= esc_html($card['description']); ?></p>
									<?php endif; ?>

									<?php if ($card['categories'] !== array()) : ?>
										<ul class="work-row__categories" aria-label="<?php esc_attr_e('Categories', 'foundry'); ?>">
											<?php foreach ($card['categories'] as $category_name) : ?>
												<li class="work-row__category"><?= esc_html($category_name); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						</div>
					</a>
				</article>
			<?php endforeach; ?>
		</div>

	<?php if ($enable_slider) : ?>
		<div class="swiper-pagination work-row__pagination"></div>
		</div>
	<?php endif; ?>

	<?php if ($show_more_work) : ?>
		<div class="work-row__more">
			<?php
			get_template_part(
				'components/partials/button',
				null,
				array(
					'variant' => 'transparent',
					'label'   => __('More work', 'foundry'),
					'url'     => site_url('/work/'),
				)
			);
			?>
		</div>
	<?php endif; ?>
</section>
