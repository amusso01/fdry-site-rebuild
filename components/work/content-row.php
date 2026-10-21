<?php

/**
 * Single work content row — a title on the left, editor content on the right
 *
 * The content stays in the right column without a title. With neither, the
 * row is not rendered.
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     @type array $row The content_row layout from work_rows.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

$row     = isset($args['row']) && is_array($args['row']) ? $args['row'] : array();
$title   = is_string($row['title'] ?? null) ? trim($row['title']) : '';
$content = is_string($row['content'] ?? null) ? trim($row['content']) : '';

if ($title === '' && $content === '') {
	return;
}
?>

<section class="work-content-row">
	<div class="content-max">
		<div class="work-content-row__grid">
			<?php if ($title !== '') : ?>
				<h2 class="work-content-row__title" data-fade-up><?= esc_html($title); ?></h2>
			<?php endif; ?>

			<?php if ($content !== '') : ?>
				<div class="work-content-row__body wysiwyg" data-fade-up>
					<?= wp_kses_post($content); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
