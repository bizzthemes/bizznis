<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Bizznis back compat functionality
 *
 * Prevents Bizznis from running on WordPress versions prior to 3.6,
 * since this theme is not meant to be backward compatible beyond that
 * and relies on many newer functions and markup changes introduced in 3.6.
 *
 * @since 1.0.0
 */

/**
 * Prevent switching to Bizznis on old versions of WordPress.
 *
 * Switches to the default theme.
 *
 * @since 1.0.0
 *
 * @return void
 */
add_action( 'after_switch_theme', 'bizznis_switch_theme' );
function bizznis_switch_theme() {
	switch_theme( WP_DEFAULT_THEME, WP_DEFAULT_THEME );
	unset( $_GET['activated'] );
	add_action( 'admin_notices', 'bizznis_upgrade_notice' );
}

/**
 * Add message for unsuccessful theme switch.
 *
 * Prints an update nag after an unsuccessful attempt to switch to
 * Bizznis on WordPress versions prior to 3.6.
 *
 * @since 1.0.0
 *
 * @return void
 */
function bizznis_upgrade_notice() {
	$message = sprintf( __( 'Bizznis requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'bizznis' ), $GLOBALS['wp_version'] );
	printf( '<div class="error"><p>%s</p></div>', $message );
}

/**
 * Prevent the Theme Customizer from being loaded on WordPress versions prior to 3.6.
 *
 * @since 1.0.0
 *
 * @return void
 */
add_action( 'load-customize.php', 'bizznis_customize' );
function bizznis_customize() {
	wp_die( sprintf( __( 'Bizznis requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'bizznis' ), $GLOBALS['wp_version'] ), '', array(
		'back_link' => true,
	) );
}

/**
 * Prevent the Theme Preview from being loaded on WordPress versions prior to 3.4.
 *
 * @since 1.0.0
 *
 * @return void
 */
add_action( 'template_redirect', 'bizznis_preview' );
function bizznis_preview() {
	if ( isset( $_GET['preview'] ) ) {
		wp_die( sprintf( __( 'Bizznis requires at least WordPress version 3.6. You are running version %s. Please upgrade and try again.', 'bizznis' ), $GLOBALS['wp_version'] ) );
	}
}
