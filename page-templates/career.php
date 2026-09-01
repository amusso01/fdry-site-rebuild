<?php
/**
 * Template Name: Career page
 * 
 * Template Post Type: page
 *
 * This template show career page
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
	</div>
</div>

<div class="mainimageteam">
	<div class="container">
		<img src="<?php echo get_field('main_image'); ?>">
	</div>
</div>

<!--Black banner-->
		<div class="bluedesign blackcolorbanner careerbanner">
			<div class="container">
				<div class="headlinebluebanner">
					<div class="row">
						<div class="col-6">
							<h2>Open Roles</h2>
							<div class="righttext">
								<p>We’re always on the lookout for top talent in the design & development industry, if you’d like to join our team send us your CV on <a class="link mail" href="mailto:studio@fdry.com">studio@fdry.com</a></p>
							</div>
						</div>
						<div class="col-6">
							<div class="rowservice">

								<?php 
									if( have_rows('roles') ):
									    while( have_rows('roles') ) : the_row(); ?>
									    	<div class="divarrow">
												<div class="pic">
													<a href="<?php echo get_the_permalink(get_sub_field('url')); ?>">
														<div class="button">
															<div class="row">
																<div class="col-1">
																	<div class="svgimage"><svg xmlns="http://www.w3.org/2000/svg" width="49" height="50" viewBox="0 0 49 50">
																		  <g id="Group_290" data-name="Group 290" transform="translate(-170.5 -1115.5)">
																		    <line id="Line_90" data-name="Line 90" y2="48" transform="translate(195.5 1116.5)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2"/>
																		    <line id="Line_91" data-name="Line 91" x2="47" transform="translate(171.5 1140.5)" fill="none" stroke="#fff" stroke-linecap="round" stroke-width="2"/>
																		  </g>
																		</svg>
																	</div>
																</div>
																<div class="col-8">
																	<div class="divtext">
																		<h3><?php echo get_sub_field('title'); ?></h3>
																		<?php echo get_sub_field('description'); ?>
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

							<p class="lastp">FDRY is a socially responsible company, committed to team diversity.<br> We refuse any form of discrimination.</p>
						</div>
					</div>
				</div>

				


			</div>
		</div>

		<!--End black banner-->

		<?php get_template_part( 'loop-templates/gallery', 'threecols' ); ?>

		<div class="benefits">
			<div class="container">
				<div class="headerbenefits">
					<?php echo get_field('benefitbanner'); ?>
				</div>
			</div>

			<div class="allbenefitsbanner">
				<div class="container">
					<div class="row">
						<div class="col-5">
							<div class="leftcolumnc">
								<?php echo get_field('benefits'); ?>
							</div>
						</div>
						<div class="col-1">
							<div class="vl"></div>
						</div>
						<div class="col-5">
							<div class="rightcolumnc">
								<?php echo get_field('play_at_work'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>



<?php get_footer(); ?>