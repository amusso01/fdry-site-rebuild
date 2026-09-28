<?php

/**
 * The template for displaying Front-page AKA homepage of the website.
 *
 * WordPress ranks front-page.php above the template picked in the page editor,
 * so this file renders the homepage whatever template the page has. The
 * sections mirror template-home.php and read the "Homepage" ACF group, which
 * is also located on page_type == front_page.
 *
 * The old homepage body is kept in components/page/legacy-home.php for
 * rollback (see that file).
 *
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

get_header('new');

?>

<main class="main homepage-main" role="main">

  <?php get_template_part('components/page/hero-video');
  ?>
  <?php get_template_part('components/page/marquee');
  ?>
  <?php get_template_part('components/page/intro-content');
  ?>
  <?php get_template_part('components/page/work-parallax');
  ?>
  <?php get_template_part('components/page/navigation-content');
  ?>
  <?php get_template_part('components/page/work-row', null, array('slider' => false, 'contained' => true));
  ?>
  <?php get_template_part('components/page/two-column-content');
  ?>
  <?php get_template_part('components/page/work-row', null, array('field' => 'work_row_2', 'slider' => false, 'contained' => true));
  ?>
  <?php get_template_part('components/page/two-column-content', null, array('prefix' => 'two_column_2'));
  ?>
</main>

<?php get_footer(); ?>
