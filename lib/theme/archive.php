<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Add custom headline and / or description to category / tag / taxonomy archive pages.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_term_intro_text_output', 'wpautop' );
add_action( 'bizznis_loop', 'bizznis_do_taxonomy_title_description', 5 );
function bizznis_do_taxonomy_title_description() {
	global $wp_query;
	# Stop here if the page is not a category, tag or taxonomy term archive
	if ( ! is_category() && ! is_tag() && ! is_tax() ) {
		return;
	}
	# Stop here if we're not on the first page
	if ( get_query_var( 'paged' ) >= 2 ) {
		return;
	}
	# Stop here if there's no term, or no term meta set
	$term = is_tax() ? get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) ) : $wp_query->get_queried_object();
	if ( ! $term || ! isset( $term->meta ) ) {
		return;
	}
	$headline = $intro_text = '';
	if ( $term->meta['headline'] ) {
		$headline = sprintf( '<h1 class="archive-title">%s</h1>', strip_tags( $term->meta['headline'] ) );
	}
	if ( $term->meta['intro_text'] ) {
		$intro_text = apply_filters( 'bizznis_term_intro_text_output', $term->meta['intro_text'] );
	}
	if ( $headline || $intro_text ) {
		printf( '<div class="archive-description taxonomy-description">%s</div>', $headline . $intro_text );
	}
}

/**
 * Add custom headline and description to author archive pages.
 *
 * @since 1.0.0
 */
add_filter( 'bizznis_author_intro_text_output', 'wpautop' );
add_action( 'bizznis_loop', 'bizznis_do_author_title_description', 5 );
function bizznis_do_author_title_description() {	
	# Stop here if we're not on an author archive page
	if ( ! is_author() ) {
		return;
	}
	# Stop here if we're not on page 1
	if ( get_query_var( 'paged' ) >= 2 ) {
		return;
	}
	# If there's a custom headline to display, it is marked up as a level 1 heading.
	$headline   = get_the_author_meta( 'headline', (int) get_query_var( 'author' ) );
	$intro_text = get_the_author_meta( 'intro_text', (int) get_query_var( 'author' ) );
	# If there's a description (intro text) to display, it is run through 'wpautop()' before being added to a div.
	$headline   = $headline ? sprintf( '<h1 class="archive-title">%s</h1>', strip_tags( $headline ) ) : '';
	$intro_text = $intro_text ? apply_filters( 'bizznis_author_intro_text_output', $intro_text ) : '';
	if ( $headline || $intro_text ) {
		printf( '<div class="archive-description author-description">%s</div>', $headline . $intro_text );
	}
}

/**
 * Add author box to the top of author archive.
 *
 * @since 1.0.0
 */
add_action( 'bizznis_loop', 'bizznis_do_author_box_archive', 5 );
function bizznis_do_author_box_archive() {
	if ( ! is_author() || get_query_var( 'paged' ) >= 2 ) {
		return;
	}
	if ( get_the_author_meta( 'bizznis_author_box_archive', get_query_var( 'author' ) ) ) {
		bizznis_author_box( 'archive' );
	}
}
