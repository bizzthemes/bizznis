<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

add_action( 'wp_head', 'bizznis_do_title', 1 );
/**
 * Output the title, wrapped in title tags.
 *
 * @since 1.1.1
 */
if ( ! function_exists( 'bizznis_do_title' ) ) :
function bizznis_do_title() {
	echo '<title>';
	wp_title();
	echo '</title>' . "\n";
}
endif;

add_filter( 'wp_title', 'bizznis_default_title', 10, 3 );
/**
 * Return filtered post title.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_default_title' ) ) :
function bizznis_default_title( $title, $sep, $seplocation ) {
	if ( is_front_page() ) {
		#* Determine the doctitle
		$title = get_bloginfo( 'name' );
		#* Append site description, if necessary
		$title = $title . " - " . get_bloginfo( 'description' );
	}
	return esc_html( trim( $title ) );
}
endif;

add_action( 'wp_head', 'bizznis_responsive_viewport', 1 );
/**
 * Optionally output the responsive CSS viewport tag.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_responsive_viewport' ) ) :
function bizznis_responsive_viewport() {
	# Child theme needs to support 'bizznis-responsive-viewport'.
	if ( ! current_theme_supports( 'bizznis-responsive-viewport' ) ) {
		return;
	}
	echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
}
endif;

add_action( 'wp_head', 'bizznis_load_favicon' );
/**
 * Echo favicon link if one is found.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_load_favicon' ) ) :
function bizznis_load_favicon( $favicon = '' ) {
	# Allow child theme to short-circuit this function
	$pre = apply_filters( 'bizznis_pre_load_favicon', false );
	if ( $pre !== false ) {
		$favicon = $pre;
	}
	elseif ( file_exists( CHILD_DIR . '/images/favicon.ico' ) ) {
		$favicon = CHILD_URL . '/images/favicon.ico';
	}
	elseif ( file_exists( CHILD_DIR . '/images/favicon.gif' ) ) {
		$favicon = CHILD_URL . '/images/favicon.gif';
	}
	elseif ( file_exists( CHILD_DIR . '/images/favicon.png' ) ) {
		$favicon = CHILD_URL . '/images/favicon.png';
	}
	elseif ( file_exists( CHILD_DIR . '/images/favicon.jpg' ) ) {
		$favicon = CHILD_URL . '/images/favicon.jpg';
	}
	# URL to favicon is filtered via 'bizznis_favicon_url' before being echoed.
	$favicon = apply_filters( 'bizznis_favicon_url', $favicon );
	if ( $favicon ) {
		echo '<link rel="Shortcut Icon" href="' . esc_url( $favicon ) . '" type="image/x-icon" />' . "\n";
	}
}
endif;

add_action( 'wp_head', 'bizznis_do_meta_pingback' );
/**
 * Adds the pingback meta tag to the head so that other sites can know how to send a pingback to our site.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_do_meta_pingback' ) ) :
function bizznis_do_meta_pingback() {
	echo '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" />' . "\n";
}
endif;

add_action( 'wp_head', 'bizznis_rel_author' );
/**
 * Echo custom rel="author" link tag.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_rel_author' ) ) :
function bizznis_rel_author() {
	$post = get_post();
	# If the appropriate information has been entered for an individual post/page
	if ( is_singular() && post_type_supports( $post->post_type, 'bizznis-rel-author' ) && isset( $post->post_author ) && $gplus_url = get_user_option( 'googleplus', $post->post_author ) ) {
		printf( '<link rel="author" href="%s" />' . "\n", esc_url( $gplus_url ) );
		return;
	}
	# If the appropriate information has been entered for an individual author archive
	if ( is_author() && get_query_var( 'author' ) && $gplus_url = get_user_option( 'googleplus', get_query_var( 'author' ) ) ) {
		printf( '<link rel="author" href="%s" />' . "\n", esc_url( $gplus_url ) );
		return;
	}
}
endif;

add_filter( 'bizznis_header_scripts', 'do_shortcode' );
add_action( 'wp_head', 'bizznis_header_scripts' );
/**
 * Echo header scripts in to wp_head().
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_header_scripts' ) ) :
function bizznis_header_scripts() {
	# Applies 'bizznis_header_scripts' filter on value stored in header_scripts setting.
	echo apply_filters( 'bizznis_header_scripts', bizznis_get_option( 'header_scripts' ) );
	# If singular, echo scripts from custom field
	if ( is_singular() ) {
		bizznis_custom_field( '_bizznis_scripts' );
	}
}
endif;

add_action( 'after_setup_theme', 'bizznis_custom_header' );
/**
 * Activate the custom header feature.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_custom_header' ) ) :
function bizznis_custom_header() {
	$wp_custom_header = get_theme_support( 'custom-header' );
	# Stop here if not active (Bizznis of WP custom header)
	if ( ! $wp_custom_header ) {
		return;
	}
	# Blog title option is obsolete when custom header is active
	add_filter( 'bizznis_pre_get_option_blog_title', '__return_empty_array' );
}
endif;

add_action( 'bizznis_css', 'bizznis_custom_header_style' );
/**
 * Custom header callback.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_custom_header_style' ) ) :
function bizznis_custom_header_style() {
	# Stop here if custom header not supported
	if ( ! current_theme_supports( 'custom-header' ) ) {
		return;
	}
	
	# Stop here if user specifies their own callback
	if ( get_theme_support( 'custom-header', 'wp-head-callback' ) ) {
		return;
	}
	
	# Output nothing if header style isn't present.
	$output = '';
	$header_image = get_header_image();
	$text_color   = get_header_textcolor();
	
	# If no options set, don't waste the output. Stop here.
	if ( empty( $header_image ) && ! display_header_text() && $text_color == get_theme_support( 'custom-header', 'default-text-color' ) ) {
		return;
	}
	$header_selector = get_theme_support( 'custom-header', 'header-selector' );
	$title_selector  = '.custom-header .site-title';
	$desc_selector   = '.custom-header .site-description';
	
	# Header selector fallback
	if ( ! $header_selector ) {
		$header_selector = '.custom-header .title-area';
	}
	
	# Header image CSS, if exists
	if ( $header_image ) {
		
		bizznis_get_css()->add( array(
			'selectors'    => array( $header_selector ),
			'declarations' => array(
				'background-image'    => 'url("' . esc_url( $header_image ) . '")',
				'background-repeat'   => 'no-repeat',
				'background-position' => 'center center',
				'min-width'			  => get_custom_header()->width . 'px',
				'min-height'		  => get_custom_header()->height . 'px',
			)
		) );
		
		bizznis_get_css()->add( array(
			'selectors'    => array( $title_selector . ' a' ),
			'declarations' => array(
				'min-width'			  => get_custom_header()->width . 'px',
				'min-height'		  => get_custom_header()->height . 'px',
			)
		) );
	
	}
	# Header text color CSS, if showing text
	if ( display_header_text() && $text_color != get_theme_support( 'custom-header', 'default-text-color' ) ) {
		
		bizznis_get_css()->add( array(
			'selectors'    => array( $title_selector . ' a', $title_selector . ' a:hover', $desc_selector ),
			'declarations' => array(
				'color'		  		 => bizznis_add_string_filter( 'maybe_hash_hex_color', $text_color ),
			)
		) );

	}

}
endif;

add_action( 'bizznis_site_title', 'bizznis_site_title' );
/**
 * Echo the site title into the header.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_site_title' ) ) :
function bizznis_site_title() {
	# Stop here if title is hiden
	if ( bizznis_get_option( 'hide_site_title' ) ) {
		return;
	}
	# Set what goes inside the wrapping tags
	$inside = sprintf( '<a href="%s">%s</a>', trailingslashit( home_url() ), get_bloginfo( 'name' ) );
	# Determine which wrapping tags to use
	$wrap = is_home() ? 'h1' : 'p';
	$wrap = apply_filters( 'bizznis_semantic_title_wrap', $wrap );
	# Build the title
	$title  = sprintf( "<{$wrap} %s>", bizznis_attr( 'site-title' ) ). $inside ."</{$wrap}>";
	# Echo (filtered)
	echo apply_filters( 'bizznis_seo_title', $title, $inside, $wrap );
}
endif;

add_action( 'bizznis_site_title', 'bizznis_site_description' );
/**
 * Echo the site description into the header.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_site_description' ) ) :
function bizznis_site_description() {
	# Stop here if tagline is hiden
	if ( bizznis_get_option( 'hide_tagline' ) ) {
		return;
	}
	# Set what goes inside the wrapping tags
	$inside = esc_html( get_bloginfo( 'description' ) );
	# Determine which wrapping tags to use
	$wrap = apply_filters( 'bizznis_semantic_description_wrap', 'p' );
	# Build the description
	$description  = sprintf( "<{$wrap} %s>", bizznis_attr( 'site-description' ) ). $inside ."</{$wrap}>";
	# Output (filtered)
	$output = $inside ? apply_filters( 'bizznis_seo_description', $description, $inside, $wrap ) : '';
	echo $output;
}
endif;

// add_filter( 'bizznis_show_header_content', 'bizznis_hide_header_content' );
/**
 * Hide header content on all pages except homepage
 *
 * Optionally call inside a Child theme
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_hide_header_content' ) ) :
function bizznis_hide_header_content() {
	# hide header content on all pages except homepage and when primary menu location is not active
	if ( is_home() || is_front_page() || ! has_nav_menu( 'primary' ) ) {
		return true;
	}
}
endif;
