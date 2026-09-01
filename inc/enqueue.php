<?php
/**
 * Understrap enqueue scripts
 *
 * @package understrap
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! function_exists( 'understrap_scripts' ) ) {
	/**
	 * Load theme's JavaScript and CSS sources.
	 */
	function understrap_scripts() {
		// Get the theme data.
		$the_theme = wp_get_theme();
		$theme_version = $the_theme->get( 'Version' );
		
		$css_version = $theme_version . '.' . filemtime(get_template_directory() . '/css/theme.min.css');
		wp_enqueue_style( 'understrap-styles', get_stylesheet_directory_uri() . '/css/theme.min.css', array(), $css_version );
		function load_wpcf7_scripts() {
			if (is_page('brief-1') || is_page('brief-2') || is_page( 'brief-final' )){
				if ( function_exists( 'wpcf7_enqueue_scripts' ) ) {
					wpcf7_enqueue_scripts();
				} 
				if ( function_exists( 'wpcf7_enqueue_styles' ) ) {
					wpcf7_enqueue_styles();
				}
			}
		}
		add_action('wp_enqueue_scripts', 'load_wpcf7_scripts');
		wp_deregister_script('jquery');
		wp_register_script( 'jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js', array(), null, false );
		wp_enqueue_script('jquery');
		wp_enqueue_script('three', get_stylesheet_directory_uri().'/js/three.min.js', array(), null, true);
		wp_enqueue_script('app', get_stylesheet_directory_uri().'/js/app.min.js', array('three'), null, true);
		//load TweenMax in the pages requested
		if(is_page('about') || is_page('work')){
			wp_enqueue_script('tweenmax', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/2.0.2/TweenMax.min.js', array(), null, true);
		}
		if(is_page('about')){
			wp_enqueue_script('about', get_stylesheet_directory_uri().'/js/about.js', array('tweenmax'), null, true);
		}
		if(is_page('work')){
				//Ajax filter scripts     
				wp_register_script( 'filterCategory', get_stylesheet_directory_uri().'/js/filterCategory.js', array( 'jquery' ), '1.0.0', true );
				wp_enqueue_script( 'filterCategory' );
			}
			
			wp_enqueue_script('lazy-jquery', get_stylesheet_directory_uri().'/js/lozad.min.js', array('jquery'), null, true);
		
		// register script to load more post 
		// wp_register_script( 'my_loadmore', get_stylesheet_directory_uri() . '/js/myloadmore.js', array('jquery') );
 
		// wp_localize_script( 'my_loadmore', 'my_load_params', array(
		// 	'ajaxurl' => site_url() . '/wp-admin/admin-ajax.php', // WordPress AJAX
		// 	'posts' => json_encode( $wp_query->query_vars ), // everything about your loop is here
		// 	'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
		// 	'max_page' => $wp_query->max_num_pages
		// ) );
		// wp_enqueue_script( 'my_loadmore' );
		
		$js_version = $theme_version . '.' . filemtime(get_template_directory() . '/js/theme.min.js');
		wp_enqueue_script( 'understrap-scripts', get_template_directory_uri() . '/js/theme.min.js', array(), $js_version, true );
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
} // endif function_exists( 'understrap_scripts' ).
add_action( 'wp_enqueue_scripts', 'understrap_scripts' );

/**
 * Localize AJAX data para el load more
 * Esto pasa los datos AJAX al JavaScript en la página de works
 * 
 * Priority 11 = después de understrap_scripts (priority 10)
 */
function my_enqueue_ajax_data() {
	if ( is_page( 'work' ) || is_page_template( 'page-works.php' ) ) {
		wp_localize_script( 'understrap-scripts', 'my_ajax_object', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'my_ajax_nonce' )
		) );
	}
}
add_action( 'wp_enqueue_scripts', 'my_enqueue_ajax_data', 11 );