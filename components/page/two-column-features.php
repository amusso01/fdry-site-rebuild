<?php

/**
 * Two column features — tagline, title, button, WYSIWYG body, and card grid
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type string $prefix          ACF field name prefix. Default two_column.
 *     @type string $tagline         Tagline text.
 *     @type string $tagline_tag     Semantic heading tag: h2–h4. Default h3.
 *     @type string $title           Section title.
 *     @type array  $button          ACF link array for primary button.
 *     @type array  $image           ACF image array.
 *     @type string $content         WYSIWYG body content.
 *     @type bool   $use_bigger_font When true, applies larger body typography.
 *     @type string $appearance      Visual variant: white or gray. Default white.
 *     @type int    $post_id         Post ID for ACF fallback. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();

$prefix = $args['prefix'] ?? 'two_column';
$prefix = is_string($prefix) && $prefix !== '' ? $prefix : 'two_column';

$allowed_appearances = array('white', 'gray');
$appearance          = $args['appearance'] ?? 'white';
$appearance          = is_string($appearance) && in_array($appearance, $allowed_appearances, true) ? $appearance : 'white';

$allowed_tags = array('h2', 'h3', 'h4');

$tagline = $args['tagline'] ?? null;

if (! is_string($tagline) || $tagline === '') {
	$acf_tagline = $post_id ? get_field($prefix . '_tagline', $post_id) : '';
	$tagline     = is_string($acf_tagline) ? trim($acf_tagline) : '';
}

$tagline_tag = $args['tagline_tag'] ?? null;

if (! is_string($tagline_tag) || ! in_array($tagline_tag, $allowed_tags, true)) {
	$acf_tag = $post_id ? get_field($prefix . '_tagline_tag', $post_id) : '';
	$tagline_tag = is_string($acf_tag) && in_array($acf_tag, $allowed_tags, true) ? $acf_tag : 'h3';
}

$title = $args['title'] ?? null;

if (! is_string($title) || $title === '') {
	$acf_title = $post_id ? get_field($prefix . '_title', $post_id) : '';
	$title     = is_string($acf_title) ? trim($acf_title) : '';
}

$content = $args['content'] ?? null;

if (! is_string($content) || $content === '') {
	$acf_content = $post_id ? get_field($prefix . '_content', $post_id) : '';
	$content     = is_string($acf_content) ? trim($acf_content) : '';
}

$use_bigger_font = $args['use_bigger_font'] ?? null;

if ($use_bigger_font === null && $post_id) {
	$use_bigger_font = (bool) get_field($prefix . '_use_bigger_font', $post_id);
} else {
	$use_bigger_font = (bool) $use_bigger_font;
}

$button = $args['button'] ?? null;

if ($button === null && $post_id) {
	$button = get_field($prefix . '_button', $post_id);
}

$button_parts = fdry_acf_link_parts($button);
$button_label = is_array($button) && ! empty($button['title']) ? $button['title'] : '';
$has_button   = $button_label !== '' && $button_parts['url'] !== '#';

$image = $args['image'] ?? null;

if ($image === null && $post_id) {
	$image = get_field($prefix . '_image', $post_id);
}

$image     = fdry_image_parts($image);
$has_image = $image['url'] !== '';

$cards = array();

if ($post_id && have_rows($prefix . '_list_items', $post_id)) {
	while (have_rows($prefix . '_list_items', $post_id)) {
		the_row();

		$icon_field = get_sub_field('icon');
		$icon       = acfFile_toSvg($icon_field);

		$card_title = get_sub_field('item');
		$card_title = is_string($card_title) ? trim($card_title) : '';

		$card_content = get_sub_field('content');
		$card_content = is_string($card_content) ? trim($card_content) : '';

		if ($icon === '' && $card_title === '' && $card_content === '') {
			continue;
		}

		$cards[] = array(
			'icon'    => $icon,
			'title'   => $card_title,
			'content' => $card_content,
		);
	}
}

if ($tagline === '' && $title === '' && ! $has_button && ! $has_image && $content === '' && $cards === array()) {
	return;
}
?>

<section class="two-column-features<?= $appearance === 'gray' ? ' two-column-features--gray' : ''; ?>">
	<div class="content-block">
		<div class="content-max">
			<?php if ($appearance === 'gray') : ?>
				<?php if ($tagline !== '' || $title !== '') : ?>
					<div class="two-column-features__header">
						<?php if ($tagline !== '') : ?>
							<<?= esc_attr($tagline_tag); ?> class="two-column-features__tagline" data-fade-up><?= esc_html($tagline); ?></<?= esc_attr($tagline_tag); ?>>
						<?php endif; ?>

						<?php if ($title !== '') : ?>
							<p class="two-column-features__title" data-fade-up data-fade-up-duration=".8"><?= esc_html($title); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ($has_image || $content !== '' || $cards !== array()) : ?>
					<div class="two-column-features__grid">
						<?php if ($has_image) : ?>
							<div class="two-column-features__left">
								<div class="two-column-features__media" data-fade-up>
									<img
										class="two-column-features__image"
										src="<?= esc_url($image['url']); ?>"
										<?php if ($image['srcset'] !== '') : ?>
											srcset="<?= esc_attr($image['srcset']); ?>"
											sizes="(min-width: 1140px) 40vw, 100vw"
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
							</div>
						<?php endif; ?>

						<?php if ($content !== '' || $cards !== array()) : ?>
							<div class="two-column-features__right">
								<?php if ($content !== '') : ?>
									<div class="two-column-features__body wysiwyg<?= $use_bigger_font ? ' two-column-features__body--bigger-font' : ''; ?>" data-fade-up>
										<?= wp_kses_post($content); ?>
									</div>
								<?php endif; ?>

								<?php if ($cards !== array()) : ?>
									<ul class="two-column-features__cards">
										<?php foreach ($cards as $index => $card) : ?>
											<?php
											$fade_delay = $index * 0.15;
											?>
											<li class="two-column-features__card" data-fade-up<?= $fade_delay > 0 ? ' data-fade-up-delay="' . esc_attr(number_format($fade_delay, 2)) . '"' : ''; ?>>
												<?php if ($card['icon'] !== '') : ?>
													<div class="two-column-features__card-icon" aria-hidden="true">
														<?= $card['icon']; ?>
													</div>
												<?php endif; ?>

												<?php if ($card['title'] !== '') : ?>
													<p class="two-column-features__card-title"><?= esc_html($card['title']); ?></p>
												<?php endif; ?>

												<?php if ($card['content'] !== '') : ?>
													<div class="two-column-features__card-content">
														<?= wp_kses_post(wpautop(esc_html($card['content']))); ?>
													</div>
												<?php endif; ?>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<div class="two-column-features__grid">
					<?php if ($tagline !== '' || $title !== '' || $has_button || $has_image) : ?>
						<div class="two-column-features__left">
							<?php if ($tagline !== '') : ?>
								<<?= esc_attr($tagline_tag); ?> class="two-column-features__tagline" data-fade-up><?= esc_html($tagline); ?></<?= esc_attr($tagline_tag); ?>>
							<?php endif; ?>

							<?php if ($title !== '') : ?>
								<p class="two-column-features__title" data-fade-up data-fade-up-duration=".8"><?= esc_html($title); ?></p>
							<?php endif; ?>

							<?php if ($has_image) : ?>
								<div class="two-column-features__media" data-fade-up>
									<img
										class="two-column-features__image"
										src="<?= esc_url($image['url']); ?>"
										<?php if ($image['srcset'] !== '') : ?>
											srcset="<?= esc_attr($image['srcset']); ?>"
											sizes="(min-width: 1140px) 40vw, 100vw"
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

							<?php if ($has_button) : ?>
								<div class="two-column-features__actions" data-fade-up>
									<?php
									get_template_part(
										'components/partials/button',
										null,
										array(
											'variant' => 'primary',
											'label'   => $button_label,
											'url'     => $button,
											'target'  => $button_parts['target'],
										)
									);
									?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ($content !== '' || $cards !== array()) : ?>
						<div class="two-column-features__right">
							<?php if ($content !== '') : ?>
								<div class="two-column-features__body wysiwyg<?= $use_bigger_font ? ' two-column-features__body--bigger-font' : ''; ?>" data-fade-up>
									<?= wp_kses_post($content); ?>
								</div>
							<?php endif; ?>

							<?php if ($cards !== array()) : ?>
								<ul class="two-column-features__cards">
									<?php foreach ($cards as $index => $card) : ?>
										<?php
										$fade_delay = $index * 0.15;
										?>
										<li class="two-column-features__card" data-fade-up<?= $fade_delay > 0 ? ' data-fade-up-delay="' . esc_attr(number_format($fade_delay, 2)) . '"' : ''; ?>>
											<?php if ($card['icon'] !== '') : ?>
												<div class="two-column-features__card-icon" aria-hidden="true">
													<?= $card['icon']; ?>
												</div>
											<?php endif; ?>

											<?php if ($card['title'] !== '') : ?>
												<p class="two-column-features__card-title"><?= esc_html($card['title']); ?></p>
											<?php endif; ?>

											<?php if ($card['content'] !== '') : ?>
												<div class="two-column-features__card-content">
													<?= wp_kses_post(wpautop(esc_html($card['content']))); ?>
												</div>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
