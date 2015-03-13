<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Launch the bizznis template structure.
 *
 * Function must be called at the end of each php template file.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis' ) ) :
function bizznis() {
	get_header();
	do_action( 'bizznis_main' );
	get_footer();
}
endif;

add_action( 'bizznis_header', 'bizznis_do_header' );
/**
 * Echo the header structure
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_header' ) ) :
function bizznis_do_header() {
	global $wp_registered_sidebars;
	do_action( 'bizznis_before_header' );
	printf( '<header %s>', bizznis_attr( 'site-header' ) );
		do_action( 'bizznis_header_top' );
		if ( apply_filters( 'bizznis_show_header_content', true ) ) {
			printf( '<div %s>', bizznis_attr( 'header-content' ) );
			bizznis_wrapper( 'header-wrapper', 'open' ); #wrapper
				if ( has_action( 'bizznis_site_title' ) ) {
					printf( '<div %s>', bizznis_attr( 'title-area' ) );
						do_action( 'bizznis_site_title' );
					echo '</div>'; #close .title-area
				}
				if ( ( isset( $wp_registered_sidebars['header-aside'] ) && is_active_sidebar( 'header-aside' ) ) || has_action( 'bizznis_header_aside' ) ) {
					printf( '<aside %s>', bizznis_attr( 'header-aside-area', array( 'class' => 'header-aside-area widget-area' ) ) );
						do_action( 'bizznis_header_aside' );
						add_filter( 'wp_nav_menu_args', 'bizznis_header_menu_args' );
						add_filter( 'wp_nav_menu', 'bizznis_header_menu_wrap' );
						dynamic_sidebar( 'header-aside' );
						remove_filter( 'wp_nav_menu_args', 'bizznis_header_menu_args' );
						remove_filter( 'wp_nav_menu', 'bizznis_header_menu_wrap' );
					echo '</aside>'; #close .aside-area
				}
			bizznis_wrapper( 'header-wrapper', 'close' ); #wrapper
			echo '</div>'; #close .header-content
		}
		do_action( 'bizznis_header_bottom' );
	echo '</header>'; #end .site-header
	do_action( 'bizznis_after_header' );
}
endif;

add_action( 'bizznis_main', 'bizznis_do_main' );
/**
 * Echo the main structure
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_main' ) ) :
function bizznis_do_main() {
	do_action( 'bizznis_before_content_sidebar_wrap' );
	printf( '<div %s>', bizznis_attr( 'content-sidebar-wrap' ) );
	do_action( 'bizznis_before_content' );
	printf( '<main %s>', bizznis_attr( 'content' ) );
		do_action( 'bizznis_loop' );
	echo '</main>';
	do_action( 'bizznis_after_content' );
	echo '</div>'; #end .main-content-sidebar
	do_action( 'bizznis_after_content_sidebar_wrap' );
}
endif;

add_action( 'bizznis_footer', 'bizznis_do_footer' );
/**
 * Echo the footer structure.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_footer' ) ) :
function bizznis_do_footer() {
	global $wp_registered_sidebars;
	do_action( 'bizznis_before_footer' );
	printf( '<footer %s>', bizznis_attr( 'site-footer' ) );
		do_action( 'bizznis_footer_top' );
		if ( has_action( 'bizznis_footer_inner' ) ) {
			do_action( 'bizznis_footer_inner' );
		}
		do_action( 'bizznis_footer_bottom' );
	echo '</footer>'; #end .site-footer
	do_action( 'bizznis_after_footer' );
}
endif;