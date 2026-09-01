<?php /* Template Name: Page event */ ?>
<?php get_header(); ?>

<div class="eventpage">
	<?php $url = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()), 'full' ); ?>
	<img src="<?php echo $url ?>" />

	<div class="bluemessage">
		<div class="container">
			<h1><?php echo get_the_title(); ?></h1>
		</div>
	</div>
	<div class="whitemessage">
		<div class="container">
			<?php echo get_the_content(); ?>
		</div>
	</div>
</div>



<?php get_footer(); ?>