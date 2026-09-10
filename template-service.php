<?php

/**
 * Template Name: NEW template Service
 * 
 * Template Post Type: page
 *
 * 
 *
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
get_header('new');
?>


<main class="main service-main" role="main">

  <?php get_template_part('components/page/centered-content'); ?>
  <?php get_template_part('components/page/showreel'); ?>

</main>

<?php get_footer(); ?>