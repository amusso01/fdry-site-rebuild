<?php
/**
 * Partial template for content in About page
 *
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<section class="about">
	<div class="bluebannernumbers">
		<aside class="info-box-about">
			<div class="container" id="counter">
				<div class="grid-about">
					<div class="grid-item-about">
						<div class="inner-box">
							<p class="number"><span class="counter-value" data-count="650">0</span>+</p>
							<p class="info-inner">Projects designed, built and promoted</p>
						</div>
					</div>
					<div class="grid-item-about twochilds">
						<div class="inner-box">
							<p class="counter-value" data-count="14">0</p>
							<p class="info-inner">Years delivering creative technology solutions</p>
						</div>
					</div>
					<div class="grid-item-about">
						<div class="inner-box">
							<p class="counter-value" data-count="12">0</p>
							<p class="info-inner">Hardworking individuals pushing Foundry to its highest potential</p>
						</div>
					</div>
				</div> <!-- grid-about -->
			</div> <!-- container -->
		</aside><!-- info-box-about -->
	</div>

	

	<?php

	get_template_part( 'loop-templates/gallery', 'threecols' ); 

	?>

	<div class="approachdiv">
		<div class="container">
			<div class="row">
				<div class="col-4 fadeAll">
					<div class="descriptionskill">
						<h3>Our Approach</h3>
						<p>Each project we undertake is backed by years of experience and technical skill and we love overcoming creative challenges and technical obstacles. You can trust that you have a dedicated team in FDRY.</p>
					</div>
				</div>
				<div class="col-8">
					
						<?php if( have_rows('skills_list') ): ?>
							<?php while( have_rows('skills_list') ): the_row(); 

							// vars
							$skill = get_sub_field('skill');
							$description = get_sub_field('skill_description');

							?>
							<div class="boxskill fadeAll">
								<div class="row">
									<div class="col-1">
										<h3>0<?php echo get_row_index(); ?></h3>
									</div>
									<div class="col-10">
										<div class="approdiv">
											<h2><?php echo $skill ?></h2>
											<p><?php echo $description ?></p>
										</div>
									</div>
								</div>
							</div>

							<?php endwhile; ?>
						<?php endif; ?>

					
				</div>
			</div>
		</div>
	</div>

	<div class="culturebox">
		<div class="container">
			<h2>Our Culture</h2>
			<h3>We don’t just get on board with a project, we get invested in achieving our clients’ goals as if they were our own.</h3>

			<div class="row">
				<div class="col-6">
					<img src="https://www.fdry.com/wp-content/uploads/2023/04/9C2F3FD6-1361-4261-8852-F98E916CDA2A.webp">
				</div>
				<div class="col-6">
					<p>When you work with FDRY you don&rsquo;t just get access to digital experts, you get access to our people and our values as a collaborative, supportive team of talented individuals who have been working together since 2012. We don&rsquo;t just get on board with a project, we get invested in achieving our clients&rsquo; goals as if they were our own.</p>
					<p>We know who we are and we know what we&rsquo;re good at, that&rsquo;s why we don&rsquo;t pretend to be digital giants. We&rsquo;re your local agency, dedicated to bringing your project to life.</p>
					<p>Fancy a cup of coffee or a quick bite to eat? We want to chat with you about your business so we can understand what it is you&rsquo;re looking to achieve. Once we&rsquo;ve nailed this, we pass on the particulars to our team of experts who make the magic happen, keeping you in the loop all the way.</p>
					<p>Our extensive experience in building interactive digital products has taught us that each project is different to the next, but whatever the unique requirements we tackle the challenge using our three-staged approach: Design, Develop, Grow.</p>
				</div>
			</div>
		</div>
	</div>

	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
	<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>				
	<div class="theteam">
		<div class="container">
			<h2>MEET THE TEAM</h2>
			<p class="description">We’re a creative and production agency so as a team we’re anything but plain. Our designers radiate creativity, and our developers eat, sleep and breathe code. Not forgetting our excellent project managers ready to take on any project, and our digital marketing wizards, the latest addition of whom is a pure genius.</p>
		</div>

		

		<div class="slider">
		  <?php

			// check if the repeater field has rows of data
			if( have_rows('the_team') ):

				// loop through the rows of data
				while ( have_rows('the_team') ) : the_row();
?>
				<div class="slick-slideshow__slide">
				    <img class="img-fluid lozad"  data-src="<?php the_sub_field('picture') ?>" alt="Team member picture">
				    <p class="name"><?php the_sub_field('name_and_surname')?></p>
						<p class="role"><?php the_sub_field('member_role') ?></p>
				</div>
				

<?php			endwhile;

			else :

				// no rows found

			endif;

			?>
		</div>


		<script src="<?php echo get_template_directory_uri(); ?>/mainjs/about.js"></script>

	</div>

	<div class="agencyblue">
		<div class="container">
			<div class="row">
				<div class="col-6">
					<img class="img-fluid" src="<?php echo get_stylesheet_directory_uri() ?>/img/images/fdry-about-life.png" alt="Our Team">
					<h2>Agency life</h2>
					<p>Alongside working on exciting projects we<br>make sure we let off some steam.</p>
					<div class="pic"><a href="<?php echo site_url( '/agency-life/' ) ?>"><div class="button"><span>LEARN MORE</span></div></a></div>
				</div>
				<div class="col-6">
					<img class="img-fluid" src="<?php echo get_stylesheet_directory_uri() ?>/img/images/960x750.jpeg" alt="Our Team">
					<h2>Work with us</h2>
					<p>If you are interested in a full time position, internship or work experience please get in touch via <a href="mailto:studio@foundrydigital.co.uk">email</a> or visit our <a href="<?php echo site_url( '/careers/' ) ?>">career page.</a></p>
					<div class="pic"><a href="<?php echo site_url( '/careers/' ) ?>"><div class="button"><span>JOBS</span></div></a></div>
				</div>
			</div>
		</div>
	</div>

</section><!-- .about -->


