<?php

/**
 * Two column list — tagline, title, image, split list, and WYSIWYG body
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional. Pass to override ACF values on any page.
 *
 *     @type string $prefix          ACF field name prefix. Default two_column.
 *     @type string $tagline         Tagline text.
 *     @type string $tagline_tag     Semantic heading tag: h2–h4. Default h3.
 *     @type string $title           Section title.
 *     @type array  $image           ACF image array.
 *     @type string $content         WYSIWYG body content.
 *     @type bool   $use_bigger_font When true, applies larger body typography.
 *     @type string $appearance      Visual variant: white or gray. Default white.
 *     @type int    $post_id         Post ID for ACF fallback. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();

$prefix = $args['prefix'] ?? 'two_column';
$prefix = is_string($prefix) && $prefix !== '' ? $prefix : 'two_column';

$allowed_appearances = array('white', 'gray');
$appearance          = $args['appearance'] ?? 'white';
$appearance          = is_string($appearance) && in_array($appearance, $allowed_appearances, true) ? $appearance : 'white';

$allowed_tags = array('h2', 'h3', 'h4');

$tagline = $args['tagline'] ?? null;

if (! is_string($tagline) || $tagline === '') {
	$acf_tagline = $post_id ? get_field($prefix . '_tagline', $post_id) : '';
	$tagline     = is_string($acf_tagline) ? trim($acf_tagline) : '';
}

$tagline_tag = $args['tagline_tag'] ?? null;

if (! is_string($tagline_tag) || ! in_array($tagline_tag, $allowed_tags, true)) {
	$acf_tag = $post_id ? get_field($prefix . '_tagline_tag', $post_id) : '';
	$tagline_tag = is_string($acf_tag) && in_array($acf_tag, $allowed_tags, true) ? $acf_tag : 'h3';
}

$title = $args['title'] ?? null;

if (! is_string($title) || $title === '') {
	$acf_title = $post_id ? get_field($prefix . '_title', $post_id) : '';
	$title     = is_string($acf_title) ? trim($acf_title) : '';
}

$content = $args['content'] ?? null;

if (! is_string($content) || $content === '') {
	$acf_content = $post_id ? get_field($prefix . '_content', $post_id) : '';
	$content     = is_string($acf_content) ? trim($acf_content) : '';
}

$use_bigger_font = $args['use_bigger_font'] ?? null;

if ($use_bigger_font === null && $post_id) {
	$use_bigger_font = (bool) get_field($prefix . '_use_bigger_font', $post_id);
} else {
	$use_bigger_font = (bool) $use_bigger_font;
}

/**
 * @param array|string|false|null $image ACF image field value.
 * @return array{url: string, alt: string, width: int, height: int}
 */
$normalize_image = static function ($image): array {
	$empty = array(
		'url'    => '',
		'alt'    => '',
		'width'  => 0,
		'height' => 0,
	);

	if (! $image) {
		return $empty;
	}

	if (is_string($image) && $image !== '') {
		return array(
			'url'    => $image,
			'alt'    => '',
			'width'  => 0,
			'height' => 0,
		);
	}

	if (! is_array($image) || empty($image['url'])) {
		return $empty;
	}

	return array(
		'url'    => is_string($image['url']) ? $image['url'] : '',
		'alt'    => is_string($image['alt'] ?? null) ? $image['alt'] : '',
		'width'  => isset($image['width']) ? (int) $image['width'] : 0,
		'height' => isset($image['height']) ? (int) $image['height'] : 0,
	);
};

$image = $args['image'] ?? null;

if ($image === null && $post_id) {
	$image = get_field($prefix . '_image', $post_id);
}

$image = $normalize_image($image);

$list_items = array();

if ($post_id && have_rows($prefix . '_list_items', $post_id)) {
	$index = 0;

	while (have_rows($prefix . '_list_items', $post_id)) {
		the_row();

		$label = get_sub_field('item');
		$label = is_string($label) ? trim($label) : '';

		if ($label === '') {
			continue;
		}

		++$index;

		$icon_field = get_sub_field('icon');
		$icon       = acfFile_toSvg($icon_field);

		$list_items[] = array(
			'label' => $label,
			'icon'  => $icon,
			'index' => $index,
		);
	}
}

$list_columns = array(
	array(),
	array(),
);

if ($list_items !== array()) {
	$split_at = (int) ceil(count($list_items) / 2);
	$list_columns[0] = array_slice($list_items, 0, $split_at);
	$list_columns[1] = array_slice($list_items, $split_at);
}

$has_image = $image['url'] !== '';
$has_list  = $list_items !== array();

if ($tagline === '' && $title === '' && ! $has_image && ! $has_list && $content === '') {
	return;
}
?>

<section class="two-column-list<?= $appearance === 'gray' ? ' two-column-list--gray' : ''; ?>">
	<div class="content-block">
		<div class="content-max">
			<?php if ($tagline !== '' || $title !== '') : ?>
				<div class="two-column-list__header">
					<?php if ($tagline !== '') : ?>
						<<?= esc_attr($tagline_tag); ?> class="two-column-list__tagline"><?= esc_html($tagline); ?></<?= esc_attr($tagline_tag); ?>>
					<?php endif; ?>

					<?php if ($title !== '') : ?>
						<p class="two-column-list__title"><?= esc_html($title); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="two-column-list__grid">
				<?php if ($has_image) : ?>
					<div class="two-column-list__left">
						<div class="two-column-list__media">
							<img
								class="two-column-list__image"
								src="<?= esc_url($image['url']); ?>"
								alt="<?= esc_attr($image['alt']); ?>"
								loading="lazy"
								decoding="async"
								<?php if ($image['width'] > 0) : ?>
									width="<?= esc_attr((string) $image['width']); ?>"
								<?php endif; ?>
								<?php if ($image['height'] > 0) : ?>
									height="<?= esc_attr((string) $image['height']); ?>"
								<?php endif; ?>>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($has_list || $content !== '') : ?>
					<div class="two-column-list__right">
						<?php if ($has_list) : ?>
							<div class="two-column-list__list">
								<?php foreach ($list_columns as $column_items) : ?>
									<?php if ($column_items === array()) : ?>
										<?php continue; ?>
									<?php endif; ?>

									<ul class="two-column-list__list-column">
										<?php foreach ($column_items as $list_item) : ?>
											<li class="two-column-list__list-item">
												<?php if ($list_item['icon'] !== '') : ?>
													<span class="two-column-list__list-icon" aria-hidden="true">
														<?= $list_item['icon']; ?>
													</span>
												<?php else : ?>
													<span class="two-column-list__list-number" aria-hidden="true">
														<?= esc_html(sprintf('%02d', $list_item['index'])); ?>
													</span>
												<?php endif; ?>

												<span class="two-column-list__list-label"><?= esc_html($list_item['label']); ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>

						<?php if ($content !== '') : ?>
							<div class="two-column-list__body wysiwyg<?= $use_bigger_font ? ' two-column-list__body--bigger-font' : ''; ?>">
								<?= wp_kses_post($content); ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
