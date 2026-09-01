<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


header("HTTP/1.1 301 Moved Permanently");
header("Location: ".get_bloginfo('url'));
exit();


get_header();

$container   = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="error-404-wrapper">

	<div class="container-fluid" id="content" tabindex="-1">

		<div class="row">

			<div class="col-md-8 offset-md-1 content-area" id="primary">

				<main class="site-main" id="main">

					<section class="error-404 not-found">

						<header class="header pt-5 overlay-404">


							<h1 class="large">Oops!</h1>
							<h2 class="page-title">We are looking for your page...</h2>
							<h2 class="page-title">but we can't find it.</h2>

							<p class="error">Error code: 404</p>

						</header><!-- .page-header -->

					</section><!-- .error-404 -->

				</main><!-- #main -->

			</div><!-- #primary -->

		</div><!-- .row -->

	</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
