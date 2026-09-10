<?php

/**
 * Centered content — tagline, title, WYSIWYG body, and optional buttons
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type string       $prefix      ACF field name prefix. Default centered.
 *     @type string       $appearance  Visual variant: light or dark. Default light.
 *     @type string       $tagline     Tagline text.
 *     @type string       $tagline_tag Semantic heading tag: h1–h4. Default h2.
 *     @type string       $title       Section title.
 *     @type string       $content     WYSIWYG body content.
 *     @type array        $button_1    ACF link array for primary button.
 *     @type array        $button_2    ACF link array for white button.
 *     @type bool         $is_first    When true, adds top spacing below the fixed header.
 *     @type int          $post_id     Post ID for ACF fallback. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();

$prefix = $args['prefix'] ?? 'centered';
$prefix = is_string($prefix) && $prefix !== '' ? $prefix : 'centered';

$allowed_appearances = array('light', 'dark');
$appearance          = $args['appearance'] ?? 'light';
$appearance          = is_string($appearance) && in_array($appearance, $allowed_appearances, true) ? $appearance : 'light';

$is_first = ! empty($args['is_first']);

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

$button_1 = $args['button_1'] ?? null;

if ($button_1 === null && $post_id) {
	$button_1 = get_field($prefix . '_button_1', $post_id);

	if (empty($button_1)) {
		$button_1 = get_field($prefix . '_button', $post_id);
	}
}

$button_2 = $args['button_2'] ?? null;

if ($button_2 === null && $post_id) {
	$button_2 = get_field($prefix . '_button_2', $post_id);
}

$button_1_parts = fdry_acf_link_parts($button_1);
$button_2_parts = fdry_acf_link_parts($button_2);

$button_1_label = is_array($button_1) && ! empty($button_1['title']) ? $button_1['title'] : '';
$button_2_label = is_array($button_2) && ! empty($button_2['title']) ? $button_2['title'] : '';

$has_button_1 = $button_1_label !== '' && $button_1_parts['url'] !== '#';
$has_button_2 = $button_2_label !== '' && $button_2_parts['url'] !== '#';

$button_1_variant = $appearance === 'dark' ? 'yellow' : 'primary';

if ($tagline === '' && $title === '' && $content === '' && ! $has_button_1 && ! $has_button_2) {
	return;
}
?>

<section class="centered-content centered-content--<?= esc_attr($appearance); ?><?= $is_first ? ' centered-content--first' : ''; ?>">
	<div class="content-block">
		<div class="content-max">
			<div class="centered-content__inner">
				<?php if ($tagline !== '') : ?>
					<<?= esc_attr($tagline_tag); ?> class="centered-content__tagline"><?= esc_html($tagline); ?></<?= esc_attr($tagline_tag); ?>>
				<?php endif; ?>

				<?php if ($title !== '') : ?>
					<p class="centered-content__title"><?= esc_html($title); ?></p>
				<?php endif; ?>

				<?php if ($content !== '') : ?>
					<div class="centered-content__body">
						<?= wp_kses_post($content); ?>
					</div>
				<?php endif; ?>

				<?php if ($has_button_1 || $has_button_2) : ?>
					<div class="centered-content__actions">
						<?php if ($has_button_1) : ?>
							<?php
							get_template_part(
								'components/partials/button',
								null,
								array(
									'variant' => $button_1_variant,
									'label'   => $button_1_label,
									'url'     => $button_1,
									'target'  => $button_1_parts['target'],
								)
							);
							?>
						<?php endif; ?>

						<?php if ($has_button_2) : ?>
							<?php
							get_template_part(
								'components/partials/button',
								null,
								array(
									'variant' => 'white',
									'label'   => $button_2_label,
									'url'     => $button_2,
									'target'  => $button_2_parts['target'],
								)
							);
							?>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
