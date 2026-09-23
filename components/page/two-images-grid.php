<?php

/**
 * Two images grid — edge-to-edge 50/50 image row within content-max
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type array $images  List of ACF image arrays or URLs (max 2).
 *     @type int   $post_id Post ID for ACF fallback. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();

$field_names = array(
	'two_images_image_1',
	'two_images_image_2',
);

$images = array();

if (isset($args['images']) && is_array($args['images'])) {
	foreach ($args['images'] as $image) {
		$image_parts = fdry_image_parts($image);

		if ($image_parts['url'] === '') {
			continue;
		}

		$images[] = $image_parts;
	}
} elseif ($post_id) {
	foreach ($field_names as $field_name) {
		$image_parts = fdry_image_parts(get_field($field_name, $post_id));

		if ($image_parts['url'] === '') {
			continue;
		}

		$images[] = $image_parts;
	}
}

if ($images === array()) {
	return;
}
?>

<section class="two-images-grid">
	<div class="content-max">
		<div class="two-images-grid__grid" data-fade-up>
			<?php foreach ($images as $image) : ?>
				<div class="two-images-grid__item">
					<img
						class="two-images-grid__image"
						src="<?= esc_url($image['url']); ?>"
						<?php if ($image['srcset'] !== '') : ?>
							srcset="<?= esc_attr($image['srcset']); ?>"
							sizes="(min-width: 640px) 50vw, 100vw"
						<?php endif; ?>
						alt="<?= esc_attr($image['alt']); ?>"
						loading="lazy"
						decoding="async"
						<?php if ($image['width'] > 0) : ?>
							width="<?= esc_attr((string) $image['width']); ?>"
						<?php endif; ?>
						<?php if ($image['height'] > 0) : ?>
							height="<?= esc_attr((string) $image['height']); ?>"
						<?php endif; ?>>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
