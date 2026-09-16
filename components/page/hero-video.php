<?php

/**
 * Hero video
 *
 * Renders a muted, looping background video behind an optional showreel
 * button. The video carries a poster and no src: sources are attached by
 * heroVideo.js so small screens, reduced-motion and data-saver users never
 * download it. See src/scripts/part/heroVideo.js.
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type string $mp4        MP4 URL for muted background autoplay.
 *     @type string $webm       WebM URL, offered before the MP4.
 *     @type array  $poster     ACF image array for the poster frame.
 *     @type string $full_video URL for the full showreel (modal hook only).
 *     @type string $label      Showreel button label.
 *     @type array  $thumb      ACF image array for the showreel thumbnail.
 *     @type string $prefix     ACF field set to read: hero or showreel.
 *     @type string $variant    Layout variant: hero (default) or inline.
 *     @type string $aria_label Accessible section label. Default Hero.
 *     @type int    $post_id    Post ID for ACF fallback. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();
$prefix  = isset($args['prefix']) && $args['prefix'] === 'showreel' ? 'showreel' : 'hero';

$media = fdry_hero_media($post_id, $prefix, $args);

if ($media['mp4'] === '' && $media['webm'] === '') {
	return;
}

$allowed_variants = array('hero', 'inline');
$variant          = $args['variant'] ?? 'hero';
$variant          = is_string($variant) && in_array($variant, $allowed_variants, true) ? $variant : 'hero';

$aria_label = $args['aria_label'] ?? null;

if (! is_string($aria_label) || $aria_label === '') {
	$aria_label = $variant === 'inline' ? __('Showreel', 'foundry') : __('Hero', 'foundry');
}

$section_classes = array('hero-video');

if ($variant === 'inline') {
	$section_classes[] = 'hero-video--inline';
}
?>

<section class="<?= esc_attr(implode(' ', $section_classes)); ?>" aria-label="<?= esc_attr($aria_label); ?>">
	<video
		class="hero-video__media"
		<?php if ($media['poster']['url'] !== '') : ?>
			poster="<?php echo esc_url($media['poster']['url']); ?>"
		<?php endif; ?>
		<?php if ($media['mp4'] !== '') : ?>
			data-src-mp4="<?php echo esc_url($media['mp4']); ?>"
		<?php endif; ?>
		<?php if ($media['webm'] !== '') : ?>
			data-src-webm="<?php echo esc_url($media['webm']); ?>"
		<?php endif; ?>
		muted
		loop
		playsinline
		preload="none"
		aria-hidden="true"></video>

	<?php if ($media['label'] !== '') : ?>
		<button
			type="button"
			class="hero-video__showreel"
			data-hero-showreel
			<?php if ($media['full_video'] !== '') : ?>
				data-hero-full-video="<?php echo esc_url($media['full_video']); ?>"
			<?php endif; ?>>
			<?php if ($media['thumb']['url'] !== '') : ?>
				<span class="hero-video__showreel-thumb">
					<img
						src="<?php echo esc_url($media['thumb']['url']); ?>"
						alt=""
						loading="lazy"
						decoding="async"
						<?php if ($media['thumb']['width'] > 0) : ?>
							width="<?php echo esc_attr((string) $media['thumb']['width']); ?>"
						<?php endif; ?>
						<?php if ($media['thumb']['height'] > 0) : ?>
							height="<?php echo esc_attr((string) $media['thumb']['height']); ?>"
						<?php endif; ?>>
				</span>
			<?php endif; ?>
			<span class="hero-video__showreel-label"><?php echo esc_html($media['label']); ?></span>
			<span class="hero-video__showreel-arrow" aria-hidden="true">
				<?php get_template_part('svg-template/svg-arrow'); ?>
			</span>
		</button>
	<?php endif; ?>
</section>
