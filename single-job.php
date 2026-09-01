<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>



<div class="defaultpage" id="career">

	<div class="container" id="content">

		<div class="row">

			<div class="content-area" id="primary">

				<main class="site-main career-main" id="main" role="main">

					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'loop-templates/content', 'page' ); ?>


					<?php endwhile; // end of the loop. ?>

				</main><!-- #main -->

			</div><!-- #primary -->

		</div><!-- .row end -->

		<div style="margin-top:20px; font-weight:bold;">
			<p><a href="<?php echo site_url('/careers') ?>">BACK TO JOBS</a></p>
		</div>

	</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>