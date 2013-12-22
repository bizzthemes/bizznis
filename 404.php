<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Handles display of 404 page.
 *
 * @since 1.0.0
 */
remove_action( 'bizznis_loop', 'bizznis_do_loop' ); #remove default loop
add_action( 'bizznis_loop', 'bizznis_404' ); #output a 404 "Not Found" error message
function bizznis_404() {
	echo
		"<article class=\"entry\">\n",
		"\t". sprintf( '<h1 class="entry-title">%s</h1>', __( 'Not found, error 404', 'bizznis' ) ) ."\n",
		"\t<div class=\"entry-content\">\n",
		"\t\t<p>". sprintf( __( 'The page you are looking for no longer exists. Perhaps you can return back to the site\'s <a href="%s">homepage</a> and see if you can find what you are looking for. Or, you can try finding it by using the search form below.', 'bizznis' ), home_url() ) ."</p>\n",
		"\t\t<p>". get_search_form() ."</p>\n",
		"\t</div>\n",
		"</article>\n";
}

bizznis(); #Fire the engine
