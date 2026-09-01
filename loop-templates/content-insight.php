<?php
/**
 * Partial template for post/insight page
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php 
	$insight = new WP_Query(array('post_type'=>'post', 'paged' => get_query_var( 'paged' ), 'post_status'=>'publish', 'posts_per_page'=>-8)); ?>

<section <?php post_class('container'); ?> id="post-<?php the_ID(); ?>">
	<div class="row grid-insight">

		<?php
		if ($insight-> have_posts() ) {
			while ($insight-> have_posts() ) {
				$insight->the_post(); 

				$cat = get_the_category(); // array of object of WP_Term
				$postCat = $cat[0]; // object WP_Term for the current post

				
				
					$thumbnail_id  = get_post_thumbnail_id($insight->ID);
					$thumbnail_alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
					$image = wp_get_attachment_image_src( $thumbnail_id,'full' ); 	
				
		?>
		
			<article class="col-md-6 insight-grid">
				<a href="<?php echo get_permalink() ?>">

				<?php 
					if($image){
						?>
						<img src="<?php echo get_template_directory_uri()?>/img/Spinner.gif" data-src="<?php echo $image[0]; ?>" alt="<?php echo $thumbnail_alt ?>"  class="img-fluid lozad" />
						<noscript><img src="<?php echo $image[0]; ?>"  class="img-fluid lozad" /></noscript>
						<?php	
					}else{
					?>
						<video autoplay loop width="650">
							<source src="<?php echo $video; ?>">
							Sorry, your browser doesn't support embedded videos.
						</video>
<?php
					}
				
				?>


				</a><!-- article.insight-box -->
				<div class="insight-info">
					<p class="insight-cat"><?php echo $postCat->name; ?></p>
					<!-- <p class="insight-date"><?php echo get_the_date('d F,Y') ?></p> -->
					<a class="see-more see-more__insight" href="<?php echo get_permalink() ?>">READ MORE <i class="fa fa-chevron-right"></i></a>
				</div>
				<div class="grey-line"></div>
							<div class="insight-title">
				<a href="<?php echo get_permalink() ?>">
					<?php the_title( '<h4  style="color: #1a1c22;">', '</h4>' ); 
					?></a><?php
					the_field('description_insight')?>
				</div>
			</article><!-- insight-grid -->

		<?php }
		
			// Restore original Post Data
		wp_reset_postdata();
		
		}	
		?>	
</div><!-- row grid-insight-->

<div class="grey-line"></div>

<footer class="row">
	<div class="col-12 insight-pagination">
		<?php echo bootstrap_pagination($insight); ?>
	</div>
</footer>

</section><!-- section -->
