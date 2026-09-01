<?php
/**
 * Template Name: Parent-pages
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

<div class="greybanner">
	<div class="container">
		<h1><?php echo get_the_title(); ?></h1>
		<div class="bigfont"><?php echo get_the_content(); ?></div>

		<?php if ( is_page("about") ) { ?>
			<div class="aboutbtns">
				<div class="pic blackbutton">
					<a href="<?php echo get_the_permalink(1361);  ?>">
						<div class="button"><span>AGENCY LIFE</span></div>
					</a>
				</div>
				<div class="pic blackbutton">
					<a href="<?php echo get_the_permalink(5321);  ?>">
						<div class="button"><span>WORK WITH US</span></div>
					</a>
				</div>
			</div>
		<?php } ?>

		<?php if(is_page("work")){ ?>
			<div class="container work-navigation">
				<div class="row">

					<nav class="col-md-12">
                
                            
                        <!--<ul id="customnavtax" class="fadeInUp">
                            <li ><a href="<?php echo get_the_permalink(50); ?>" class="category-all active" data-category="category-all">Featured</a></li>
                            <li  ><a href="/work/category/design/" >Brand &amp; Design</a></li>
                            <li  ><a href="/work/category/website/" >Web Development</a></li>
                            <li  ><a href="/work/category/ecommerce/" >E-commerce Design</a></li>
                            <li  ><a href="/work/category/growth/">Digital Marketing</a></li>
                        </ul>-->
                        
                        <ul id="customnavtax" class="tab-nav fadeInUp">
                        	<li><a href="#all" class="active">Featured</a></li>
				            <li><a href="#design">Brand &amp; Design</a></li>
				            <li><a href="#website">Web Development</a></li>
				            <li><a href="#ecommerce">E-commerce Design</a></li>
				            <li><a href="#growth">Digital Marketing</a></li>
				        </ul> 
                            
                    </nav>

				</div><!-- row -->
			</div>
		<?php } ?>
		
	</div>
</div>

<div class="wrapper" id="page-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content">

		<div class="row">

			<div class="col-md-12">

						

			</div><!-- .col-md-12 -->

		</div><!-- .row -->

	</div><!-- Container end -->

						<?php if(is_page("work")){

								//get_template_part( 'loop-templates/content', 'work' ); 
								get_template_part( 'loop-templates/tab', 'work' ); 
							
							}elseif(is_page("service")){

								get_template_part( 'loop-templates/content', 'service' ); 

							}elseif(is_page("about")){

								get_template_part( 'loop-templates/content', 'about' ); 

							}elseif(is_page("contact")){

								get_template_part( 'loop-templates/content', 'contact' ); 

							}elseif(is_page("insights")){

								get_template_part( 'loop-templates/content', 'insight' ); 

							}elseif (is_page('brief-1')) {

								get_template_part( 'loop-templates/content', 'brief-1' ); 

							}elseif (is_page('brief-2')) {

								get_template_part( 'loop-templates/content', 'brief-2' ); 

							}elseif (is_page('brief-final')) {

								get_template_part( 'loop-templates/content', 'brief-final' ); 
							}
						?>
</div><!-- Wrapper end -->

<?php get_footer(); ?>