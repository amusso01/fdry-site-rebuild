<?php

/**
 * Two column cards — tagline, title, button, WYSIWYG body, and card grid
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type string       $prefix      ACF field name prefix. Default two_column.
 *     @type string       $tagline     Tagline text.
 *     @type string       $tagline_tag Semantic heading tag: h1–h4. Default h2.
 *     @type string       $title       Section title.
 *     @type array        $button      ACF link array for primary button.
 *     @type string       $content          WYSIWYG body content.
 *     @type bool          $use_bigger_font  When true, applies larger body typography.
 *     @type int          $post_id          Post ID for ACF fallback. Default queried object.
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

$allowed_tags = array('h1', 'h2', 'h3', 'h4');

$tagline = $args['tagline'] ?? null;

if (! is_string($tagline) || $tagline === '') {
	$acf_tagline = $post_id ? get_field($prefix . '_tagline', $post_id) : '';
	$tagline     = is_string($acf_tagline) ? trim($acf_tagline) : '';
}

$tagline_tag = $args['tagline_tag'] ?? null;

if (! is_string($tagline_tag) || ! in_array($tagline_tag, $allowed_tags, true)) {
	$acf_tag = $post_id ? get_field($prefix . '_tagline_tag', $post_id) : '';
	$tagline_tag = is_string($acf_tag) && in_array($acf_tag, $allowed_tags, true) ? $acf_tag : 'h2';
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

$cards = array();

if ($post_id && have_rows($prefix . '_cards', $post_id)) {
	while (have_rows($prefix . '_cards', $post_id)) {
		the_row();

		$svg_field = get_sub_field('svg');
		$svg       = acfFile_toSvg($svg_field);

		$card_title = get_sub_field('title');
		$card_title = is_string($card_title) ? trim($card_title) : '';

		$card_content = get_sub_field('content');
		$card_content = is_string($card_content) ? trim($card_content) : '';

		if ($svg === '' && $card_title === '' && $card_content === '') {
			continue;
		}

		$cards[] = array(
			'svg'     => $svg,
			'title'   => $card_title,
			'content' => $card_content,
		);
	}
}

if ($tagline === '' && $title === '' && ! $has_button && $content === '' && $cards === array()) {
	return;
}
?>

<section class="two-column-cards">
	<div class="content-block">
		<div class="content-max">
			<div class="two-column-cards__grid">
				<?php if ($tagline !== '' || $title !== '' || $has_button) : ?>
					<div class="two-column-cards__left">
						<?php if ($tagline !== '') : ?>
							<<?= esc_attr($tagline_tag); ?> class="two-column-cards__tagline"><?= esc_html($tagline); ?></<?= esc_attr($tagline_tag); ?>>
						<?php endif; ?>

						<?php if ($title !== '') : ?>
							<p class="two-column-cards__title"><?= esc_html($title); ?></p>
						<?php endif; ?>

						<?php if ($has_button) : ?>
							<div class="two-column-cards__actions">
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
					<div class="two-column-cards__right">
						<?php if ($content !== '') : ?>
							<div class="two-column-cards__body wysiwyg<?= $use_bigger_font ? ' two-column-cards__body--bigger-font' : ''; ?>">
								<?= wp_kses_post($content); ?>
							</div>
						<?php endif; ?>

						<?php if ($cards !== array()) : ?>
							<ul class="two-column-cards__cards">
								<?php foreach ($cards as $card) : ?>
									<li class="two-column-cards__card">
										<?php if ($card['svg'] !== '') : ?>
											<div class="two-column-cards__card-icon" aria-hidden="true">
												<?= $card['svg']; ?>
											</div>
										<?php endif; ?>

										<?php if ($card['title'] !== '') : ?>
											<p class="two-column-cards__card-title"><?= esc_html($card['title']); ?></p>
										<?php endif; ?>

										<?php if ($card['content'] !== '') : ?>
											<div class="two-column-cards__card-content wysiwyg">
												<?= wp_kses_post($card['content']); ?>
											</div>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
