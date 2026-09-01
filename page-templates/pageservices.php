<?php
/**
 * Template Name: Page services
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

<div class="servicepage">
	
	<div class="greybanner">
		<div class="container">
			<h1><?php echo get_the_title(); ?></h1>
			<div class="bigfont"><?php echo get_the_content(); ?></div>
		</div>
	</div>

	<div class="bluedesign parentservicepage" style="margin-bottom: 0;">
			<div class="container">
				<div class="headlinebluebanner">
					<div class="row">
						<div class="col-5">
							<h2>Services</h2>
						</div>
						<div class="col-7">
							<div class="rowservice">
								
									<?php
										if( have_rows('services') ):
										    while( have_rows('services') ) : the_row(); ?>
										    	<div class="divarrow mainserv">
													<div class="pic">
														
															<div class="button">
																<div class="row <?php if(get_the_ID() == 10){ ?>active<?php } ?>" id="parent-<?php echo get_row_index(); ?>">
																	<div class="col-1">
																		<?php if ( get_sub_field('icon') ) { ?>
																			<div class="svgimage"><?php echo get_sub_field('icon'); ?></div>
																		<?php }else{ ?>
																			<div class="svgimage"><img src="<?php echo get_template_directory_uri(); ?>/img/svg/brand_identity_icon.svg" /></div>
																		<?php } ?>
																	</div>
																	<div class="col-8">
																		<a href="<?php echo get_sub_field('servicelinkurl'); ?>">
																			<div class="divtext">
																				<h3><?php echo get_sub_field('title'); ?></h3>
																				<p><?php echo get_sub_field('description'); ?></p>
																			</div>
																		</a>
																	</div>
																	<div class="col-3">
																		<a class="btnaccordion <?php if(get_the_ID() == 10){ ?>active<?php } ?>" href="javascript:void(0)" data-id="<?php echo get_row_index(); ?>" id="btn-<?php echo get_row_index(); ?>">
																			<div id="actionbtn-<?php echo get_row_index(); ?>" class="actionbtn <?php if(get_the_ID() == 10){ ?>active<?php } ?>"></div>
																		</a>
																	</div>
																</div>
															</div>
														
													</div>
												</div>

												<div class="subservicediv <?php if(get_the_ID() == 10){ ?>active<?php } ?>" id="content-<?php echo get_row_index(); ?>">
													<?php
														if( have_rows('internal_services') ):
														    while( have_rows('internal_services') ) : the_row(); ?>
														    	<div class="divarrow">
																	<div class="pic">
																		<a href="<?php echo get_sub_field('url'); ?>">
																			<div class="button">
																				<div class="row">
																					<div class="col-1">&nbsp;</div>
																					<div class="col-9">
																						<div class="divtext">
																							<h3><?php echo get_sub_field('title'); ?></h3>
																						</div>
																					</div>
																					<div class="col-2">&nbsp;</div>
																				</div>
																				

																				
																				
																			</div>
																		</a>
																	</div>
																</div>
														    	
													<?php
														    endwhile;
														endif;
													?>
												</div>
									<?php    
										    endwhile;
										endif;
									?>
									
							</div>
						</div>
					</div>
				</div>

				


			</div>
		</div>


		<div class="allworks">
			<?php 
	$works = new WP_Query( array ('post_type' => 'works_post','posts_per_page' => 4));
?>



<section <?php post_class('container-fluid'); ?> id="post-<?php the_ID(); ?>">


	<div class="work-grid" style="grid-template-columns: 1fr 1fr;">
		<?php
		if ($works-> have_posts() ) {
			while ($works-> have_posts() ) {
				$works->the_post(); 

				$cat = get_the_category(); // array of object of WP_Term
				$postCat = []; // object WP_Term for the current post

				foreach($cat as $category){
					$postCat[] = $category->cat_name;
				}
				// var_dump($postCat);

				$thumbnail_id  = get_post_thumbnail_id($works->ID);
				$thumbnail_alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
				$image = wp_get_attachment_image_src( $thumbnail_id,'large' ); 
		?>
				<a href="<?php echo get_permalink(); ?>" class="ajax-call  <?php foreach($postCat as $name){ echo $name.' '; }   ?>"><article class="work-box " <?php echo $postCat->slug; ?>"  >

					<div class="hovereffect">
						<img src="<?php echo get_template_directory_uri()?>/img/Spinner.gif" data-src="<?php echo $image[0]; ?>"  class="img-fluid lozad" />
						<noscript><img src="<?php echo $image[0]; ?>"  class="img-fluid lozad" /></noscript>
						<div class="overlay">
							
							<h2 class="work-title" ><?php the_title(); ?></h2>
							<p class="work-description info"><?php echo get_field('description')?></p>
						</div>
					</div>
		
				</article></a><!-- article.work-box -->
		<?php }
			// Restore original Post Data
		wp_reset_postdata();
		}	
		?>
	</div><!-- work-grid -->

</section><!-- section -->
<div class="pic"> 	<a href="<?php echo get_the_permalink(50); ?>"><div class="button"><span>MORE WORK</span></div> 	</a></div>
		</div>

</div>

<div class="extrahelpbanner we-help hidden-animate">
	<div class="container">
		<?php echo get_field('extra_help'); ?>
		<div class="pic"> 	<a href="<?php echo site_url( '/contact/');?>"><div class="button"><span>GET IN TOUCH</span></div> 	</a></div>
	</div>
</div>


<script type="text/javascript">
	$( ".btnaccordion" ).click(function() {
		if( $( this ).hasClass( "active" ) ){
			var dataId = $(this).attr("data-id");
			$( "#content-"+dataId ).removeClass( "active" );
			$( this ).removeClass( "active" );
			$( "#actionbtn-"+dataId ).removeClass( "active" );
			$( "#parent-"+dataId ).removeClass( "active" );
		}else{
			var dataId = $(this).attr("data-id");
			$( "#content-"+dataId ).addClass( "active" );
			$( this ).addClass( "active" );
			$( "#actionbtn-"+dataId ).addClass( "active" );
			$( "#parent-"+dataId ).addClass( "active" );
		}
	  
	});
</script>

<?php get_footer(); ?>