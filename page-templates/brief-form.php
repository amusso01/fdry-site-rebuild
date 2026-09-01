<?php
/**
 * Template Name: Brief Form page
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
<?php echo do_shortcode( '[wpforms id="4039" title="false"]' );?>

<?php get_footer(); ?>