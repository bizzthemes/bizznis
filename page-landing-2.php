<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
	
	Template Name: Landing: No Container | Header, Footer
*/

/**
 * Force full width content layout
 *
 * @since 1.1.7
 */
add_filter( 'bizznis_pre_get_option_site_layout', 'bizznis_return_landing_2_layout' );
function bizznis_return_landing_2_layout() {
	return 'full-width-content';
}

/**
 * Remove main wrapper
 *
 * @since 1.1.7
 */
add_filter( 'bizznis_wrapper_main-wrapper', '__return_false' ); 

//* Run the Bizznis loop
bizznis();