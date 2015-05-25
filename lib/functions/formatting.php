<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Return a phrase shortened in length to a maximum number of characters.
 *
 * @since 1.0.0
 */
function bizznis_truncate_phrase( $text, $max_characters ) {
	$text = trim( $text );
	if ( strlen( $text ) > $max_characters ) {
		# Truncate $text to $max_characters + 1
		$text = mb_substr( $text, 0, $max_characters + 1 );
		# Truncate to the last space in the truncated string
		$text = trim( mb_substr( $text, 0, strrpos( $text, ' ' ) ) );
	}
	return $text;
}

/**
 * Return content stripped down and limited content.
 *
 * @since 1.0.0
 */
function get_the_content_limit( $max_characters, $more_link_text = '(more...)', $stripteaser = false ) {
	$content = get_the_content( '', $stripteaser );
	# Strip tags and shortcodes so the content truncation count is done correctly
	$content = strip_tags( strip_shortcodes( $content ), apply_filters( 'get_the_content_limit_allowedtags', '<script>,<style>' ) );
	# Remove inline styles / scripts
	$content = trim( preg_replace( '#<(s(cript|tyle)).*?</\1>#si', '', $content ) );
	# Truncate $content to $max_char
	$content = bizznis_truncate_phrase( $content, $max_characters );
	# More link?
	if ( $more_link_text ) {
		$link   = apply_filters( 'get_the_content_more_link', sprintf( '&#x02026; <a href="%s" class="more-link">%s</a>', get_permalink(), bizznis_a11y_more_link( $more_link_text ) ), $more_link_text );
		$output = sprintf( '<p>%s %s</p>', $content, $link );
	} else {
		$output = sprintf( '<p>%s</p>', $content );
		$link = '';
	}
	return apply_filters( 'get_the_content_limit', $output, $content, $link, $max_characters );
}

/**
 * Return more link text plus hidden title for screen readers, to improve accessibility.
 *
 * @since 1.2.0
 *
 * @param string  $more_link_text Text of the more link.
 * @return string $more_link_text with or withput the hidden title.
 */
function bizznis_a11y_more_link( $more_link_text )  {
	if ( bizznis_a11y() && ! empty( $more_link_text ) ) {
		$more_link_text .= ' <span class="screen-reader-text">' . __( 'about ', 'bizznis' ) . get_the_title() . '</span>';
	}
	
	return $more_link_text;
}

/**
 * Echo the limited content.
 *
 * @since 1.0.0
 */
function the_content_limit( $max_characters, $more_link_text = '(more...)', $stripteaser = false ) {
	$content = get_the_content_limit( $max_characters, $more_link_text, $stripteaser );
	echo apply_filters( 'the_content_limit', $content );
}

/**
 * Add 'rel="nofollow"' attribute and value to links within string passed in.
 *
 * @since 1.0.0
 */
function bizznis_rel_nofollow( $text ) {
	$text = bizznis_strip_attr( $text, 'a', 'rel' );
	return stripslashes( wp_rel_nofollow( $text ) );
}

/**
 * Sanitize multiple HTML classes in one pass.
 *
 * @since 1.0.0
 */
function bizznis_sanitize_html_classes( $classes, $return_format = 'input' ) {
	if ( 'input' == $return_format ) {
		$return_format = is_array( $classes ) ? 'array' : 'string';
	}
	$classes = is_array( $classes ) ? $classes : explode( ' ', $classes );
	$sanitized_classes = array_map( 'sanitize_html_class', $classes );
	if ( 'array' == $return_format ) {
		return $sanitized_classes;
	}
	else {
		return implode( ' ', $sanitized_classes );
	}
}

/**
 * Return an array of allowed tags for output formatting. Mainly used by 'wp_kses()' for sanitizing output.
 *
 * @since 1.0.0
 */
function bizznis_formatting_allowedtags() {
	return apply_filters(
		'bizznis_formatting_allowedtags',
		array(
			'a'          => array( 'href' => array(), 'title' => array(), ),
			'b'          => array(),
			'blockquote' => array(),
			'br'         => array(),
			'div'        => array( 'align' => array(), 'class' => array(), 'style' => array(), ),
			'em'         => array(),
			'i'          => array(),
			'p'          => array( 'align' => array(), 'class' => array(), 'style' => array(), ),
			'span'       => array( 'align' => array(), 'class' => array(), 'style' => array(), ),
			'strong'     => array(),
			# <img src="" class="" alt="" title="" width="" height="" />
			// 'img'        => array( 'src' => array(), 'class' => array(), 'alt' => array(), 'width' => array(), 'height' => array(), 'style' => array() ),
		)
	);
}

/**
 * Wrapper for 'wp_kses()' that can be used as a filter function.
 *
 * @since 1.0.0
 */
function bizznis_formatting_kses( $string ) {
	return wp_kses( $string, bizznis_formatting_allowedtags() );
}

/**
 * Calculate the time difference - a replacement for 'human_time_diff()' until it is improved.
 *
 * @since 1.0.0
 */
function bizznis_human_time_diff( $older_date, $newer_date = false ) {
	# If no newer date is given, assume now
	$newer_date = $newer_date ? $newer_date : time();
	# Difference in seconds
	$since = absint( $newer_date - $older_date );
	if ( ! $since ) {
		return '0 ' . _x( 'seconds', 'time difference', 'bizznis' );
	}
	# Hold units of time in seconds, and their pluralised strings (not translated yet)
	$units = array(
		array( 31536000, _nx_noop( '%s year', '%s years', 'time difference' ) ),  // 60 * 60 * 24 * 365
		array( 2592000, _nx_noop( '%s month', '%s months', 'time difference' ) ), // 60 * 60 * 24 * 30
		array( 604800, _nx_noop( '%s week', '%s weeks', 'time difference' ) ),    // 60 * 60 * 24 * 7
		array( 86400, _nx_noop( '%s day', '%s days', 'time difference' ) ),       // 60 * 60 * 24
		array( 3600, _nx_noop( '%s hour', '%s hours', 'time difference' ) ),      // 60 * 60
		array( 60, _nx_noop( '%s minute', '%s minutes', 'time difference' ) ),
		array( 1, _nx_noop( '%s second', '%s seconds', 'time difference' ) ),
	);
	# Step one: the first unit
	for ( $i = 0, $j = count( $units ); $i < $j; $i++ ) {
		$seconds = $units[$i][0];
		# Finding the biggest chunk (if the chunk fits, break)
		if ( ( $count = floor( $since / $seconds ) ) != 0 ) {
			break;
		}
	}
	# Translate unit string, and add to the output
	$output = sprintf( translate_nooped_plural( $units[$i][1], $count, 'bizznis' ), $count );
	# Note the next unit
	$ii = $i + 1;
	# Step two: the second unit
	if ( $ii < $j ) {
		$seconds2 = $units[$ii][0];
		# Check if this second unit has a value > 0
		if ( ( $count2 = floor( ( $since - ( $seconds * $count ) ) / $seconds2 ) ) != 0 ) {
			# Add translated separator string, and translated unit string
			$output .= sprintf( ' %s ' . translate_nooped_plural( $units[$ii][1], $count2, 'bizznis' ),	_x( 'and', 'separator in time difference', 'bizznis' ),	$count2	);
		}
	}
	return $output;
}

/**
 * Mark up content with code tags. Escapes all HTML, so '<' gets changed to '&lt;' and displays correctly.
 *
 * @since 1.0.0
 */
function bizznis_code( $content ) {
	return '<code>' . esc_html( $content ) . '</code>';
}

/**
 * Sanitizes a hex color.
 *
 * This is a copy of the core function for use when the customizer is not being shown.
 *
 * @since  1.1.1.
 *
 * @param  string         $color    The proposed color.
 * @return string|null              The sanitized color.
 */
if ( ! function_exists( 'sanitize_hex_color' ) ) :
function sanitize_hex_color( $color ) {
	if ( '' === $color ) {
		return '';
	}
	# 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
		return $color;
	}
	return null;
}
endif;

/**
 * Sanitizes a hex color without a hash. Use sanitize_hex_color() when possible.
 *
 * This is a copy of the core function for use when the customizer is not being shown.
 *
 * @since  1.1.1.
 *
 * @param  string         $color    The proposed color.
 * @return string|null              The sanitized color.
 */
if ( ! function_exists( 'sanitize_hex_color_no_hash' ) ) :
function sanitize_hex_color_no_hash( $color ) {
	$color = ltrim( $color, '#' );
	if ( '' === $color ) {
		return '';
	}
	return sanitize_hex_color( '#' . $color ) ? $color : null;
}
endif;

/**
 * Ensures that any hex color is properly hashed.
 *
 * This is a copy of the core function for use when the customizer is not being shown.
 *
 * @since  1.1.1.
 *
 * @param  string         $color    The proposed color.
 * @return string|null              The sanitized color.
 */
if ( ! function_exists( 'maybe_hash_hex_color' ) ) :
function maybe_hash_hex_color( $color ) {
	if ( $unhashed = sanitize_hex_color_no_hash( $color ) ) {
		return '#' . $unhashed;
	}
	return $color;
}
endif;
