<?php
/**
 * Template Name: Service-page-inner
 * 
 * Template Post Type: page
 *
 * This template shows the three services of the website
 * 
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();
?>

<main class="inner-service-pages">
	<div class="container-fluid inner-service-hero">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-9 text-center ">
					<?php the_title( '<h1>', '</h1>', true );?>
				</div>
				<div class="col-lg-8 text-center">
					<?php 
					global $post;
					$content = $post->post_content;
		
					if ( !empty( $content ) ) :
						echo $content;
					endif;
					?>
					<a href="<?php echo site_url( '/contact/');?>" class="btn">GET IN TOUCH</a>
				</div>
				<div class="img-fluid text-center col-md-12">
					<?php the_post_thumbnail( array(900, 400), 'hero image of the page' ) ?>
				</div>
			</div>
		</div>
	</div>
	<?php
	$color = 'gray';
	$count = 1;
	$i=0;
	$class = array('brand', 'graphic', 'promotional', 'design');
// check if the repeater field has rows of data
if( have_rows('service_article') ):

 	// loop through the rows of data
	while ( have_rows('service_article') ) : the_row();
	if($count % 2 == 0){
		$color = 'gray';
	?>
	<div class="container-fluid <?php echo $color; ?>">
		<div class="container hidden-animate">
			<div class="row " id="<?php echo $class[$i] ?>">
				<div class="col-md-6 image article-box">
					<img src="<?php the_sub_field('side_image'); ?>" alt="article image">
				</div>
				<div class="col-md-6 article-box">
					<h3><?php the_sub_field('title');?></h3>
					<p><?php the_sub_field('paragraph');?></p>
					<div class="service-inner-link">
						<a href="<?php echo site_url('/brief-1/')?>">LET'S TALK</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	}else{
		$color = 'white';
	?>
	<div class="container-fluid <?php echo $color; ?>">
		<div class="container hidden-animate">
			<div class="row" id="<?php echo $class[$i] ?>">
				<div class="col-md-6 article-box">
					<h3><?php the_sub_field('title');?></h3>
					<p><?php the_sub_field('paragraph');?></p>
					<div class="service-inner-link">
						<a href="<?php echo site_url('/brief-1/')?>">LET'S TALK</a>
					</div>
				</div>
				<div class="col-md-6 image article-box">
					<img src="<?php the_sub_field('side_image'); ?>" alt="article image">
				</div>
			</div>
		</div>
	</div>
	<?php
	}
	$i++;
	$count++;
    endwhile;

else :

    // no rows found

endif;

?>
		
</main><!-- main .service-inside -->
	
	
	
<?php get_footer(); ?>
	