<?php

/**
 * Image text alternate — alternating content/image rows
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

$rows = array();

if ($post_id && have_rows('image_text_alternate', $post_id)) {
	while (have_rows('image_text_alternate', $post_id)) {
		the_row();

		$tagline = get_sub_field('tagline');
		$tagline = is_string($tagline) ? trim($tagline) : '';

		$row_title = get_sub_field('row_title');
		$row_title = is_string($row_title) ? trim($row_title) : '';

		$row_content = get_sub_field('row_content');
		$row_content = is_string($row_content) ? trim($row_content) : '';

		$image = $normalize_image(get_sub_field('image'));

		if ($tagline === '' && $row_title === '' && $row_content === '' && $image['url'] === '') {
			continue;
		}

		$rows[] = array(
			'tagline'     => $tagline,
			'row_title'   => $row_title,
			'row_content' => $row_content,
			'image'       => $image,
		);
	}
}

if ($rows === array()) {
	return;
}

foreach ($rows as $index => $row) {
	$row_number = $index + 1;
	$is_odd     = $row_number % 2 === 1;
	$row_class  = $is_odd ? 'image-text-alternate__row--content-left' : 'image-text-alternate__row--image-left';
	$has_image  = $row['image']['url'] !== '';
	?>
	<section class="image-text-alternate__row <?= esc_attr($row_class); ?>">
		<div class="content-block">
			<div class="content-max">
				<div class="image-text-alternate__inner">
					<?php if ($row['tagline'] !== '' || $row['row_title'] !== '' || $row['row_content'] !== '') : ?>
						<div class="image-text-alternate__content">
							<?php if ($row['tagline'] !== '') : ?>
								<h3 class="image-text-alternate__tagline"><?= esc_html($row['tagline']); ?></h3>
							<?php endif; ?>

							<?php if ($row['row_title'] !== '') : ?>
								<p class="image-text-alternate__title"><?= esc_html($row['row_title']); ?></p>
							<?php endif; ?>

							<?php if ($row['row_content'] !== '') : ?>
								<div class="image-text-alternate__body">
									<?= wp_kses_post($row['row_content']); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ($has_image) : ?>
						<div class="image-text-alternate__media">
							<img
								class="image-text-alternate__image"
								src="<?= esc_url($row['image']['url']); ?>"
								alt="<?= esc_attr($row['image']['alt']); ?>"
								loading="lazy"
								decoding="async"
								<?php if ($row['image']['width'] > 0) : ?>
									width="<?= esc_attr((string) $row['image']['width']); ?>"
								<?php endif; ?>
								<?php if ($row['image']['height'] > 0) : ?>
									height="<?= esc_attr((string) $row['image']['height']); ?>"
								<?php endif; ?>>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
}
