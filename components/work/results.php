<?php

/**
 * Single work results — an image beside 2 to 4 stats (label, value, description)
 *
 * The count sets a work-results--count-{2,3,4} modifier: fewer stats get
 * bigger values. Stats without a value are skipped; with none the section is
 * not rendered. Without an image the stats take the full width
 * (work-results--no-media).
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

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();
$rows    = get_field('work_results_stats', $post_id);
$stats   = array();

foreach (is_array($rows) ? $rows : array() as $row) {
	$value = is_string($row['value'] ?? null) ? trim($row['value']) : '';

	if ($value === '') {
		continue;
	}

	$stats[] = array(
		'label'       => is_string($row['label'] ?? null) ? trim($row['label']) : '',
		'value'       => $value,
		'description' => is_string($row['description'] ?? null) ? trim($row['description']) : '',
	);
}

$stats = array_slice($stats, 0, 4);

if ($stats === array()) {
	return;
}

$eyebrow  = get_field('work_results_eyebrow', $post_id);
$eyebrow  = is_string($eyebrow) ? trim($eyebrow) : '';
$title    = get_field('work_results_title', $post_id);
$title    = is_string($title) ? trim($title) : '';
$image_id = (int) get_field('work_results_image', $post_id);

// Widths from _work-results.scss: 7 of 11 columns after the 80px gap.
$image = $image_id > 0 ? fdry_work_image(
	$image_id,
	array(
		'class'   => 'work-results__image',
		'loading' => 'lazy',
		'sizes'   => '(max-width: 639px) calc(100vw - 50px), (max-width: 1139px) calc(100vw - 64px), (max-width: 1679px) calc((100vw - 240px) * 0.636), (max-width: 1919px) 916px, min(calc((100vw - 240px) * 0.636), 1171px)',
	),
	'calc(100vw - 50px)'
) : '';

$classes = array(
	'work-results',
	'work-results--count-' . max(2, count($stats)),
);

if ($image === '') {
	$classes[] = 'work-results--no-media';
}
?>

<section class="<?= esc_attr(implode(' ', $classes)); ?>">
	<div class="content-max">
		<div class="work-results__grid">
			<?php if ($image !== '') : ?>
				<div class="work-results__media" data-fade-up>
					<?= $image; ?>
				</div>
			<?php endif; ?>

			<div class="work-results__content">
				<?php if ($eyebrow !== '') : ?>
					<p class="work-results__eyebrow" data-fade-up><?= esc_html($eyebrow); ?></p>
				<?php endif; ?>

				<?php if ($title !== '') : ?>
					<h2 class="work-results__title" data-fade-up><?= esc_html($title); ?></h2>
				<?php endif; ?>

				<dl class="work-results__stats">
					<?php foreach ($stats as $index => $stat) : ?>
						<div class="work-results__stat"<?= fdry_fade_attrs(false, 0.1 * $index); ?>>
							<?php if ($stat['label'] !== '') : ?>
								<dt class="work-results__label"><?= esc_html($stat['label']); ?></dt>
							<?php endif; ?>
							<dd class="work-results__value"><?= esc_html($stat['value']); ?></dd>
							<?php if ($stat['description'] !== '') : ?>
								<dd class="work-results__description"><?= esc_html($stat['description']); ?></dd>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>
		</div>
	</div>
</section>
