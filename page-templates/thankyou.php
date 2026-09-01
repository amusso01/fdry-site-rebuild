<?php
/**
 * Template Name: Thank you
 * 
 * Template Post Type:  page
 *
 * 
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>



<div class="wrapper" style="min-height:50vh; " id="page-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content">

		<div class="row">

			<div class="col-md-12">
				<header class="site-main" id="parent" role="main">
					<?php while ( have_posts() ) : the_post(); ?>

						<?php the_title( '<h1 class="parent-title">', '</h1>' ); ?>

						 <div class="line-gradient"></div>


						<div class="parent-content">

							<?php the_content(); ?>

						</div><!-- .entry-content -->

						<?php endwhile; // end of the loop. ?>
					
						

				</header><!-- #header -->

			</div><!-- .col-md-12 -->

		</div><!-- .row -->

	</div><!-- Container end -->

	
</div><!-- Wrapper end -->

<?php get_footer();

?>