<?php

/**
 * Template Name: NEW template About
 * 
 * Template Post Type: page
 *
 * This template show About page
 * 
 *
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
get_header('new');
?>

<main class="main about-main" role="main">

  <?php
  get_template_part('components/page/centered-content', null, array(
    'is_first'    => true,
    'tagline_tag' => 'h1',
  ));
  get_template_part('components/page/four-column-grid', null, array(
    'prefix'  => 'three_column',
    'columns' => 3,
  ));
  get_template_part('components/page/image-banner');
  get_template_part('components/page/three-grid-images');
  get_template_part('components/page/two-column-features');
  get_template_part('components/page/two-column-features', null, array(
    'prefix'     => 'two_column_gray',
    'appearance' => 'gray',
  ));
  get_template_part('components/page/team');
  get_template_part('components/page/two-images-grid');
  ?>

</main>

<?php get_footer(); ?>