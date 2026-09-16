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

/**
 * @param array|string|false|null $image ACF image field value.
 * @return array{url: string, alt: string, width: int, height: int}
 */
$normalize_image = static function ($image): array {
	$empty = array(
		'url'    => '',
		'alt'    => '',
		'width'  => 0,
		'height' => 0,
	);

	if (! $image) {
		return $empty;
	}

	if (is_string($image) && $image !== '') {
		return array(
			'url'    => $image,
			'alt'    => '',
			'width'  => 0,
			'height' => 0,
		);
	}

	if (! is_array($image) || empty($image['url'])) {
		return $empty;
	}

	return array(
		'url'    => is_string($image['url']) ? $image['url'] : '',
		'alt'    => is_string($image['alt'] ?? null) ? $image['alt'] : '',
		'width'  => isset($image['width']) ? (int) $image['width'] : 0,
		'height' => isset($image['height']) ? (int) $image['height'] : 0,
	);
};

$desktop_image = $args['desktop_image'] ?? null;

if ($desktop_image === null && $post_id) {
	$desktop_image = get_field('banner_desktop_image', $post_id);
}

$mobile_image = $args['mobile_image'] ?? null;

if ($mobile_image === null && $post_id) {
	$mobile_image = get_field('banner_mobile_image', $post_id);
}

$desktop_image = $normalize_image($desktop_image);
$mobile_image  = $normalize_image($mobile_image);

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
			<picture class="inner-image-banner__picture">
				<?php if ($has_mobile_image) : ?>
					<source
						media="(max-width: 639px)"
						srcset="<?= esc_url($mobile_image['url']); ?>">
				<?php endif; ?>

				<img
					class="inner-image-banner__image"
					src="<?= esc_url($display_image['url']); ?>"
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
