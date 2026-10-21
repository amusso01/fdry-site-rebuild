<?php

/**
 * Single work meta bar — platform, industry, strategy pills and Visit site
 *
 * Data from fdry_work_meta(): the work_platform and work_website fields and
 * the industry and strategy taxonomies. Each strategy pill links to its
 * term's Service link, or is plain text without one. Empty details are
 * skipped; with none at all the bar is not rendered.
 *
 * @author Andrea Musso
 *
 * @package foundry
 *
 * @param array $args {
 *     Optional.
 *
 *     @type int $post_id Post ID for ACF fields and terms. Default queried object.
 * }
 */

if (! defined('ABSPATH')) {
	exit;
}

if (! isset($args) || ! is_array($args)) {
	$args = array();
}

$post_id = isset($args['post_id']) ? (int) $args['post_id'] : (int) get_queried_object_id();
$meta    = fdry_work_meta($post_id);

$has_details = $meta['platform'] !== '' || $meta['industries'] !== array() || $meta['strategies'] !== array();

if (! $has_details && $meta['website'] === '') {
	return;
}
?>

<section class="work-meta-bar" aria-label="<?php esc_attr_e('Project details', 'foundry'); ?>">
	<div class="content-max">
		<div class="work-meta-bar__grid" data-fade-up>
			<?php if ($has_details) : ?>
				<dl class="work-meta-bar__details">
					<?php if ($meta['platform'] !== '') : ?>
						<div class="work-meta-bar__detail">
							<dt class="work-meta-bar__label"><?php esc_html_e('Platform', 'foundry'); ?></dt>
							<dd class="work-meta-bar__value"><?= esc_html($meta['platform']); ?></dd>
						</div>
					<?php endif; ?>

					<?php if ($meta['industries'] !== array()) : ?>
						<div class="work-meta-bar__detail">
							<dt class="work-meta-bar__label"><?php esc_html_e('Industry', 'foundry'); ?></dt>
							<dd class="work-meta-bar__value"><?= esc_html(implode(', ', $meta['industries'])); ?></dd>
						</div>
					<?php endif; ?>

					<?php if ($meta['strategies'] !== array()) : ?>
						<div class="work-meta-bar__detail work-meta-bar__detail--strategy">
							<dt class="work-meta-bar__label"><?php esc_html_e('Strategy', 'foundry'); ?></dt>
							<dd class="work-meta-bar__value">
								<ul class="work-meta-bar__pills">
									<?php foreach ($meta['strategies'] as $strategy) : ?>
										<li>
											<?php if ($strategy['url'] !== '') : ?>
												<a class="work-meta-bar__pill work-meta-bar__pill--link" href="<?= esc_url($strategy['url']); ?>"><?= esc_html($strategy['name']); ?></a>
											<?php else : ?>
												<span class="work-meta-bar__pill"><?= esc_html($strategy['name']); ?></span>
											<?php endif; ?>
										</li>
									<?php endforeach; ?>
								</ul>
							</dd>
						</div>
					<?php endif; ?>
				</dl>
			<?php endif; ?>

			<?php if ($meta['website'] !== '') : ?>
				<a class="work-meta-bar__visit" href="<?= esc_url($meta['website']); ?>" target="_blank" rel="noopener">
					<?php esc_html_e('Visit site', 'foundry'); ?>
					<span class="screen-reader-text"><?php esc_html_e('(opens in a new tab)', 'foundry'); ?></span>
					<span class="work-meta-bar__visit-arrow" aria-hidden="true"><?php get_template_part('svg-template/svg-arrow'); ?></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
