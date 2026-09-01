<?php

/**
 * Navigation content — main links with sublink pills
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

if (! $post_id || ! have_rows('navigation_content', $post_id)) {
	return;
}

/**
 * Escape a link href, preserving bare "#" placeholders (esc_url strips them).
 *
 * @param string $url Normalised link URL.
 * @return string
 */
$escape_link_href = static function (string $url): string {
	if ($url === '' || $url === '#') {
		return '#';
	}

	return esc_url($url);
};

$rows = array();

while (have_rows('navigation_content', $post_id)) {
	the_row();

	$main_link = get_sub_field('main_link');

	if (! is_array($main_link) || empty($main_link['title'])) {
		continue;
	}

	$main_parts = fdry_acf_link_parts($main_link);

	$sublinks = array();

	if (have_rows('sublinks')) {
		while (have_rows('sublinks')) {
			the_row();

			$sublink = get_sub_field('sublink');

			if (! is_array($sublink) || empty($sublink['title'])) {
				continue;
			}

			$sublink_parts = fdry_acf_link_parts($sublink);

			$sublinks[] = array(
				'label'  => $sublink['title'],
				'url'    => $sublink_parts['url'],
				'target' => $sublink_parts['target'],
			);
		}
	}

	$rows[] = array(
		'label'    => $main_link['title'],
		'url'      => $main_parts['url'],
		'target'   => $main_parts['target'],
		'sublinks' => $sublinks,
	);
}

if ($rows === array()) {
	return;
}
?>

<section class="navigation-content" aria-label="<?php esc_attr_e('Services navigation', 'foundry'); ?>">
	<div class="navigation-content__inner">
		<ul class="navigation-content__list">
				<?php foreach ($rows as $row) : ?>
					<li class="navigation-content__row">
						<a
							class="navigation-content__main"
							href="<?= esc_attr($escape_link_href($row['url'])); ?>"
							<?php if ($row['target'] !== '') : ?>
								target="<?= esc_attr($row['target']); ?>"
								<?php if ($row['target'] === '_blank') : ?>
									rel="noopener noreferrer"
								<?php endif; ?>
							<?php endif; ?>
						>
							<span class="navigation-content__main-fill" aria-hidden="true"></span>
							<span class="navigation-content__arrow" aria-hidden="true">
								<?php get_template_part('svg-template/svg-arrow'); ?>
							</span>
							<span class="navigation-content__title"><?= esc_html($row['label']); ?></span>
						</a>

						<?php if ($row['sublinks'] !== array()) : ?>
							<ul class="navigation-content__sublinks">
								<?php foreach ($row['sublinks'] as $sublink) : ?>
									<li class="navigation-content__sublink-item">
										<a
											class="navigation-content__sublink"
											href="<?= esc_attr($escape_link_href($sublink['url'])); ?>"
											<?php if ($sublink['target'] !== '') : ?>
												target="<?= esc_attr($sublink['target']); ?>"
												<?php if ($sublink['target'] === '_blank') : ?>
													rel="noopener noreferrer"
												<?php endif; ?>
											<?php endif; ?>
										>
											<?= esc_html($sublink['label']); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
	</div>
</section>
