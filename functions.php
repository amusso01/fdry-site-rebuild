<?php
/**
 * Understrap functions and definitions
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

$understrap_includes = array(
    '/theme-settings.php',                  // Initialize theme default settings.
    '/setup.php',                           // Theme setup and custom theme supports.
    '/widgets.php',                         // Register widget area.
    '/enqueue.php',                         // Enqueue scripts and styles.
    '/template-tags.php',                   // Custom template tags for this theme.
  '/pagination.php',                      // Custom pagination for this theme.
  '/my-pagination.php',                   // My personal pagination for the foundry theme
    '/hooks.php',                           // Custom hooks.
    '/extras.php',                          // Custom functions that act independently of the theme templates.
    '/customizer.php',                      // Customizer additions.
    '/custom-comments.php',                 // Custom Comments file.
    '/jetpack.php',                         // Load Jetpack compatibility file.
    '/class-wp-bootstrap-navwalker.php',    // Load custom WordPress nav walker.
    '/woocommerce.php',                     // Load WooCommerce functions.
    '/editor.php',                          // Load Editor functions.
);

foreach ( $understrap_includes as $file ) {
    $filepath = locate_template( '/inc' . $file );
    if ( ! $filepath ) {
        trigger_error( sprintf( 'Error locating /inc%s for inclusion', $file ), E_USER_ERROR );
    }
    require_once $filepath;
}

/*
     * Creating a function to create our Works
     */
    
     function custom_Works_type() {
        
        // Set UI labels for Custom Post Type
        $labels = array(
                        'name'                => _x( 'Works', 'Post Type General Name', 'foundry' ),
                        'singular_name'       => _x( 'Work', 'Post Type Singular Name', 'foundry' ),
                        'menu_name'           => __( 'Works', 'foundry' ),
                        'parent_item_colon'   => __( 'Parent Destination', 'foundry' ),
                        'all_items'           => __( 'All Works', 'foundry' ),
                        'view_item'           => __( 'View Works', 'foundry' ),
                        'add_new_item'        => __( 'Add New Works', 'foundry' ),
                        'add_new'             => __( 'Add New', 'foundry' ),
                        'edit_item'           => __( 'Edit Works', 'foundry' ),
                        'update_item'         => __( 'Update Works', 'foundry' ),
                        'search_items'        => __( 'Search Works', 'foundry' ),
                        'not_found'           => __( 'Not Found', 'foundry' ),
                        'not_found_in_trash'  => __( 'Not found in Trash', 'foundry' ),
                        );
        
        // Set other options for Custom Post Type
        
        $args = array(
                      'label'               => __( 'Works' ),
                      'description'         => __( 'Work of our portfolio'),
                      'labels'              => $labels,
                      // Features this CPT supports in Post Editor
                      'supports'            => array( 'title', 'editor', 'thumbnail','custom-fields'),

                      'taxonomies'          => array( 'category' ),
                   
                      /* A hierarchical CPT is like Pages and can have
                       * Parent and child items. A non-hierarchical CPT
                       * is like Posts.
                       */
                      'hierarchical'        => false,
                      'public'              => true,
                      'show_ui'             => true,
                      'show_in_menu'        => true,
                      'show_in_nav_menus'   => true,
                      'show_in_admin_bar'   => true,
                      'menu_position'       => 5,
                      'menu_icon'           => 'dashicons-portfolio',
                      'can_export'          => true,
                      'has_archive'         => true,
                      'exclude_from_search' => false,
                      'show_in_graphql' => true,
                      'graphql_single_name' => 'work',
                      'graphql_plural_name' => 'works',
                      'publicly_queryable'  => true,
                      'capability_type'     => 'post',
                      'rewrite' => array('slug' => 'works'),
                      );
        
        // Registering your Custom Post Type
        register_post_type( 'works_post', $args );
        
    }
    
    /* Hook into the 'init' action so that the function
     * Containing our post type registration is not
     * unnecessarily executed.
     */
    
    add_action( 'init', 'custom_Works_type', 0 );



/*--------------------------addsvg----------------------------------------------*/
add_filter( 'upload_mimes', 'custom_upload_mimes' );
function custom_upload_mimes( $existing_mimes = array() ) {
  // Add the file extension to the array
  $existing_mimes['svg'] = 'image/svg+xml';
  return $existing_mimes;
}
/*-------------------------------------------------------------------------------*/




// ============ Rename Post to Insight ============

function revcon_change_post_label() {
  global $menu;
  global $submenu;
  $menu[5][0] = 'Insights';
  $submenu['edit.php'][5][0] = 'Insights';
  $submenu['edit.php'][10][0] = 'Add Insight';
  $submenu['edit.php'][16][0] = 'Insights Tags';
}
function revcon_change_post_object() {
  global $wp_post_types;
  $labels = &$wp_post_types['post']->labels;
  $labels->name = 'Insights';
  $labels->singular_name = 'Insight';
  $labels->add_new = 'Add Insights';
  $labels->add_new_item = 'Add Insights';
  $labels->edit_item = 'Edit Insight';
  $labels->new_item = 'Insights';
  $labels->view_item = 'View Insights';
  $labels->search_items = 'Search Insights';
  $labels->not_found = 'No Insights found';
  $labels->not_found_in_trash = 'No Insights found in Trash';
  $labels->all_items = 'All Insights';
  $labels->menu_name = 'Insights';
  $labels->name_admin_bar = 'Insights';
}

add_action( 'admin_menu', 'revcon_change_post_label' );
add_action( 'init', 'revcon_change_post_object' );




// Our custom post type function
function create_posttype() {
  
  register_post_type( 'job',
  // CPT Options
      array(
          'labels' => array(
              'name' => __( 'Jobs' ),
              'singular_name' => __( 'Job' )
          ),
          'public' => true,
          'has_archive' => true,
          'show_in_rest' => true,

      )
  );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );



//Endpoint works
add_action('init', function () {
    // Añadir una regla de reescritura para /work/category/{slug}
    add_rewrite_rule(
        '^work/category/([^/]+)/?$',
        'index.php?post_type=works_post&category_name=$matches[1]',
        'top'
    );
});

add_filter('query_vars', function ($vars) {
    // Registrar la variable query para la categoría
    $vars[] = 'category_name';
    return $vars;
});

add_action('template_include', function ($template) {
    if (get_query_var('post_type') === 'works_post' && get_query_var('category_name')) {
        // Usa un template específico si es /work/category/{slug}
        return locate_template('archive-works-category.php') ?: $template;
    }
    return $template;
});

// Refrescar reglas de reescritura al activar el tema
add_action('after_switch_theme', function () {
    flush_rewrite_rules();
});

/**
 * ✅ ACTUALIZADO: Agrega data-post-id a cada post para validación
 */
function build_work_post_html( $query ) {
    $output = '';
    while ( $query->have_posts() ) {
        $query->the_post();

        $cat = get_the_category();
        $postCat = [];
        foreach ( $cat as $category ) {
            $postCat[] = $category->cat_name;
        }

        $thumbnail_id = get_post_thumbnail_id( get_the_ID() );
        $image = wp_get_attachment_image_src( $thumbnail_id, 'large' );
        
        // ✅ NUEVO: Agregar data-post-id para validación de duplicados
        $post_id = get_the_ID();

        $output .= '<a href="' . get_permalink() . '" class="ajax-call ' . implode( ' ', $postCat ) . '" data-post-id="' . esc_attr( $post_id ) . '">';
        $output .= '<article class="work-box">';
        $output .= '<div class="hovereffect">';
        $output .= '<img src="' . get_template_directory_uri() . '/img/Spinner.gif" data-src="' . $image[0] . '" class="img-fluid lozad work-grid-image" />';
        $output .= '<noscript><img src="' . $image[0] . '" class="img-fluid lozad" /></noscript>';
        $output .= '<div class="overlay">';
        $output .= '<h2 class="work-title">' . get_the_title() . '</h2>';
        $output .= '<p class="work-description info">' . get_field( 'description' ) . '</p>';
        if ( ! empty( $postCat ) ) {
                $output .= '<div class="work-description info catname">';
                foreach ( $postCat as $cat_name ) {
                    $output .= '<span>'
                             . esc_html( $cat_name )
                             . '</span>';
                }
                $output .= '</div>';
            }
        $output .= '</div>'; // .overlay
        $output .= '</div>'; // .hovereffect
        $output .= '</article>'; // .work-box
        $output .= '</a>'; // .ajax-call
    }
    return $output;
}

function get_posts_by_category( $category_slug, $paged = 1 ) {
    $posts_per_page = 24;

    $args = array(
        'post_type'      => 'works_post',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'tax_query'      => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ),
        ),
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        $output = '<div class="work-grid-container" data-category="' . esc_attr($category_slug) . '" data-page="0">';
        $output .= '<div class="work-grid">';
        
        $output .= build_work_post_html( $query );
        
        $output .= '</div>'; // .work-grid

        if ( $query->max_num_pages > 1 ) {
            $output .= '<div class="load-more-wrapper">';
            $output .= '<a href="#" class="load-more-button">Load More</a>';
            $output .= '</div>';
        }
        
        $output .= '</div>'; // .work-grid-container
        
        wp_reset_postdata();
        return $output;
    } else {
        return '<p>No posts found</p>';
    }
}

function get_all_posts( $paged = 1 ) {
    $posts_per_page = 24;

    $args = array(
        'post_type'      => 'works_post',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        $output = '<div class="work-grid-container" data-category="all" data-page="0">';
        $output .= '<div class="work-grid">';
        
        $output .= build_work_post_html( $query );
        
        $output .= '</div>'; // .work-grid

        if ( $query->max_num_pages > 1 ) {
            $output .= '<div class="load-more-wrapper">';
            $output .= '<a href="#" class="load-more-button">Load More</a>';
            $output .= '</div>';
        }
        
        $output .= '</div>'; // .work-grid-container
        
        wp_reset_postdata();
        return $output;
    } else {
        return '<p>No posts found</p>';
    }
}

// HANDLER AJAX (SOLO UNA VEZ - SIN DUPLICACIONES)
add_action( 'wp_ajax_load_more_posts', 'my_load_more_posts_handler' );
add_action( 'wp_ajax_nopriv_load_more_posts', 'my_load_more_posts_handler' );

function my_load_more_posts_handler() {
    check_ajax_referer( 'my_ajax_nonce', 'nonce' );

    $page = isset( $_POST['page'] ) ? intval( $_POST['page'] ) : 0;
    $category_slug = isset( $_POST['category'] ) ? sanitize_text_field( $_POST['category'] ) : 'all';
    $posts_per_page = 24;
    
    // IMPORTANTE: Convertir página (0, 1, 2...) a paged de WordPress (1, 2, 3...)
    $paged = $page + 1;

    $args = array(
        'post_type'      => 'works_post',
        'posts_per_page' => $posts_per_page,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    if ( $category_slug !== 'all' ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ),
        );
    }

    $query = new WP_Query( $args );

    $response = array(
        'html'       => '',
        'more_posts' => false,
    );

    if ( $query->have_posts() ) {
        $response['html'] = build_work_post_html( $query );
        
        // Verifica si hay más posts después de la página actual
        if ( $query->max_num_pages > $paged ) {
            $response['more_posts'] = true;
        }
    }

    wp_send_json_success( $response );
    wp_die();
}