<?php

/**
 * Service page showreel — inline hero video inside a content block
 *
 * Thin wrapper: resolves the showreel_* ACF field set and hands it to the
 * shared hero-video partial in its inline variant.
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
$media   = fdry_hero_media($post_id, 'showreel', $args);

if ($media['mp4'] === '' && $media['webm'] === '') {
	return;
}
?>

<section class="showreel">
	<div class="content-block">
		<div class="content-max">
			<?php
			get_template_part(
				'components/page/hero-video',
				null,
				array_merge(
					$media,
					array(
						'prefix'     => 'showreel',
						'variant'    => 'inline',
						'aria_label' => __('Showreel', 'foundry'),
						'post_id'    => $post_id,
					)
				)
			);
			?>
		</div>
	</div>
</section>
