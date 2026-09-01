<?php

/**
 * Hero video
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type string $autoplay_video URL for the muted background autoplay video.
 *     @type string $full_video     URL for the full showreel (modal hook only).
 *     @type string $showreel_label Showreel button label.
 *     @type array  $showreel_thumb ACF image array for the showreel thumbnail.
 *     @type int    $post_id        Post ID for ACF fallback. Default queried object.
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
 * @param array|string|false|null $file ACF file field value.
 */
$normalize_file_url = static function ($file): string {
	if (! $file) {
		return '';
	}

	if (is_string($file)) {
		return trim($file);
	}

	if (is_array($file) && ! empty($file['url']) && is_string($file['url'])) {
		return trim($file['url']);
	}

	return '';
};

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

$autoplay_video = $args['autoplay_video'] ?? null;

if ($autoplay_video === null && $post_id) {
	$autoplay_video = get_field('hero_autoplay_video', $post_id);
}

$autoplay_video = $normalize_file_url($autoplay_video);

if ($autoplay_video === '') {
	return;
}

$full_video = $args['full_video'] ?? null;

if ($full_video === null && $post_id) {
	$full_video = get_field('hero_full_video', $post_id);
}

$full_video = $normalize_file_url($full_video);

$showreel_label = $args['showreel_label'] ?? null;

if (! is_string($showreel_label) || $showreel_label === '') {
	$acf_label      = $post_id ? get_field('hero_showreel_label', $post_id) : '';
	$showreel_label = is_string($acf_label) && $acf_label !== '' ? $acf_label : 'SEE FULL SHOWREEL';
}

$showreel_thumb = $args['showreel_thumb'] ?? null;

if ($showreel_thumb === null && $post_id) {
	$showreel_thumb = get_field('hero_showreel_thumb', $post_id);
}

$showreel_thumb = $normalize_image($showreel_thumb);
?>

<section class="hero-video" aria-label="<?php esc_attr_e('Hero', 'foundry'); ?>">
	<video
		class="hero-video__media"
		src="<?php echo esc_url($autoplay_video); ?>"
		muted
		autoplay
		loop
		playsinline
		preload="auto"
		aria-hidden="true"></video>

	<?php if ($showreel_label !== '') : ?>
		<button
			type="button"
			class="hero-video__showreel"
			data-hero-showreel
			<?php if ($full_video !== '') : ?>
				data-hero-full-video="<?php echo esc_url($full_video); ?>"
			<?php endif; ?>>
			<?php if ($showreel_thumb['url'] !== '') : ?>
				<span class="hero-video__showreel-thumb">
					<img
						src="<?php echo esc_url($showreel_thumb['url']); ?>"
						alt=""
						loading="lazy"
						decoding="async"
						<?php if ($showreel_thumb['width'] > 0) : ?>
							width="<?php echo esc_attr((string) $showreel_thumb['width']); ?>"
						<?php endif; ?>
						<?php if ($showreel_thumb['height'] > 0) : ?>
							height="<?php echo esc_attr((string) $showreel_thumb['height']); ?>"
						<?php endif; ?>>
				</span>
			<?php endif; ?>
			<span class="hero-video__showreel-label"><?php echo esc_html($showreel_label); ?></span>
			<span class="hero-video__showreel-arrow" aria-hidden="true">
				<?php get_template_part('svg-template/svg-arrow'); ?>
			</span>
		</button>
	<?php endif; ?>
</section>
