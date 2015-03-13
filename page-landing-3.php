<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
	
	Template Name: Landing: No Container | No Header, No Footer
*/

/**
 * Force full width content layout
 *
 * @since 1.1.7
 */
add_filter( 'bizznis_pre_get_option_site_layout', 'bizznis_return_landing_3_layout' );
function bizznis_return_landing_3_layout() {
	return 'full-width-content';
}

/**
 * Remove header and footer for this page template
 *
 * @since 1.1.7
 */
add_action( 'wp_head', 'bizznis_page_landing_3_hooks' );
function bizznis_page_landing_3_hooks() {
	remove_action( 'bizznis_header', 'bizznis_do_header' );
	remove_action( 'bizznis_footer', 'bizznis_do_footer' );
}

/**
 * Remove main wrapper
 *
 * @since 1.1.7
 */
add_filter( 'bizznis_wrapper_main-wrapper', '__return_false' ); 

//* Run the Bizznis loop
bizznis();