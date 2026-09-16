<?php

/**
 * Template Name: NEW template Service inner
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


<main class="main service-inner-main" role="main">

  <?php
  get_template_part('components/page/centered-content', null, array(
    'is_first'    => true,
    'tagline_tag' => 'h1',
  ));
  get_template_part('components/page/four-column-grid');
  get_template_part('components/page/image-banner');
  get_template_part('components/page/two-column-list');
  get_template_part('components/page/image-text-alternate');
  get_template_part('components/page/two-column-list', null, array(
    'prefix'     => 'two_column_gray',
    'appearance' => 'gray',
  ));
  get_template_part('components/page/two-column-list', null, array(
    'prefix' => 'two_column_2',
  ));
  get_template_part('components/page/two-column-list', null, array(
    'prefix'     => 'two_column_gray_2',
    'appearance' => 'gray',
  ));
  ?>

</main>

<?php get_footer(); ?>