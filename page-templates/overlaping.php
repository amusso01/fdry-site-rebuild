<?php
/**
 * Template Name: Overlapping
 * Template Post Type: page
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();
?>

<section class="overlapingsection">
    <div class="twocolumns desktopcolumns">
        <?php 
        if (have_rows('service_article', 7634)):
            while (have_rows('service_article', 7634)) : the_row();
                $ind = get_row_index();
                $is_even = ($ind % 2) == 0;
                ?>
                <div class="row reveal" style="background-color: <?php echo get_sub_field('background_color'); ?>;">
                    <div class="col-6 <?php echo $is_even ? '' : 'order-last'; ?>">
                        <div class="divimage">
                            <img src="<?php echo get_sub_field('side_image'); ?>" class="reveal-content">
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="undercell reveal-content">
                            <div class="table-box">
                                <div class="table-cell">
                                    <h2><?php echo get_sub_field('title'); ?></h2>
                                    <?php echo get_sub_field('paragraph'); ?>
                                    <div class="pic">
                                        <a href="<?php echo get_the_permalink(1355); ?>">
                                            <div class="button"><span>LET’S TALK</span></div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            endwhile;
        endif;
        ?>
    </div>
</section>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        function revealElements() {
            $('.row').each(function () {
                let windowHeight = $(window).height();
                let elementTop = $(this).offset().top;
                let elementBottom = elementTop + $(this).outerHeight();
                let scrollTop = $(window).scrollTop();
                let scrollBottom = scrollTop + windowHeight;
                
                // Agregar la clase si el elemento está visible
                if (elementTop < scrollBottom - 100 && elementBottom > scrollTop + 100) {
                    $(this).addClass('active');
                } else {
                    $(this).removeClass('active'); // Remover la clase cuando no esté en pantalla
                }
            });
        }

        $(window).on("scroll", function () {
            revealElements();
        });

        revealElements(); // Ejecutar al inicio por si hay elementos visibles
    });
</script>

<style type="text/css">
html { scroll-behavior: smooth; }

.overlapingsection .twocolumns { position: relative; overflow: hidden; padding: 50px 0 0; }

.overlapingsection .twocolumns .row {
    position: relative;
    margin-top: -100px;
    padding: 50px;
    background: white;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
    transition: opacity 1s ease-out, transform 1s ease-out;
    opacity: 0;
    transform: translateY(50px);
}

.overlapingsection .twocolumns .row.active {
    opacity: 1;
    transform: translateY(0);
}

.overlapingsection .reveal-content {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 1s ease-out 0.5s, transform 1s ease-out 0.5s;
}

.overlapingsection .row.active .reveal-content {
    opacity: 1;
    transform: translateY(0);
}

.overlapingsection .divimage img { max-width: 100%; height: auto; }

.overlapingsection .twocolumns .row:last-child{margin-bottom: 0!important;}

@media (max-width: 900px) {
    .overlapingsection .twocolumns.desktopcolumns{display: block!important;}
    .overlapingsection .twocolumns .row { opacity: 1 !important; }
    .overlapingsection .twocolumns .row { padding: 50px 5%; }
    .overlapingsection .undercell .table-cell { padding: 5% 0%; height: auto; }
}
</style>

<?php get_footer(); ?>
