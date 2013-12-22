<?php
/**
 * Bizznis bbPress settings class
 *
 * Registers a few bbPress specific options on the Bizznis Theme Setting page.
 * 
 * @since 1.0.0
 */
class Bizznis_BBP_Settings {
	
	/**
	 * construct ALL THE THINGS
	 *
	 * @since 1.0.0
	 */
	function __construct() {	
		# Option default values
		add_filter( 'bizznis_theme_settings_defaults',  array( $this, 'options_defaults' ) );
		# Saniztize options
		add_action( 'bizznis_settings_sanitizer_init',  array( $this, 'sanitization_filters' ) );
		# Register settings
		add_action( 'bizznis_theme_settings_metaboxes', array( $this, 'register_settings_box' ) );
	}

	/**
	 * Set defaults
	 *
	 * @since 1.0.0
	 */
	function options_defaults( $defaults ) {
		$defaults['bizznis_bbp_sidebar'] = '';
		$defaults['bizznis_bbp_desc']    = '';
		$defaults['bizznis_bbp_layout']  = 'bizznis-default';
		return $defaults;
	}

	/**
	 * Set sanitizations
	 *
	 * @since 1.0.0
	 */
	function sanitization_filters() {
		# bizznis_bbp_sidebar
		bizznis_add_option_filter( 'one_zero', BIZZNIS_SETTINGS_FIELD, array( 'bizznis_bbp_sidebar' ) );
		# bizznis_bbp_desc
		bizznis_add_option_filter( 'one_zero', BIZZNIS_SETTINGS_FIELD, array( 'bizznis_bbp_desc'    ) );
		# bizznis_bbp_layout
		bizznis_add_option_filter( 'no_html', BIZZNIS_SETTINGS_FIELD,  array( 'bizznis_bbp_layout'  ) );
	}

	/**
	 * Register the settings metabox
	 *
	 * @since 1.0.0
	 * @param $_bizznis_theme_settings_pagehook
	 */
	function register_settings_box( $_bizznis_theme_settings_pagehook ) {
		add_meta_box( 'bizznis-theme-settings-bbp', __( 'bbPress Integration', 'bizznis' ), array( $this, 'settings_box' ), $_bizznis_theme_settings_pagehook, 'main', 'low' );
	}

	/**
	 * Render the settings metabox
	 *
	 * @since 1.0.0
	 */
	function settings_box() {	
		?>
		<p>
			<label for="bizznis_bbp_layout"><?php _e( 'Forum Layout: ', 'bizznis' ); ?></label>
			<select name="<?php echo BIZZNIS_SETTINGS_FIELD; ?>[bizznis_bbp_layout]" id="bizznis_bbp_layout">
				<option value="bizznis-default" <?php selected( bizznis_get_option( 'bizznis_bbp_layout' ), 'bizznis-default' ); ?>><?php _e( 'Default Layout', 'bizznis' ); ?></option> 
				<?php
				foreach ( bizznis_get_layouts() as $id => $data ) {	
					echo '<option value="' . esc_attr( $id ) . '" ' . selected( bizznis_get_option( 'bizznis_bbp_layout' ), esc_attr( $id ) ) . '>' . esc_attr( $data['label'] ) . '</option>';
				}
				?>
			</select>
		</p>
		<p>
			<input type="checkbox" id="bizznis_bbp_sidebar" name="<?php echo BIZZNIS_SETTINGS_FIELD; ?>[bizznis_bbp_sidebar]" value="1" <?php checked( bizznis_get_option( 'bizznis_bbp_sidebar' ) ); ?> />
			<label for="bizznis_bbp_sidebar"><?php _e( 'Register a sidebar that will be used on all bbPress pages', 'bizznis' ); ?></label>
		</p>
		<p><span class="description"><?php printf( __( 'This option will add addition sidebar in your <a href="%s">Widgets screen</a>. Created sidebar will replace Primary Sidebar on all bbPress pages.', 'bizznis' ), admin_url( 'widgets.php' ) ); ?></span></p>
		<p>
			<input type="checkbox" id="bizznis_bbp_desc" name="<?php echo BIZZNIS_SETTINGS_FIELD; ?>[bizznis_bbp_desc]" value="1" <?php checked( bizznis_get_option( 'bizznis_bbp_desc' ) ); ?> />
			<label for="bizznis_bbp_desc"><?php _e( 'Remove forum and topic descriptions.', 'bizznis' ); ?></label>
		</p>
		<p><span class="description"><?php _e( 'This option will remove topic descriptions. E.g. "This forum contains [&hellip;]" notices.', 'bizznis' ); ?></span></p>
		<?php
	}
}

/**
 * Launch the ingration settings
 *
 * @since 1.0.0
 */
$bizznis_bbp_settings = new Bizznis_BBP_Settings(); #ready, set, go!