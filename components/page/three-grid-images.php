<?php

/**
 * Three grid images — three-column image row on a full-bleed dark band
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type array $images  List of ACF image arrays or URLs (max 3).
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
	'three_grid_image_1',
	'three_grid_image_2',
	'three_grid_image_3',
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

<section class="three-grid-images">
	<div class="content-block">
		<div class="content-max">
			<ul class="three-grid-images__grid">
				<?php foreach ($images as $image) : ?>
					<li class="three-grid-images__item">
						<img
							class="three-grid-images__image"
							src="<?= esc_url($image['url']); ?>"
							<?php if ($image['srcset'] !== '') : ?>
								srcset="<?= esc_attr($image['srcset']); ?>"
								sizes="(min-width: 1140px) 33vw, 100vw"
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
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
