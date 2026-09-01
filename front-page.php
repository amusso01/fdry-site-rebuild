<?php
/**
 * The template for displaying Front-page AKA homepage of the website.
 *
 * This is the template that displays the HOME of the website by default.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

$container   = get_theme_mod( 'understrap_container_type' );

?>


<section id="full-screen-video">
  <div id="loading-animation"  style="min-height: 900px!important; height: 100%!important;">
    <div id="loader">
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="dot"></div>
      <div class="lading"></div>
    </div>            
  </div>
	<header class="jumbo-video" style="">
    <div class="container-video">
      <div id="video_overlays"></div>
       
      <script src="https://player.vimeo.com/api/player.js"></script>


      <video muted="" id="iframe"  autoplay="" playsinline="" loop="" style=" width:100%;
       margin: auto;"><source src="<?php echo get_stylesheet_directory_uri(); ?>/video/showreel.mp4" type="video/mp4"></video>
    
     <img id="iframeresponsive" data-src="<?php echo get_stylesheet_directory_uri(); ?>/video/mobile-still.jpg" alt="Showcase of Projects" >
    </div>

    <img id="iframeresponsive" src="https://www.fdry.com/wp-content/uploads/2023/07/mobile-hero.png">
    
  </header>

</section>







<div class="wrapper" id="home-wrapper">

	<main class="site-main" id="main">

		<?php get_template_part( 'loop-templates/content', 'home' ); ?>

	</main><!-- #main -->


</div><!-- Wrapper end #home-wrapper -->

<?php get_footer(); ?>