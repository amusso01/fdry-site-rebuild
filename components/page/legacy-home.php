<?php

/**
 * Legacy homepage body (FDRY 2023), kept as a rollback only. Nothing includes it.
 *
 * Replaced by the component sections in front-page.php. To roll back, swap the
 * <main> block in front-page.php for
 * get_template_part('components/page/legacy-home'), set Settings → Reading →
 * Homepage back to the old page (its ACF fields hold this content) and set the
 * two legacy front-page ACF groups back to active if it needs editing. Delete
 * it once the new homepage is confirmed.
 *
 * @package foundry
 */

?>
<section id="full-screen-video">
  <div id="loading-animation" style="min-height: 900px!important; height: 100%!important;">
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


      <video muted="" id="iframe" autoplay="" playsinline="" loop="" style=" width:100%;
       margin: auto;">
        <source src="<?php echo get_stylesheet_directory_uri(); ?>/video/showreel.mp4" type="video/mp4">
      </video>

      <img id="iframeresponsive" data-src="<?php echo get_stylesheet_directory_uri(); ?>/video/mobile-still.jpg" alt="Showcase of Projects">
    </div>

    <img id="iframeresponsive" src="https://www.fdry.com/wp-content/uploads/2023/07/mobile-hero.png">

  </header>

</section>







<div class="wrapper" id="home-wrapper">

  <main class="site-main" id="main">

    <?php get_template_part('loop-templates/content', 'home'); ?>

  </main><!-- #main -->


</div><!-- Wrapper end #home-wrapper -->
