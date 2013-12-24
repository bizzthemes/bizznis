<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Register new layouts in Bizznis.
 *
 * @since 1.0.0
 */
function bizznis_register_layout( $id = '', $args = array() ) {
	global $_bizznis_layouts;
	if ( ! is_array( $_bizznis_layouts ) ) {
		$_bizznis_layouts = array();
	}
	# Don't allow empty $id, or double registrations
	if ( ! $id || isset( $_bizznis_layouts[$id] ) ) {
		return false;
	}
	$defaults = array(
		'label' => __( 'No Label Selected', 'bizznis' ),
		'img'   => BIZZNIS_ADMIN_IMAGES_URL . '/layouts/none.png',
		'type'  => 'site',
	);
	$args = wp_parse_args( $args, $defaults );
	$_bizznis_layouts[$id] = $args;
	return $args;
}

/**
 * Set a default layout.
 *
 * @since 1.0.0
 */
function bizznis_set_default_layout( $id = '' ) {
	global $_bizznis_layouts;
	if ( ! is_array( $_bizznis_layouts ) ) {
		$_bizznis_layouts = array();
	}
	# Don't allow empty $id, or unregistered layouts
	if ( ! $id || ! isset( $_bizznis_layouts[$id] ) ) {
		return false;
	}
	# Remove default flag for all other layouts
	foreach ( (array) $_bizznis_layouts as $key => $value ) {
		if ( isset( $_bizznis_layouts[$key]['default'] ) ) {
			unset( $_bizznis_layouts[$key]['default'] );
		}
	}
	$_bizznis_layouts[$id]['default'] = true;
	return $id;
}

/**
 * Unregister a layout in Bizznis.
 *
 * @since 1.0.0
 */
function bizznis_unregister_layout( $id = '' ) {
	global $_bizznis_layouts;
	if ( ! $id || ! isset( $_bizznis_layouts[$id] ) ) {
		return false;
	}
	unset( $_bizznis_layouts[$id] );
	return true;
}

/**
 * Return all registered Bizznis layouts.
 *
 * @since 1.0.0
 */
function bizznis_get_layouts( $type = '' ) {
	global $_bizznis_layouts;
	# If no layouts exists, return empty array
	if ( ! is_array( $_bizznis_layouts ) ) {
		$_bizznis_layouts = array();
		return $_bizznis_layouts;
	}
	# Return all layouts, if no type specified
	if ( '' == $type ) {
		return $_bizznis_layouts;
	}
	$layouts = array();
	# Cycle through looking for layouts of $type
	foreach ( (array) $_bizznis_layouts as $id => $data ) {
		if ( $data['type'] == $type ) {
			$layouts[$id] = $data;
		}
	}
	return $layouts;
}

/**
 * Return registered layouts in a format the WordPress Customizer accepts.
 *
 * @since 1.0.0
 */
function bizznis_get_layouts_for_customizer( $type = '' ) {
	$layouts = bizznis_get_layouts( $type );
	if ( empty( $layouts ) ) {
		return $layouts;
	}
	# Simplified layout array
	foreach ( (array) $layouts as $id => $data ) {
		$customizer_layouts[$id] = $data['label'];
	}
	return $customizer_layouts;
}

/**
 * Return the data from a single layout, specified by the $id passed to it.
 *
 * @since 1.0.0
 */
function bizznis_get_layout( $id ) {
	$layouts = bizznis_get_layouts();
	if ( ! $id || ! isset( $layouts[$id] ) ) {
		return;
	}
	return $layouts[$id];
}

/**
 * Return the layout that is set to default.
 *
 * @since 1.0.0
 */
function bizznis_get_default_layout() {
	global $_bizznis_layouts;
	$default = 'nolayout';
	foreach ( (array) $_bizznis_layouts as $key => $value ) {
		if ( isset( $value['default'] ) && $value['default'] ) {
			$default = $key;
			break;
		}
	}
	return $default;
}

/**
 * Return the site layout for different contexts.
 *
 * @since 1.0.0
 */
function bizznis_site_layout( $use_cache = true ) {
	# Allow child theme to short-circuit this function
	$pre = apply_filters( 'bizznis_site_layout', null );
	if ( null !== $pre ) {
		return $pre;
	}
	# If we're supposed to use the cache, setup cache. Use if value exists.
	if ( $use_cache ) {
		# Setup cache
		static $layout_cache = '';
		# If cache is populated, return value
		if ( $layout_cache !== '' ) {
			return esc_attr( $layout_cache );
		}
	}
	global $wp_query;
	# If viewing a singular page or post
	if ( is_singular() ) {
		$custom_field = bizznis_get_custom_field( '_bizznis_layout' );
		$site_layout  = $custom_field ? $custom_field : bizznis_get_option( 'site_layout' );
	}
	# If viewing a taxonomy archive
	elseif ( is_category() || is_tag() || is_tax() ) {
		$term = $wp_query->get_queried_object();
		$site_layout = $term && isset( $term->meta['layout'] ) && $term->meta['layout'] ? $term->meta['layout'] : bizznis_get_option( 'site_layout' );
	}
	# If viewing a supported post type
	elseif ( is_post_type_archive() && bizznis_has_post_type_archive_support() ) {
		$site_layout = bizznis_get_cpt_option( 'layout' ) ? bizznis_get_cpt_option( 'layout' ) : bizznis_get_option( 'site_layout' );
	}
	# If viewing an author archive
	elseif ( is_author() ) {
		$site_layout = get_the_author_meta( 'layout', (int) get_query_var( 'author' ) ) ? get_the_author_meta( 'layout', (int) get_query_var( 'author' ) ) : bizznis_get_option( 'site_layout' );
	}
	# Else pull the theme option
	else {
		$site_layout = bizznis_get_option( 'site_layout' );
	}
	# Use default layout as a fallback, if necessary
	if ( ! bizznis_get_layout( $site_layout ) ) {
		$site_layout = bizznis_get_default_layout();
	}
	# Push layout into cache, if caching turned on
	if ( $use_cache ) {
		$layout_cache = $site_layout;
	}
	# Return site layout
	return esc_attr( $site_layout );
}

/**
 * Output the form elements necessary to select a layout.
 *
 * @since 1.0.0
 */
function bizznis_layout_selector( $args = array() ) {
	# Enqueue the Javascript
	bizznis_load_admin_js();
	# Merge defaults with user args
	$args = wp_parse_args(
		$args,
		array(
			'name'     => '',
			'selected' => '',
			'type'     => '',
			'echo'     => true,
		)
	);
	$output = '';
	foreach ( bizznis_get_layouts( $args['type'] ) as $id => $data ) {
		$class = $id == $args['selected'] ? ' selected' : '';
		$output .= sprintf(
			'<label title="%1$s" class="box%2$s"><img src="%3$s" alt="%1$s" /><br /> <input type="radio" name="%4$s" id="%5$s" value="%5$s" %6$s /></label>',
            esc_attr( $data['label'] ),
			esc_attr( $class ),
			esc_url( $data['img'] ),
			esc_attr( $args['name'] ),
			esc_attr( $id ),
			checked( $id, $args['selected'], false )
		);
	}
	# Echo or return output
	if ( $args['echo'] ) {
		echo $output;
	}
	else {
		return $output;
	}
}
