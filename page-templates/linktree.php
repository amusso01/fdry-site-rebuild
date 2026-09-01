<?php
/**
 * Template Name: Linktree page
 * 
 * Template Post Type: post, page
 *
 * This template shows the all the parent pages of the site the highest in hierarchy
 * 
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/mainstyle/linktree.css">

<section class="main" style="--dark-color:#000;--light-color:#fff;background-color:rgb(209 213 219 / 1);--text-color:#fcfcfc;--link-color:51, 51, 51;--link-background:239, 239, 239;--link-shadow:102, 102, 102;--link-border:51, 51, 51;color:51, 51, 51;">
  <div class="background">
    <div class="background_image"></div>
  </div>
  <div class="bg_container">
    <div class="container"></div>
  </div>
  <div class="container">
     <center><img style="margin-top: 60px;" src="<?php echo get_stylesheet_directory_uri(); ?>/img/iconFDRY.webp"></center>
    <div class="container_component">
      <div class="profile">
        <?php if (get_the_content()) { ?>
            <div style="margin: 30px 0 40px" class="text text_center maininfo">
              <?php echo get_the_content(); ?>
            </div>
        <?php } ?>
        
        <?php
          if( have_rows('linktree') ):
              while( have_rows('linktree') ) : the_row(); ?>
                <div class="container_link">
                  <div class="link_outer">
                    <a href="<?php echo get_sub_field('url'); ?>" target="_blank" rel="noopener noreferrer" class="link link_circle link_circle_shadow">
                      <div class="link_icon">
                        <div class="link_image"><?php echo get_sub_field('icon_svg'); ?></div>
                      </div>
                      <div class="link_outer_text">
                        <div class="text text_center">
                          <strong><?php echo get_sub_field('label_url'); ?></strong>
                        </div>
                      </div>
                      <div class="link_end"></div>
                    </a>
                  </div>
                </div>
        <?php
              endwhile;
          endif;
        ?>
        
      </div>
    </div>
  </div>
</section>


<?php get_footer(); ?>