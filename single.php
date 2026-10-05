<?php

/**
 * The template for displaying all single posts.
 *
 * @package understrap
 */

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

get_header('new');

?>

<div class="wrapper" id="single-wrapper">


	<?php while (have_posts()) : the_post(); ?>


		<?php if (get_post_type() == 'works_post') {

			get_template_part('loop-templates/content', 'single-work');

		?>
			<div class="backwork">
				<center>
					<div class="pic blackbutton">
						<a href="<?php echo esc_url(fdry_template_page_url('template-work.php', '/works/')); ?>">
							<div class="button"><span>BACK TO WORK</span></div>
						</a>
					</div>
				</center>
			</div>
		<?php

		} elseif (get_post_type() == 'post') {

			// Rollback: loop-templates/content-single-insight.php
			get_template_part('components/single/insight');
		}
		?>


	<?php endwhile; // end of the loop. 
	?>


</div><!-- Wrapper end -->

<?php get_footer(); ?>