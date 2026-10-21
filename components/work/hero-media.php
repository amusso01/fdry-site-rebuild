<?php

/**
 * Single work hero media — a 2:1 image, or a muted looping video over it
 *
 * The image is the LCP element: eager, fetchpriority high and never faded.
 * With a video it is also the still. The <video> ships with no src:
 * heroVideo.js attaches it ([data-hero-video]) except on small screens, with
 * reduced motion and with save-data, where the image stands in.
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional.
 *
 *     @type int $post_id Post ID for ACF fields. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id  = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();
$image_id = (int) get_field('work_hero_image', $post_id);

// Widths from _work-hero-media.scss (80 / 32 / 25px insets) and .content-max.
$image = $image_id > 0 ? fdry_work_image(
	$image_id,
	array(
		'class'         => 'work-hero-media__image',
		'loading'       => 'eager',
		'fetchpriority' => 'high',
		'sizes'         => '(max-width: 639px) calc(100vw - 50px), (max-width: 1139px) calc(100vw - 64px), (max-width: 1679px) calc(100vw - 160px), (max-width: 1919px) 1520px, min(calc(100vw - 160px), 1920px)',
	),
	'calc(100vw - 50px)'
) : '';

if ($image === '') {
	return;
}

// The video stays saved when the editor switches back to Image.
$video = get_field('work_hero_media_type', $post_id) === 'video'
	? fdry_media_url(get_field('work_hero_video', $post_id))
	: '';
?>

<section class="work-hero-media">
	<div class="content-max">
		<div
			class="work-hero-media__frame"
			<?php if ($video !== '') : ?>
				data-hero-video
				data-mobile-max="<?= esc_attr((string) FDRY_HERO_MOBILE_MAX_PX); ?>"
			<?php endif; ?>>
			<?= $image; ?>

			<?php if ($video !== '') : ?>
				<video
					class="work-hero-media__video"
					data-hero-video-media
					data-src-mp4="<?= esc_url($video); ?>"
					muted
					loop
					playsinline
					preload="none"
					aria-hidden="true"></video>
			<?php endif; ?>
		</div>
	</div>
</section>
