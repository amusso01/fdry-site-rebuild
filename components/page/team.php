<?php

/**
 * Team — tagline, title, WYSIWYG body, and member portrait slider
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type string $tagline     Tagline text.
 *     @type string $tagline_tag Semantic heading tag: h2–h4. Default h3.
 *     @type string $title       Section title.
 *     @type string $content     WYSIWYG body content.
 *     @type int    $post_id     Post ID for ACF fallback. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();

$allowed_tags = array('h2', 'h3', 'h4');

$tagline = $args['tagline'] ?? null;

if (! is_string($tagline) || $tagline === '') {
	$acf_tagline = $post_id ? get_field('team_tagline', $post_id) : '';
	$tagline     = is_string($acf_tagline) ? trim($acf_tagline) : '';
}

$tagline_tag = $args['tagline_tag'] ?? null;

if (! is_string($tagline_tag) || ! in_array($tagline_tag, $allowed_tags, true)) {
	$acf_tag = $post_id ? get_field('team_tagline_tag', $post_id) : '';
	$tagline_tag = is_string($acf_tag) && in_array($acf_tag, $allowed_tags, true) ? $acf_tag : 'h3';
}

$title = $args['title'] ?? null;

if (! is_string($title) || $title === '') {
	$acf_title = $post_id ? get_field('team_title', $post_id) : '';
	$title     = is_string($acf_title) ? trim($acf_title) : '';
}

$content = $args['content'] ?? null;

if (! is_string($content) || $content === '') {
	$acf_content = $post_id ? get_field('team_content', $post_id) : '';
	$content     = is_string($acf_content) ? trim($acf_content) : '';
}

$members = array();

if ($post_id && have_rows('team_members', $post_id)) {
	while (have_rows('team_members', $post_id)) {
		the_row();

		$name = get_sub_field('name');
		$name = is_string($name) ? trim($name) : '';

		$role = get_sub_field('role');
		$role = is_string($role) ? trim($role) : '';

		$image = fdry_image_parts(get_sub_field('image'));

		if ($name === '' && $role === '' && $image['url'] === '') {
			continue;
		}

		$members[] = array(
			'name'  => $name,
			'role'  => $role,
			'image' => $image,
		);
	}
}

if ($tagline === '' && $title === '' && $content === '' && $members === array()) {
	return;
}

$has_copy = $tagline !== '' || $title !== '' || $content !== '';
?>

<section class="team" aria-label="<?php esc_attr_e('Meet the team', 'foundry'); ?>">
	<div class="content-block">
		<div class="content-max">
			<div class="team__layout">
		<?php if ($has_copy) : ?>
			<div class="team__copy">
				<?php if ($tagline !== '') : ?>
					<<?= esc_attr($tagline_tag); ?> class="team__tagline" data-fade-up><?= esc_html($tagline); ?></<?= esc_attr($tagline_tag); ?>>
				<?php endif; ?>

				<?php if ($title !== '') : ?>
					<p class="team__title" data-fade-up data-fade-up-duration=".2"><?= esc_html($title); ?></p>
				<?php endif; ?>

				<?php if ($content !== '') : ?>
					<div class="team__body wysiwyg" data-fade-up data-fade-up-duration=".4">
						<?= wp_kses_post($content); ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ($members !== array()) : ?>
			<div class="team__slider-wrap" data-fade-up data-fade-up-delay="0.5">
				<div class="swiper team__slider">
					<div class="swiper-wrapper">
						<?php foreach ($members as $member) : ?>
							<article class="swiper-slide team__slide">
								<div class="team__card">
									<?php if ($member['image']['url'] !== '') : ?>
										<img
											class="team__photo"
											src="<?= esc_url($member['image']['url']); ?>"
											<?php if ($member['image']['srcset'] !== '') : ?>
												srcset="<?= esc_attr($member['image']['srcset']); ?>"
												sizes="(min-width: 1140px) 20vw, (min-width: 640px) 45vw, 85vw"
											<?php endif; ?>
											alt="<?= esc_attr($member['image']['alt'] !== '' ? $member['image']['alt'] : $member['name']); ?>"
											loading="lazy"
											decoding="async"
											<?php if ($member['image']['width'] > 0) : ?>
												width="<?= esc_attr((string) $member['image']['width']); ?>"
											<?php endif; ?>
											<?php if ($member['image']['height'] > 0) : ?>
												height="<?= esc_attr((string) $member['image']['height']); ?>"
											<?php endif; ?>>
									<?php endif; ?>

									<?php if ($member['name'] !== '' || $member['role'] !== '') : ?>
										<div class="team__meta">
											<?php if ($member['name'] !== '') : ?>
												<p class="team__name"><?= esc_html($member['name']); ?></p>
											<?php endif; ?>

											<?php if ($member['role'] !== '') : ?>
												<p class="team__role"><?= esc_html($member['role']); ?></p>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>

					<button
						type="button"
						class="team__nav team__nav--prev"
						aria-label="<?php esc_attr_e('Previous team member', 'foundry'); ?>">
						<span class="team__nav-icon team__nav-icon--prev" aria-hidden="true">
							<?php get_template_part('svg-template/svg-arrow'); ?>
						</span>
					</button>

					<button
						type="button"
						class="team__nav team__nav--next"
						aria-label="<?php esc_attr_e('Next team member', 'foundry'); ?>">
						<span class="team__nav-icon" aria-hidden="true">
							<?php get_template_part('svg-template/svg-arrow'); ?>
						</span>
					</button>
				</div>
			</div>
		<?php endif; ?>
			</div>
		</div>
	</div>
</section>
