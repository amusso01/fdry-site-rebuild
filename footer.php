<?php

/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package understrap
 */

if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

$container = get_theme_mod('understrap_container_type');

if (defined('FDRY_USING_NEW_HEADER') && FDRY_USING_NEW_HEADER) :
?>
  </div><!-- #content -->
<?php
endif;
?>

<?php get_template_part('components/footer/tech-banner'); ?>

<?php get_template_part('sidebar-templates/sidebar', 'footerfull'); ?>

<?php get_template_part('components/footer/brief'); ?>

<?php get_template_part('components/footer/site-footer'); ?>

<?php wp_footer(); ?>

<script src="<?php echo get_template_directory_uri(); ?>/mainjs/footer.js"></script>


<?php if (defined('FDRY_USING_NEW_HEADER') && FDRY_USING_NEW_HEADER) : ?>
  </div><!-- #page -->
<?php endif; ?>

</body>

</html>