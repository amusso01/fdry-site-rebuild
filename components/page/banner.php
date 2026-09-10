<?php

/**
 * Image banner — full-width image inside content gutters
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type array|string $image   ACF image array or URL.
 *     @type int          $post_id Post ID for ACF fallback. Default queried object.
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

$image = $args['image'] ?? null;

if ($image === null && $post_id) {
	$image = get_field('banner_image', $post_id);
}

$image = $normalize_image($image);

if ($image['url'] === '') {
	return;
}
?>

<section class="image-banner">
	<div class="content-block">
		<div class="content-max">
			<div class="image-banner__media">
				<img
					class="image-banner__image"
					src="<?= esc_url($image['url']); ?>"
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
		</div>
	</div>
</section>
