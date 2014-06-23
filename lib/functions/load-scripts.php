<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Enqueue the scripts used on the front-end of the site.
 *
 * @since 1.0.0
 */
add_action( 'wp_enqueue_scripts', 'bizznis_load_scripts' );
function bizznis_load_scripts() {
	# If a single post or page, threaded comments are enabled, and comments are open
	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() ) {
		wp_enqueue_script( 'comment-reply' );		
	}
}

/**
 * Load the html5 shiv for IE8 and below. Can't enqueue with IE conditionals.
 *
 * @since 1.0.6
 *
 */
add_action( 'wp_head', 'bizznis_html5_ie_fix' );
function bizznis_html5_ie_fix() {
	echo '<!--[if lt IE 9]><script src="' . BIZZNIS_ADMIN_JS_URL . '/html5shiv.js"></script><![endif]-->' . "\n";
}

/**
 * Conditionally enqueues the scripts used in the admin.
 *
 * @since 1.0.0
 */
add_action( 'admin_enqueue_scripts', 'bizznis_load_admin_scripts' );
function bizznis_load_admin_scripts( $hook_suffix ) {
	# Only add thickbox/preview if there is an update to Bizznis available
	if ( bizznis_update_check() ) {
		add_thickbox();
		wp_enqueue_script( 'theme-preview' );
		bizznis_load_admin_js();
	}
	# If we're on a Bizznis admin screen
	if ( bizznis_is_menu_page( 'bizznis' ) ) {
		bizznis_load_admin_js();
	}
}

/**
 * Enqueues the custom script used in the admin, and localizes several strings or values used in the scripts.
 *
 * @since 1.0.0
 */
function bizznis_load_admin_js() {
	wp_enqueue_script( 'bizznis_admin_js', BIZZNIS_ADMIN_JS_URL . '/admin.js', array( 'jquery' ), PARENT_THEME_VERSION, true );
	# Strings
	$strings = array(
		'categoryChecklistToggle' => __( 'Select / Deselect All', 'bizznis' ),
		'saveAlert'               => __( 'The changes you made will be lost if you navigate away from this page.', 'bizznis' ),
		'confirmUpgrade'          => __( 'Updating Bizznis will overwrite the current installed version of Bizznis. Are you sure you want to update?. "Cancel" to stop, "OK" to update.', 'bizznis' ),
		'confirmReset'            => __( 'Are you sure you want to reset?', 'bizznis' ),
	);
	wp_localize_script( 'bizznis_admin_js', 'bizznisL10n', $strings );
	# Toggles
	$toggles = array();
	/*
	* Deprecated since 1.1.0
	*
	$toggles = array(
		// Checkboxes - when checked, show extra settings
		'nav'                       => array( '#bizznis-settings\\[nav\\]', '#bizznis_nav_settings', '_checked' ),
		'content_archive_thumbnail' => array( '#bizznis-settings\\[content_archive_thumbnail\\]', '#bizznis_image_size', '_checked' ),
		'nav_extras_enable'         => array( '#bizznis-settings\\[nav_extras_enable\\]', '#bizznis_nav_extras_settings', '_checked' ),
		// Select toggles
		'blog_title'                => array( '#bizznis-settings\\[blog_title\\]', '#bizznis_blog_title_image', 'image' ),
		'nav_extras'                => array( '#bizznis-settings\\[nav_extras\\]', '#bizznis_nav_extras_twitter', 'twitter' ),
		'content_archive'           => array( '#bizznis-settings\\[content_archive\\]', '#bizznis_content_limit_setting', 'full' ),
	);
	*/
	wp_localize_script( 'bizznis_admin_js', 'bizznis_toggles', apply_filters( 'bizznis_toggles', $toggles ) );
}