<?php
/**
 * Partial template for content in About page
 *
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$args = array(

	'post_type' => 'job',
	'posts_per_page' => -1,
);

$the_query = new WP_Query( $args );




?>

<section class="about">
	<article class="approach-about container">
		<div class="row">
			<div class="col-md-6 offset-md-3 text-center">
				<h3>OPEN ROLES</h3>
				<p>Look out for our open roles! Don’t see a role for you? We’re always on the lookout for top talent in the design & development industry, if you’d like to join our team send us your CV on <a href="mailto:studio@fdry.com">studio@fdry.com</a> <br><br>FDRY is a socially responsible company, committed to team diversity. <br>We refuse any form of discrimination.</p>
			</div>
			<div class="col-lg-10 offset-lg-1">

			<?php if ( $the_query->have_posts() ) : ?>

				<ul class="skills-list">

				<?php while( $the_query->have_posts() ) : $the_query->the_post();

					$job_type = get_field('job_type' , $post->ID);
					$job_specific = get_field('job_specific' , $post->ID);

				?>

					<li class="hidden-animate list-unstyled" >
						<a href="<?php echo get_the_permalink() ?>" style="display:flex; flex-direction:row; justify-content:space-between; align-items:center;">
							<span ><?php echo get_the_title(); ?></span> 
							<span style="font-weight: normal;"><?php echo $job_type ?> - <?php echo $job_specific; ?></span>
							<span style="font-weight:bold;" >VIEW POSITION</span>
						</a>
					</li>

				<?php endwhile; ?>

				</ul>

			<?php endif; 
			// Reset Post Data
			wp_reset_postdata();
			?>		
			</div>
		</div>
	</article><!-- approach -->

	<div class="container mb-3">
		<div class="row">
			<div class="col-md-4">
				<img src="<?php echo get_template_directory_uri() ?>/img/images/office.jpeg" alt="Our Office">
			</div>
			<div class="col-md-4">
				<img src="<?php echo get_template_directory_uri() ?>/img/images/building.jpeg" alt="Our Building">
			</div>
			<div class="col-md-4">
				<img src="<?php echo get_template_directory_uri() ?>/img/images/poole.jpeg" alt="Our Office">
			</div>
		</div>
	</div>

	<article class="approach-career approach-about container">
		<div class="row">
			<div class="col-md-6 offset-md-3 text-center">
				<h3>WHAT’S IN IT FOR YOU?</h3>
				<p>Get access to perks and rewards for commitment and efforts.</a></p>
			</div>
		</div>
	</article> 




	<article class="container">
		<div class="row">
			<div class="col-md-8 offset-md-2">
				<div class="career-grid">
					<div class="career-box">
						<h2>Benefits tailored to you</h2>
						<ul>
							<li>Quaterly performance bonus</li>
							<li>Pension contributions</li>
																						<li>Incentives to accelerate your skills</li>
							<li>Individual learning plan</li>
																						<li>Flexible working</li>
						</ul>
					</div>
					<div class="career-box">
						<h2>Play at work</h2>
						<ul>
							<li>Pool and ping pong tables</li>
							<li>Free beers and refreshments after 3.00pm</li>
							<li>More than free coffee... barista on site</li>
																						<li>Wellness rooms</li>
							<li>Dog friendly</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</article>




</section><!-- .about -->


