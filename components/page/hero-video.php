<?php

/**
 * Hero video
 *
 * Renders a muted, looping background video behind an optional showreel
 * button. The still is a separate <picture>, not the video's poster
 * attribute, so it stays visible when the video is never loaded at all.
 * The video carries no src: sources are attached by
 * heroVideo.js so small screens, reduced-motion and data-saver users never
 * download it. See src/scripts/part/heroVideo.js.
 *
 * The showreel button opens a <dialog> with the full reel. It renders only
 * when the reel file is deployed, and its <video> also ships without a src:
 * showreelModal.js attaches one on hover/focus or click, so the page itself
 * downloads none of it. See src/scripts/part/showreelModal.js.
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
 *     @type array  $poster_mobile ACF image array shown at or below FDRY_HERO_MOBILE_MAX_PX.
 *     @type string $full_video    Full showreel MP4 URL. Default the theme file (fdry_showreel_sources()).
 *     @type string $full_video_sd 720p showreel MP4 URL for small screens and slow connections.
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

$has_showreel = $media['full_video'] !== '' && $media['label'] !== '';
$modal_id     = $has_showreel ? wp_unique_id('showreel-modal-') : '';
?>

<section
	class="<?= esc_attr(implode(' ', $section_classes)); ?>"
	data-mobile-max="<?= esc_attr((string) FDRY_HERO_MOBILE_MAX_PX); ?>"
	aria-label="<?= esc_attr($aria_label); ?>">
	<?php if ($media['poster']['url'] !== '') : ?>
		<picture class="hero-video__poster">
			<?php if ($media['poster_mobile']['url'] !== '') : ?>
				<source
					media="(max-width: <?php echo esc_attr((string) FDRY_HERO_MOBILE_MAX_PX); ?>px)"
					srcset="<?php echo esc_url($media['poster_mobile']['url']); ?>">
			<?php endif; ?>
			<img
				src="<?php echo esc_url($media['poster']['url']); ?>"
				alt=""
				decoding="async"
				<?php if ($variant === 'inline') : ?>
					loading="lazy"
				<?php else : ?>
					loading="eager"
					fetchpriority="high"
				<?php endif; ?>
				<?php if ($media['poster']['width'] > 0) : ?>
					width="<?php echo esc_attr((string) $media['poster']['width']); ?>"
				<?php endif; ?>
				<?php if ($media['poster']['height'] > 0) : ?>
					height="<?php echo esc_attr((string) $media['poster']['height']); ?>"
				<?php endif; ?>>
		</picture>
	<?php endif; ?>

	<video
		class="hero-video__media"
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

	<?php if ($has_showreel) : ?>
		<button
			type="button"
			class="hero-video__showreel"
			data-hero-showreel
			aria-haspopup="dialog"
			aria-controls="<?php echo esc_attr($modal_id); ?>">
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

		<dialog
			class="showreel-modal"
			id="<?php echo esc_attr($modal_id); ?>"
			aria-label="<?php echo esc_attr($media['label']); ?>">
			<div class="showreel-modal__inner">
				<button
					type="button"
					class="showreel-modal__close"
					data-showreel-close
					aria-label="<?php esc_attr_e('Close showreel', 'foundry'); ?>">
					<svg width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true" focusable="false">
						<path d="M3 3l14 14M17 3L3 17" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" />
					</svg>
				</button>
				<video
					class="showreel-modal__video"
					data-src="<?php echo esc_url($media['full_video']); ?>"
					data-src-sd="<?php echo esc_url($media['full_video_sd']); ?>"
					<?php if ($media['poster']['url'] !== '') : ?>
						poster="<?php echo esc_url($media['poster']['url']); ?>"
					<?php endif; ?>
					controls
					playsinline
					preload="none"></video>
				<p class="showreel-modal__error" hidden>
					<?php esc_html_e('The showreel could not be loaded. Please try again later.', 'foundry'); ?>
				</p>
			</div>
		</dialog>
	<?php endif; ?>
</section>
