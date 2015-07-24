<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Pass nav menu link attributes through attribute parser.
 *
 * Adds nav menu link attributes via the Bizznis markup API.
 *
 * @since 1.2.2
 *
 * @param array $atts {
 *		The HTML attributes applied to the menu item's <a>, empty strings are ignored.
 *
 *		@type string $title Title attribute.
 *		@type string $target Target attribute.
 *		@type string $rel The rel attribute.
 *		@type string $href The href attribute.
 * }
 * @param object $item The current menu item.
 * @param array $args An array of wp_nav_menu() arguments.
 *
 * @return array Maybe modified menu attributes array.
 */
add_filter( 'nav_menu_link_attributes', 'bizznis_nav_menu_link_attributes', 10, 3 );
function bizznis_nav_menu_link_attributes( $atts, $item, $args ) {
	return bizznis_parse_attr( 'nav-link', $atts );
}

/**
 * Register the custom menu locations, if theme has support for them.
 *
 * Does the `bizznis_register_nav_menus` action.
 *
 * @since 1.0.0
 */
add_action( 'after_setup_theme', 'bizznis_register_nav_menus' );
function bizznis_register_nav_menus() {
	# Stop here if menus not supported
	if ( ! current_theme_supports( 'bizznis-menus' ) ) {
		return;
	}
	$menus = get_theme_support( 'bizznis-menus' );
	# Register supported menus
	register_nav_menus( (array) $menus[0] );
	do_action( 'bizznis_register_nav_menus' );
}

add_action( 'bizznis_header_top', 'bizznis_do_nav' );
/**
 * Echo the "Primary Navigation" menu.
 *
 * Applies the `bizznis_primary_nav` and legacy `bizznis_do_nav` filters.
 *
 * @since 1.0.0
 *
 * @uses bizznis_nav_menu() Display a navigation menu.
 * @uses bizznis_nav_menu_supported() Checks for support of specific nav menu.
 * @uses bizznis_a11y() Checks for acessibility support to add a heading to the main navigation.
 */
if ( ! function_exists( 'bizznis_do_nav' ) ) :
function bizznis_do_nav() {
	# Do nothing if menu not supported
	if ( ! bizznis_nav_menu_supported( 'primary' ) ) {
		return;
	}
	$class = 'menu menu-bizznis menu-primary';
	if ( bizznis_a11y( 'headings' ) ) {
		printf( '<h2 class="screen-reader-text">%s</h2>', __( 'Main navigation', 'bizznis' ) );
	}
	bizznis_nav_menu( array(
		'theme_location' => 'primary',
		'menu_class'     => $class,
	) );
}
endif;

add_action( 'bizznis_header_bottom', 'bizznis_do_subnav' );
/**
 * Echo the "Secondary Navigation" menu.
 *
 * Applies the `bizznis_secondary_nav` and legacy `bizznis_do_subnav` filters.
 *
 * @since 1.0.0
 *
 * @uses bizznis_nav_menu() Display a navigation menu.
 * @uses bizznis_nav_menu_supported() Checks for support of specific nav menu.
 */
if ( ! function_exists( 'bizznis_do_subnav' ) ) :
function bizznis_do_subnav() {
	# Do nothing if menu not supported
	if ( ! bizznis_nav_menu_supported( 'secondary' ) ) {
		return;
	}
	$class = 'menu menu-bizznis menu-secondary';
	bizznis_nav_menu( array(
		'theme_location' => 'secondary',
		'menu_class'     => $class,
	) );
}
endif;

add_filter( 'nav_primary_after', 'bizznis_nav_right', 10, 2 );
/**
 * Filter the Primary Navigation menu items, appending either RSS links, search form, twitter link, or today's date.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_nav_right' ) ) :
function bizznis_nav_right( $menu = '', $args = '' ) {
	# Stop here if extras not enabled
	if ( ! bizznis_get_option( 'nav_extras_enable' ) ) {
		return $menu;
	}
	# show selected option
	switch ( bizznis_get_option( 'nav_extras' ) ) {
		case 'search':
			# I hate output buffering, but I have no choice
			ob_start();
			get_search_form();
			$search = ob_get_clean();
			$menu  .= '<div class="menu-bizznis-extra right search">' . $search . '</div>';
			break;
		case 'twitter':
			$menu .= sprintf( '<ul class="menu-bizznis-extra right twitter"><li><a href="%s">%s</a></li></ul>', esc_url( 'http://twitter.com/' . bizznis_get_option( 'nav_extras_twitter_id' ) ), esc_html( bizznis_get_option( 'nav_extras_twitter_text' ) ) );
			break;
		case 'date':
			$menu .= '<div class="menu-bizznis-extra right date">' . date_i18n( get_option( 'date_format' ) ) . '</div>';
			break;
	}
	return $menu;
}
endif;

/**
 * Sets a common class, `.bizznis-nav-menu`, for the custom menu widget if used in the header sidebar.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_header_menu_args' ) ) :
function bizznis_header_menu_args( $args ) {
	$args['container']   = '';
	$args['link_before'] = $args['link_before'] ? $args['link_before'] : sprintf( '<span %s>', bizznis_attr( 'nav-link-wrap' ) );
	$args['link_after']  = $args['link_after'] ? $args['link_after'] : '</span>';
	$args['menu_class'] .= ' menu-bizznis';
	return $args;
}
endif;

/**
 * Wrap the header navigation menu in its own nav tags with markup API.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_header_menu_wrap' ) ) :
function bizznis_header_menu_wrap( $menu ) {
	return sprintf( '<nav %s>', bizznis_attr( 'nav-header', array( 'class' => 'nav-bizznis nav-header' ) ) ) . $menu . '</nav>';
}
endif;

/**
 * Add navigation menu description
 *
 * Optionally call it inside a child theme
 *
 * @since 1.0.0
 */
// add_filter( 'walker_nav_menu_start_el', 'bizznis_add_menu_description', 10, 2 );
function bizznis_add_menu_description( $item_output, $item ) {
	$description = $item->post_content;
	if ( ' ' !== $description ) {
		return preg_replace( '/(<a.*?>[^<]*?)</', '$1' . '<span class="menu-description">' . $description . '</span><', $item_output);
	}
	 else {
		return $item_output;
	}
}
