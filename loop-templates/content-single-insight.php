<?php
/**
 * Single post partial template.
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="header-blog">
	<div class="container">
			<a href="<?php echo get_the_permalink(14); ?>"><p class="labelblog">INSIGHTS</p></a>
			<?php the_title( '<h1>', '</h1>' ); ?>
			<div class="insight-hero container-fluid">
				<div class="social-share-icon">
					<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo the_permalink() ?>" class="option a1 color-facebook waves-effect waves-light"><i class="fa fa-facebook"></i></a>
					<a href="https://twitter.com/home?status=<?php echo the_permalink() ?>" class="option a2 color-twitter waves-effect waves-light"><i class="fa fa-twitter"></i></a>
					<a href="https://www.linkedin.com/shareArticle?mini=true&url=&title=&summary=&source=<?php the_permalink() ?>" class="option a3 color-google-plus waves-effect waves-light"><i class="fa fa-linkedin"></i></a>
				</div>
			</div>
	</div>
	
	<?php if ( get_field('hero_image') ) : ?>
        <img src="<?php the_field('hero_image'); ?>">
    <?php elseif ( has_post_thumbnail() ) : ?>
        <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>">
    <?php endif; ?>
</div>

<!--<section class="insight-hero container-fluid" id="insight-hero" style="padding: 0!important; background-image: url(<?php the_field('hero_image');?>">

	<header class="insight-title" 
  style="
        width: 100%;
        height: 100%;
        margin: auto;
        padding: 20px 14px;
        background-color: rgba(0,0,0,0.3);
        "
  >
		<div style="position: relative;top: 50%;-webkit-transform: translateY(-50%);-ms-transform: translateY(-50%);transform: translateY(-50%);">
			<div class="inner-category-hero"><h4>INSIGHTS</h4></div>
			<?php the_title( '<h1>', '</h1>' ); ?>
		</div>
	</header>
	<div class="social-share-icon">
		<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo the_permalink() ?>" class="option a1 color-facebook waves-effect waves-light"><i class="fa fa-facebook"></i></a>
		<a href="https://twitter.com/home?status=<?php echo the_permalink() ?>" class="option a2 color-twitter waves-effect waves-light"><i class="fa fa-twitter"></i></a>
		<a href="https://www.linkedin.com/shareArticle?mini=true&url=&title=&summary=&source=<?php the_permalink() ?>" class="option a3 color-google-plus waves-effect waves-light"><i class="fa fa-linkedin"></i></a>
		<a href="javascript:;" class="option a color-facebook waves-effect waves-light"><i class="fa fa-share-alt"></i></a>
	</div>
</section>--><!-- insight-hero -->

<main class="container insight-content">

	<article <?php post_class('col-lg-8 offset-lg-2'); ?> id="post-<?php the_ID(); ?>">

		<header class="insight-info">

			<div class="entry-meta">
			<?php $cat=get_the_category();?>
				<!-- <p class="date"><?php the_date('D, j F H:s') ?></p> -->
				<p class="category"><?php echo $cat[0]->cat_name; ?></p>

			</div><!-- .entry-meta -->

		</header><!-- .entry-header -->

		<div class="entry-content">

			<?php the_content(); ?>

		</div><!-- .entry-content -->


		<footer class="entry-footer">

			<a href="<?php echo site_url('insights') ?>" class="see-more go-left"><i class="fa fa-chevron-left left"></i>INSIGHTS</a>


		</footer><!-- .entry-footer -->

</article><!-- #post-## -->

</main><!-- .container .insight-content-->



<article class="container">

<a href="<?php echo site_url( '/work' ) ?>">
	<img src="https://www.fdry.com/wp-content/uploads/2023/10/Case-Studies_Image.png" alt="Our Work" class="d-none d-sm-none d-md-block">
	<img src="https://www.fdry.com/wp-content/uploads/2023/10/case-studies_banner_mobile.png" alt="Our Work" class="d-block d-sm-block d-md-none">
</a>

</article>