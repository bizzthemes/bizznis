<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * This class creates menus and settings pages and is extended by subclasses that 
 * define specific types of admin pages.
 *
 * @since 1.0.0
 */
abstract class Bizznis_Admin {
	
	# Properties
	public $pagehook; 			# name of the page hook when the menu is registered.
	public $page_id; 			# ID of the admin menu and settings page.
	public $settings_field; 	# name of the settings field in the options table.
	public $default_settings; 	# associative array (field name => values) for the default settings on this admin page.
	public $menu_ops; 			# associative array of configuration options for the admin menu(s).
	public $page_ops; 			# associative array of configuration options for the settings page.
	
	/**
	 * Call this method in a subclass constructor to create an admin menu and settings page.
	 *
	 * @since 1.0.0
	 */
	public function create( $page_id = '', $menu_ops = array(), $page_ops = array(), $settings_field = '', $default_settings = array() ) {
		# Set the properties
		$this->page_id          = $this->page_id          ? $this->page_id          : (string) $page_id;
		$this->menu_ops         = $this->menu_ops         ? $this->menu_ops         : (array) $menu_ops;
		$this->page_ops         = $this->page_ops         ? $this->page_ops         : (array) $page_ops;
		$this->settings_field   = $this->settings_field   ? $this->settings_field   : (string) $settings_field;
		$this->default_settings = $this->default_settings ? $this->default_settings : (array) $default_settings;		
		# Default page ops
		$this->page_ops = wp_parse_args(
			$this->page_ops,
			array(
				'save_button_text'  => __( 'Save Settings', 'bizznis' ),
				'reset_button_text' => __( 'Reset Settings', 'bizznis' ),
				'saved_notice_text' => __( 'Settings saved.', 'bizznis' ),
				'reset_notice_text' => __( 'Settings reset.', 'bizznis' ),
				'error_notice_text' => __( 'Error saving settings.', 'bizznis' ),
			)
		);
		# Stop here if page_id not set
		if ( ! $this->page_id ) {
			return;
		}
		# Check to make sure there we are only creating one menu per subclass
		if ( isset( $this->menu_ops['theme_menu'] ) ) {
			wp_die( sprintf( __( 'You cannot use %s to create two menus in the same subclass. Please use separate subclasses for each menu.', 'bizznis' ), 'Bizznis_Admin' ) );
		}
		# Theme options actions
		//* set up settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		//* set up notices
		add_action( 'admin_notices', array( $this, 'notices' ) );
		//* add feedback link
		add_action( 'admin_title_right', array( $this, 'feedback_link' ), 15 );
		//* load the page content
		add_action( 'admin_init', array( $this, 'settings_init' ) );
		//* Load help tab
		add_action( 'admin_init', array( $this, 'load_help' ) );
		//* Load contextual assets (registered admin page)
		add_action( 'admin_init', array( $this, 'load_assets' ) );
		//* add a sanitizer/validator
		add_filter( 'pre_update_option_' . $this->settings_field, array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Register the database settings for storage.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		# If this page doesn't store settings, no need to register them
		if ( ! $this->settings_field ) {
			return;
		}
		register_setting( $this->settings_field, $this->settings_field );
		add_option( $this->settings_field, $this->default_settings );
		if ( ! bizznis_is_menu_page( $this->page_id ) ) {
			return;
		}
		if ( bizznis_get_option( 'reset', $this->settings_field ) ) {
			if ( update_option( $this->settings_field, $this->default_settings ) ) {
				bizznis_admin_redirect( $this->page_id, array( 'reset' => 'true' ) );
			}
			else {
				bizznis_admin_redirect( $this->page_id, array( 'error' => 'true' ) );
			}
			exit;
		}
	}

	/**
	 * Display notices on the save or reset of settings.
	 *
	 * @since 1.0.0
	 */
	public function notices() {
		if ( ! bizznis_is_menu_page( $this->page_id ) ) {
			return;
		}
		if ( isset( $_REQUEST['settings-updated'] ) && $_REQUEST['settings-updated'] == 'true' ) {
			echo '<div id="message" class="updated"><p><strong>' . $this->page_ops['saved_notice_text'] . '</strong></p></div>';
		}
		elseif ( isset( $_REQUEST['reset'] ) && 'true' == $_REQUEST['reset'] ) {
			echo '<div id="message" class="updated"><p><strong>' . $this->page_ops['reset_notice_text'] . '</strong></p></div>';
		}
		elseif ( isset( $_REQUEST['error'] ) && $_REQUEST['error'] == 'true' ) {
			echo '<div id="message" class="updated"><p><strong>' . $this->page_ops['error_notice_text'] . '</strong></p></div>';
		}
	}
	
	/**
	 * Add the feedback link to admin navigation
	 *
	 * @since 1.0.0
	 */
	public function feedback_link() {
		if ( ! bizznis_is_menu_page( $this->page_id ) ) {
			return;
		}
		printf( __( '<a href="%s" target="_blank" class="feedback" title="Report a Bug">Report a Bug</a>', 'bizznis' ), esc_url( 'https://github.com/bizzthemes/bizznis/issues' ) );
	}

	/**
	 * Save method. Override this method to modify form data (for validation, sanitization, etc.) 
	 * before it gets saved.
	 *
	 * @since 1.0.0
	 */
	public function save( $newvalue, $oldvalue ) {
		return $newvalue;
	}

	/**
	 * Initialize the settings page. This method must be re-defined in the extended classes,
	 * to hook in the required components for the page.
	 *
	 * @since 1.0.0
	 */
	abstract public function settings_init();
	
	/**
	 * Load the optional help method, if one exists.
	 *
	 * @since 1.1.0
	 */
	public function load_help() {
		if ( method_exists( $this, 'help' ) ) {
			add_action( "load-{$this->pagehook}", array( $this, 'help' ) );
		}
	}

	/**
	 * Load script and stylesheet assets via scripts() and styles() methods, if they exist.
	 *
	 * @since 1.1.0
	 */
	public function load_assets() {
		//* Hook scripts method
		if ( method_exists( $this, 'scripts' ) ) {
			add_action( "load-{$this->pagehook}", array( $this, 'scripts' ) );
		}
		//* Hook styles method
		if ( method_exists( $this, 'styles' ) ) {
			add_action( "load-{$this->pagehook}", array( $this, 'styles' ) );
		}
	}

	/**
	 * Output the main admin page. This method must be re-defined in the extended class, 
	 * to output the main admin page content.
	 *
	 * @since 1.0.0
	 */
	abstract public function admin();

	/**
	 * Helper function that constructs name attributes for use in form fields.
	 *
	 * @since 1.0.0
	 */
	protected function get_field_name( $name ) {
		return sprintf( '%s[%s]', $this->settings_field, $name );
	}
	
	/**
	 * Echo constructed name attributes in form fields.
	 *
	 * @since 1.1.0
	 *
	 * @uses Genesis_Admin:get_field_name() Construct name attributes for use in form fields.
	 *
	 * @param string $name Field name base
	 * @return string Full field name
	 */
	protected function field_name( $name ) {
		echo $this->get_field_name( $name );
	}

	/**
	 * Helper function that constructs id attributes for use in form fields.
	 *
	 * @since 1.0.0
	 */
	protected function get_field_id( $id ) {
		return sprintf( '%s[%s]', $this->settings_field, $id );
	}
	
	/**
	 * Echo constructed id attributes in form fields.
	 *
	 * @since 1.1.0
	 *
	 * @uses Genesis_Admin::get_field_id() Constructs id attributes for use in form fields.
	 *
	 * @param string $id Field id base
	 * @return string Full field id
	 */
	protected function field_id( $id ) {
		echo $this->get_field_id( $id );
	}

	/**
	 * Helper function that returns a setting value from this form's settings field for 
	 * use in form fields.
	 *
	 * @since 1.0.0
	 */
	protected function get_field_value( $key ) {
		return bizznis_get_option( $key, $this->settings_field );
	}
	
	/**
	 * Echo a setting value from this form's settings field for use in form fields.
	 *
	 * @uses Genesis_Admin::get_field_value() Constructs value attributes for use in form fields.
	 *
	 * @since 1.1.0
	 *
	 * @param string $key Field key
	 * @return string Field value
	 */
	protected function field_value( $key ) {
		echo $this->get_field_value( $key );
	}

}

/**
 * Abstract subclass of Bizznis_Admin which adds support for displaying a form.
 *
 * @since 1.0.0
 */
abstract class Bizznis_Admin_Form extends Bizznis_Admin {

	/**
	 * Output settings page form elements.
	 *
	 * @since 1.0.0
	 */
	abstract public function form();

	/**
	 * Normal settings page admin.
	 *
	 * @since 1.0.0
	 */
	public function admin() {
		?>
		<div class="wrap bizznis-admin bizznis-form">
		<form method="post" action="options.php">
			<?php settings_fields( $this->settings_field ); ?>
			<h1>
				<?php do_action( 'admin_title_left', $this->pagehook ); ?>
				<?php echo esc_html( get_admin_page_title() ); ?>
				<?php do_action( 'admin_title_right', $this->pagehook ); ?>
			</h1>
			<?php do_action( "{$this->pagehook}_settings_page_form", $this->pagehook ); ?>
			<?php do_settings_fields( $this->page_id, 'default' ); ?>
			<?php do_settings_sections( $this->page_id );?>
			<p class="submit bottom-buttons">
				<?php
				submit_button( $this->page_ops['save_button_text'], 'primary', 'submit', false );
				submit_button( $this->page_ops['reset_button_text'], 'secondary', $this->get_field_name( 'reset' ), false, array( 'onclick' => 'return bizznis_confirm(\'' . esc_js( __( 'Are you sure you want to reset?', 'bizznis' ) ) . '\');' ) );
				?>
			</p>
		</form>
		</div>
		<?php
	}

	/**
	 * Initialize the settings page, by hooking the form into the page.
	 *
	 * @since 1.0.0
	 */
	public function settings_init() {
		add_action( "{$this->pagehook}_settings_page_form", array( $this, 'form' ) );
	}

}


/**
 * Abstract subclass of Bizznis_Admin which adds support for creating a basic admin page
 * that doesn't make use of a Settings API form or metaboxes.
 *
 * @since 1.0.0
 */
abstract class Bizznis_Admin_Basic extends Bizznis_Admin {

	/**
	 * Satisfies the abstract requirements of Bizznis_Admin.
	 *
	 * @since 1.0.0
	 */
	public function settings_init() {}

}