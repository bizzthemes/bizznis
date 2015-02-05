<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * This class initializes the parent theme definitions and loads all the parent theme /lib/ files
 *
 * @since 1.0.0
 */
final class Bizznis_Init {
	
	/**
	 * Fires up constants, WP supported functions and translation strings
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->constants();
		$this->theme_support();
		$this->post_type_support();
		$this->i18n();
	}
	
	/**
	 * Defines theme constants throughout the template
	 *
	 * @since 1.0.0
	 */
	private function constants() {
		# Theme Info
		define( 'PARENT_THEME_NAME', 			'Bizznis' );
		define( 'PARENT_THEME_VERSION', 		'1.1.6' );
		define( 'PARENT_THEME_BRANCH', 			'1.1' );
		define( 'PARENT_DB_VERSION', 			'1160' );
		define( 'PARENT_THEME_RELEASE_DATE', 	date_i18n( 'F j, Y', '1423569600' ) );
		# Directory Locations
		define( 'PARENT_DIR', 					get_template_directory() );
		define( 'CHILD_DIR', 					get_stylesheet_directory() );
		define( 'BIZZNIS_LIB_DIR', 				PARENT_DIR . 		'/lib' );
		define( 'BIZZNIS_IMAGES_DIR', 			PARENT_DIR . 		'/images' );
		define( 'BIZZNIS_ADMIN_DIR', 			BIZZNIS_LIB_DIR . 	'/admin' );
		define( 'BIZZNIS_ADMIN_IMAGES_DIR', 	BIZZNIS_ADMIN_DIR . '/images' );
		define( 'BIZZNIS_ADMIN_CSS_DIR', 		BIZZNIS_ADMIN_DIR . '/css' );
		define( 'BIZZNIS_ADMIN_JS_DIR', 		BIZZNIS_ADMIN_DIR . '/js' );
		define( 'BIZZNIS_CLASSES_DIR', 			BIZZNIS_LIB_DIR . 	'/classes' );
		define( 'BIZZNIS_FUNCTIONS_DIR', 		BIZZNIS_LIB_DIR . 	'/functions' );
		define( 'BIZZNIS_FRONTEND_DIR', 		BIZZNIS_LIB_DIR . 	'/frontend' );
		define( 'BIZZNIS_WIDGETS_DIR', 			BIZZNIS_LIB_DIR . 	'/widgets' );
		define( 'BIZZNIS_INT_DIR', 				BIZZNIS_LIB_DIR . 	'/integrations' );
		define( 'BIZZNIS_INT_WC_DIR', 			BIZZNIS_INT_DIR . 	'/woocommerce' );
		define( 'BIZZNIS_INT_BBP_DIR', 			BIZZNIS_INT_DIR . 	'/bbpress' );
		define( 'BIZZNIS_INT_JP_DIR', 			BIZZNIS_INT_DIR . 	'/jetpack' );
		# URL Locations
		define( 'PARENT_URL', 					get_template_directory_uri() );
		define( 'CHILD_URL', 					get_stylesheet_directory_uri() );
		define( 'BIZZNIS_LIB_URL', 				PARENT_URL . 		'/lib' );
		define( 'BIZZNIS_IMAGES_URL', 			PARENT_URL . 		'/images' );
		define( 'BIZZNIS_ADMIN_URL', 			BIZZNIS_LIB_URL . 	'/admin' );
		define( 'BIZZNIS_ADMIN_IMAGES_URL', 	BIZZNIS_ADMIN_URL . '/images' );
		define( 'BIZZNIS_ADMIN_CSS_URL', 		BIZZNIS_ADMIN_URL . '/css' );
		define( 'BIZZNIS_ADMIN_JS_URL', 		BIZZNIS_ADMIN_URL . '/js' );
		define( 'BIZZNIS_CLASSES_URL', 			BIZZNIS_LIB_URL . 	'/classes' );
		define( 'BIZZNIS_FUNCTIONS_URL', 		BIZZNIS_LIB_URL . 	'/functions' );
		define( 'BIZZNIS_SHORTCODES_URL', 		BIZZNIS_LIB_URL . 	'/shortcodes' );
		define( 'BIZZNIS_FRONTEND_URL', 		BIZZNIS_LIB_URL . 	'/frontend' );
		define( 'BIZZNIS_WIDGETS_URL', 			BIZZNIS_LIB_URL . 	'/widgets' );
		define( 'BIZZNIS_INT_URL', 				BIZZNIS_LIB_URL . 	'/integrations' );
		define( 'BIZZNIS_INT_WC_URL', 			BIZZNIS_INT_URL . 	'/woocommerce' );
		define( 'BIZZNIS_INT_BBP_URL', 			BIZZNIS_INT_URL . 	'/bbpress' );
		define( 'BIZZNIS_INT_JP_URL', 			BIZZNIS_INT_URL . 	'/jetpack' );
		# Settings Field (for DB storage)
		define( 'BIZZNIS_SETTINGS_FIELD', 		apply_filters( 'bizznis_settings_field', 'bizznis-settings' ) );
	}
	
	/**
	 * Activates default theme features.
	 *
	 * @since 1.0.0
	 */
	public function theme_support() {
		add_theme_support( 'menus' ); #wp
		add_theme_support( 'post-thumbnails' ); #wp
		add_theme_support( 'automatic-feed-links' ); #wp
		add_theme_support( 'title-tag' ); #wp
		add_theme_support( 'html5', array( #wp
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );
		add_theme_support( 'bizznis-inpost-layouts' );
		add_theme_support( 'bizznis-archive-layouts' );
		add_theme_support( 'bizznis-breadcrumbs' );
		add_theme_support( 'bizznis-responsive-viewport' ); #html5
		# Maybe add support for Bizznis menus
	if ( ! current_theme_supports( 'bizznis-menus' ) ) {
		add_theme_support( 'bizznis-menus', array(
			'primary'   => __( 'Primary Navigation Menu', 'bizznis' ),
			'secondary' => __( 'Secondary Navigation Menu', 'bizznis' ),
		) );
	}
		# Turn on footer widgets if Bizznis is active
		# Turn on custom header image / custom logo
		# Turn on custom background image / color
	if ( ! is_child_theme() ) {
		add_theme_support( 'custom-background', array(
			'default-color' => 'f5f5f5'
		) );
		# add_theme_support( 'custom-header' );
		add_theme_support( 'bizznis-footer-widgets', 4 );
		add_theme_support( 'bizznis-after-entry-widgets' );
		add_editor_style();
	}
	}
	
	/**
	 * Initialize post type support for Bizznis features (Layout selector, SEO, Scripts).
	 *
	 * @since 1.0.0
	 */
	public function post_type_support() {
		add_post_type_support( 'post', array( 'bizznis-scripts', 'bizznis-layouts', 'bizznis-rel-author' ) );
		add_post_type_support( 'page', array( 'bizznis-scripts', 'bizznis-layouts' ) );
	}
	
	/**
	 * Load the Bizznis textdomain for internationalization.
	 *
	 * @since 1.0.0
	 */
	public function i18n() {
		# Load from WP languages directory
		if ( $loaded = load_theme_textdomain( 'bizznis', trailingslashit( WP_LANG_DIR ) . 'bizznis' ) ) {
			define( 'BIZZNIS_LANGUAGES_DIR', trailingslashit( WP_LANG_DIR ) . 'bizznis' );
		}
		# So we can define with a child theme
		if ( ! defined( 'BIZZNIS_LANGUAGES_DIR' ) ) {
			define( 'BIZZNIS_LANGUAGES_DIR', get_template_directory() . '/lib/languages' );
		}
		load_theme_textdomain( 'bizznis', BIZZNIS_LANGUAGES_DIR );
	}
	
	/**
	 * Loads all the parent theme files
	 *
	 * @since 1.0.0
	 */
	public function launch() {
		# Run the hook before bizznis framework is loaded
		do_action( 'bizznis_pre_load' );
		# Stop here, if necessary
	if ( defined( 'BIZZNIS_LOAD_FRAMEWORK' ) && BIZZNIS_LOAD_FRAMEWORK === false ) {
		return;
	}
		# Stop here, if WP is old
	if ( version_compare( $GLOBALS['wp_version'], '3.6', '<' ) ) {
		load_template( BIZZNIS_FUNCTIONS_DIR . '/back-compat.php' );
	}
		# Load Theme
		load_template( BIZZNIS_FRONTEND_DIR . '/structure.php' );
		load_template( BIZZNIS_FRONTEND_DIR . '/header.php' );
		load_template( BIZZNIS_FRONTEND_DIR . '/footer.php' );
		load_template( BIZZNIS_FRONTEND_DIR . '/menu.php' );
		load_template( BIZZNIS_FRONTEND_DIR . '/layouts.php' );
		load_template( BIZZNIS_FRONTEND_DIR . '/post.php' );
		load_template( BIZZNIS_FRONTEND_DIR . '/loops.php' );
		load_template( BIZZNIS_FRONTEND_DIR . '/comments.php' );
		load_template( BIZZNIS_FRONTEND_DIR . '/widget-area.php' );
		load_template( BIZZNIS_FRONTEND_DIR . '/archive.php' );
		load_template( BIZZNIS_FRONTEND_DIR . '/search.php' );
		# Load Admin
	if ( is_admin() ) {
		load_template( BIZZNIS_ADMIN_DIR . '/admin.php' );
		load_template( BIZZNIS_ADMIN_DIR . '/upgrade.php' );
	}	
		load_template( BIZZNIS_ADMIN_DIR . '/meta-inpost.php' );
		load_template( BIZZNIS_ADMIN_DIR . '/meta-term.php' );
		load_template( BIZZNIS_ADMIN_DIR . '/meta-user.php' );
		# Load Customizer
		load_template( BIZZNIS_ADMIN_DIR . '/customizer.php' );
		load_template( BIZZNIS_ADMIN_DIR . '/customizer-controls.php' );
		load_template( BIZZNIS_ADMIN_DIR . '/customizer-css.php' );
		load_template( BIZZNIS_ADMIN_DIR . '/customizer-display.php' );
		# Load Functions
		require_if_theme_supports( 'bizznis-breadcrumbs', BIZZNIS_FUNCTIONS_DIR . '/breadcrumb.php' ); #optional
		load_template( BIZZNIS_FUNCTIONS_DIR . '/compat.php' );
		load_template( BIZZNIS_FUNCTIONS_DIR . '/sanitization.php' );
		load_template( BIZZNIS_FUNCTIONS_DIR . '/general.php' );
		load_template( BIZZNIS_FUNCTIONS_DIR . '/options.php' );
		load_template( BIZZNIS_FUNCTIONS_DIR . '/image.php' );
		load_template( BIZZNIS_FUNCTIONS_DIR . '/markup.php' );
		load_template( BIZZNIS_FUNCTIONS_DIR . '/layout.php' );
		load_template( BIZZNIS_FUNCTIONS_DIR . '/formatting.php' );
		load_template( BIZZNIS_FUNCTIONS_DIR . '/widgetize.php' );
		load_template( BIZZNIS_FUNCTIONS_DIR . '/shortcodes.php' );
		load_template( BIZZNIS_FUNCTIONS_DIR . '/deprecated.php' );
		load_template( BIZZNIS_FUNCTIONS_DIR . '/menu.php' );
		# Load Widgets
		load_template( BIZZNIS_WIDGETS_DIR 	 . '/widgets.php' );
		# Load Integrations
		load_template( BIZZNIS_INT_WC_DIR 	 . '/woocommerce.php' );
		load_template( BIZZNIS_INT_BBP_DIR 	 . '/bbpress.php' );
		load_template( BIZZNIS_INT_JP_DIR 	 . '/jetpack.php' );
		# Load Javascript
		load_template( BIZZNIS_FUNCTIONS_DIR . '/load-scripts.php' );
		# Load CSS
		load_template( BIZZNIS_FUNCTIONS_DIR . '/load-styles.php' );
		# Load allowed tags
		global $_bizznis_formatting_allowedtags;
		$_bizznis_formatting_allowedtags = bizznis_formatting_allowedtags();
	}

}

/**
 * Launch the bizznis class
 *
 * @since 1.0.0
 */
add_action( 'bizznis_init', 'bizznis_launch' );
function bizznis_launch() {
	$bizznis = new Bizznis_Init; #ready, set, go!
	$bizznis->launch();
}

//* This is the hook to rule them all
do_action( 'bizznis_init' );