<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Update Bizznis to the latest version.
 *
 * This iterative update function will take a Bizznis installation, no matter
 * how old, and update its options to the latest version.
 *
 * It used to iterate over theme version, but now uses a database version
 * system, which allows for changes within pre-releases, too.
 *
 * @since 1.0.0
 */
add_action( 'admin_init', 'bizznis_upgrade', 20 );
function bizznis_upgrade() {
	# Don't do anything if we're on the latest version
	if ( bizznis_get_option( 'db_version', null, false ) >= PARENT_DB_VERSION ) {
		return;
	}
	/*
	#########################
	# UPDATE TO VERSION x.x.x
	#########################
	if ( version_compare( bizznis_get_option( 'theme_version', null, false ), '1.9', '<' ) ) {
		# Vestige nav settings, for backward compatibility
		if ( 'nav-menu' != bizznis_get_option( 'nav_type' ) ) {
			_bizznis_vestige( array( 'nav_type', 'nav_superfish', 'nav_home', 'nav_pages_sort', 'nav_categories_sort', 'nav_depth', 'nav_exclude', 'nav_include', ) );
		}
		# Vestige subnav settings, for backward compatibility
		if ( 'nav-menu' != bizznis_get_option( 'subnav_type' ) ) {
			_bizznis_vestige( array( 'subnav_type', 'subnav_superfish', 'subnav_home', 'subnav_pages_sort', 'subnav_categories_sort', 'subnav_depth', 'subnav_exclude', 'subnav_include', ) );
		}
		$theme_settings = get_option( BIZZNIS_SETTINGS_FIELD );
		$new_settings   = array( 'theme_version' => '1.6', );
		$settings = wp_parse_args( $new_settings, $theme_settings );
		update_option( BIZZNIS_SETTINGS_FIELD, $settings );
	}
	*/
	# UPDATE DB TO VERSION 1260
	if ( bizznis_get_option( 'db_version', null, false ) < '1260' ) {
		bizznis_upgrade_1260();
	}
	do_action( 'bizznis_upgrade' );
}

/**
 * Upgrade the database to version 1260.
 *
 * @since 1.0.0
 */
function bizznis_upgrade_1260() {
	# Update Settings
	bizznis_update_settings( array(
		'theme_version' => '1.2.6',
		'db_version'    => '1260',
	) );
}

/**
 * Converts array of keys from Bizznis options to vestigial options.
 * This is done for backwards compatibility.
 *
 * @since 1.0.0
 */
function _bizznis_vestige( $keys = array(), $setting = BIZZNIS_SETTINGS_FIELD ) {
	# If no $keys passed, do nothing
	if ( ! $keys ) {
		return;
	}
	# Pull options
	$options = get_option( $setting );
	$vestige = get_option( 'bizznis-vestige' );
	# Cycle through $keys, creating new vestige array
	$new_vestige = array();
	foreach ( (array) $keys as $key ) {
		if ( isset( $options[$key] ) ) {
			$new_vestige[$key] = $options[$key];
			unset( $options[$key] );
		}
	}
	# If no new vestigial options being pushed, do nothing
	if ( ! $new_vestige ) {
		return;
	}
	# Merge the arrays, if necessary
	$vestige = $vestige ? wp_parse_args( $new_vestige, $vestige ) : $new_vestige;
	# Insert into options table
	update_option( 'bizznis-vestige', $vestige );
	update_option( $setting, $options );
}