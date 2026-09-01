<?php
/**
 * Partial template for work page
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<?php 
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$works = new WP_Query( array ('posts_per_page' => 30, 'paged' => $paged, 'post_type' => 'works_post'));
?>



<section <?php post_class('container-fluid'); ?> id="post-<?php the_ID(); ?>">


	<div class="work-grid">
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
				<a href="<?php echo get_permalink(); ?>" class="ajax-call  <?php foreach($postCat as $name){ echo $name.' '; }   ?>"><article class="work-box " <?php echo $postCat->slug; ?>  >

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
	<nav class="paginationworks">
	    <ul>
	        <li><?php previous_posts_link( '&laquo; PREV', $works->max_num_pages) ?></li> 
	        <li><?php next_posts_link( 'NEXT &raquo;', $works->max_num_pages) ?></li>
	    </ul>
	</nav>

</section><!-- section -->



