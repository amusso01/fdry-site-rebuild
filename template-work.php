<?php

/**
 * Template Name: NEW template Work
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


<main class="main work-main" role="main">

  <?php get_template_part('components/page/work-archive'); ?>

</main>

<?php get_footer(); ?>