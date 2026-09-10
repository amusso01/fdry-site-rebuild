<?php

/**
 * Template Name: NEW template Service Child
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


<main class="main service-child-main" role="main">

  <?php get_template_part('components/page/centered-content', null, array('is_first' => true)); ?>
  <?php get_template_part('components/page/banner'); ?>
  <?php get_template_part('components/page/navigation-content'); ?>
  <?php get_template_part('components/page/work-row', null, array('show_more_work' => true)); ?>
  <?php get_template_part('components/page/two-column-cards'); ?>
  <?php get_template_part('components/page/work-row', null, array('field' => 'work_row_2')); ?>
  <?php get_template_part('components/page/two-column-cards', null, array('prefix' => 'two_column_2')); ?>

</main>

<?php get_footer(); ?>