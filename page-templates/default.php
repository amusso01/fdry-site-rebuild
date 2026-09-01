<?php
/**
 * Template Name: Default page
 * 
 * Template Post Type: page
 *
 * This template shows Brief form
 * 
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>

<div class="defaultpage">
	<div class="container">
		<h1><?php echo get_the_title(); ?></h1>
		<?php the_content(); ?>
	</div>
</div>




<?php get_footer(); ?>