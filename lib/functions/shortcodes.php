<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Produces the date of post publication.
 *
 * @since 1.0.0
 */
add_shortcode( 'post_date', 'bizznis_post_date_shortcode' );
function bizznis_post_date_shortcode( $atts ) {
	$defaults = array(
		'after'  => '',
		'before' => '',
		'format' => get_option( 'date_format' ),
		'label'  => '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'post_date' );
	$display = ( 'relative' == $atts['format'] ) ? bizznis_human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) . ' ' . __( 'ago', 'bizznis' ) : get_the_time( $atts['format'] );
	$output = sprintf( '<time %s>', bizznis_attr( 'entry-time' ) ) . $atts['before'] . $atts['label'] . $display . $atts['after'] . '</time>';
	return apply_filters( 'bizznis_post_date_shortcode', $output, $atts );
}

/**
 * Produces the time of post publication.
 *
 * @since 1.0.0
 */
add_shortcode( 'post_time', 'bizznis_post_time_shortcode' );
function bizznis_post_time_shortcode( $atts ) {
	$defaults = array(
		'after'  => '',
		'before' => '',
		'format' => get_option( 'time_format' ),
		'label'  => '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'post_time' );
	$output = sprintf( '<time %s>', bizznis_attr( 'entry-time' ) ) . $atts['before'] . $atts['label'] . get_the_time( $atts['format'] ) . $atts['after'] . '</time>';
	return apply_filters( 'bizznis_post_time_shortcode', $output, $atts );
}

/**
 * Produces the author of the post (unlinked display name).
 *
 * @since 1.0.0
 */
add_shortcode( 'post_author', 'bizznis_post_author_shortcode' );
function bizznis_post_author_shortcode( $atts ) {
	$defaults = array(
		'after'  => '',
		'before' => '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'post_author' );
	$author = get_the_author();
	$output  = sprintf( '<span %s>', bizznis_attr( 'entry-author' ) );
	$output .= $atts['before'];
	$output .= sprintf( '<span %s>', bizznis_attr( 'entry-author-name' ) ) . esc_html( $author ) . '</span>';
	$output .= $atts['after'];
	$output .= '</span>';
	return apply_filters( 'bizznis_post_author_shortcode', $output, $atts );
}

/**
 * Produces the author of the post (link to author URL).
 *
 * @since 1.0.0
 */
add_shortcode( 'post_author_link', 'bizznis_post_author_link_shortcode' );
function bizznis_post_author_link_shortcode( $atts ) {
	$defaults = array(
		'after'    => '',
		'before'   => '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'post_author_link' );
	$url = get_the_author_meta( 'url' );
	# If no url, use post author shortcode function.
	if ( ! $url ) {
		return bizznis_post_author_shortcode( $atts );
	}
	$author = get_the_author();
	$output  = sprintf( '<span %s>', bizznis_attr( 'entry-author' ) );
	$output .= $atts['before'];
	$output .= sprintf( '<a href="%s" %s>', $url, bizznis_attr( 'entry-author-link' ) );
	$output .= sprintf( '<span %s>', bizznis_attr( 'entry-author-name' ) );
	$output .= esc_html( $author );
	$output .= '</span></a>' . $atts['after'] . '</span>';
	return apply_filters( 'bizznis_post_author_link_shortcode', $output, $atts );
}

/**
 * Produces the author of the post (link to author archive).
 *
 * @since 1.0.0
 */
add_shortcode( 'post_author_posts_link', 'bizznis_post_author_posts_link_shortcode' );
function bizznis_post_author_posts_link_shortcode( $atts ) {
	$defaults = array(
		'after'  => '',
		'before' => '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'post_author_posts_link' );
	$author = get_the_author();
	$url    = get_author_posts_url( get_the_author_meta( 'ID' ) );
	$output  = sprintf( '<span %s>', bizznis_attr( 'entry-author' ) );
	$output .= $atts['before'];
	$output .= sprintf( '<a href="%s" %s>', $url, bizznis_attr( 'entry-author-link' ) );
	$output .= sprintf( '<span %s>', bizznis_attr( 'entry-author-name' ) );
	$output .= esc_html( $author );
	$output .= '</span></a>' . $atts['after'] . '</span>';
	return apply_filters( 'bizznis_post_author_posts_link_shortcode', $output, $atts );
}

/**
 * Produces the link to the current post comments.
 *
 * @since 1.0.0
 */
add_shortcode( 'post_comments', 'bizznis_post_comments_shortcode' );
function bizznis_post_comments_shortcode( $atts ) {
	$defaults = array(
		'after'       => '',
		'before'      => '',
		'hide_if_off' => 'enabled',
		'more'        => __( '% Comments', 'bizznis' ),
		'one'         => __( '1 Comment', 'bizznis' ),
		'zero'        => __( 'Leave a Comment', 'bizznis' ),
	);
	$atts = shortcode_atts( $defaults, $atts, 'post_comments' );
	if ( ( ! bizznis_get_option( 'comments_posts' ) || ! comments_open() ) && 'enabled' === $atts['hide_if_off'] ) {
		return;
	}
	# Darn you, WordPress!
	ob_start();
	comments_number( $atts['zero'], $atts['one'], $atts['more'] );
	$comments = ob_get_clean();
	$comments = sprintf( '<a href="%s">%s</a>', get_comments_link(), $comments );
	$output = sprintf( '<span class="entry-comments-link">' . $atts['before'] . $comments . $atts['after'] . '</span>' );
	return apply_filters( 'bizznis_post_comments_shortcode', $output, $atts );
}

/**
 * Produces the tag links list.
 *
 * @since 1.0.0
 */
add_shortcode( 'post_tags', 'bizznis_post_tags_shortcode' );
function bizznis_post_tags_shortcode( $atts ) {
	$defaults = array(
		'after'  => '',
		'before' => __( 'Tagged With: ', 'bizznis' ),
		'sep'    => ', ',
	);
	$atts = shortcode_atts( $defaults, $atts, 'post_tags' );
	$tags = get_the_tag_list( $atts['before'], trim( $atts['sep'] ) . ' ', $atts['after'] );
	# Stop here if no tags
	if ( ! $tags ) {
		return;
	}
	$output = sprintf( '<span %s>', bizznis_attr( 'entry-tags' ) ) . $tags . '</span>';
	return apply_filters( 'bizznis_post_tags_shortcode', $output, $atts );
}

/**
 * Produces the category links list.
 *
 * @since 1.0.0
 */
add_shortcode( 'post_categories', 'bizznis_post_categories_shortcode' );
function bizznis_post_categories_shortcode( $atts ) {
	$defaults = array(
		'sep'    => ', ',
		'before' => __( 'Filed Under: ', 'bizznis' ),
		'after'  => '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'post_categories' );
	$cats = get_the_category_list( trim( $atts['sep'] ) . ' ' );
	$output = sprintf( '<span %s>', bizznis_attr( 'entry-categories' ) ) . $atts['before'] . $cats . $atts['after'] . '</span>';
	return apply_filters( 'bizznis_post_categories_shortcode', $output, $atts );
}

/**
 * Produces the linked post taxonomy terms list.
 *
 * @since 1.0.0
 */
add_shortcode( 'post_terms', 'bizznis_post_terms_shortcode' );
function bizznis_post_terms_shortcode( $atts ) {
	global $post;
	$defaults = array(
			'after'    => '',
			'before'   => __( 'Filed Under: ', 'bizznis' ),
			'sep'      => ', ',
			'taxonomy' => 'category',
	);
	$atts = shortcode_atts( $defaults, $atts, 'post_terms' );
	$terms = get_the_term_list( $post->ID, $atts['taxonomy'], $atts['before'], trim( $atts['sep'] ) . ' ', $atts['after'] );
	if ( is_wp_error( $terms ) ) {
		return;
	}
	if ( empty( $terms ) ) {
		return;
	}
	$output = sprintf( '<span %s>', bizznis_attr( 'entry-terms' ) ) . $terms . '</span>';
	return apply_filters( 'bizznis_post_terms_shortcode', $output, $terms, $atts );
}

/**
 * Produces the edit post link for logged in users.
 *
 * @since 1.0.0
 */
add_shortcode( 'post_edit', 'bizznis_post_edit_shortcode' );
function bizznis_post_edit_shortcode( $atts ) {
	if ( ! apply_filters( 'bizznis_edit_post_link', true ) ) {
		return;
	}
	$defaults = array(
		'after'  => '',
		'before' => '',
		'link'   => __( '(Edit)', 'bizznis' ),
	);
	$atts = shortcode_atts( $defaults, $atts, 'post_edit' );
	# Darn you, WordPress!
	ob_start();
	edit_post_link( $atts['link'], $atts['before'], $atts['after'] );
	$edit = ob_get_clean();
	$output = $edit;
	return apply_filters( 'bizznis_post_edit_shortcode', $output, $atts );

}

/**
 * Produces the "Return to Top" link.
 *
 * @since 1.0.0
 */
add_shortcode( 'footer_backtotop', 'bizznis_footer_backtotop_shortcode' );
function bizznis_footer_backtotop_shortcode( $atts ) {
	$defaults = array(
		'after'    => '',
		'before'   => '',
		'href'     => '#wrap',
		'nofollow' => true,
		'text'     => __( 'Return to top of page', 'bizznis' ),
	);
	$atts = shortcode_atts( $defaults, $atts, 'footer_backtotop' );
	$nofollow = $atts['nofollow'] ? 'rel="nofollow"' : '';
	$output = sprintf( '%s<a href="%s" %s>%s</a>%s', $atts['before'], esc_url( $atts['href'] ), $nofollow, $atts['text'], $atts['after'] );
	return apply_filters( 'bizznis_footer_backtotop_shortcode', $output, $atts );
}

/**
 * Adds the visual copyright notice.
 *
 * @since 1.0.0
 */
add_shortcode( 'footer_copyright', 'bizznis_footer_copyright_shortcode' );
function bizznis_footer_copyright_shortcode( $atts ) {
	$defaults = array(
		'after'     => '',
		'before'    => '',
		'copyright' => '&#x000A9;',
		'first'     => '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'footer_copyright' );
	$output = $atts['before'] . $atts['copyright'] . ' ';
	if ( '' != $atts['first'] && date( 'Y' ) != $atts['first'] ) {
		$output .= $atts['first'] . '&#x02013;';
	}
	$output .= date( 'Y' ) . $atts['after'];
	return apply_filters( 'bizznis_footer_copyright_shortcode', $output, $atts );
}

/**
 * Adds the link to the child theme, if the details are defined.
 *
 * @since 1.0.0
 */
add_shortcode( 'footer_childtheme_link', 'bizznis_footer_childtheme_link_shortcode' );
function bizznis_footer_childtheme_link_shortcode( $atts ) {
	if ( ! is_child_theme() || ! defined( 'CHILD_THEME_NAME' ) || ! defined( 'CHILD_THEME_URL' ) ) {
		return;
	}
	$defaults = array(
		'after'  => '',
		'before' => '&#x000B7;',
	);
	$atts = shortcode_atts( $defaults, $atts, 'footer_childtheme_link' );
	$output = sprintf( '%s<a href="%s" title="%s">%s</a>%s', $atts['before'], esc_url( CHILD_THEME_URL ), esc_attr( CHILD_THEME_NAME ), esc_html( CHILD_THEME_NAME ), $atts['after'] );
	return apply_filters( 'bizznis_footer_childtheme_link_shortcode', $output, $atts );
}

/**
 * Adds link to the Bizznis page on the BizzThemes website.
 *
 * @since 1.0.0
 */
add_shortcode( 'footer_bizznis_link', 'bizznis_footer_bizznis_link_shortcode' );
function bizznis_footer_bizznis_link_shortcode( $atts ) {
	$defaults = array(
		'after'  => '',
		'before' => '',
		'url'    => 'http://bizzthemes.com/themes/bizznis',
	);
	$atts = shortcode_atts( $defaults, $atts, 'footer_bizznis_link' );
	$output = $atts['before'] . '<a href="' . esc_url( $atts['url'] ) . '" title="Bizznis Theme">Bizznis Theme</a>' . $atts['after'];
	return apply_filters( 'bizznis_footer_bizznis_link_shortcode', $output, $atts );

}

/**
 * Adds link to the BizzThemes home page.
 *
 * @since 1.0.0
 */
add_shortcode( 'footer_bizzthemes_link', 'bizznis_footer_bizzthemes_link_shortcode' );
function bizznis_footer_bizzthemes_link_shortcode( $atts ) {
	$defaults = array(
		'after'  => '',
		'before' => __( 'by', 'bizznis' ),
	);
	$atts = shortcode_atts( $defaults, $atts, 'footer_bizzthemes_link' );
	$output = $atts['before'] . sprintf( __( ' <a href="%1$s">%2$s</a>', 'bizznis' ), 'http://www.bizzthemes.com/', 'BizzThemes' ) . $atts['after'];
	return apply_filters( 'bizznis_footer_bizzthemes_link_shortcode', $output, $atts );
}

/**
 * Adds link to WordPress - http://wordpress.org/ .
 *
 * @since 1.0.0
 */
add_shortcode( 'footer_wordpress_link', 'bizznis_footer_wordpress_link_shortcode' );
function bizznis_footer_wordpress_link_shortcode( $atts ) {
	$defaults = array(
		'after'  => '',
		'before' => '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'footer_wordpress_link' );
	$output = sprintf( '%s<a href="%s" title="%s">%s</a>%s', $atts['before'], 'http://wordpress.org/', 'WordPress', 'WordPress', $atts['after'] );
	return apply_filters( 'bizznis_footer_wordpress_link_shortcode', $output, $atts );
}

/**
 * Adds admin login / logout link.
 *
 * @since 1.0.0
 */
add_shortcode( 'footer_loginout', 'bizznis_footer_loginout_shortcode' );
function bizznis_footer_loginout_shortcode( $atts ) {
	$defaults = array(
		'after'    => '',
		'before'   => '',
		'redirect' => '',
	);
	$atts = shortcode_atts( $defaults, $atts, 'footer_loginout' );
	if ( ! is_user_logged_in() ) {
		$link = '<a href="' . esc_url( wp_login_url( $atts['redirect'] ) ) . '">' . __( 'Log in', 'bizznis' ) . '</a>';
	}
	else {
		$link = '<a href="' . esc_url( wp_logout_url( $atts['redirect'] ) ) . '">' . __( 'Log out', 'bizznis' ) . '</a>';
	}
	$output = $atts['before'] . apply_filters( 'loginout', $link ) . $atts['after'];
	return apply_filters( 'bizznis_footer_loginout_shortcode', $output, $atts );
}
