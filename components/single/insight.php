<?php

/**
 * Single insight — eyebrow, title, category pill, date and reading time,
 * hero image, the post, then the image cards grid (components/page/image-cards-grid.php)
 *
 * Rendered from single.php for the `post` type. The old partial is kept in
 * loop-templates/content-single-insight.php as a rollback.
 *
 * Posts are legacy-free (PERFORMANCE-PLAN.md #16): no theme.css or mainstyle.css.
 * The Gutenberg output, its typography and spacing, and everything around it
 * are styled in _single-insight.scss. The old .insight-content / .entry-content
 * wrappers stay as hooks: with __content they outrank WordPress's block CSS.
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

$category = '';

foreach (get_the_category() as $term) {
	if ($term instanceof WP_Term && $term->slug !== 'uncategorized') {
		$category = $term->name;
		break;
	}
}

// ACF hero_image (returns a URL) first, then the featured image, as before.
// The WebP swap has already turned the URL into the .webp copy, which matches
// no attachment, so look up the original. Without the ID there is no srcset,
// and phones download the full-size file.
$hero_url = get_field('hero_image');
$hero_url = is_string($hero_url) ? trim($hero_url) : '';
$image_id = $hero_url !== '' ? attachment_url_to_postid(fdry_webp_original_url($hero_url)) : 0;

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
?>

<main class="single-insight">
	<article id="post-<?php the_ID(); ?>" <?php post_class('single-insight__article'); ?>>
		<header class="single-insight__header content-block">
			<p class="single-insight__eyebrow"<?= fdry_fade_attrs(true); ?>>
				<a class="single-insight__eyebrow-link" href="<?= esc_url($insights_url); ?>"><?= esc_html__('Insights', 'foundry'); ?></a>
			</p>

			<h1 class="single-insight__title"<?= fdry_fade_attrs(true, 0.1); ?>><?= esc_html($title); ?></h1>

			<?php if ($category !== '') : ?>
				<p class="single-insight__tag"<?= fdry_fade_attrs(true, 0.2); ?>><?= esc_html($category); ?></p>
			<?php endif; ?>

			<p class="single-insight__meta"<?= fdry_fade_attrs(true, 0.3); ?>>
				<time datetime="<?= esc_attr(get_the_date('c')); ?>"><?= esc_html(get_the_date('j F Y')); ?></time>
				<span aria-hidden="true">·</span>
				<span><?= esc_html(sprintf(__('%d min read', 'foundry'), fdry_reading_time(get_the_ID()))); ?></span>
			</p>
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

				<?php // .insight-content .entry-content: kept as hooks, see the docblock. ?>
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

	<?php // The same three cards on every post, from Global variables. ?>
	<?php get_template_part('components/page/image-cards-grid'); ?>
</main>
