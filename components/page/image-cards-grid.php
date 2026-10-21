<?php

/**
 * Image cards grid — three 3:2 image cards within content-max, each with an optional centred title, WYSIWYG body and button below it
 *
 * The three-column take on two-images-grid.php. Its cards come from the
 * Global variables options page (`single_insight_cards`), so every insight
 * post shows the same ones.
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override the ACF values.
 *
 *     @type array $items {
 *         List of up to three items.
 *
 *         @type array|string $image   ACF image array or URL.
 *         @type string       $title   Title below the image, rendered as H2.
 *         @type string       $content WYSIWYG body content.
 *         @type array        $button  ACF link array for the white button.
 *     }
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

if (isset($args['items']) && is_array($args['items'])) {
	$raw_items = $args['items'];
} else {
	$raw_items = get_field('single_insight_cards', 'option');
	$raw_items = is_array($raw_items) ? $raw_items : array();
}

$items = array();

foreach (array_slice($raw_items, 0, 3) as $raw_item) {
	if (! is_array($raw_item)) {
		continue;
	}

	$image   = fdry_image_parts($raw_item['image'] ?? null);
	$title   = is_string($raw_item['title'] ?? null) ? trim($raw_item['title']) : '';
	$content = is_string($raw_item['content'] ?? null) ? trim($raw_item['content']) : '';

	$button       = $raw_item['button'] ?? null;
	$button_parts = fdry_acf_link_parts($button);
	$button_label = is_array($button) && ! empty($button['title']) ? $button['title'] : '';
	// A label is enough: a `#` URL still shows the button.
	$has_button   = $button_label !== '';

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

<section class="image-cards-grid">
	<div class="content-block">
		<div class="content-max">
			<ul class="image-cards-grid__grid">
				<?php foreach ($items as $index => $item) : ?>
					<?php
					$image      = $item['image'];
					$fade_attrs = fdry_fade_attrs(false, $index * 0.15);
					$has_copy   = $item['title'] !== '' || $item['content'] !== '' || $item['has_button'];
					?>
					<li class="image-cards-grid__item">
						<?php if ($image['url'] !== '') : ?>
							<div class="image-cards-grid__media"<?= $fade_attrs; ?>>
								<img
									class="image-cards-grid__image"
									src="<?= esc_url($image['url']); ?>"
									<?php if ($image['srcset'] !== '') : ?>
										srcset="<?= esc_attr($image['srcset']); ?>"
										sizes="(max-width: 919px) 100vw, (max-width: 1919px) 33vw, 600px"
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
							<div class="image-cards-grid__copy">
								<?php if ($item['title'] !== '') : ?>
									<h2 class="image-cards-grid__title"<?= $fade_attrs; ?>><?= esc_html($item['title']); ?></h2>
								<?php endif; ?>

								<?php if ($item['content'] !== '') : ?>
									<div class="image-cards-grid__body wysiwyg"<?= $fade_attrs; ?>>
										<?= wp_kses_post($item['content']); ?>
									</div>
								<?php endif; ?>

								<?php if ($item['has_button']) : ?>
									<div class="image-cards-grid__actions"<?= $fade_attrs; ?>>
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
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
