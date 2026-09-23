<?php

/**
 * Inner image banner — full-width desktop/mobile image inside content gutters
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type array|string $desktop_image ACF image array or URL.
 *     @type array|string $mobile_image  ACF image array or URL.
 *     @type int          $post_id       Post ID for ACF fallback. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();

$desktop_image = $args['desktop_image'] ?? null;

if ($desktop_image === null && $post_id) {
	$desktop_image = get_field('banner_desktop_image', $post_id);
}

$mobile_image = $args['mobile_image'] ?? null;

if ($mobile_image === null && $post_id) {
	$mobile_image = get_field('banner_mobile_image', $post_id);
}

$desktop_image = fdry_image_parts($desktop_image);
$mobile_image  = fdry_image_parts($mobile_image);

if ($desktop_image['url'] === '') {
	return;
}

$has_mobile_image = $mobile_image['url'] !== '';
$display_image    = $desktop_image;
$display_alt      = $desktop_image['alt'] !== '' ? $desktop_image['alt'] : $mobile_image['alt'];
?>

<section class="inner-image-banner">
	<div class="content-block">
		<div class="content-max">
			<picture class="inner-image-banner__picture" data-fade-up>
				<?php if ($has_mobile_image) : ?>
					<?php if ($mobile_image['srcset'] !== '') : ?>
						<source
							media="(max-width: 639px)"
							srcset="<?= esc_attr($mobile_image['srcset']); ?>"
							sizes="100vw">
					<?php else : ?>
						<source
							media="(max-width: 639px)"
							srcset="<?= esc_url($mobile_image['url']); ?>">
					<?php endif; ?>
				<?php endif; ?>

				<img
					class="inner-image-banner__image"
					src="<?= esc_url($display_image['url']); ?>"
					<?php if ($display_image['srcset'] !== '') : ?>
						srcset="<?= esc_attr($display_image['srcset']); ?>"
						sizes="100vw"
					<?php endif; ?>
					alt="<?= esc_attr($display_alt); ?>"
					loading="lazy"
					decoding="async"
					<?php if ($display_image['width'] > 0) : ?>
						width="<?= esc_attr((string) $display_image['width']); ?>"
					<?php endif; ?>
					<?php if ($display_image['height'] > 0) : ?>
						height="<?= esc_attr((string) $display_image['height']); ?>"
					<?php endif; ?>>
			</picture>
		</div>
	</div>
</section>
