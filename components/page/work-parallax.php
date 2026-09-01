<?php

/**
 * Work parallax stack
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type int $post_id Post ID for ACF fallback. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();
$cards   = fdry_get_work_parallax_cards($post_id);

if ($cards === array()) {
	return;
}
?>

<section class="work-parallax" aria-label="<?php esc_attr_e('Featured work', 'foundry'); ?>">
	<div class="content-block">
		<div class="content-max">
			<div class="work-parallax__stack">
				<?php foreach ($cards as $index => $card) : ?>
					<article
						class="work-parallax__card"
						style="z-index: <?= esc_attr((string) ($index + 1)); ?>;">
						<a
							class="work-parallax__link"
							href="<?= esc_url($card['permalink']); ?>"
							aria-label="<?= esc_attr(sprintf(__('View %s', 'foundry'), $card['title'])); ?>">
							<div class="work-parallax__media">
								<img
									class="work-parallax__image"
									src="<?= esc_url($card['image_url']); ?>"
									alt="<?= esc_attr($card['image_alt'] !== '' ? $card['image_alt'] : $card['title']); ?>"
									loading="eager"
									decoding="async"
									<?php if ($index === 0) : ?>
										fetchpriority="high"
									<?php endif; ?>
									<?php if ($card['image_width'] > 0) : ?>
										width="<?= esc_attr((string) $card['image_width']); ?>"
									<?php endif; ?>
									<?php if ($card['image_height'] > 0) : ?>
										height="<?= esc_attr((string) $card['image_height']); ?>"
									<?php endif; ?>>
							</div>

							<div class="work-parallax__overlay">
								<div class="work-parallax__meta">
									<?php if ($card['title'] !== '') : ?>
										<p class="work-parallax__title"><?= esc_html($card['title']); ?></p>
									<?php endif; ?>

									<?php if ($card['tagline'] !== '') : ?>
										<p class="work-parallax__tagline"><?= esc_html($card['tagline']); ?></p>
									<?php endif; ?>

									<span class="work-parallax__arrow" aria-hidden="true">
										<?php get_template_part('svg-template/svg-arrow'); ?>
									</span>
								</div>

								<?php if ($card['categories'] !== array()) : ?>
									<ul class="work-parallax__categories" aria-label="<?php esc_attr_e('Categories', 'foundry'); ?>">
										<?php foreach ($card['categories'] as $category_name) : ?>
											<li class="work-parallax__category"><?= esc_html($category_name); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
							</div>
						</a>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>
