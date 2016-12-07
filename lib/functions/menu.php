<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Determine if a child theme supports a particular Bizznis nav menu.
 *
 * @since 1.0.0
 */
function bizznis_nav_menu_supported( $menu ) {
	if ( ! current_theme_supports( 'bizznis-menus' ) ) {
		return false;
	}
	
	$menus = get_theme_support( 'bizznis-menus' );
	
	if ( array_key_exists( $menu, (array) $menus[0] ) ) {
		return true;
	}
	
	return false;
}

/**
 * Return the markup to display a menu consistent with the Bizznis format.
 *
 * Applies the `bizznis_$location_nav` filter e.g. `bizznis_header_nav`. For primary and secondary menu locations, it
 * also applies the `bizznis_do_nav` and `bizznis_do_subnav` filters for backwards compatibility.
 *
 * @since 1.1.0
 *
 * @param string $args Menu arguments.
 * @return string Navigation menu markup.
 */
function bizznis_get_nav_menu( $args = array() ) {
	$args = wp_parse_args( $args, array(
		'theme_location' => '',
		'container'      => '',
		'link_before'    => sprintf( '<span %s>', bizznis_attr( 'nav-link-wrap' ) ),
		'link_after'     => '</span>',
		'menu_class'     => 'menu menu-bizznis',
		'echo'           => 0,
	) );
	
	// If a menu is not assigned to theme location, abort.
	if ( ! has_nav_menu( $args['theme_location'] ) ) {
		return;
	}
	
	$sanitized_location = sanitize_key( $args['theme_location'] );
	
	$nav  = apply_filters( "nav_{$sanitized_location}_before", '', $args );
	$nav .= wp_nav_menu( $args );
	$nav .= apply_filters( "nav_{$sanitized_location}_after", '', $args );
	
	// Do nothing if there is nothing to show.
	if ( ! $nav ) {
		return;
	}
	$nav_markup_open = sprintf( '<nav %s>', bizznis_attr( 'nav-' . $sanitized_location, array( 'class' => "nav-bizznis nav-{$sanitized_location}" ) ) );
	$nav_markup_open .= bizznis_wrapper( 'menu-' . $sanitized_location, 'open', false ); #wrapper
	$nav_markup_close = bizznis_wrapper( 'menu-' . $sanitized_location, 'close', false ); #wrapper
	$nav_markup_close .= '</nav>';
	$nav_output = $nav_markup_open . $nav . $nav_markup_close;
	$filter_location = 'bizznis_' . $sanitized_location . '_nav';
	
	// Handle back-compat for primary and secondary nav filters.
	if ( 'primary' === $args['theme_location'] ) {
		$filter_location = 'bizznis_do_nav';
	} elseif ( 'secondary' === $args['theme_location'] ) {
		$filter_location = 'bizznis_do_subnav';
	}
	
	/**
	 * Filter the navigation markup.
	 *
	 * @since 1.1.0
	 *
	 * @param string $nav_output Opening container markup, nav, closing container markup.
	 * @param string $nav Navigation list (`<ul>`).
	 * @param array $args {
	 *     Arguments for `wp_nav_menu()`.
	 *
	 *     @type string $theme_location Menu location ID.
	 *     @type string $container Container markup.
	 *     @type string $menu_class Class(es) applied to the `<ul>`.
	 *     @type bool $echo 0 to indicate `wp_nav_menu()` should return not echo.
	 * }
	 */
	return apply_filters( $filter_location, $nav_output, $nav, $args );
}

/**
 * Echo the output from `bizznis_get_nav_menu()`.
 *
 * @since 1.1.0
 *
 * @uses bizznis_get_nav_menu() Return the markup to display a menu consistent with the Bizznis format.
 *
 * @param string $args Menu arguments.
 */
function bizznis_nav_menu( $args ) {
	echo bizznis_get_nav_menu( $args );
}