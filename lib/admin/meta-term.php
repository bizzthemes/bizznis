<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Term meta defaults.
 *
 * @since 1.3.4
 *
 * @return array Array of default term meta.
 */
function bizznis_term_meta_defaults() {

	return apply_filters( 'bizznis_term_meta_defaults', array(		
		'headline'            => '',
		'intro_text'          => '',
		'display_title'       => 0, //* vestigial
		'display_description' => 0, //* vestigial
		'doctitle'            => '',
		'description'         => '',
		'layout'              => '',
		'noindex'             => 0,
		'nofollow'            => 0,
		'noarchive'           => 0,
	) );

}

/**
 * Loop through the custom taxonomies and add the archive options to each custom taxonomy edit screen.
 *
 * @since 1.0.0
 */
add_action( 'admin_init', 'bizznis_add_taxonomy_archive_options' );
function bizznis_add_taxonomy_archive_options() {
	foreach ( get_taxonomies( array( 'show_ui' => true ) ) as $tax_name ) {
		add_action( $tax_name . '_edit_form', 'bizznis_taxonomy_archive_options', 10, 2 );
	}
}

/**
 * Add new fields for display on archives.
 *
 * @since 1.0.0
 */
function bizznis_taxonomy_archive_options( $tag, $taxonomy ) {
	$tax = get_taxonomy( $taxonomy );
	?>
	<h3><?php echo esc_html( $tax->labels->singular_name ) . ' ' . __( 'Archive Settings', 'bizznis' ); ?></h3>
	<table class="form-table">
		<tbody>
			<tr class="form-field">
				<th scope="row"><label for="bizznis-meta[headline]"><?php _e( 'Archive Headline', 'bizznis' ); ?></label></th>
				<td>
					<input name="bizznis-meta[headline]" id="bizznis-meta[headline]" type="text" value="<?php echo esc_attr( get_term_meta( $tag->term_id, 'headline', true ) ); ?>" size="40" />
					<p class="description"><?php _e( 'Leave empty if you do not want to display a headline.', 'bizznis' ); ?></p>
				</td>
			</tr>
			<tr class="form-field">
				<th scope="row"><label for="bizznis-meta[intro_text]"><?php _e( 'Archive Intro Text', 'bizznis' ); ?></label></th>
				<td>
					<textarea name="bizznis-meta[intro_text]" id="bizznis-meta[intro_text]" rows="3" cols="50" class="large-text"><?php echo esc_textarea( get_term_meta( $tag->term_id, 'intro_text', true ) ); ?></textarea>
					<p class="description"><?php _e( 'Leave empty if you do not want to display any intro text.', 'bizznis' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * Loop through the custom taxonomies and add the layout options to each custom taxonomy edit screen.
 *
 * @since 1.0.0
 */
add_action( 'admin_init', 'bizznis_add_taxonomy_layout_options' );
function bizznis_add_taxonomy_layout_options() {
	foreach ( get_taxonomies( array( 'show_ui' => true ) ) as $tax_name ) {
		add_action( $tax_name . '_edit_form', 'bizznis_taxonomy_layout_options', 10, 2 );
	}
}

/**
 * Display layout picker.
 *
 * @since 1.0.0
 */
function bizznis_taxonomy_layout_options( $tag, $taxonomy ) {
	$tax = get_taxonomy( $taxonomy );
	$customize_url = add_query_arg( 'return', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), wp_customize_url() );
	?>
	<h3><?php _e( 'Layout Settings', 'bizznis' ); ?></h3>
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><?php _e( 'Choose Layout', 'bizznis' ); ?></th>
				<td>
					<fieldset class="bizznis-layout-selector">
						<legend class="screen-reader-text"><?php _e( 'Choose Layout', 'bizznis' ); ?></legend>
						<p><input type="radio" class="default-layout" name="bizznis-meta[layout]" id="default-layout" value="" <?php checked( get_term_meta( $tag->term_id, 'layout', true ), '' ); ?> /> <label for="default-layout" class="default"><?php printf( __( 'Default Layout set in <a href="%s">Theme Settings</a>', 'bizznis' ), menu_page_url( 'bizznis', 0 ) ); ?></label></p>
						<?php bizznis_layout_selector( array( 'name' => 'bizznis-meta[layout]', 'selected' => get_term_meta( $tag->term_id, 'layout', true ), 'type' => 'site' ) ); ?>
					</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

/**
 * For backward compatibility only.
 *
 * Filter each term, pulling term meta automatically so it can be accessed directly by the term object.
 *
 * @since 1.0.0
 */
add_filter( 'get_term', 'bizznis_get_term_filter', 10, 2 ); #wp
function bizznis_get_term_filter( $term, $taxonomy ) {
	//* Stop here, if $term is not object
	if ( ! is_object( $term ) ) {
		return $term;
	}
	
	//* Do nothing, if called in the context of creating a term via an ajax call
	if ( did_action( 'wp_ajax_add-tag' ) ) {
		return $term;
	}
	
	//* Pull all meta for this term ID
	$term_meta = get_term_meta( $term->term_id );
	
	//* Convert array values to string
	foreach ( (array) $term_meta as $key => $value ) {
		$term_meta[ $key ] = $value[0];
	}
	
	$term->meta = wp_parse_args( $term_meta, bizznis_term_meta_defaults() );
	
	# Sanitize term meta
	foreach ( $term->meta as $field => $value ) {
		if ( is_array( $value ) ) {
			$value = stripslashes_deep( array_filter( $value, 'wp_kses_decode_entities' ) );
		} else {
			$value = stripslashes( wp_kses_decode_entities( $value ) );
		}

		/**
		 * Term meta value filter.
		 *
		 * Allow term meta value to be filtered before being injected into the $term->meta array.
		 *
		 * @since
		 *
		 * @param string|array  $value The term meta value.
		 * @param string  $term The term that is being filtered.
		 * @param string  $taxonomy The taxonomy to which the term belongs.
		 */
		$term->meta[ $field ] = apply_filters( "bizznis_term_meta_{$field}", $value, $term, $taxonomy );

	}
	$term->meta = apply_filters( 'bizznis_term_meta', $term->meta, $term, $taxonomy );
	
	return $term;
}

/**
 * Add Bizznis term-meta data to functions that return multiple terms.
 *
 * @since 1.0.0
 */
add_filter( 'get_terms', 'bizznis_get_terms_filter', 10, 2 ); #wp
function bizznis_get_terms_filter( array $terms, $taxonomy ) {
	foreach( $terms as $term ) {
		$term = bizznis_get_term_filter( $term, $taxonomy );
	}
	
	return $terms;
}

/**
 * Save term meta data.
 *
 * @since 1.0.0
 */
add_action( 'edit_term', 'bizznis_term_meta_save', 10, 2 );
function bizznis_term_meta_save( $term_id, $tt_id ) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}
	
	$values = isset( $_POST['bizznis-meta'] ) ? (array) $_POST['bizznis-meta'] : array();

	$values = wp_parse_args( $values, bizznis_term_meta_defaults() );

	if ( ! current_user_can( 'unfiltered_html' ) && isset( $values['archive_description'] ) ) {
		$values['archive_description'] = bizznis_formatting_kses( $values['archive_description'] );
	}
	
	foreach ( $values as $key => $value ) {
		update_term_meta( $term_id, $key, $value );
	}
}

/**
 * Delete term meta data.
 *
 * @since 1.0.0
 */
add_action( 'delete_term', 'bizznis_term_meta_delete', 10, 2 );
function bizznis_term_meta_delete( $term_id, $tt_id ) {
	foreach ( bizznis_term_meta_defaults() as $key => $value ) {
		delete_term_meta( $term_id, $key );
	}
}

/**
 * Create new term meta record for split terms.
 *
 * When WordPress splits terms, ensure that the term meta gets preserved for the newly created term.
 *
 * @since 1.2.0
 *
 * @param integer @old_term_id The ID of the term being split.
 * @param integer @new_term_id The ID of the newly created term.
 */
add_action( 'split_shared_term', 'bizznis_split_shared_term' );
function bizznis_split_shared_term( $old_term_id, $new_term_id ) {
	$term_meta = (array) get_option( 'bizznis-term-meta' );
	
	if ( ! isset( $term_meta[ $old_term_id ] ) ) {
		return;
	}
	
	$term_meta[ $new_term_id ] = $term_meta[ $old_term_id ];

	update_option( 'bizznis-term-meta', $term_meta );
}