<?php
/**
 * Partial template for Work post.
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
$container   = get_theme_mod( 'understrap_container_type' );
?>
<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">
	<main <?php post_class(); ?> id="work-main post-<?php the_ID(); ?>">

		<header class="entry-header">

			<?php the_title( '<h1 class="parent-title">', '</h1>' ); ?>



			<div class="work-single-intro">

				<?php the_content(); ?>

			</div><!-- .work-single-intro -->
		</header><!-- .entry-header -->

		<section class="brand">
			<div class="row no-gutters">
				<div class="col-md-6">
					<div class="box-gray h-100">
						<div class="brand-content">
							<?php the_field('the_brand');?>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="box-black h-100">
						<div class="svg-brand">
							<?php $svgImg = get_field('brand_logo'); ?>
							<img src="<?php echo $svgImg ?>" alt="brand-logo" class="logo-svg" <?php if( get_field('size_brand_logo') == 'Small' ){ ?>style="max-width: 50%!important;"<?php } ?> />
						</div>
					</div>
				</div>
				<div class="col-md-8 offset-md-2">
					<article class="approach">
						<?php the_field('approach') ?>
					</article>
				</div>				
			</div><!-- row no-gutters -->
		</section><!-- .brand -->
	</main><!-- main#work-main -->
</div><!-- Container end -->

	<?php if ( get_field('video_carousel') ) { ?>
		<section class="fullvideo">
			<video autoplay="true" muted="true" loop="true" style="max-width: 100%; width: 100%;">
				<source src="<?php echo get_field('video_carousel'); ?>" type="video/webm">
				<source src="<?php echo get_field('video_carousel'); ?>" type="video/mp4">
			</video>
		</section><!-- full video -->
	<?php } ?>

	<?php if ( get_field('carousel') ) { ?>
    <section class="carousell box-gray">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 p-0">
                    <?php 
                    $media_items = get_field('carousel'); 
                    if( $media_items ){
                    ?>
                    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel" data-interval="4000">
                        <div class="carousel-inner" role="listbox">
                            <?php foreach ($media_items as $key => $item) { 
                                // 1. Determinamos si es el activo
                                $active_class = ($key === 0) ? 'active' : '';
                                
                                // 2. Obtenemos el tipo de archivo (video/mp4, image/jpeg, etc.)
                                $mime_type = $item['mime_type'];
                            ?>
                            
                            <div class="carousel-item <?php echo $active_class; ?>">
                                
                                <?php 
                                // 3. VERIFICACIÓN: ¿Es un video?
                                if ( strpos($mime_type, 'video') !== false ) { ?>
                                    
                                    <video width="100%" autoplay muted loop playsinline style="width: 100%; display: block;">
                                        <source src="<?php echo $item['url']; ?>" type="<?php echo $mime_type; ?>">
                                    </video>

                                <?php } else { ?>
                                    
                                    <img class="d-block img-fluid" src="<?php echo $item['sizes']['2048x2048']; ?>" alt="<?php echo $item['alt']; ?>">
                                    
                                <?php } ?>

                            </div>

                            <?php } // Fin foreach ?>
                        </div>

                        <?php if (count($media_items) > 1) { ?>
                            <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        <?php } ?>
                    </div>
                    <?php } ?>  
                </div>
            </div>
        </div></section><?php } ?>

	<?php if ( get_field('video') ) { ?>
		<section class="fullvideo">
			<video autoplay="true" muted="true" loop="true" style="max-width: 100%; width: 100%;">
				<source src="<?php echo get_field('video'); ?>" type="video/webm">
				<source src="<?php echo get_field('video'); ?>" type="video/mp4">
			</video>
		</section><!-- full video -->
	<?php } ?>
	

	<section class="products">
		<div class="container">
			<div class="row design">
				<div class="col-md-8 offset-md-2">
					<?php the_field('design') ?>
				</div>
			</div><!-- row.design -->
		</div><!-- container -->
<?php 	
		$fullImage = get_field('full_image');
        if($fullImage){
            if( is_preview() ){ ?>
                <div id="full-image-container"></div>
                <script>
                    document.addEventListener('DOMContentLoaded', function(){
                        var img = document.createElement('img');
                        img.src = '<?php echo esc_url($fullImage); ?>';
                        img.className = 'full-width-img';
                        document.getElementById('full-image-container').appendChild(img);
                    });
                </script>
            <?php } else { ?>
                <img class="full-width-img" src="<?php echo esc_url($fullImage); ?>">
            <?php }
        }

	if(!get_field('do_you_want_a_video')){

		$images = get_field('mobile_images');
		$size = 'full'; // (thumbnail, medium, large, full or custom size)
		$i=1;
		if ($images) {
	?>
		<div class="container-fluid">
			<div class="row mobile">
	<?php
			foreach ($images as $image) {
	?>
				<div class="col-md-6 p-0 <?php echo "test mobile-background-".$i;?>">
					<?php echo wp_get_attachment_image( $image['ID'], $size ); ?>
				</div>
	<?php	
				$i++;
			}
	?>
			</div><!-- row.mobile -->
		</div><!-- container-fluid -->
	<?php
		}

	}else{
		$video_image = get_field('image_and_video');
		$selector = $video_image['imageorvideo'];
		$mobileImage = $video_image['mobile_image'];
		$mobileVideoleft = $video_image['video'];
		$mobileVideo = $video_image['mobile_video'];

		if($video_image):	
	?>
		<div class="container-fluid">
			<div class="row mobile 123">
			
			<?php if( $selector == 1 ){ ?> <!--Video--->
				<div class="col-md-12 p-0 test2 mobile-background-2">
					<video autoplay="true" muted="true" loop="true" style="max-width: 100%">
						<source src="<?php echo $mobileVideoleft['url'] ?>" type="video/webm">
						<source src="<?php echo $mobileVideoleft['url'] ?>" type="video/mp4">
					</video>
				</div>
			<?php }else{ ?> <!--Imagen-->
				<div class="col-md-6 p-0 test2 mobile-background-1">
					<img src="<?php echo $mobileImage['url'] ?>" alt="<?php echo $mobileImage['alt'] ?>">
				</div>
				<div class="col-md-6 p-0 test2 mobile-background-2">
					<video autoplay="true" muted="true" loop="true" style="max-width: 100%">
						<source src="<?php echo $mobileVideo['url'] ?>" type="video/webm">
						<source src="<?php echo $mobileVideo['url'] ?>" type="video/mp4">	
					</video>
				</div>
			<?php } ?>
			
			<!--<div class="col-md-6 p-0 test2 mobile-background-2">
				<video autoplay="true" muted="true" loop="true" style="max-width: 100%">
					<source src="<?php echo $mobileVideo['url'] ?>" type="video/webm">
					<source src="<?php echo $mobileVideo['url'] ?>" type="video/mp4">	
				</video>
			</div>-->
			
			</div>
		</div>
		<?php endif; ?>
	<?php } ?>
		
	</section><!-- products -->

	<?php if ( get_field('col_image_left') && get_field('col_image_right') ) { ?>
		<section class="twocolsimage"><!-- Two cols image -->
			<div class="container-fluid">
				<div class="row mobile 123">
					<div class="col-md-6 p-0 test2 mobile-background-1">
						<img src="<?php echo get_field('col_image_left'); ?>" alt="Left Image">
					</div>
					<div class="col-md-6 p-0 test2 mobile-background-1">
						<img src="<?php echo get_field('col_image_right'); ?>" alt="Right Image">
					</div>
				</div>
			</div>
		</section><!-- Two cols image -->
	<?php } ?>

	<div class="container final-box-work">
		<div class="row">
			<div class="col-md-4 result">
				<div class="inner-result">
					<?php the_field('result') ?>
				</div>
			</div>
			<div class="col-md-8 quote">
				<div class="inner-quote">
					<p><?php the_field('quote') ?></p>
				</div>
			</div>
		</div>
		<!--<div class="grey-line"></div>
			<a href="<?php echo site_url('work') ?>" class="see-more go-left"><i class="fa fa-chevron-left left"></i>VIEW MORE PROJECTS</a>-->
	</div>
		





	

	



