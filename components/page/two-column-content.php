<?php

/**
 * Two column content — tagline, title, link list, and WYSIWYG body
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
 *     @type array        $links       List of ACF link arrays.
 *     @type string       $content     WYSIWYG body content.
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

/**
 * Escape a link href, preserving bare "#" placeholders (esc_url strips them).
 *
 * @param string $url Normalised link URL.
 * @return string
 */
$escape_link_href = static function (string $url): string {
	if ($url === '' || $url === '#') {
		return '#';
	}

	return esc_url($url);
};

$links = array();

if (isset($args['links']) && is_array($args['links'])) {
	foreach ($args['links'] as $link) {
		if (! is_array($link) || empty($link['title'])) {
			continue;
		}

		$parts = fdry_acf_link_parts($link);

		$links[] = array(
			'label'  => $link['title'],
			'url'    => $parts['url'],
			'target' => $parts['target'],
		);
	}
} elseif ($post_id && have_rows($prefix . '_links', $post_id)) {
	while (have_rows($prefix . '_links', $post_id)) {
		the_row();

		$link = get_sub_field('link');

		if (! is_array($link) || empty($link['title'])) {
			continue;
		}

		$parts = fdry_acf_link_parts($link);

		$links[] = array(
			'label'  => $link['title'],
			'url'    => $parts['url'],
			'target' => $parts['target'],
		);
	}
}

if ($tagline === '' && $title === '' && $links === array() && $content === '') {
	return;
}
?>

<section class="two-column-content">
	<div class="content-block">
		<div class="content-max">
			<div class="two-column-content__grid">
				<?php if ($tagline !== '' || $title !== '') : ?>
					<div class="two-column-content__header">
						<?php if ($tagline !== '') : ?>
							<<?= esc_attr($tagline_tag); ?> class="two-column-content__tagline"><?= esc_html($tagline); ?></<?= esc_attr($tagline_tag); ?>>
						<?php endif; ?>

						<?php if ($title !== '') : ?>
							<p class="two-column-content__title"><?= esc_html($title); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ($links !== array()) : ?>
					<ul class="two-column-content__links">
						<?php foreach ($links as $link) : ?>
							<li class="two-column-content__link-item">
								<a
									class="two-column-content__link"
									href="<?= esc_attr($escape_link_href($link['url'])); ?>"
									<?php if ($link['target'] !== '') : ?>
										target="<?= esc_attr($link['target']); ?>"
										<?php if ($link['target'] === '_blank') : ?>
											rel="noopener noreferrer"
										<?php endif; ?>
									<?php endif; ?>
								>
									<span class="two-column-content__link-label"><?= esc_html($link['label']); ?></span>
									<span class="two-column-content__link-arrow" aria-hidden="true">
										<?php get_template_part('svg-template/svg-arrow'); ?>
									</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ($content !== '') : ?>
					<div class="two-column-content__body">
						<?= wp_kses_post($content); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
