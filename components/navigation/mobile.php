<?php

/**
 * Mobile / tablet nav overlay (Secondary menu 2026 accordion).
 *
 * @author Andrea Musso
 *
 * @package Foundry
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$menu_tree = $args['menu_tree'] ?? fdry_get_secondary_menu_tree();

if (! is_array($menu_tree)) {
	$menu_tree = array();
}

$view_all_work = $args['view_all_work'] ?? fdry_acf_link_parts(
	fdry_get_nav_menu_acf_field('view_all_work_link', 'secondarymenu')
);

if (! is_array($view_all_work)) {
	$view_all_work = array(
		'url'    => '#',
		'target' => '',
	);
}
?>

<div class="site-nav-mobile">
	<?php if (has_nav_menu('mainmenu')) : ?>
		<nav class="site-nav-mobile__main" aria-label="<?php esc_attr_e('Main menu', 'foundry'); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'mainmenu',
					'menu_id'        => 'menu_main_mobile',
					'container'      => false,
					'depth'          => 1,
					'menu_class'     => 'site-nav-mobile__main-list menu',
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
	<?php endif; ?>

	<?php if ($menu_tree !== array()) : ?>
		<div class="site-nav-mobile__secondary">
			<?php
			$in_accordion = false;

			foreach ($menu_tree as $node) :
				$item         = $node['item'];
				$children     = $node['children'];
				$has_children = $children !== array();

				if ($has_children) :
					if (! $in_accordion) :
						$in_accordion = true;
						?>
						<div class="accordion-container site-nav-mobile__accordion">
						<?php
					endif;
					?>
					<div class="ac site-nav-mobile__ac">
						<h2 class="ac-header site-nav-mobile__ac-header">
							<button type="button" class="ac-trigger site-nav-mobile__ac-trigger">
								<?php echo esc_html($item->title); ?>
							</button>
						</h2>
						<div class="ac-panel site-nav-mobile__ac-panel">
							<div class="ac-panel-inner site-nav-mobile__ac-panel-inner">
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
							</div>
						</div>
					</div>
					<?php
				else :
					if ($in_accordion) :
						$in_accordion = false;
						?>
						</div>
						<?php
					endif;
					?>
					<a
						class="site-nav-mobile__parent-link"
						href="<?php echo esc_url($item->url); ?>"
						<?php echo $item->target ? ' target="' . esc_attr($item->target) . '"' : ''; ?>
						<?php echo $item->target === '_blank' ? ' rel="noopener noreferrer"' : ''; ?>>
						<span class="site-nav-mobile__parent-link-label"><?php echo esc_html($item->title); ?></span>
					</a>
					<?php
				endif;
			endforeach;

			if ($in_accordion) :
				?>
				</div>
				<?php
			endif;
			?>
		</div>
	<?php endif; ?>

	<div class="site-nav-mobile__footer">
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
