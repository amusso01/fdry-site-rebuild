<?php

/**
 * Two images grid — edge-to-edge 50/50 image row within content-max, each image with an optional centred title, WYSIWYG body and button below it
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type array $items {
 *         List of up to two items.
 *
 *         @type array|string $image   ACF image array or URL.
 *         @type string       $title   Title below the image, rendered as H2.
 *         @type string       $content WYSIWYG body content.
 *         @type array        $button  ACF link array for the white button.
 *     }
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

$raw_items = array();

if (isset($args['items']) && is_array($args['items'])) {
	$raw_items = $args['items'];
} elseif ($post_id) {
	foreach (array(1, 2) as $index) {
		$raw_items[] = array(
			'image'   => get_field('two_images_image_' . $index, $post_id),
			'title'   => get_field('two_images_title_' . $index, $post_id),
			'content' => get_field('two_images_content_' . $index, $post_id),
			'button'  => get_field('two_images_button_' . $index, $post_id),
		);
	}
}

$items = array();

foreach (array_slice($raw_items, 0, 2) as $raw_item) {
	if (! is_array($raw_item)) {
		continue;
	}

	$image   = fdry_image_parts($raw_item['image'] ?? null);
	$title   = is_string($raw_item['title'] ?? null) ? trim($raw_item['title']) : '';
	$content = is_string($raw_item['content'] ?? null) ? trim($raw_item['content']) : '';

	$button       = $raw_item['button'] ?? null;
	$button_parts = fdry_acf_link_parts($button);
	$button_label = is_array($button) && ! empty($button['title']) ? $button['title'] : '';
	$has_button   = $button_label !== '' && $button_parts['url'] !== '#';

	if ($image['url'] === '' && $title === '' && $content === '' && ! $has_button) {
		continue;
	}

	$items[] = array(
		'image'        => $image,
		'title'        => $title,
		'content'      => $content,
		'button'       => $button,
		'button_parts' => $button_parts,
		'button_label' => $button_label,
		'has_button'   => $has_button,
	);
}

if ($items === array()) {
	return;
}
?>

<section class="two-images-grid">
	<div class="content-max">
		<div class="two-images-grid__grid">
			<?php foreach ($items as $index => $item) : ?>
				<?php
				$image      = $item['image'];
				$fade_delay = $index * 0.15;
				$fade_attrs = 'data-fade-up' . ($fade_delay > 0 ? ' data-fade-up-delay="' . esc_attr(number_format($fade_delay, 2)) . '"' : '');
				$has_copy   = $item['title'] !== '' || $item['content'] !== '' || $item['has_button'];
				?>
				<div class="two-images-grid__item">
					<?php if ($image['url'] !== '') : ?>
						<div class="two-images-grid__media" <?= $fade_attrs; ?>>
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
					<?php endif; ?>

					<?php if ($has_copy) : ?>
						<div class="two-images-grid__copy">
							<?php if ($item['title'] !== '') : ?>
								<h2 class="two-images-grid__title" <?= $fade_attrs; ?>><?= esc_html($item['title']); ?></h2>
							<?php endif; ?>

							<?php if ($item['content'] !== '') : ?>
								<div class="two-images-grid__body wysiwyg" <?= $fade_attrs; ?>>
									<?= wp_kses_post($item['content']); ?>
								</div>
							<?php endif; ?>

							<?php if ($item['has_button']) : ?>
								<div class="two-images-grid__actions" <?= $fade_attrs; ?>>
									<?php
									get_template_part(
										'components/partials/button',
										null,
										array(
											'variant' => 'white',
											'label'   => $item['button_label'],
											'url'     => $item['button'],
											'target'  => $item['button_parts']['target'],
										)
									);
									?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
