<?php
/**
 * Bizznis WooCommerce settings class
 *
 * Registers a few WooCommerce specific options on the Bizznis Theme Setting page.
 * 
 * @since 1.0.0
 */
class Bizznis_WC_Settings {
	
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
		$defaults['bizznis_wc_sidebar'] = '';
		return $defaults;
	}

	/**
	 * Set sanitizations
	 *
	 * @since 1.0.0
	 */
	function sanitization_filters() {
		# bizznis_wc_sidebar
		bizznis_add_option_filter( 'one_zero', BIZZNIS_SETTINGS_FIELD, array( 'bizznis_wc_sidebar' ) );
	}

	/**
	 * Register the settings metabox
	 *
	 * @since 1.0.0
	 * @param $_bizznis_theme_settings_pagehook
	 */
	function register_settings_box( $_bizznis_theme_settings_pagehook ) {
		add_meta_box( 'bizznis-theme-settings-wc', __( 'WooCoomerce Integration', 'bizznis' ), array( $this, 'settings_box' ), $_bizznis_theme_settings_pagehook, 'main', 'low' );
	}

	/**
	 * Render the settings metabox
	 *
	 * @since 1.0.0
	 */
	function settings_box() {	
		?>
		<p>
			<input type="checkbox" id="bizznis_wc_sidebar" name="<?php echo BIZZNIS_SETTINGS_FIELD; ?>[bizznis_wc_sidebar]" value="1" <?php checked( bizznis_get_option( 'bizznis_wc_sidebar' ) ); ?> />
			<label for="bizznis_wc_sidebar"><?php _e( 'Register a sidebar that will be used on all shop pages', 'bizznis' ); ?></label>
		</p>
		<p><span class="description"><?php printf( __( 'This option will add addition sidebar in your <a href="%s">Widgets screen</a>. Created sidebar will replace Primary Sidebar on all shop pages.', 'bizznis' ), admin_url( 'widgets.php' ) ); ?></span></p>
		<?php
	}
}

/**
 * Launch the ingration settings
 *
 * @since 1.0.0
 */
$bizznis_wc_settings = new Bizznis_WC_Settings(); #ready, set, go!