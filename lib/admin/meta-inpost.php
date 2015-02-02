<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Register a new meta box to the post or page edit screen, so that the user can
 * set layout options on a per-post or per-page basis.
 *
 * @since 1.0.0
 */
add_action( 'admin_menu', 'bizznis_add_inpost_layout_box' );
function bizznis_add_inpost_layout_box() {
	if ( ! current_theme_supports( 'bizznis-inpost-layouts' ) ) {
		return;
	}
	foreach ( (array) get_post_types( array( 'public' => true ) ) as $type ) {
		if ( post_type_supports( $type, 'bizznis-layouts' ) ) {
			add_meta_box( 'bizznis_inpost_layout_box', __( 'Layout Settings', 'bizznis' ), 'bizznis_inpost_layout_box', $type, 'normal', 'core' );
		}
	}
}

/**
 * Callback for in-post layout meta box.
 *
 * @since 1.0.0
 */
function bizznis_inpost_layout_box() {
	wp_nonce_field( 'bizznis_inpost_layout_save', 'bizznis_inpost_layout_nonce' );
	$layout = bizznis_get_custom_field( '_bizznis_layout' );
	$customize_url = add_query_arg( 'return', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), 'customize.php' );
	?>
	<div class="bizznis-layout-selector">
		<p><input type="radio" name="bizznis_layout[_bizznis_layout]" class="default-layout" id="default-layout" value="" <?php checked( $layout, '' ); ?> /> <label class="default" for="default-layout"><?php printf( __( 'Default Layout set in <a href="%s">Customizer</a>', 'bizznis' ), $customize_url ); ?></label></p>

		<p><?php bizznis_layout_selector( array( 'name' => 'bizznis_layout[_bizznis_layout]', 'selected' => $layout, 'type' => 'site' ) ); ?></p>
	</div>
	<br class="clear" />
	<p><label for="bizznis_custom_body_class"><b><?php _e( 'Custom Body Class', 'bizznis' ); ?></b></label></p>
	<p><input class="large-text" type="text" name="bizznis_layout[_bizznis_custom_body_class]" id="bizznis_custom_body_class" value="<?php echo esc_attr( bizznis_get_custom_field( '_bizznis_custom_body_class' ) ); ?>" /></p>
	<p><label for="bizznis_custom_post_class"><b><?php _e( 'Custom Post Class', 'bizznis' ); ?></b></label></p>
	<p><input class="large-text" type="text" name="bizznis_layout[_bizznis_custom_post_class]" id="bizznis_custom_post_class" value="<?php echo esc_attr( bizznis_get_custom_field( '_bizznis_custom_post_class' ) ); ?>" /></p>
	<?php
}

/**
 * Save the layout options when we save a post or page.
 *
 * @since 1.0.0
 */
add_action( 'save_post', 'bizznis_inpost_layout_save', 1, 2 );
function bizznis_inpost_layout_save( $post_id, $post ) {
	if ( ! isset( $_POST['bizznis_layout'] ) ) {
		return;
	}
	$data = wp_parse_args( $_POST['bizznis_layout'], array(
		'_bizznis_layout'            => '',
		'_bizznis_custom_body_class' => '',
		'_bizznis_post_class'        => '',
	) );
	$data = array_map( 'bizznis_sanitize_html_classes', $data );
	bizznis_save_custom_fields( $data, 'bizznis_inpost_layout_save', 'bizznis_inpost_layout_nonce', $post, $post_id );
}

/**
 * Register a new meta box to the post or page edit screen, so that the user can 
 * apply scripts on a per-post or per-page basis.
 *
 * @since 1.0.0
 */
add_action( 'admin_menu', 'bizznis_add_inpost_scripts_box' );
function bizznis_add_inpost_scripts_box() {
	# If user doesn't have unfiltered html capability, don't show this box
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		return;
	}
	foreach ( (array) get_post_types( array( 'public' => true ) ) as $type ) {
		if ( post_type_supports( $type, 'bizznis-scripts' ) ) {
			add_meta_box( 'bizznis_inpost_scripts_box', __( 'Scripts', 'bizznis' ), 'bizznis_inpost_scripts_box', $type, 'normal', 'low' );
		}
	}
}

/**
 * Callback for in-post Scripts meta box.
 *
 * @since 1.0.0
 */
function bizznis_inpost_scripts_box() {
	wp_nonce_field( 'bizznis_inpost_scripts_save', 'bizznis_inpost_scripts_nonce' );
	?>
	<p><label for="bizznis_scripts" class="screen-reader-text"><b><?php _e( 'Page-specific Scripts', 'bizznis' ); ?></b></label></p>
	<p><textarea class="widefat" rows="4" cols="4" name="bizznis_scripts[_bizznis_scripts]" id="bizznis_scripts"><?php echo esc_textarea( bizznis_get_custom_field( '_bizznis_scripts' ) ); ?></textarea></p>
	<p><?php printf( __( 'Suitable for page-specific script. Must include %s tags.', 'bizznis' ), bizznis_code( 'script' ) ); ?></p>
	<?php
}

/**
 * Save the Scripts settings when we save a post or page.
 *
 * @since 1.0.0
 */
add_action( 'save_post', 'bizznis_inpost_scripts_save', 1, 2 );
function bizznis_inpost_scripts_save( $post_id, $post ) {
	if ( ! isset( $_POST['bizznis_scripts'] ) ) {
		return;
	}
	# If user doesn't have unfiltered html capability, don't try to save
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		return;
	}
	$data = wp_parse_args( $_POST['bizznis_scripts'], array(
		'_bizznis_scripts' => '',
	) );
	bizznis_save_custom_fields( $data, 'bizznis_inpost_scripts_save', 'bizznis_inpost_scripts_nonce', $post, $post_id );
}