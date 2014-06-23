<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

add_action( 'bizznis_footer_top', 'bizznis_footer_widget_areas', 5 );
/**
 * Echo the markup necessary to facilitate the footer widget areas.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_footer_widget_areas' ) ) :
function bizznis_footer_widget_areas() {
	$footer_widgets = get_theme_support( 'bizznis-footer-widgets' );
	if ( ! $footer_widgets || ! isset( $footer_widgets[0] ) || ! is_numeric( $footer_widgets[0] ) ) {
		return;
	}
	$footer_widgets = (int) $footer_widgets[0];
	# Check to see if first widget area has widgets. If not, do nothing. No need to check all footer widget areas.
	if ( ! is_active_sidebar( 'footer-1' ) ) {
		return;
	}
	$inside  = '';
	$output  = '';
	$counter = 1;
	while ( $counter <= $footer_widgets ) {
		# Darn you, WordPress! Gotta output buffer.
		ob_start();
		dynamic_sidebar( 'footer-' . $counter );
		$widgets = ob_get_clean();
		$inside .= sprintf( '<div class="footer-widgets-%d widget-area">%s</div>', $counter, $widgets );
		$counter++;
	}
	if ( $inside ) {
		$output .= sprintf( '<div %s>', bizznis_attr( 'footer-widgets' ) );
		$output .= sprintf( '<div %s>', bizznis_attr( 'footer-widgets-container', array( 'class' => 'wrap' ) ) );
		$output .= $inside;
		$output .= '</div>';
		$output .= '</div>';
	}
	echo apply_filters( 'bizznis_footer_widget_areas', $output, $footer_widgets );
}
endif;

add_filter( 'bizznis_footer_output', 'do_shortcode', 20 );
add_action( 'bizznis_footer_inner', 'bizznis_footer_credits' );
/**
 * Echo the markup necessary to facilitate the footer credits.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_footer_credits' ) ) :
function bizznis_footer_credits() {
	# Build the text strings. Includes shortcodes
	$backtotop_text = '[footer_backtotop]';
	$creds_text     = sprintf( '[footer_copyright before="%s "] &#x000B7; [footer_childtheme_link before="" after=" %s"] [footer_bizzthemes_link url="http://www.bizzthemes.com/" before="%s "] &#x000B7; [footer_wordpress_link] &#x000B7; [footer_loginout]', __( 'Copyright', 'bizznis' ), __( 'on', 'bizznis' ), __( 'by', 'bizznis' ) );
	# Filter the text strings
	$backtotop_text = apply_filters( 'bizznis_footer_backtotop_text', $backtotop_text );
	$creds_text     = apply_filters( 'bizznis_footer_creds_text', $creds_text );
	$output = sprintf( '<div %s>', bizznis_attr( 'footer-creds' ) );
	$output .= sprintf( '<div %s>', bizznis_attr( 'footer-creds-container', array( 'class' => 'wrap' ) ) );
	$output .= '<p>' . $creds_text . '</p>';
	$output .= '</div>';
	$output .= '</div>';
	echo apply_filters( 'bizznis_footer_output', $output, $backtotop_text, $creds_text );
}
endif;

add_filter( 'bizznis_footer_scripts', 'do_shortcode' );
add_action( 'wp_footer', 'bizznis_footer_scripts' );
/**
 * Echo the footer scripts, defined in Theme Settings.
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'bizznis_footer_scripts' ) ) :
function bizznis_footer_scripts() {
	echo apply_filters( 'bizznis_footer_scripts', bizznis_option( 'footer_scripts' ) );
}
endif;
