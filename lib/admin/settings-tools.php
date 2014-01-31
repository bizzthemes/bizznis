<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Register a new admin page, providing content and corresponding menu item for the Import / Export page.
 *
 * @since 1.0.0
 */
class Bizznis_Admin_Import_Export extends Bizznis_Admin_Basic {

	/**
	 * Create an admin menu item and settings page.
	 *
	 * Also hook in the handling of file imports and exports.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$page_id = 'bizznis-tools';
		$menu_ops = array(
			'theme_menu' => array(
				'parent_slug' => 'bizznis',
				'page_title'  => __( 'Theme Import/Export Tools', 'bizznis' ),
				'menu_title'  => __( 'Theme Tools', 'bizznis' ),
				'capability'  => 'edit_theme_options',
			)
		);
		$this->create( $page_id, $menu_ops );
		add_action( 'admin_init', array( $this, 'export' ) );
		add_action( 'admin_init', array( $this, 'import' ) );
	}
	
	/**
	 * Contextual help content.
	 *
	 * @since 1.0.0
	 */
	public function help() {
		$screen = get_current_screen();
		$general_settings_help =
			'<h3>' . __( 'Import/Export', 'bizznis' ) . '</h3>' .
			'<p>'  . __( 'This allows you to import or export Bizznis Settings.', 'bizznis' ) . '</p>' .
			'<p>'  . __( 'This is specific to Bizznis settings and does not includes posts, pages, or images, which is what the built-in WordPress import/export menu does.', 'bizznis' ) . '</p>' .
			'<p>'  . __( 'It also does not include other settings for plugins, widgets, or post/page/term/user specific settings.', 'bizznis' ) . '</p>';
		$import_settings_help =
			'<h3>' . __( 'Import', 'bizznis' ) . '</h3>' .
			'<p>'  . sprintf( __( 'You can import a file you\'ve previously exported. The file name will start with %s followed by one or more strings indicating which settings it contains, finally followed by the date and time it was exported.', 'bizznis' ), bizznis_code( 'bizznis-' ) ) . '</p>' .
			'<p>' . __( 'Once you upload an import file, it will automatically overwrite your existing settings.', 'bizznis' ) . ' <strong>' . __( 'This cannot be undone', 'bizznis' ) . '</strong>.</p>';
		$export_settings_help =
			'<h3>' . __( 'Export', 'bizznis' ) . '</h3>' .
			'<p>'  . sprintf( __( 'You can export your Bizznis-related settings to back them up, or copy them to another site. Child themes and plugins may add their own checkboxes to the list. The settings are exported in %s format.', 'bizznis' ), '<abbr title="' . __( 'JavaScript Object Notation', 'bizznis' ) . '">' . __( 'JSON', 'bizznis' ) . '</abbr>' ) . '</p>';

		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-general-settings',
			'title'   => __( 'Import/Export', 'bizznis' ),
			'content' => $general_settings_help,
		) );
		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-import',
			'title'   => __( 'Import', 'bizznis' ),
			'content' => $import_settings_help,
		) );
		$screen->add_help_tab( array(
			'id'      => $this->pagehook . '-export',
			'title'   => __( 'Export', 'bizznis' ),
			'content' => $export_settings_help,
		) );
		# Add help sidebar
		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'bizznis' ) . '</strong></p>' .
			'<p><a href="' . sprintf( __( '%s', 'bizznis' ), 'http://bizzthemes.com/support/' ) . '" target="_blank" title="' . __( 'Get Support', 'bizznis' ) . '">' . __( 'Get Support', 'bizznis' ) . '</a></p>'
		);
	}

	/**
	 * Callback for displaying the Bizznis Import / Export admin page.
	 *
	 * Call the bizznis_tools_form action after the last default table row.
	 *
	 * @since 1.0.0
	 */
	public function admin() {
		?>
		<div class="wrap">
			<?php screen_icon( 'tools' ); ?>
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><b><?php _e( 'Import Settings', 'bizznis' ); ?></p></th>
						<td>
							<p class="description"><?php printf( __( 'Upload the data file (%s) from your computer and we\'ll import your settings.', 'bizznis' ), bizznis_code( '.json' ) ); ?></p>
							<p class="description"><?php _e( 'Choose the file from your computer and click "Upload file and Import"', 'bizznis' ); ?></p>
							<p>
								<form enctype="multipart/form-data" method="post" action="<?php echo menu_page_url( 'bizznis-tools', 0 ); ?>">
									<?php wp_nonce_field( 'bizznis-import' ); ?>
									<input type="hidden" name="bizznis-import" value="1" />
									<label for="bizznis-import-upload"><?php sprintf( __( 'Upload File: (Maximum Size: %s)', 'bizznis' ), ini_get( 'post_max_size' ) ); ?></label>
									<input type="file" id="bizznis-import-upload" name="bizznis-import-upload" size="25" />
									<?php
									submit_button( __( 'Upload File and Import', 'bizznis' ), 'primary', 'upload' );
									?>
								</form>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row"><b><?php _e( 'Export Settings', 'bizznis' ); ?></b></th>
						<td>
							<p class="description"><?php printf( __( 'When you click the button below, Bizznis will generate a data file (%s) for you to save to your computer.', 'bizznis' ), bizznis_code( '.json' ) ); ?></p>
							<p class="description"><?php _e( 'Once you have saved the download file, you can use the import function on another site to import this data.', 'bizznis' ); ?></p>
							<p>
								<form method="post" action="<?php echo menu_page_url( 'bizznis-tools', 0 ); ?>">
									<?php
									wp_nonce_field( 'bizznis-export' );
									$this->export_checkboxes();
									if ( $this->get_export_options() )
										submit_button( __( 'Download Export File', 'bizznis' ), 'primary', 'download' );
									?>
								</form>
							</p>
						</td>
					</tr>
					<?php do_action( 'bizznis_tools_form' ); ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	/**
	 * Add custom notices that display after successfully importing or exporting the settings.
	 *
	 * @since 1.0.0
	 */
	public function notices() {
		if ( ! bizznis_is_menu_page( 'bizznis-tools' ) ) {
			return;
		}
		if ( isset( $_REQUEST['imported'] ) && 'true' === $_REQUEST['imported'] ) {
			echo '<div id="message" class="updated"><p><strong>' . __( 'Settings successfully imported.', 'bizznis' ) . '</strong></p></div>';
		}
		elseif ( isset( $_REQUEST['error'] ) && 'true' === $_REQUEST['error'] ) {
			echo '<div id="message" class="error"><p><strong>' . __( 'There was a problem importing your settings. Please try again.', 'bizznis' ) . '</strong></p></div>';
		}
	}

	/**
	 * Return array of export options and their arguments.
	 *
	 * Plugins and themes can hook into the bizznis_export_options filter to add
	 * their own settings to the exporter.
	 *
	 * @since 1.6.0
	 */
	protected function get_export_options() {
		$options = array(
			'theme' => array(
				'label'          => __( 'Theme Settings', 'bizznis' ),
				'settings-field' => BIZZNIS_SETTINGS_FIELD,
			),
			/* Disabled SEO options:
			'seo' => array(
				'label' => __( 'SEO Settings', 'bizznis' ),
				'settings-field' => BIZZNIS_SEO_SETTINGS_FIELD,
			)
			*/
		);
		return (array) apply_filters( 'bizznis_export_options', $options );
	}

	/**
	 * Echo out the checkboxes for the export options.
	 *
	 * @since 1.6.0
	 *
	 * @uses \Bizznis_Admin_Import_Export::get_export_options() Get array of export options.
	 *
	 * @return null Return null if there are no options to export.
	 */
	protected function export_checkboxes() {
		if ( ! $options = $this->get_export_options() ) {
			# Not even the Bizznis theme / seo export options were returned from the filter
			printf( '<p><em>%s</em></p>', __( 'No export options available.', 'bizznis' ) );
			return;
		}
		foreach ( $options as $name => $args ) {
			# Ensure option item has an array key, and that label and settings-field appear populated
			if ( is_int( $name ) || ! isset( $args['label'] ) || ! isset( $args['settings-field'] ) || '' === $args['label'] || '' === $args['settings-field'] ) {
				return;
			}
			printf( '<p><label for="bizznis-export-%1$s"><input id="bizznis-export-%1$s" name="bizznis-export[%1$s]" type="checkbox" value="1" /> %2$s</label></p>', esc_attr( $name ), esc_html( $args['label'] ) );
		}
	}

	/**
	 * Generate the export file, if requested, in JSON format.
	 *
	 * After checking we're on the right page, and trying to export, loop through the list of requested options to
	 * export, grabbing the settings from the database, and building up a file name that represents that collection of
	 * settings.
	 *
	 * A .json file is then sent to the browser, named with "bizznis" at the start and ending with the current
	 * date-time.
	 *
	 * The bizznis_export action is fired after checking we can proceed, but before the array of export options are
	 * retrieved.
	 *
	 * @since 1.0.0
	 */
	public function export() {
		if ( ! bizznis_is_menu_page( 'bizznis-tools' ) ) {
			return;
		}
		if ( empty( $_REQUEST['bizznis-export'] ) ) {
			return;
		}
		check_admin_referer( 'bizznis-export' );
		do_action( 'bizznis_export', $_REQUEST['bizznis-export'] );
		$options = $this->get_export_options();
		$settings = array();
		# Exported file name always starts with "bizznis"
		$prefix = array( 'bizznis' );
		# Loop through set(s) of options
		foreach ( (array) $_REQUEST['bizznis-export'] as $export => $value ) {
			# Grab settings field name (key)
			$settings_field = $options[$export]['settings-field'];
			# Grab all of the settings from the database under that key
			$settings[$settings_field] = get_option( $settings_field );
			# Add name of option set to build up export file name
			$prefix[] = $export;
		}
		if ( ! $settings ) {
			return;
		}
		# Complete the export file name by joining parts together
		$prefix = join( '-', $prefix );
	    $output = json_encode( (array) $settings );
		# Prepare and send the export file to the browser
	    header( 'Content-Description: File Transfer' );
	    header( 'Cache-Control: public, must-revalidate' );
	    header( 'Pragma: hack' );
	    header( 'Content-Type: text/plain' );
	    header( 'Content-Disposition: attachment; filename="' . $prefix . '-' . date( 'Ymd-His' ) . '.json"' );
	    header( 'Content-Length: ' . mb_strlen( $output ) );
	    echo $output;
	    exit;
	}

	/**
	 * Handle the file uploaded to import settings.
	 *
	 * Upon upload, the file contents are JSON-decoded. If there were errors, or no options to import, then reload the
	 * page to show an error message.
	 *
	 * Otherwise, loop through the array of option sets, and update the data under those keys in the database.
	 * Afterwards, reload the page with a success message.
	 *
	 * Calls bizznis_import action is fired after checking we can proceed, but before attempting to extract the contents
	 * from the uploaded file.
	 *
	 * @since 1.0.0
	 */
	public function import() {
		if ( ! bizznis_is_menu_page( 'bizznis-tools' ) ) {
			return;
		}
		if ( empty( $_REQUEST['bizznis-import'] ) ) {
			return;
		}
		check_admin_referer( 'bizznis-import' );
		do_action( 'bizznis_import', $_REQUEST['bizznis-import'], $_FILES['bizznis-import-upload'] );
		# WP filesystem check
		$url = wp_nonce_url( 'themes.php?page=bizznis-tools' );
		if ( false === ( $creds = request_filesystem_credentials( $url, '', false, false, null ) ) ) {
			return;
		}
		# Try to get the filesystem running
		if ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials($url, '', true, false, null);
			return;
		}
		# Let's finally use the filesystem
		global $wp_filesystem;
		$upload = $wp_filesystem->get_contents( $_FILES['bizznis-import-upload']['tmp_name'] );
		# Decode json format
		$options = json_decode( $upload, true );
		# Check for errors
		if ( ! $options || $_FILES['bizznis-import-upload']['error'] ) {
			bizznis_admin_redirect( 'bizznis-tools', array( 'error' => 'true' ) );
			exit;
		}
		# Identify the settings keys that we should import
		$exportables = $this->get_export_options();
		$importable_keys = array();
		foreach ( $exportables as $exportable ) {
			$importable_keys[] = $exportable['settings-field'];
		}
		# Cycle through data, import Bizznis settings
		foreach ( (array) $options as $key => $settings ) {
			if ( in_array( $key, $importable_keys ) ) {
				update_option( $key, $settings );
			}
		}
		# Redirect, add success flag to the URI
		bizznis_admin_redirect( 'bizznis-tools', array( 'imported' => 'true' ) );
		exit;
	}

}
