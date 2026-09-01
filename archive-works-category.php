<?php get_header(); ?>

<div class="greybanner">
    <div class="container">
        <h1>WORK</h1>
        <div class="bigfont"><!-- wp:heading -->
            <?php echo get_post_field('post_content', 50); ?>
        </div>

        
        <div class="container work-navigation">
                <div class="row">

                    <nav class="col-md-12">
                        <ul id="category-menu" class="fadeInUp">
                            <li class="cat-item">
                                <a href="<?php echo get_the_permalink(50); ?>" class="<?php echo (is_category() && get_query_var('cat') == 50) ? 'active' : ''; ?>">Featured</a>
                            </li>
                            <li class="cat-item">
                                <a href="/work/category/design/" class="<?php echo (is_category('design')) ? 'active' : ''; ?>">Brand &amp; Design</a>
                            </li>
                            <li class="cat-item">
                                <a href="/work/category/website/" class="<?php echo (is_category('website')) ? 'active' : ''; ?>">Web Development</a>
                            </li>
                            <li class="cat-item">
                                <a href="/work/category/ecommerce/" class="<?php echo (is_category('ecommerce')) ? 'active' : ''; ?>">E-commerce Design</a>
                            </li>
                            <li class="cat-item">
                                <a href="/work/category/growth/" class="<?php echo (is_category('growth')) ? 'active' : ''; ?>">Digital Marketing</a>
                            </li>
                        </ul>
                    </nav><!-- col-md-12 -->
                </div><!-- row -->
            </div>
                
    </div>
</div>

<div class="wrapper" id="page-wrapper">
    <section <?php post_class('container-fluid'); ?> id="post-<?php the_ID(); ?>">
        <?php 
        // Configuramos la consulta para obtener solo 6 posts
        $args = array(
            'post_type' => 'works_post',
            'posts_per_page' => -1, // Limitar a 6 por página
            'category_name' => get_query_var('category_name') // Filtrar por categoría
        );
        
        // Ejecutar la consulta
        $the_query = new WP_Query( $args );
        
        // Verificar si hay posts
        if ( $the_query->have_posts() ) : ?>
            <div class="work-grid">
                <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

                    <?php
                    $thumbnail_id  = get_post_thumbnail_id($works->ID);
                    $thumbnail_alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
                    $image = wp_get_attachment_image_src( $thumbnail_id, 'large' );  ?>

                    <a href="<?php echo get_permalink(); ?>" class="ajax-call <?php foreach($postCat as $name){ echo $name.' '; } ?>">
                        <article class="work-box" <?php echo $postCat->slug; ?>>
                            <div class="hovereffect">
                                <img src="<?php echo get_template_directory_uri()?>/img/Spinner.gif" data-src="<?php echo $image[0]; ?>" class="img-fluid lozad" />
                                <noscript><img src="<?php echo $image[0]; ?>" class="img-fluid lozad" /></noscript>
                                <div class="overlay">
                                    <h2 class="work-title"><?php the_title(); ?></h2>
                                    <p class="work-description info"><?php echo get_field('description') ?></p>
                                </div>
                            </div>
                        </article>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p>No hay posts en esta categoría.</p>
        <?php endif; ?>

    </section>
</div>

<?php get_footer(); ?>
