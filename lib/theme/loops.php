<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Attach a loop to the 'bizznis_loop' output hook so we can get some front-end output.
 *
 * @since 1.0.0
 */
add_action( 'bizznis_loop', 'bizznis_do_loop' );
function bizznis_do_loop() {
	global $wp_query, $more;
	$args = apply_filters( 'bizznis_loop_args', array() ); # Filtered args
	if ( $args ) $wp_query = new WP_Query( $args );
	$more = is_singular() ? $more : 0; # Only set $more to 0 if we're on an archive
	# Loop entry markup
	bizznis_loop_entry();
	wp_reset_query();
}

/**
 * Standard loop, meant to be executed without modification in most circumstances where 
 * content needs to be displayed.
 *
 * @since 1.0.0
 */
function bizznis_loop_entry() {
	if ( have_posts() ) : while ( have_posts() ) : the_post();
			do_action( 'bizznis_before_entry' );
			printf( '<article %s>', bizznis_attr( 'entry' ) );
				do_action( 'bizznis_entry_header' );
				printf( '<div %s>', bizznis_attr( 'entry-content' ) );
					do_action( 'bizznis_entry_content' );
				echo '</div>'; # end .entry-content
				do_action( 'bizznis_entry_footer' );
			echo '</article>';
			do_action( 'bizznis_after_entry' );
		endwhile; # end of one post
		do_action( 'bizznis_after_endwhile' );
	else : # if no posts exist
		do_action( 'bizznis_loop_else' );
	endif; # end loop
}

//* MARKUP for the GRID LOOP VIEW

/**
 * The grid loop - a specific implementation of a custom loop.
 *
 * @since 1.0.0
 */
function bizznis_loop_grid( $args = array() ) {
	# Global vars
	global $_bizznis_loop_args;
	# Parse args
	$args = apply_filters(
		'bizznis_loop_grid_args',
		wp_parse_args(
			$args,
			array(
				'features'				=> 2,
				'features_on_all'		=> false,
				'feature_image_size'	=> 0,
				'feature_image_class'	=> 'alignleft',
				'feature_content_limit'	=> 0,
				'grid_image_size'		=> 'thumbnail',
				'grid_image_class'		=> 'alignleft',
				'grid_content_limit'	=> 0,
				'more'					=> __( 'Read more', 'bizznis' ) . '&#x02026;',
			)
		)
	);
	# If user chose more features than posts per page, adjust features
	if ( get_option( 'posts_per_page' ) < $args['features'] ) {
		$args['features'] = get_option( 'posts_per_page' );
	}
	# What page are we on?
	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;
	# Potentially remove features on page 2+
	if ( ! $args['features_on_all'] && $paged > 1 )
		$args['features'] = 0;
	# Set global loop args
	$_bizznis_loop_args = $args;
	# Remove some unnecessary stuff from the grid loop
	remove_action( 'bizznis_entry_content', 'bizznis_do_post_image' );
	remove_action( 'bizznis_entry_content', 'bizznis_do_post_content' );
	remove_action( 'bizznis_entry_content', 'bizznis_do_post_permalink' );
	remove_action( 'bizznis_entry_content', 'bizznis_do_post_content_nav' );
	# Custom loop output
	add_filter( 'post_class', 'bizznis_loop_grid_post_class' );
	add_action( 'bizznis_entry_content', 'bizznis_loop_grid_content' );
	# The loop
	bizznis_loop_entry();
	# Reset loops
	bizznis_reset_loops();
	remove_filter( 'post_class', 'bizznis_loop_grid_post_class' );
	remove_action( 'bizznis_entry_content', 'bizznis_loop_grid_content' );
}

/**
 * Filter the post classes to output custom classes for the feature and grid layout.
 *
 * @since 1.0.0
 */
function bizznis_loop_grid_post_class( array $classes ) {
	global $_bizznis_loop_args, $wp_query;
	$grid_classes = array();
	if ( $_bizznis_loop_args['features'] && $wp_query->current_post < $_bizznis_loop_args['features'] ) {
		$grid_classes[] = 'bizznis-feature';
		$grid_classes[] = sprintf( 'bizznis-feature-%s', $wp_query->current_post + 1 );
		$grid_classes[] = $wp_query->current_post&1 ? 'bizznis-feature-even' : 'bizznis-feature-odd';
	}
	elseif ( $_bizznis_loop_args['features']&1 ) {
		$grid_classes[] = 'bizznis-grid';
		$grid_classes[] = sprintf( 'bizznis-grid-%s', $wp_query->current_post - $_bizznis_loop_args['features'] + 1 );
		$grid_classes[] = $wp_query->current_post&1 ? 'bizznis-grid-odd' : 'bizznis-grid-even';
	}
	else {
		$grid_classes[] = 'bizznis-grid';
		$grid_classes[] = sprintf( 'bizznis-grid-%s', $wp_query->current_post - $_bizznis_loop_args['features'] + 1 );
		$grid_classes[] = $wp_query->current_post&1 ? 'bizznis-grid-even' : 'bizznis-grid-odd';
	}
	return array_merge( $classes, apply_filters( 'bizznis_loop_grid_post_class', $grid_classes ) );
}

/**
 * Output specially formatted content, based on the grid loop args.
 *
 * @since 1.0.0
 */
function bizznis_loop_grid_content() {
	global $_bizznis_loop_args;
	if ( in_array( 'bizznis-feature', get_post_class() ) ) {
		if ( $_bizznis_loop_args['feature_image_size'] ) {
			$image = bizznis_get_image( array(
				'size'    => $_bizznis_loop_args['feature_image_size'],
				'context' => 'grid-loop',
				'attr'    => bizznis_parse_attr( 'entry-image-grid-loop', array( 'class' => $_bizznis_loop_args['feature_image_class'] ) ),
			) );
			printf( '<a href="%s" title="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), $image );
		}
		if ( $_bizznis_loop_args['feature_content_limit'] )
			the_content_limit( (int) $_bizznis_loop_args['feature_content_limit'], esc_html( $_bizznis_loop_args['more'] ) );
		else
			the_content( esc_html( $_bizznis_loop_args['more'] ) );
	}
	else {
		if ( $_bizznis_loop_args['grid_image_size'] ) {
			$image = bizznis_get_image( array(
				'size'    => $_bizznis_loop_args['grid_image_size'],
				'context' => 'grid-loop',
				'attr'    => bizznis_parse_attr( 'entry-image', array( 'class' => $_bizznis_loop_args['grid_image_class'] ) ),
			) );
			printf( '<a href="%s" title="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), $image );
		}
		if ( $_bizznis_loop_args['grid_content_limit'] )
			the_content_limit( (int) $_bizznis_loop_args['grid_content_limit'], esc_html( $_bizznis_loop_args['more'] ) );
		else {
			the_excerpt();
			printf( '<a href="%s" class="more-link">%s</a>', get_permalink(), esc_html( $_bizznis_loop_args['more'] ) );
		}
	}

}

/**
 * Modify the global $_bizznis_displayed_ids each time a loop iterates.
 *
 * @since 1.0.0
 */
add_action( 'bizznis_after_entry', 'bizznis_add_id_to_global_exclude' );
function bizznis_add_id_to_global_exclude() {
	global $_bizznis_displayed_ids;
	# Add each ID to a global array, which can be used any time by other loops to prevent posts from being displayed twice on a page.
	$_bizznis_displayed_ids[] = get_the_ID();
}
