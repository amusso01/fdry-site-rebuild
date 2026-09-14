<?php

/**
 * Service page showreel — inline hero video inside content block
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type string $autoplay_video Vimeo URL or direct media URL for muted background autoplay.
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
 * @param array|string|false|null $value ACF url/file field value.
 */
$normalize_media_url = static function ($value): string {
	if (! $value) {
		return '';
	}

	if (is_string($value)) {
		return trim($value);
	}

	if (is_array($value) && ! empty($value['url']) && is_string($value['url'])) {
		return trim($value['url']);
	}

	return '';
};

$autoplay_video = $args['autoplay_video'] ?? null;

if ($autoplay_video === null && $post_id) {
	$autoplay_video = get_field('showreel_autoplay_video', $post_id);
}

$autoplay_video = $normalize_media_url($autoplay_video);

if ($autoplay_video === '') {
	return;
}

$full_video = $args['full_video'] ?? null;

if ($full_video === null && $post_id) {
	$full_video = get_field('showreel_full_video', $post_id);
}

$full_video = $normalize_media_url($full_video);

$showreel_label = $args['showreel_label'] ?? null;

if (! is_string($showreel_label) || $showreel_label === '') {
	$acf_label      = $post_id ? get_field('showreel_label', $post_id) : '';
	$showreel_label = is_string($acf_label) && $acf_label !== '' ? $acf_label : 'SEE FULL SHOWREEL';
}

$showreel_thumb = $args['showreel_thumb'] ?? null;

if ($showreel_thumb === null && $post_id) {
	$showreel_thumb = get_field('showreel_thumb', $post_id);
}
?>

<section class="showreel">
	<div class="content-block">
		<div class="content-max">
			<?php
			get_template_part(
				'components/page/hero-video',
				null,
				array(
					'autoplay_video' => $autoplay_video,
					'full_video'     => $full_video,
					'showreel_label' => $showreel_label,
					'showreel_thumb' => $showreel_thumb,
					'variant'        => 'inline',
					'aria_label'     => __('Showreel', 'foundry'),
					'post_id'        => $post_id,
				)
			);
			?>
		</div>
	</div>
</section>
