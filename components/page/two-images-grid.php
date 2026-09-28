<?php

/**
 * Two images grid — edge-to-edge 50/50 image row within content-max, with optional centred title, WYSIWYG body and button below
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type array  $images  List of ACF image arrays or URLs (max 2).
 *     @type string $title   Title below the images, rendered as H2.
 *     @type string $content WYSIWYG body content.
 *     @type array  $button  ACF link array for the white button.
 *     @type int    $post_id Post ID for ACF fallback. Default queried object.
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

$title = $args['title'] ?? null;

if (! is_string($title) || $title === '') {
	$acf_title = $post_id ? get_field('two_images_title', $post_id) : '';
	$title     = is_string($acf_title) ? trim($acf_title) : '';
}

$content = $args['content'] ?? null;

if (! is_string($content) || $content === '') {
	$acf_content = $post_id ? get_field('two_images_content', $post_id) : '';
	$content     = is_string($acf_content) ? trim($acf_content) : '';
}

$button = $args['button'] ?? null;

if ($button === null && $post_id) {
	$button = get_field('two_images_button', $post_id);
}

$button_parts = fdry_acf_link_parts($button);
$button_label = is_array($button) && ! empty($button['title']) ? $button['title'] : '';
$has_button   = $button_label !== '' && $button_parts['url'] !== '#';

$has_copy = $title !== '' || $content !== '' || $has_button;

if ($images === array() && ! $has_copy) {
	return;
}
?>

<section class="two-images-grid">
	<div class="content-max">
		<?php if ($images !== array()) : ?>
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
		<?php endif; ?>

		<?php if ($has_copy) : ?>
			<div class="content-block">
				<div class="two-images-grid__copy">
					<?php if ($title !== '') : ?>
						<h2 class="two-images-grid__title" data-fade-up><?= esc_html($title); ?></h2>
					<?php endif; ?>

					<?php if ($content !== '') : ?>
						<div class="two-images-grid__body wysiwyg" data-fade-up>
							<?= wp_kses_post($content); ?>
						</div>
					<?php endif; ?>

					<?php if ($has_button) : ?>
						<div class="two-images-grid__actions" data-fade-up>
							<?php
							get_template_part(
								'components/partials/button',
								null,
								array(
									'variant' => 'white',
									'label'   => $button_label,
									'url'     => $button,
									'target'  => $button_parts['target'],
								)
							);
							?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</section>
