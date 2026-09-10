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

  <?php get_template_part('components/page/centered-content', null, array('is_first' => true)); ?>
  <?php get_template_part('components/page/showreel'); ?>
  <?php get_template_part('components/page/navigation-content'); ?>
  <?php get_template_part('components/page/work-row', null, array('show_more_work' => true)); ?>
  <?php get_template_part('components/page/centered-content', null, array(
    'prefix'     => 'centered_dark',
    'appearance' => 'dark',
  )); ?>

</main>

<?php get_footer(); ?>