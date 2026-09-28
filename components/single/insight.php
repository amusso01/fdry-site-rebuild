<?php

/**
 * Single insight — eyebrow, title, share links, hero image, then the post
 *
 * Rendered from single.php for the `post` type. The old partial is kept in
 * loop-templates/content-single-insight.php as a rollback.
 *
 * The Gutenberg output keeps the legacy .insight-content / .entry-content
 * wrappers: its typography (heading sizes, bullets, link colours) still comes
 * from theme.css and mainstyle.css. Everything around it is new markup,
 * styled in _single-insight.scss.
 *
 * LinkedIn and X share the post. Instagram has no share URL, so it links to
 * the FDRY profile instead.
 *
 * @author Andrea Musso
 *
 * @package foundry
 */

if (! defined('ABSPATH')) {
	exit;
}

$title        = get_the_title();
$permalink    = (string) get_permalink();
$insights_url = fdry_template_page_url('template-insight.php', '/insights/');
$work_url     = fdry_template_page_url('template-work.php', '/work/');

$category = '';

foreach (get_the_category() as $term) {
	if ($term instanceof WP_Term && $term->slug !== 'uncategorized') {
		$category = $term->name;
		break;
	}
}

// ACF hero_image (returns a URL) first, then the featured image, as before.
$hero_url = get_field('hero_image');
$hero_url = is_string($hero_url) ? trim($hero_url) : '';
$image_id = $hero_url !== '' ? attachment_url_to_postid($hero_url) : 0;

if ($hero_url === '') {
	$image_id = (int) get_post_thumbnail_id();
}

$image_alt = $image_id ? (string) get_post_meta($image_id, '_wp_attachment_image_alt', true) : '';
$image_alt = $image_alt !== '' ? $image_alt : wp_strip_all_tags($title);

// The hero is the LCP element: eager, high priority, and not faded in.
if ($image_id) {
	$image_html = wp_get_attachment_image(
		$image_id,
		'full',
		false,
		array(
			'class'         => 'single-insight__image',
			'alt'           => $image_alt,
			'loading'       => 'eager',
			'fetchpriority' => 'high',
			'decoding'      => 'async',
			'sizes'         => '100vw',
		)
	);
} elseif ($hero_url !== '') {
	$image_html = sprintf(
		'<img class="single-insight__image" src="%s" alt="%s" fetchpriority="high" decoding="async">',
		esc_url($hero_url),
		esc_attr($image_alt)
	);
} else {
	$image_html = '';
}

$share = array(
	'linkedin'  => array(
		'url'   => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($permalink),
		'label' => __('Share on LinkedIn', 'foundry'),
	),
	'instagram' => array(
		'url'   => 'https://www.instagram.com/FDRY_digital/',
		'label' => __('FDRY on Instagram', 'foundry'),
	),
	'x'         => array(
		'url'   => 'https://x.com/intent/tweet?url=' . rawurlencode($permalink) . '&text=' . rawurlencode(wp_strip_all_tags(html_entity_decode($title, ENT_QUOTES, 'UTF-8'))),
		'label' => __('Share on X', 'foundry'),
	),
);
?>

<main class="single-insight">
	<article id="post-<?php the_ID(); ?>" <?php post_class('single-insight__article'); ?>>
		<header class="single-insight__header content-block">
			<p class="single-insight__eyebrow" data-fade-up>
				<a class="single-insight__eyebrow-link" href="<?= esc_url($insights_url); ?>"><?= esc_html__('Insights', 'foundry'); ?></a>
			</p>

			<h1 class="single-insight__title" data-fade-up data-fade-up-delay="0.1"><?= esc_html($title); ?></h1>

			<ul class="single-insight__share" aria-label="<?= esc_attr__('Share this article', 'foundry'); ?>" data-fade-up data-fade-up-delay="0.2">
				<?php foreach ($share as $network => $link) : ?>
					<li>
						<a
							class="single-insight__share-link"
							href="<?= esc_url($link['url']); ?>"
							target="_blank"
							rel="noopener noreferrer"
							aria-label="<?= esc_attr($link['label']); ?>">
							<span class="single-insight__share-icon" aria-hidden="true">
								<?php get_template_part('svg-template/svg-' . $network); ?>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</header>

		<?php if ($image_html !== '') : ?>
			<div class="single-insight__media content-block">
				<?= $image_html; ?>
			</div>
		<?php endif; ?>

		<div class="single-insight__body content-block">
			<div class="single-insight__column">
				<?php if ($category !== '') : ?>
					<p class="single-insight__category"><?= esc_html($category); ?></p>
				<?php endif; ?>

				<!-- Legacy hooks: the Gutenberg typography in theme.css / mainstyle.css hangs on .insight-content .entry-content. Drop them with theme.css. -->
				<div class="single-insight__content insight-content">
					<div class="entry-content">
						<?php the_content(); ?>
					</div>
				</div>

				<a class="single-insight__back" href="<?= esc_url($insights_url); ?>">
					<?php get_template_part('svg-template/svg-arrow'); ?>
					<?= esc_html__('Insights', 'foundry'); ?>
				</a>
			</div>
		</div>
	</article>

	<div class="single-insight__work content-block">
		<a class="single-insight__work-link" href="<?= esc_url($work_url); ?>">
			<picture>
				<source media="(max-width: 767px)" srcset="https://www.fdry.com/wp-content/uploads/2023/10/case-studies_banner_mobile.png">
				<img class="single-insight__work-image" src="https://www.fdry.com/wp-content/uploads/2023/10/Case-Studies_Image.png" alt="<?= esc_attr__('Our Work', 'foundry'); ?>" loading="lazy" decoding="async">
			</picture>
		</a>
	</div>
</main>
