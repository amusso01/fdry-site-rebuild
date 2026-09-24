<?php

/**
 * Template Name: NEW template one column
 *
 * Template Post Type: page
 *
 * This template show One column page page
 */

if (! defined('ABSPATH')) {
	exit;
}

get_header('new');

$post_id = (int) get_queried_object_id();
?>

<main class="main one-column" role="main">
	<?php if (trim((string) get_post_field('post_content', $post_id)) !== '') : ?>
		<div class="one-column__inner content-block content-narrow">
			<div class="wysiwyg" data-fade-up-group>
				<?php the_content(); ?>
			</div>
		</div>
	<?php endif; ?>
</main>

<?php get_footer(); ?>
