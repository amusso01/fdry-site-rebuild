<?php

/**
 * Two-column intro content
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type string       $tagline     Tagline text.
 *     @type string       $tagline_tag Semantic heading tag: h1–h4. Default h2.
 *     @type string       $title       Section title.
 *     @type string       $content     WYSIWYG body content.
 *     @type array|string $logo_1      ACF image array or URL for badge 1.
 *     @type array|string $logo_2      ACF image array or URL for badge 2.
 *     @type array|string $logo_3      ACF image array or URL for badge 3.
 *     @type array        $button_1    ACF link array for primary button.
 *     @type array        $button_2    ACF link array for white button.
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

$allowed_tags = array('h1', 'h2', 'h3', 'h4');

$tagline = $args['tagline'] ?? null;

if (! is_string($tagline) || $tagline === '') {
	$acf_tagline = $post_id ? get_field('intro_tagline', $post_id) : '';
	$tagline     = is_string($acf_tagline) ? trim($acf_tagline) : '';
}

$tagline_tag = $args['tagline_tag'] ?? null;

if (! is_string($tagline_tag) || ! in_array($tagline_tag, $allowed_tags, true)) {
	$acf_tag = $post_id ? get_field('intro_tagline_tag', $post_id) : '';
	$tagline_tag = is_string($acf_tag) && in_array($acf_tag, $allowed_tags, true) ? $acf_tag : 'h2';
}

$title = $args['title'] ?? null;

if (! is_string($title) || $title === '') {
	$acf_title = $post_id ? get_field('intro_title', $post_id) : '';
	$title     = is_string($acf_title) ? trim($acf_title) : '';
}

$content = $args['content'] ?? null;

if (! is_string($content) || $content === '') {
	$acf_content = $post_id ? get_field('intro_content', $post_id) : '';
	$content     = is_string($acf_content) ? trim($acf_content) : '';
}

$logo_fields = array('logo_1', 'logo_2', 'logo_3');
$acf_logo_map = array(
	'logo_1' => 'intro_logo_1',
	'logo_2' => 'intro_logo_2',
	'logo_3' => 'intro_logo_3',
);
$logos = array();

foreach ($logo_fields as $logo_key) {
	$logo_image = $args[ $logo_key ] ?? null;

	if ($logo_image === null && $post_id) {
		$logo_image = get_field($acf_logo_map[ $logo_key ], $post_id);
	}

	$logo = $normalize_image($logo_image);

	if ($logo['url'] !== '') {
		$logos[] = $logo;
	}
}

$button_1 = $args['button_1'] ?? null;

if ($button_1 === null && $post_id) {
	$button_1 = get_field('intro_button_1', $post_id);
}

$button_2 = $args['button_2'] ?? null;

if ($button_2 === null && $post_id) {
	$button_2 = get_field('intro_button_2', $post_id);
}

$button_1_parts = fdry_acf_link_parts($button_1);
$button_2_parts = fdry_acf_link_parts($button_2);

$button_1_label = is_array($button_1) && ! empty($button_1['title']) ? $button_1['title'] : '';
$button_2_label = is_array($button_2) && ! empty($button_2['title']) ? $button_2['title'] : '';

$has_button_1 = $button_1_label !== '' && $button_1_parts['url'] !== '#';
$has_button_2 = $button_2_label !== '' && $button_2_parts['url'] !== '#';

if ($tagline === '' && $title === '' && $content === '' && $logos === array() && ! $has_button_1 && ! $has_button_2) {
	return;
}
?>

<section class="intro-content">
	<div class="content-block">
		<div class="content-max">
			<div class="intro-content__grid">
				<div class="intro-content__copy">
					<?php if ($tagline !== '') : ?>
						<<?= esc_attr($tagline_tag); ?> class="intro-content__tagline"><?= esc_html($tagline); ?></<?= esc_attr($tagline_tag); ?>>
					<?php endif; ?>

					<?php if ($title !== '') : ?>
						<p class="intro-content__title"><?= esc_html($title); ?></p>
					<?php endif; ?>

					<?php if ($content !== '') : ?>
						<div class="intro-content__body">
							<?= wp_kses_post($content); ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ($logos !== array() || $has_button_1 || $has_button_2) : ?>
					<div class="intro-content__aside">
						<?php if ($logos !== array()) : ?>
							<ul class="intro-content__badges" aria-label="<?php esc_attr_e('Awards and recognition', 'foundry'); ?>">
								<?php foreach ($logos as $logo) : ?>
									<li class="intro-content__badge">
										<img
											class="intro-content__badge-image"
											src="<?= esc_url($logo['url']); ?>"
											alt="<?= esc_attr($logo['alt']); ?>"
											loading="lazy"
											decoding="async"
											<?php if ($logo['width'] > 0) : ?>
												width="<?= esc_attr((string) $logo['width']); ?>"
											<?php endif; ?>
											<?php if ($logo['height'] > 0) : ?>
												height="<?= esc_attr((string) $logo['height']); ?>"
											<?php endif; ?>>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>

						<?php if ($has_button_1 || $has_button_2) : ?>
							<div class="intro-content__actions">
								<?php if ($has_button_1) : ?>
									<?php
									get_template_part(
										'components/partials/button',
										null,
										array(
											'variant' => 'primary',
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
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
