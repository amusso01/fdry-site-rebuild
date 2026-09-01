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

  <?php get_template_part('components/page/hero-video'); ?>
  <?php get_template_part('components/page/marquee'); ?>
  <?php get_template_part('components/page/intro-content'); ?>


</main>

<?php get_footer(); ?>