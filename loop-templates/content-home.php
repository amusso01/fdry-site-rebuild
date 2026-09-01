<?php
/**
 * Partial template for content in front-page AKA homepage
 *
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$works = new WP_Query( array (
	'post_type' => 'works_post',
	'posts_per_page' => 3
));

// Headline
$headArray = get_field('headline');


// create
$createExcept = get_field('create_excerpt');
$createServices = get_field('create_sub_services');
$createWorks = get_field('create_works');

// build
$buildExcept = get_field('build_excerpt');
$buildServices = get_field('build_sub_services');
$buildWorks = get_field('build_works');

// promote
$promoteExcept = get_field('promote_excerpt');
$promoteServices = get_field('promote_sub_services');
$promoteWorks = get_field('promote_works');


?>

<div class="container-fluid services-wrapper">

	<div class="sliderbody">
        <div class="slider">
            <div class="slide-track">
            	<?php
                  if( have_rows('logos') ):
                      while( have_rows('logos') ) : the_row(); ?>
                         <div class="slide"><img src="<?php echo get_sub_field('logo'); ?>" alt="" /></div>
                <?php
                      endwhile;
                  endif;
                ?>
                <?php
                  if( have_rows('logos') ):
                      while( have_rows('logos') ) : the_row(); ?>
                         <div class="slide"><img src="<?php echo get_sub_field('logo'); ?>" alt="" /></div>
                <?php
                      endwhile;
                  endif;
                ?>
            </div>
        </div>
    </div>

	<div class="greyboxbanner" style="padding: 125px 0 130px;">
		<div class="container">
			<article style="margin-bottom: 0;" 
			    data-aos="fade-up"  
			    data-aos-duration="700" 
			    data-aos-anchor-placement="top-bottom"
			    data-aos-offset="100"
			    class="row headline first_home_section aos-animate right-container d-flex">
			    
			    <div class="col-md-9">
			        <p class="headline__pre"><?php echo get_field('preheadline'); ?></p>
			        <h2 class="ultraWeight headline__head"><?php echo get_field('headline'); ?></h2>
			        <?php echo get_field('content_headline'); ?>

			        <div class="pic blackbutton">
			            <a href="<?php echo site_url('/work/') ?>">
			                <div class="button"><span>WORK</span></div>
			            </a>
			        </div>

			        <div class="pic blackbutton">
			            <a href="<?php echo site_url('/service/') ?>">
			                <div class="button"><span>SERVICES</span></div>
			            </a>
			        </div>
			    </div>

			    <?php if (have_rows('right_images')){ ?>
				    <div class="col-md-3 images-column">
				        <?php if (have_rows('right_images')) : ?>
				            <?php while (have_rows('right_images')) : the_row(); ?>
				                <img src="<?php echo get_sub_field('right_image'); ?>" class="image-item">
				            <?php endwhile; ?>
				        <?php endif; ?>
				    </div>
			    <?php } ?>
			</article>
		</div>
	</div>
	<!--End Design 2023-->

	<section data-aos="fade-up"  
		data-aos-duration="700" 
		data-aos-offset = "100"
		data-aos-anchor-placement="top-bottom" class="create home__services aos-animate">
		<div data-aos="fade-up"  
		data-aos-duration="700" 
		data-aos-offset = "100"
		data-aos-anchor-placement="top-bottom" class=" container-fluid create__relatedWorks home__relatedWorks aos-animate">
		
			<div class="home-work-grid">
			<?php foreach($createWorks as $work) : ?>
			<?php	$post_id = $work['single_work']->ID;
			$link = get_the_permalink( $post_id);
			$thumbnail_id  = get_post_thumbnail_id($post_id);
			$thumbnail_alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
			$image = get_field('home_image', $post_id); 
			$cat = get_the_category( $post_id );
			$postCat = $cat[0];
			$desc = get_field('description', $post_id);

				?>
				<a href="<?php echo $link ?>" class="work-box-home">
					<article class="<?php echo $postCat->slug; ?>">
						<div class="hovereffect">
							<img  src="<?php echo $image ?>" alt="<?php echo $thumbnail_alt ?>" class="img-fluid" >
							<div class="overlay">
								<p class="work-cat"><?php echo $desc ?></p>
								<h2 class="work-title" ><?php echo $work['single_work']->post_title ?></h2>

							</div>
						</div>
					</article>
				</a><!-- work-box-home -->

			<?php endforeach; ?>
			</div>

			<div class="pic">
				<a href="<?php echo site_url('/work/') ?>">
			  		<div class="button"><span>MORE WORK</span></div>
				</a>
			</div>

		</div>

		<div class="bluedesign">
			<div class="container">
				<div class="headlinebluebanner">
					<div class="row">
						<div class="col-5">
							<h2>Create</h2>
							<div class="righttext">
								<p><?php echo $createExcept ?></p>
								<div class="pic">
										<a href="<?php echo get_the_permalink(6801); ?>">
									  		<div class="button"><span>OVERVIEW</span></div>
										</a>
								</div>
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

		<div class="container" style="display: none;">
			<div class="row create__inner home__services-inner">
				<div  class="create__headline col-lg-5">
					<div class="create__headline-svg home__services-svg">
						<div class="svg-service">
							<svg id="create" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 60 59.967">
								<defs>
								<linearGradient id="linear-gradient" x1="0.147" y1="0.854" x2="0.853" y2="0.148" gradientUnits="objectBoundingBox">
								<stop offset="0" stop-color="#ff9500"></stop>
								<stop offset="0.265" stop-color="#ff7100"></stop>
								<stop offset="0.852" stop-color="#ff1700"></stop>
								<stop offset="0.992" stop-color="red"></stop>
								</linearGradient>
								<linearGradient id="linear-gradient-2" x1="0.225" y1="0.771" x2="0.764" y2="0.228" xlink:href="#linear-gradient"></linearGradient>
								</defs>
								<g id="Group_410" data-name="Group 410" transform="translate(0 0)">
								<g id="Group_408" data-name="Group 408">
								<path id="Path_3124" data-name="Path 3124" class="cls-1" d="M29.949,59.905A29.611,29.611,0,0,1,.891,36.748c-.223-1-.334-1.781-.445-2.561a25.331,25.331,0,0,1-.223-2.895L0,29.956A32.835,32.835,0,0,1,3.229,16.485a2.827,2.827,0,0,1,1.559-1.447,2.52,2.52,0,0,1,2,.111,2.546,2.546,0,0,1,1,3.674,26.454,26.454,0,0,0-2.672,11.69,24.913,24.913,0,1,0,29.17-25.05,24.318,24.318,0,0,0-21.6,6.68,3.212,3.212,0,0,1-2.449.891,2.525,2.525,0,0,1-1.781-1.225A2.459,2.459,0,0,1,9.018,8.58,29.383,29.383,0,0,1,22.935.786,30.145,30.145,0,0,1,59.119,22.719a30.048,30.048,0,0,1-23.6,36.629A32.522,32.522,0,0,1,29.949,59.905ZM.891,29.956,1,31.181c.111.891.111,1.893.223,2.783.111.668.223,1.447.445,2.449A29.007,29.007,0,0,0,35.4,58.458a28.652,28.652,0,0,0,19.038-12.8,28.787,28.787,0,0,0,3.785-22.712A29.252,29.252,0,0,0,23.158,1.677,28.114,28.114,0,0,0,9.686,9.248a1.788,1.788,0,0,0-.445,2.115,1.341,1.341,0,0,0,1.113.779,1.749,1.749,0,0,0,1.67-.668,25.8,25.8,0,0,1,39.3,4.008A25.712,25.712,0,0,1,24.16,54.9,25.464,25.464,0,0,1,4.342,30.4,26.479,26.479,0,0,1,7.125,18.377a1.68,1.68,0,0,0-.668-2.449,1.862,1.862,0,0,0-1.336-.111,2.185,2.185,0,0,0-1,1A29.734,29.734,0,0,0,.891,29.956Z" transform="translate(0 0.062)"></path>
								</g>
								<g id="Group_409" data-name="Group 409" transform="translate(17.034 16.992)">
								<path id="Path_3125" data-name="Path 3125" class="cls-2" d="M28.326,41.252h0a2.547,2.547,0,0,1-2.561-2.672V30.787h-7.9a2.532,2.532,0,0,1-2.227-1.336,2.511,2.511,0,0,1,2.338-3.785h7.9V17.983A2.519,2.519,0,0,1,28.438,15.2h0a2.528,2.528,0,0,1,1.781.668A2.772,2.772,0,0,1,31,17.983v7.793h7.793a2.577,2.577,0,0,1,2.783,2.561,2.528,2.528,0,0,1-.668,1.781,2.772,2.772,0,0,1-2.115.779H31v7.793a3.2,3.2,0,0,1-.779,2A4.1,4.1,0,0,1,28.326,41.252ZM26.211,29.9h.445V38.58a1.669,1.669,0,0,0,1.67,1.781h0a1.291,1.291,0,0,0,1.113-.445A1.844,1.844,0,0,0,30,38.58V29.9H38.68a2.116,2.116,0,0,0,1.447-.557,1.429,1.429,0,0,0,.445-1.113,1.7,1.7,0,0,0-1.893-1.67H30V17.872a2.117,2.117,0,0,0-.557-1.447,1.429,1.429,0,0,0-1.113-.445h0a1.7,1.7,0,0,0-1.67,1.893v8.573h-8.8a1.685,1.685,0,0,0-1.559.779,1.517,1.517,0,0,0,0,1.67,1.777,1.777,0,0,0,1.559.891H23.65Z" transform="translate(-15.3 -15.2)"></path>
								</g>
								</g>
							</svg>
						</div>
					</div>
					<div class="create__title home__services-title">
						<p>DESIGN SOLUTIONS</p>
						<h3 class="ultraWeight" >Create</h3>
					</div>
					<div class="create__excerpt home__services-excerpt">
						<p><?php echo $createExcept ?></p>
						<a href="<?php echo site_url('/service/create/') ?>">OVERVIEW</a>
					</div>
				</div>
				<div class="create__relatedServices home__services-relatedServices col-lg-7">
					<div class="services__grid">
					<?php foreach($createServices as  $service) : ?>
							<div class="service__grid-single home__services-single">
								<a href="<?php echo $service['sub_service_link'] ?>">
									<div class="homeService__title">
										<h4><?php echo $service['sub_service_title'] ?></h4>
									</div>
									<div class="homeService__excerpt">
										<p><?php echo $service['sub_service_excerpt'] ?></p>
									</div>
									<p class="homeService__cta">
										<span>DISCOVER MORE</span> 
										<span><i class="fa fa-chevron-right"></i></span>
									</p>
								</a>
							</div>
					<?php endforeach; ?>
					
					</div>
					
				</div>
			</div>
		</div>

		

	</section>
	
	
	<section data-aos="fade-up"  
		data-aos-duration="700" 
		data-aos-offset = "100"
		data-aos-anchor-placement="top-bottom"  class="build home__services aos-animate">

		<div data-aos="fade-up"  
		data-aos-duration="700" 
		data-aos-offset = "100"
		data-aos-anchor-placement="top-bottom" class=" container-fluid build__relatedWorks home__relatedWorks aos-animate">
		
			<div class="home-work-grid">
			<?php foreach($buildWorks as $work) : ?>
			<?php	$post_id = $work['single_work']->ID;
			$link = get_the_permalink( $post_id);
			$thumbnail_id  = get_post_thumbnail_id($post_id);
			$thumbnail_alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
			$image = get_field('home_image', $post_id); 
			$cat = get_the_category( $post_id );
			$postCat = $cat[0];
			$desc = get_field('description', $post_id);

			// var_dump($work);
				?>
				<a href="<?php echo $link ?>" class="work-box-home">
					<article class="<?php echo $postCat->slug; ?>">
						<div class="hovereffect">
							<img  src="<?php echo $image ?>" alt="<?php echo $thumbnail_alt ?>" class="img-fluid" >
							<div class="overlay">
								<p class="work-cat"><?php echo $desc ?></p>
								<h2 class="work-title" ><?php echo $work['single_work']->post_title ?></h2>

							</div>
						</div>
					</article>
				</a><!-- work-box-home -->

			<?php endforeach; ?>
			</div>
			<div class="pic">
				<a href="<?php echo site_url('/work/') ?>">
			  		<div class="button"><span>MORE WORK</span></div>
				</a>
			</div>

		</div>

		<!--Black banner-->
		<div style="margin-bottom: 0;" class="bluedesign blackcolorbanner">
			<div class="container">
				<div class="headlinebluebanner">
					<div class="row">
						<div class="col-5">
							<h2>Grow</h2>
							<div class="righttext">
								<p><?php echo $promoteExcept ?></p>
								<div class="pic">
										<a href="<?php echo get_the_permalink(6821); ?>">
									  		<div class="button"><span>OVERVIEW</span></div>
										</a>
								</div>
							</div>
						</div>
						<div class="col-7">
							<div class="rowservice">

								<?php foreach($promoteServices as  $service) : ?>
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


		<!--End green banner-->

	</section>

	<section data-aos="fade-up"  
		data-aos-duration="700" 
		data-aos-offset = "100"
		data-aos-anchor-placement="top-bottom" class="create home__services aos-animate" id="lastworkpost">
		<div data-aos="fade-up"  
			data-aos-duration="700" 
			data-aos-offset = "100"
			data-aos-anchor-placement="top-bottom" class=" container-fluid create__relatedWorks home__relatedWorks aos-animate">
			
				<div class="home-work-grid">
					<?php
						if( have_rows('last_section_work_home') ):
						    while( have_rows('last_section_work_home') ) : the_row(); ?>

						    	<?php
						    		$post_id = get_sub_field('work_post');
									$link = get_the_permalink( $post_id);
									$thumbnail_id  = get_post_thumbnail_id($post_id);
									$thumbnail_alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
									$image = wp_get_attachment_url( get_post_thumbnail_id($post_id) );
									$cat = get_the_category( $post_id );
									$postCat = $cat[0];
									$desc = get_field('description', $post_id);
						    	?>

						    	<a href="<?php echo $link ?>" class="work-box-home">
									<article class="<?php echo $postCat->slug; ?>">
										<div class="hovereffect">
											<img  src="<?php echo $image ?>" alt="<?php echo $thumbnail_alt ?>" class="img-fluid" >
											<div class="overlay">
												<p class="work-cat"><?php echo $desc ?></p>
												<h2 class="work-title" ><?php echo get_the_title($post_id); ?></h2>

											</div>
										</div>
									</article>
								</a>

					<?php
						    endwhile;
						endif;
					?>

				</div>

				<div class="pic">
					<a href="<?php echo site_url('/work/') ?>">
				  		<div class="button"><span>MORE WORK</span></div>
					</a>
				</div>

		</div>
	</section>

	<div class="greyboxbanner secondgreybannerbox">
		<div class="container">
			<article   
			data-aos="fade-up"  
			data-aos-duration="700" 
			data-aos-anchor-placement="top-bottom"
			data-aos-offset = "100"
			class="row headline first_home_section aos-animate">
				<div class="col-md-8">
					<h1 class="headline__pre" ><?php echo get_field('preheadline_seconfgreybanner'); ?></h1>
					<h2 class="ultraWeight headline__head"><?php echo get_field('headline_secondgreybanner'); ?></h2>
					<?php echo get_field('content_secondgreybanner'); ?>

					<div class="pic blackbutton">
						<a href="<?php echo get_the_permalink(16); ?>">
					  		<div class="button"><span>ABOUT</span></div>
						</a>
					</div>

					<div class="pic blackbutton">
						<a href="<?php echo get_the_permalink(1355); ?>">
					  		<div class="button"><span>CONTACT</span></div>
						</a>
					</div>


				</div>
				<div class="col-md-4 col-sm-12 col-xs-12 iconfdrycol">
					<img src="https://www.fdry.com/wp-content/uploads/2023/08/foundry-digital-power-button.svg"/>
				</div>
			</article>
		</div>
	</div>

	<div class="blackagencybanner">
		<div class="container">

<img src="<?php echo get_field('growth_acceleration_image'); ?>" alt="Digital Marketing and Ecommerce Web Design Agency" title="Digital Marketing and Ecommerce Web Design Agency">

<img src="https://www.fdry.com/wp-content/uploads/2023/08/blank.png" alt="FDRY Foundry Digital" title="FDRY Foundry Digital" width="80" height="80">

			<?php echo get_field('growth_acceleration'); ?>
			<center><div class="linksbannergrey"><a href="<?php echo get_the_permalink(7020); ?>" >WooCommerce Agency</a>|<a href="<?php echo get_the_permalink(7042); ?>">Shopify Agency</a>|<a href="<?php echo get_the_permalink(7634); ?>">Social Media Marketing Agency</a>|<a href="<?php echo get_the_permalink(6929); ?>">Ecommerce SEO Agency</a></div></center>
		</div>
	</div>


</div>

