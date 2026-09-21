<?php

/**
 * Four column grid — icon, title, and content cards
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type string $prefix  ACF field name prefix. Default four_column.
 *     @type int    $post_id Post ID for ACF fallback. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();

$prefix = $args['prefix'] ?? 'four_column';
$prefix = is_string($prefix) && $prefix !== '' ? $prefix : 'four_column';

$field_name = $prefix . '_grid';
$cards      = array();

if ($post_id && have_rows($field_name, $post_id)) {
	while (have_rows($field_name, $post_id)) {
		the_row();

		$icon_field = get_sub_field('icon');
		$icon       = acfFile_toSvg($icon_field);

		$card_title = get_sub_field('title');
		$card_title = is_string($card_title) ? trim($card_title) : '';

		$card_content = get_sub_field('content');
		$card_content = is_string($card_content) ? trim($card_content) : '';

		if ($icon === '' && $card_title === '' && $card_content === '') {
			continue;
		}

		$cards[] = array(
			'icon'    => $icon,
			'title'   => $card_title,
			'content' => $card_content,
		);
	}
}

if ($cards === array()) {
	return;
}
?>

<section class="four-column-grid">
	<div class="content-block">
		<div class="content-max">
			<ul class="four-column-grid__cards">
				<?php foreach ($cards as $card) : ?>
					<li class="four-column-grid__card">
						<?php if ($card['icon'] !== '') : ?>
							<div class="four-column-grid__card-icon" aria-hidden="true">
								<?= $card['icon']; ?>
							</div>
						<?php endif; ?>

						<?php if ($card['title'] !== '') : ?>
							<h2 class="four-column-grid__card-title"><?= esc_html($card['title']); ?></h2>
						<?php endif; ?>

						<?php if ($card['content'] !== '') : ?>
							<div class="four-column-grid__card-content wysiwyg">
								<?= wp_kses_post($card['content']); ?>
							</div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
</section>
