<?php

/**
 * Secondary nav overlay (Secondary menu 2026).
 *
 * @author Andrea Musso
 *
 * @package Foundry
 */

if (! defined('ABSPATH')) {
	exit;
}

$menu_tree = fdry_get_secondary_menu_tree();

if ($menu_tree === array() && ! has_nav_menu('mainmenu')) {
	return;
}

$parents_with_children = array_values(
	array_filter(
		$menu_tree,
		static function (array $node): bool {
			return ! empty($node['children']);
		}
	)
);

$active_parent_id = ! empty($parents_with_children)
	? (int) $parents_with_children[0]['item']->ID
	: 0;

$view_all_work = fdry_acf_link_parts(
	fdry_get_nav_menu_acf_field('view_all_work_link', 'secondarymenu')
);
?>

<div
	class="site-nav-overlay"
	id="site-nav-overlay"
	aria-hidden="true"
	inert>
	<div class="site-nav-overlay__inner content-block">
		<?php
		get_template_part(
			'components/navigation/mobile',
			null,
			array(
				'menu_tree'     => $menu_tree,
				'view_all_work' => $view_all_work,
			)
		);
		?>

		<?php if ($menu_tree !== array()) : ?>
			<div class="site-nav-overlay__desktop">
				<div class="site-nav-overlay__layout">
					<div class="site-nav-overlay__primary">
						<ul class="site-nav-overlay__parents" role="list">
							<?php foreach ($menu_tree as $node) :
								$item     = $node['item'];
								$children = $node['children'];
								$has_children = $children !== array();
								$is_active    = $has_children && (int) $item->ID === $active_parent_id;
								?>
								<li class="site-nav-overlay__parent-item">
									<?php if ($has_children) : ?>
										<a
											class="site-nav-overlay__parent site-nav-overlay__parent--has-children<?php echo $is_active ? ' is-active' : ''; ?>"
											href="<?php echo esc_url($item->url); ?>"
											data-nav-parent="<?php echo esc_attr((string) $item->ID); ?>"
											aria-controls="site-nav-panel-<?php echo esc_attr((string) $item->ID); ?>"
											<?php echo $item->target ? ' target="' . esc_attr($item->target) . '"' : ''; ?>
											<?php echo $item->target === '_blank' ? ' rel="noopener noreferrer"' : ''; ?>>
											<span class="site-nav-overlay__parent-label"><?php echo esc_html($item->title); ?></span>
											<span class="site-nav-overlay__parent-arrow" aria-hidden="true">
												<?php get_template_part('svg-template/svg-arrow'); ?>
											</span>
										</a>
									<?php else : ?>
										<a
											class="site-nav-overlay__parent site-nav-overlay__parent--link"
											href="<?php echo esc_url($item->url); ?>"
											<?php echo $item->target ? ' target="' . esc_attr($item->target) . '"' : ''; ?>
											<?php echo $item->target === '_blank' ? ' rel="noopener noreferrer"' : ''; ?>>
											<span class="site-nav-overlay__parent-label"><?php echo esc_html($item->title); ?></span>
										</a>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>

						<div class="site-nav-overlay__footer">
							<?php
							get_template_part(
								'components/partials/button',
								null,
								array(
									'variant' => 'white',
									'label'   => __('View all our work', 'foundry'),
									'url'     => $view_all_work['url'],
									'target'  => $view_all_work['target'],
								)
							);
							?>
						</div>
					</div>

					<?php if ($parents_with_children !== array()) : ?>
						<div class="site-nav-overlay__panels">
							<?php foreach ($parents_with_children as $node) :
								$item     = $node['item'];
								$children = $node['children'];
								$is_active = (int) $item->ID === $active_parent_id;
								?>
								<div
									class="site-nav-overlay__panel<?php echo $is_active ? ' is-active' : ''; ?>"
									id="site-nav-panel-<?php echo esc_attr((string) $item->ID); ?>"
									data-nav-panel="<?php echo esc_attr((string) $item->ID); ?>"
									<?php echo $is_active ? '' : ' hidden'; ?>>
									<ul class="site-nav-overlay__children" role="list">
										<?php foreach ($children as $child) : ?>
											<li class="site-nav-overlay__child-item">
												<a
													class="site-nav-overlay__child"
													href="<?php echo esc_url($child->url); ?>"
													<?php echo $child->target ? ' target="' . esc_attr($child->target) . '"' : ''; ?>
													<?php echo $child->target === '_blank' ? ' rel="noopener noreferrer"' : ''; ?>>
													<?php echo esc_html($child->title); ?>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>

									<div class="site-nav-overlay__media" aria-hidden="true">
										<?php
										$menu_video = get_field('menu_video', (int) $item->ID);

										if (is_array($menu_video) && ! empty($menu_video['url'])) {
											$menu_video = $menu_video['url'];
										}

										$menu_video = is_string($menu_video) ? trim($menu_video) : '';
										?>

										<?php if ($menu_video !== '') : ?>
											<video
												class="site-nav-overlay__video"
												src="<?php echo esc_url($menu_video); ?>"
												muted
												autoplay
												loop
												playsinline
												preload="metadata"></video>
										<?php else : ?>
											<div class="site-nav-overlay__video-placeholder">
												<span class="site-nav-overlay__video-label"><?php esc_html_e('Video placeholder', 'foundry'); ?></span>
											</div>
										<?php endif; ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>
