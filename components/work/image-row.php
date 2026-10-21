<?php

/**
 * Single work image row — two media side by side, each an image or a video
 *
 * Every cell has the design's 628×805 ratio and crops its media to fill
 * (_work-image-row.scss). An empty slot leaves its column empty; with no
 * media at all the row is not rendered.
 *
 * Videos ship with no src: heroVideo.js attaches them when the row comes near
 * the viewport ([data-hero-video-lazy]), on every screen size
 * ([data-hero-video-all-screens]). With reduced motion or save-data the still
 * shows instead, or the paused first frame when there is no still
 * ([data-hero-video-no-still]).
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     @type array $row The image_row layout from work_rows.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

$row = isset($args['row']) && is_array($args['row']) ? $args['row'] : array();

$slots = array(
	'left'  => fdry_work_row_media($row['media_1'] ?? null),
	'right' => fdry_work_row_media($row['media_2'] ?? null),
);

if ($slots['left']['type'] === '' && $slots['right']['type'] === '') {
	return;
}

// Half the content width after the 24px gap: 25 / 32 / 80px insets and .content-max.
$sizes = '(max-width: 639px) calc(100vw - 50px), (max-width: 1139px) calc(50vw - 44px), (max-width: 1679px) calc(50vw - 92px), (max-width: 1919px) 748px, min(calc(50vw - 92px), 948px)';
?>

<section class="work-image-row">
	<div class="content-max">
		<div class="work-image-row__grid">
			<?php foreach ($slots as $side => $media) : ?>
				<?php if ($media['type'] === 'image') : ?>
					<div class="work-image-row__media work-image-row__media--<?= esc_attr($side); ?>" data-fade-up>
						<?= fdry_work_image(
							$media['image_id'],
							array(
								'class'   => 'work-image-row__image',
								'loading' => 'lazy',
								'sizes'   => $sizes,
							),
							'calc(100vw - 50px)'
						); ?>
					</div>
				<?php elseif ($media['type'] === 'video') : ?>
					<?php
					$still = $media['still_id'] > 0 ? fdry_work_image(
						$media['still_id'],
						array(
							'class'   => 'work-image-row__still',
							'loading' => 'lazy',
							'sizes'   => $sizes,
						),
						'calc(100vw - 50px)'
					) : '';
					?>
					<div
						class="work-image-row__media work-image-row__media--<?= esc_attr($side); ?> work-image-row__media--video<?= $still !== '' ? ' work-image-row__media--still' : ''; ?>"
						data-fade-up
						data-hero-video
						data-hero-video-lazy
						data-hero-video-all-screens
						<?php if ($still === '') : ?>
							data-hero-video-no-still
						<?php endif; ?>>
						<?= $still; ?>
						<video
							class="work-image-row__video"
							data-hero-video-media
							<?php if ($media['webm'] !== '') : ?>
								data-src-webm="<?= esc_url($media['webm']); ?>"
							<?php endif; ?>
							<?php if ($media['mp4'] !== '') : ?>
								data-src-mp4="<?= esc_url($media['mp4']); ?>"
							<?php endif; ?>
							muted
							loop
							playsinline
							preload="none"
							aria-hidden="true"></video>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
