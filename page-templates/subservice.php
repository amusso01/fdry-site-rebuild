<?php
/**
 * Template Name: Page sub service (Sub service)
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

<div id="breadcrumb" class="darkbluebanner">
    <div class="container">
        <div class="breadcrumb">
            <?php if ( wp_get_post_parent_id(get_the_ID()) == 6801 ) {
                $titleservice = "Design";
            }elseif( wp_get_post_parent_id(get_the_ID()) == 6815 ){
                $titleservice = "Develop";
            }else{
                $titleservice = "Grow";
            } ?>
            <a href="<?php echo home_url(); ?>">Home</a>
            <span>/</span>
            <a href="<?php echo get_the_permalink(10); ?>">Services</a>
            <!--<span>/</span>
            <a href="<?php //echo get_the_permalink( wp_get_post_parent_id(get_the_ID()) ); ?>"><?php //echo $titleservice; ?></a>-->
        </div>
        <h1><?php echo get_the_title(); ?></h1>
        <?php echo get_the_content(); ?>
    </div>
</div>

<?php $feat_image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) ); ?>
<?php if ( $feat_image || get_field('gif_image') ) { ?>
    <div class="featuredimage">
        <div class="container">
            <?php if ( get_field('gif_image') ) { ?>
                <img src="<?php echo get_field('gif_image'); ?>">
            <?php }else{ ?>
                <img src="<?php echo $feat_image; ?>">
            <?php } ?>
            
        </div>
    </div>
<?php } ?>

<?php if ( have_rows('featured_words') ) { ?>
    <div class="wordslider">
        <div class="slide-track">

            <?php
                if( have_rows('featured_words') ):
                    while( have_rows('featured_words') ) : the_row(); ?>
                        <div class="slide">
                            <p><?php echo get_sub_field('word'); ?></p>
                        </div>
                        <div class="slide dot">
                            <p>•</p>
                        </div>
            <?php
                    endwhile;
                endif;
            ?>
        </div>
    </div>
<?php } ?>

<?php if( get_the_ID() == 7634 ){ ?> 
    <?php get_template_part( 'loop-templates/content', 'media' );  ?>
<?php }else{ ?>
    <div class="twocolumns desktopcolumns">
        <?php 
            if( have_rows('service_article') ):
                while( have_rows('service_article') ) : the_row();

                    $ind = get_row_index();
                    if (($ind % 2) == 0) {
                        //par
                        ?>
                            <div class="row">
                                <div class="col-6">
                                     <div class="divimage">
                                        <img src="<?php echo get_sub_field('side_image'); ?>">
                                    </div>
                                </div>
                                <div class="col-6">

                                    <div class="undercell">
                                        <div class="table-box">
                                            <div class="table-cell">
                                                <h2><?php echo get_sub_field('title'); ?></h2>
                                                <?php echo get_sub_field('paragraph'); ?>
                                                <div class="pic">   <a href="<?php echo get_the_permalink(1355); ?>"><div class="button"><span>LET’S TALK</span></div>    </a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        <?php
                    } else {
                        //impar
                        ?>
                            <div class="row">
                                <div class="col-6">
                                    <div class="undercell">
                                        <div class="table-box">
                                            <div class="table-cell">
                                                <h2><?php echo get_sub_field('title'); ?></h2>
                                                <?php echo get_sub_field('paragraph'); ?>
                                                <div class="pic">   <a href="<?php echo get_the_permalink(1355); ?>"><div class="button"><span>LET’S TALK</span></div>    </a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="divimage">
                                        <img src="<?php echo get_sub_field('side_image'); ?>">
                                    </div>
                                </div>
                            </div>
                        <?php
                    }
                    
                endwhile;
            endif;
        ?>
    </div>

    <div class="twocolumns mobilecolumns">
        <?php 
            if( have_rows('service_article') ):
                while( have_rows('service_article') ) : the_row(); ?>

            <div class="row">
                                <div class="col-6">
                                    <div class="undercell">
                                        <div class="table-box">
                                            <div class="table-cell">
                                                <h2><?php echo get_sub_field('title'); ?></h2>
                                                <?php echo get_sub_field('paragraph'); ?>
                                                <div class="pic">   <a href="<?php echo get_the_permalink(1355); ?>"><div class="button"><span>LET’S TALK</span></div>    </a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="divimage">
                                        <img src="<?php echo get_sub_field('side_image'); ?>">
                                    </div>
                                </div>
            </div>       
        <?php
                endwhile;
            endif;
        ?>
    </div>
<?php } ?>

<?php if ( get_field('last_description') ) { ?>
    <div class="lastsectionservice">
        <div class="container">
            <?php echo get_field('last_description'); ?>
        </div>
    </div>
<?php } ?>



<?php get_footer(); ?>