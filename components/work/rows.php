<?php

/**
 * Single work rows — the work_rows flexible content, in the editor's order
 *
 * Each row goes to its partial by layout: image_row → work/image-row.php,
 * content_row → work/content-row.php.
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
$rows    = get_field('work_rows', $post_id);

$partials = array(
	'image_row'   => 'components/work/image-row',
	'content_row' => 'components/work/content-row',
);

foreach (is_array($rows) ? $rows : array() as $row) {
	$layout = $row['acf_fc_layout'] ?? '';

	if (! isset($partials[$layout])) {
		continue;
	}

	get_template_part($partials[$layout], null, array('row' => $row));
}
