<?php

/**
 * Template Name: NEW template homepage
 * 
 * Template Post Type: page
 *
 * This template show career page
 * 
 *
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
get_header('new');
?>

<main class="main homepage-main" role="main">

  <?php get_template_part('components/page/marquee'); ?>


</main>

<?php get_footer(); ?>