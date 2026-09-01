<?php
/**
 * Template Name: Page main service (Parent service)
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

global $post;
$pageID = $post->ID;
?>

<div class="servicepage">
	
	<div class="greybanner">
		<div class="container">
			<h1><?php echo get_the_title(); ?></h1>
			<div class="bigfont"><?php echo get_the_content(); ?></div>
		</div>
	</div>

	<?php 
		if ( get_the_ID() == 6801) {
			// desig
			$titleserv = "Create";
			$descrip = "Our graphic designers use UX & visual strategy to create eye-catching identities and user interfaces.";
			$createExcept = get_field('create_excerpt',7);
			$createServices = get_field('create_sub_services',7);
			$createWorks = get_field('create_works',7);
		}elseif( get_the_ID() == 6815 ){
			// develop
			$titleserv = "Develop";
			$descrip = "We pair robust development with stunning design to build the best websites, apps and digital platforms.";
			$createExcept = get_field('build_excerpt',7);
			$createServices = get_field('build_sub_services',7);
			$createWorks = get_field('build_works',7);
		}elseif ( get_the_ID() == 6821) {
			// grow
			$titleserv = "Grow";
			$descrip = "We pair robust development with stunning design to build the best websites, apps and digital platforms.";
			$createExcept = get_field('promote_excerpt',7);
			$createServices = get_field('promote_sub_services',7);
			$createWorks = get_field('promote_works',7);
		}
		 

		
	?>
	<div class="bluedesign">
			<div class="container">
				<div class="headlinebluebanner">
					<div class="row">
						<div class="col-5">
							<h2><?php echo $titleserv; ?></h2>
							<div class="righttext">
								<p><?php echo $createExcept ?></p>
							</div>
						</div>
						<div class="col-7">
							<div class="rowservice">

								<?php foreach($createServices as  $service) : ?>
									<div class="divarrow">
										<div class="pic">
											<a href="<?php echo $service['sub_service_link'] ?>">
												<div class="button">
													<div class="row">
														<div class="col-1">
															<?php if ( $service['icon'] ) { ?>
																<div class="svgimage"><img src="<?php echo get_template_directory_uri(); ?>/img/svg/<?php echo $service['icon']; ?>" /></div>
															<?php }else{ ?>
																<div class="svgimage"><img src="<?php echo get_template_directory_uri(); ?>/img/svg/brand_identity_icon.svg" /></div>
															<?php } ?>
														</div>
														<div class="col-8">
															<div class="divtext">
																<h3><?php echo $service['sub_service_title'] ?></h3>
																<p><?php echo $service['sub_service_excerpt'] ?></p>
															</div>
														</div>
														<div class="col-2">&nbsp;</div>
													</div>
													

													
													
												</div>
											</a>
										</div>
									</div>
								<?php endforeach; ?>

							</div>
						</div>
					</div>
				</div>

				


			</div>
		</div>

		<div class="allworks">
			<?php 
				$postarrayall = array();
				if( have_rows('featured_allwork', $pageID) ):
				    while( have_rows('featured_allwork', $pageID) ) : the_row();

				        $postid = get_sub_field('post_work');
				        array_push($postarrayall, $postid);

				    endwhile;
				endif;
			?>
			<?php 
				$works = new WP_Query( array ('post_type' => 'works_post','posts_per_page' => 4, 'post__in' => $postarrayall));
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
							<a href="<?php echo get_permalink(); ?>" class="ajax-call  <?php foreach($postCat as $name){ echo $name.' '; }   ?>"><article class="work-box " <?php echo $postCat->slug; ?> >

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


		<div class="bluedesign ourprocess" style="margin-bottom: 0;">
			<div class="container">
				<div class="headlinebluebanner">
					<div class="row">
						<div class="col-4">
							<h2>OUR PROCESS</h2>
							<div class="processtext"><?php echo get_field('our_process_description'); ?></div>
						</div>
						<div class="col-8">
							<div class="rowservice">
								
									<?php
										if( have_rows('our_process') ):
										    while( have_rows('our_process') ) : the_row(); ?>
										    	<div class="divarrow mainserv">
													<div class="pic">
														<a class="btnaccordion" href="javascript:void(0)" data-id="<?php echo get_row_index(); ?>" id="btn-<?php echo get_row_index(); ?>">
															<div class="button">
																<div class="row">
																	<div class="col-1">
																		<p class="indexrow">0<?php echo get_row_index(); ?></p>
																	</div>
																	<div class="col-10">
																		<div class="divtext" style="padding-left: 0;">
																			<h3><?php echo get_sub_field('title'); ?></h3>
																		</div>
																	</div>
																	<div class="col-1">
																		<div id="actionbtn-<?php echo get_row_index(); ?>" class="actionbtn"></div>
																	</div>
																</div>
															</div>
														</a>
													</div>
												</div>

												<div class="subservicediv" id="content-<?php echo get_row_index(); ?>">
														<?php echo get_sub_field('content'); ?>
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

</div>

<div class="allworks">
			<?php 
				$postarray = array();
				if( have_rows('featured_work', $pageID) ):
				    while( have_rows('featured_work', $pageID) ) : the_row();

				        $postid = get_sub_field('post_work');
				        array_push($postarray, $postid);

				    endwhile;
				endif;
			?>

			<?php 
				$works = new WP_Query( array ('post_type' => 'works_post','posts_per_page' => 2, 'post__in' => $postarray ));
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
							<a href="<?php echo get_permalink(); ?>" class="ajax-call  <?php foreach($postCat as $name){ echo $name.' '; }   ?>"><article class="work-box " <?php echo $postCat->slug; ?> >

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

<div class="extrahelpbanner creatyveagency we-help hidden-animate">
	<div class="container">
		<?php echo get_field('creative_agency',$pageID); ?>
	</div>
</div>


<script type="text/javascript">
	$( ".btnaccordion" ).click(function() {
		if( $( this ).hasClass( "active" ) ){
			var dataId = $(this).attr("data-id");
			$( "#content-"+dataId ).removeClass( "active" );
			$( this ).removeClass( "active" );
			$( "#actionbtn-"+dataId ).removeClass( "active" );
		}else{
			var dataId = $(this).attr("data-id");
			$( "#content-"+dataId ).addClass( "active" );
			$( this ).addClass( "active" );
			$( "#actionbtn-"+dataId ).addClass( "active" );
		}
	  
	});
</script>
<?php get_footer(); ?>