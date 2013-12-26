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
	public $pagehook; 			#name of the page hook when the menu is registered.
	public $page_id; 			#ID of the admin menu and settings page.
	public $settings_field; 	#name of the settings field in the options table.
	public $default_settings; 	#associative array (field name => values) for the default settings on this admin page.
	public $menu_ops; 			#associative array of configuration options for the admin menu(s).
	public $page_ops; 			#associative array of configuration options for the settings page.
	
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
				'screen_icon'       => 'options-general',
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
		if ( isset( $this->menu_ops['theme_menu'] ) && ( isset( $this->menu_ops['theme_submenu'] ) ) || isset( $this->menu_ops['submenu'] ) && ( isset( $this->menu_ops['main_menu'] ) || isset( $this->menu_ops['first_submenu'] ) ) ) {
			wp_die( sprintf( __( 'You cannot use %s to create two menus in the same subclass. Please use separate subclasses for each menu.', 'bizznis' ), 'Bizznis_Admin' ) );
		}
		# Theme options actions
		add_action( 'admin_menu', array( $this, 'maybe_add_theme_menu' ), 5 ); 						# create the theme options menu
		add_action( 'admin_init', array( $this, 'register_settings' ) ); 							# set up settings
		add_action( 'admin_notices', array( $this, 'notices' ) ); 									# set up notices
		add_action( 'admin_init', array( $this, 'settings_init' ) ); 								# load the page content (metaboxes or custom form)
		add_filter( 'pre_update_option_' . $this->settings_field, array( $this, 'save' ), 10, 2 ); 	#add a sanitizer/validator
	}
	
	/**
	 * Possibly create a new top level theme menu.
	 *
	 * @since 1.0.0
	 */
	public function maybe_add_theme_menu() {
		# Maybe add theme menu
		if ( isset( $this->menu_ops['theme_menu'] ) && is_array( $this->menu_ops['theme_menu'] ) ) {
			$menu = wp_parse_args( $this->menu_ops['theme_menu'],
				array(
					'page_title' => '',
					'menu_title' => '',
					'capability' => 'edit_theme_options'
				)
			);
			$this->pagehook = add_theme_page( $menu['page_title'], $menu['menu_title'], $menu['capability'], $this->page_id, array( $this, 'admin' ) );
		}
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
	 * Helper function that constructs id attributes for use in form fields.
	 *
	 * @since 1.0.0
	 */
	protected function get_field_id( $id ) {
		return sprintf( '%s[%s]', $this->settings_field, $id );
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
			<?php screen_icon( $this->page_ops['screen_icon'] ); ?>
			<h2>
				<?php do_action( 'bizznis_admin_title_left', $this->pagehook ); ?>
				<?php echo esc_html( get_admin_page_title() ); ?>
				<?php do_action( 'bizznis_admin_title_right', $this->pagehook ); ?>
			</h2>
			<?php do_action( $this->pagehook . '_settings_page_form', $this->pagehook ); ?>
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
		add_action( $this->pagehook . '_settings_page_form', array( $this, 'form' ) );
		if ( method_exists( $this, 'help' ) ) {
			add_action( 'load-' . $this->pagehook, array( $this, 'help' ) );
		}
	}

}

/**
 * Abstract subclass of Bizznis_Admin which adds support for registering and displaying metaboxes.
 *
 * @since 1.0.0
 */
abstract class Bizznis_Admin_Boxes extends Bizznis_Admin {

	/**
	 * Register the metaboxes.
	 *
	 * @since 1.0.0
	 */
	abstract public function metaboxes();

	/**
	 * Include the necessary sortable metabox scripts.
	 *
	 * @since 1.0.0
	 */
	public function scripts() {
		wp_enqueue_script( 'common' );
		wp_enqueue_script( 'wp-lists' );
		wp_enqueue_script( 'postbox' );
	}
	
	/**
	 * Use this as the settings admin callback to create an admin page with sortable metaboxes.
	 *
	 * @since 1.0.0
	 */
	public function admin() {
		global $wp_meta_boxes;
		$screen = get_current_screen();
		?>
		<div class="wrap bizznis-admin bizznis-metaboxes">
		<form method="post" action="options.php">
			<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
			<?php settings_fields( $this->settings_field ); ?>
			<?php screen_icon( $this->page_ops['screen_icon'] ); ?>
			<h2>
				<?php do_action( 'bizznis_admin_title_left', $this->pagehook ); ?>
				<?php echo esc_html( get_admin_page_title() ); ?>
				<?php do_action( 'bizznis_admin_title_right', $this->pagehook ); ?>
			</h2>
			<div id="dashboard-widgets-wrap">
				<div id="dashboard-widgets" class="metabox-holder">
					<?php do_action( 'bizznis_admin_before_metaboxes', $this->pagehook ); ?>
					<div id='postbox-container-1' class='postbox-container'>
					<?php  do_meta_boxes( $this->pagehook, 'main', null ); ?>
					</div>
					<div id='postbox-container-2' class='postbox-container'>
					<?php do_meta_boxes( $this->pagehook, 'column2', null ); ?>
					</div>
					<div id='postbox-container-3' class='postbox-container'>
					<?php do_meta_boxes( $this->pagehook, 'column3', null ); ?>
					</div>
					<div id='postbox-container-4' class='postbox-container'>
					<?php do_meta_boxes( $this->pagehook, 'column4', null ); ?>
					</div>
					<?php do_action( 'bizznis_admin_after_metaboxes', $this->pagehook ); ?>
				</div>
			</div>
			<p class="submit bottom-buttons">
				<?php
				submit_button( $this->page_ops['save_button_text'], 'primary', 'submit', false );
				submit_button( $this->page_ops['reset_button_text'], 'secondary', $this->get_field_name( 'reset' ), false, array( 'onclick' => 'return bizznis_confirm(\'' . esc_js( __( 'Are you sure you want to reset?', 'bizznis' ) ) . '\');' ) );
				?>
			</p>
		</form>
		</div>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready( function ($) {
				// close postboxes that should be closed
				$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
				// postboxes setup
				postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			});
			//]]>
		</script>
		<?php
	}
	
	/**
	 * Echo out the do_meta_boxes() and wrapping markup.
	 *
	 * This method can be overwritten in a child class, to adjust the markup surrounding the metaboxes, and optionally
	 * call do_meta_boxes() with other contexts. The overwritten method MUST contain div elements with classes of
	 * metabox-holder and postbox-container.
	 *
	 * @since 1.0.0
	 */
	public function do_metaboxes() {

		global $wp_meta_boxes;

		?>
		<div class="metabox-holder">
			<div class="postbox-container">
				<?php
				do_action( 'bizznis_admin_before_metaboxes', $this->pagehook );
				do_meta_boxes( $this->pagehook, 'main', null );
				if ( isset( $wp_meta_boxes[$this->pagehook]['column2'] ) ) {
					do_meta_boxes( $this->pagehook, 'column2', null );
				}
				do_action( 'bizznis_admin_after_metaboxes', $this->pagehook );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Initialize the settings page, by enqueuing scripts
	 *
	 * @since 1.0.0
	 */
	public function settings_init() {
		add_action( 'load-' . $this->pagehook, array( $this, 'scripts' ) );
		add_action( 'load-' . $this->pagehook, array( $this, 'metaboxes' ) );
		add_action( $this->pagehook . '_settings_page_boxes', array( $this, 'do_metaboxes' ) );
		if ( method_exists( $this, 'help' ) ) {
			add_action( 'load-' . $this->pagehook, array( $this, 'help' ) );
		}
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
	public function settings_init() {
		if ( method_exists( $this, 'help' ) ) {
			add_action( 'load-' . $this->pagehook, array( $this, 'help' ) );
		}
	}

}