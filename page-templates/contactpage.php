<?php
/**
 * Template Name: Page contAct page
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

<section class="contactpage">

	<div class="darkbluebanner headercontact">
		<div class="container">
			<h1><?php echo get_the_title(); ?></h1>
        <?php echo get_the_content(); ?>
		</div>
	</div>

	<div class="darkbluebanner locationsection">
		<div class="container">
			<h2>CONTACT DETAILS</h2>
			<?php 
				if( have_rows('telephone') ):
				    while( have_rows('telephone') ) : the_row(); 
				    	$num = get_sub_field('phone_number');
				    endwhile;
				endif;
			?>
			<h3>Telephone: <a class="link" target="_blank" href="tel:<?php echo $num; ?>">+44 (0)20 8123 4669</a></h3>
			<h3>Email: <a class="link mail" target="_blank" href="mailto:<?php echo get_field('email'); ?>">studio@fdry.com</a></h3>

			<div class="locationmap">
				<div class="row">
					<div class="col-6">
						<h4>London, Fulham</h4>
						<p>88 Peterborough Road, London, SW6 3HH London</p>
						<div class="pic"><a target="_blank" href="<?php echo get_field('address_1'); ?>"><div class="button"><span>VIEW MAP</span></div>  </a></div>
					</div>
					<div class="col-6">
						<h4>London, Chelsea / Victoria</h4>
						<p>123 Buckingham Palace Road, London, SW1W 9SH</p>
						<div class="pic"><a target="_blank" href="<?php echo get_field('address_2'); ?>"><div class="button"><span>VIEW MAP</span></div>  </a></div>
					</div>
				</div>
			</div>
		</div>
	</div>


</section>

<?php get_footer(); ?>