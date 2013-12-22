<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Handles the search results page.
 *
 * @since 1.0.0
 */
add_action( 'bizznis_loop', 'bizznis_do_search_title', 5 );
function bizznis_do_search_title() {
	$title = sprintf( '<div class="archive-description"><h1 class="archive-title">%s %s</h1></div>', apply_filters( 'bizznis_search_title_text', __( 'Search Results for:', 'bizznis' ) ), get_search_query() );
	echo apply_filters( 'bizznis_search_title_output', $title ) ."\n";
}

bizznis(); #Fire the engine