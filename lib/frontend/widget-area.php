<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

add_action( 'bizznis_init', 'bizznis_register_default_widget_areas', 15 );
/**
 * Hook the callback that registers the default Bizznis widget areas.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_register_default_widget_areas' ) ) :
function bizznis_register_default_widget_areas() {

	//* Temporarily register placeholder widget areas, so that child themes can unregister directly in functions.php.
	bizznis_register_widget_area( array( 'id' => 'header-aside' ) );
	bizznis_register_widget_area( array( 'id' => 'sidebar' ) );
	bizznis_register_widget_area( array( 'id' => 'sidebar-alt' ) );

	//* Call all final widget area registration after themes setup, so text can be translated.
	add_action( 'after_setup_theme', '_bizznis_register_default_widget_areas_cb' );
	add_action( 'after_setup_theme', 'bizznis_register_footer_widget_areas' );
	add_action( 'after_setup_theme', 'bizznis_register_after_entry_widget_area' );

}
endif;

/**
 * Register the default Bizznis widget areas, if the placeholder widget areas are still registered.
 *
 * @since 1.2.0
 *
 * @uses bizznis_register_widget_area() Register widget areas.
 */
if ( ! function_exists( '_bizznis_register_default_widget_areas_cb' ) ) :
function _bizznis_register_default_widget_areas_cb() {

	global $wp_registered_sidebars;

	if ( isset( $wp_registered_sidebars['header-aside'] ) ) {
		bizznis_register_widget_area(
			array(
				'id'               => 'header-aside',
				'name'             => is_rtl() ? __( 'Header Left', 'bizznis' ) : __( 'Header Right', 'bizznis' ),
				'description'      => __( 'This is the header widget area. It typically appears next to the site title or logo. This widget area is not suitable to display every type of widget, and works best with a custom menu, a search form, or possibly a text widget.', 'bizznis' ),
				'_bizznis_builtin' => true,
			)
		);
	}

	if ( isset( $wp_registered_sidebars['sidebar'] ) ) {
		bizznis_register_widget_area(
			array(
				'id'               => 'sidebar',
				'name'             => __( 'Primary Sidebar', 'bizznis' ),
				'description'      => __( 'This is the primary sidebar if you are using a two or three column site layout option.', 'bizznis' ),
				'_bizznis_builtin' => true,
			)
		);
	}

	if ( isset( $wp_registered_sidebars['sidebar-alt'] ) ) {
		bizznis_register_widget_area(
			array(
				'id'               => 'sidebar-alt',
				'name'             => __( 'Secondary Sidebar', 'bizznis' ),
				'description'      => __( 'This is the secondary sidebar if you are using a three column site layout option.', 'bizznis' ),
				'_bizznis_builtin' => true,
			)
		);
	}

}
endif;

/**
 * Register footer widget areas based on the number of widget areas the user wishes to create
 * with 'add_theme_support()'.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_register_footer_widget_areas' ) ) :
function bizznis_register_footer_widget_areas() {
	$footer_widgets = get_theme_support( 'bizznis-footer-widgets' );
	if ( ! $footer_widgets || ! isset( $footer_widgets[0] ) || ! is_numeric( $footer_widgets[0] ) ) {
		return;
	}
	$footer_widgets = (int) $footer_widgets[0];
	$counter = 1;
	while ( $counter <= $footer_widgets ) {
		bizznis_register_widget_area(
			array(
				'id'               => sprintf( 'footer-%d', $counter ),
				'name'             => sprintf( __( 'Footer %d', 'bizznis' ), $counter ),
				'description'      => sprintf( __( 'Footer %d widget area.', 'bizznis' ), $counter ),
				'_bizznis_builtin' => true,
			)
		);
		$counter++;
	}
}
endif;

/**
 * Register after-entry widget area if user specifies in the child theme.
 *
 * @since 1.1.0
 *
 * @uses bizznis_register_widget_area() Register widget area.
 * @return null Return early if there's no theme support.
 */
if ( ! function_exists( 'bizznis_register_after_entry_widget_area' ) ) :
function bizznis_register_after_entry_widget_area() {
	if ( ! current_theme_supports( 'bizznis-after-entry-widgets' ) ) {
		return;
	}
	bizznis_register_widget_area(
		array(
			'id'          => 'after-entry',
			'name'        => __( 'After Entry', 'bizznis' ),
			'description' => __( 'Widgets in this widget area will display after single entries.', 'bizznis' ),
			'_bizznis_builtin'    => true,
		)
	);
}
endif;

add_action( 'bizznis_sidebar', 'bizznis_do_sidebar' );
/**
 * Echo primary sidebar default content.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_sidebar' ) ) :
function bizznis_do_sidebar() {
	# Only shows if sidebar is empty, and current user has the ability to edit theme options (manage widgets).
	if ( ! dynamic_sidebar( 'sidebar' ) && current_user_can( 'edit_theme_options' )  ) {
		bizznis_default_widget_area_content( __( 'Primary Sidebar Widget Area', 'bizznis' ) );
	}
}
endif;

add_action( 'bizznis_sidebar_alt', 'bizznis_do_sidebar_alt' );
/**
 * Echo alternate sidebar default content.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_sidebar_alt' ) ) :
function bizznis_do_sidebar_alt() {
	# Only shows if sidebar is empty, and current user has the ability to edit theme options (manage widgets).
	if ( ! dynamic_sidebar( 'sidebar-alt' ) && current_user_can( 'edit_theme_options' ) ) {
		bizznis_default_widget_area_content( __( 'Secondary Sidebar Widget Area', 'bizznis' ) );
	}
}
endif;

/**
 * Template for default widget area content.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_default_widget_area_content' ) ) :
function bizznis_default_widget_area_content( $name ) {	
	echo '<section class="widget widget_text">';
	echo '<div class="widget-wrap">';
	$heading = ( bizznis_a11y( 'headings' ) ? 'h3' : 'h4' );
		printf( '<%1$s class="widgettitle">%2$s</%1$s>', $heading, esc_html( $name ) );
		echo '<div class="textwidget"><p>';
			printf( __( 'This is the %s. You can add content to this area by visiting your <a href="%s">Widgets Panel</a> and adding new widgets to this area.', 'bizznis' ), $name, admin_url( 'widgets.php' ) );
		echo '</p></div>';
	echo '</div>';
	echo '</section>';
}
endif;