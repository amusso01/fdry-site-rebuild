<?php

/**
 * Template Name: NEW template Insight
 *
 * Template Post Type: page
 *
 * This template show the Insights page (intro and post grid)
 *
 *
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
get_header('new');
?>


<main class="main insight-main" role="main">

  <?php get_template_part('components/page/insight-archive'); ?>

</main>

<?php get_footer(); ?>
