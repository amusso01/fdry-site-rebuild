<?php

/**
 * Template Name: NEW template Single Work
 *
 * Template Post Type: works_post
 *
 * A case study (works_post) built from components. Works without this
 * template still render through single.php (content-single-work.php).
 *
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
get_header('new');
?>


<main class="main single-work-main" role="main">

  <?php
  get_template_part('components/page/centered-content', null, array(
    'prefix'      => 'work_hero',
    'tagline'     => get_the_title(),
    'tagline_tag' => 'h1',
    'is_first'    => true,
  ));
  get_template_part('components/work/hero-media');
  get_template_part('components/work/meta-bar');
  get_template_part('components/work/results');
  get_template_part('components/work/rows');
  ?>

</main>

<?php get_footer(); ?>
