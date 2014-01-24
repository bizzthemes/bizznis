<?php
/*
	WARNING! DO NOT EDIT THIS FILE!
	This file is part of the core Bizznis parent theme. 
	Please do all modifications in the form of a child theme.
*/

/**
 * Pings http://bizznis.bizzthemes.com/ asking if a new version of this theme is available.
 * If not, it returns false.
 *
 * If so, the external server passes serialized data back to this function,
 * which gets unserialized and returned for use.
 *
 * @since 1.0.0
 */
function bizznis_update_check() {	
	# If updates are disabled
	if ( ! bizznis_get_option( 'update' ) || ! current_theme_supports( 'bizznis-auto-updates' ) ) {
		add_filter( 'http_request_args', 'bizznis_prevent_theme_update', 5, 2 );
		return false;
	}
	# Check the updates transient
	$bizznis_update = get_site_transient('update_themes');
	if ( ! isset( $bizznis_update->response ) ) {
		return false;
	}
	$bizznis_update = $bizznis_update->response;
	if ( ! isset( $bizznis_update['bizznis'] ) ) {
		return false;
	}
	else {
		$bizznis_update = $bizznis_update['bizznis'];
	}
	# If we're already using the latest version, return false
	if ( version_compare( PARENT_THEME_VERSION, $bizznis_update['new_version'], '>=' ) ) {
		return false;
	}
	return $bizznis_update;
}

/**
 * Update Bizznis to the latest version.
 *
 * This iterative update function will take a Bizznis installation, no matter
 * how old, and update its options to the latest version.
 *
 * It used to iterate over theme version, but now uses a database version
 * system, which allows for changes within pre-releases, too.
 *
 * @since 1.0.0
 */
add_action( 'admin_init', 'bizznis_upgrade', 20 );
function bizznis_upgrade() {
	# Don't do anything if we're on the latest version
	if ( bizznis_get_option( 'db_version', null, false ) >= PARENT_DB_VERSION ) {
		return;
	}
	/*
	#########################
	# UPDATE TO VERSION 1.0.
	#########################
	if ( version_compare( bizznis_get_option( 'theme_version', null, false ), '1.9', '<' ) ) {
		# Vestige nav settings, for backward compatibility
		if ( 'nav-menu' != bizznis_get_option( 'nav_type' ) ) {
			_bizznis_vestige( array( 'nav_type', 'nav_superfish', 'nav_home', 'nav_pages_sort', 'nav_categories_sort', 'nav_depth', 'nav_exclude', 'nav_include', ) );
		}
		# Vestige subnav settings, for backward compatibility
		if ( 'nav-menu' != bizznis_get_option( 'subnav_type' ) ) {
			_bizznis_vestige( array( 'subnav_type', 'subnav_superfish', 'subnav_home', 'subnav_pages_sort', 'subnav_categories_sort', 'subnav_depth', 'subnav_exclude', 'subnav_include', ) );
		}
		$theme_settings = get_option( BIZZNIS_SETTINGS_FIELD );
		$new_settings   = array( 'theme_version' => '1.6', );
		$settings = wp_parse_args( $new_settings, $theme_settings );
		update_option( BIZZNIS_SETTINGS_FIELD, $settings );
	}
	*/
	# UPDATE DB TO VERSION 1005
	if ( bizznis_get_option( 'db_version', null, false ) < '1005' ) {
		bizznis_upgrade_1005();
	}
	do_action( 'bizznis_upgrade' );
}

/**
 * Upgrade the database to version 1004.
 *
 * @since 1.0.5
 */
function bizznis_upgrade_1005() {
	# Update Settings
	_bizznis_update_settings( array(
		'theme_version' => '1.0.5',
		'db_version'    => '1005',
	) );
}

/**
 * Redirects the user back to the theme settings page, refreshing the data and
 * notifying the user that they have successfully updated.
 *
 * @since 1.0.0
 */
add_action( 'bizznis_upgrade', 'bizznis_upgrade_redirect' );
function bizznis_upgrade_redirect() {
	if ( ! is_admin() || ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}
	bizznis_admin_redirect( 'bizznis-about' );
	exit;
}

/**
 * Displays the notice that the theme settings were successfully updated to the
 * latest version.
 *
 * Currently, only used for pre-release update notices.
 *
 * @since 1.0.0
 */
add_action( 'admin_notices', 'bizznis_upgraded_notice' );
function bizznis_upgraded_notice() {
	if ( ! bizznis_is_menu_page( 'bizznis' ) ) {
		return;
	}
	if ( isset( $_REQUEST['upgraded'] ) && 'true' == $_REQUEST['upgraded'] ) {
		echo '<div class="updated highlight" id="message"><p><strong>' . sprintf( __( 'Congratulations! You are now rocking Bizznis %s', 'bizznis' ), bizznis_get_option( 'theme_version' ) ) . '</strong></p></div>';
	}
}

/**
 * Filters the action links at the end of an update.
 *
 * This function filters the action links that are presented to the
 * user at the end of a theme update. If the theme being updated is
 * not Bizznis, the filter returns the default values. Otherwise,
 * it will provide a link to the Bizznis Theme Settings page, which
 * will trigger the database/settings upgrade.
 *
 * @since 1.0.0
 */
add_filter( 'update_theme_complete_actions', 'bizznis_update_action_links', 10, 2 );
function bizznis_update_action_links( $actions, $theme ) {
	if ( 'bizznis' != $theme ) {
		return $actions;
	}
	return sprintf( '<a href="%s">%s</a>', menu_page_url( 'bizznis', 0 ), __( 'Click here to complete the upgrade', 'bizznis' ) );
}

/**
 * Displays the update nag at the top of the dashboard if there is a Bizznis
 * update available.
 *
 * @since 1.0.0
 */
add_action( 'admin_notices', 'bizznis_update_nag' );
function bizznis_update_nag() {
	$bizznis_update = bizznis_update_check();
	if ( ! is_super_admin() || ! $bizznis_update ) {
		return false;
	}
	echo '<div class="update-nag">';
	printf(
		__( 'Bizznis %s is available. <a href="%s" onclick="return bizznis_confirm(\'%s\');">Update now</a>.', 'bizznis' ),
		esc_html( $bizznis_update['new_version'] ),
		wp_nonce_url( 'update.php?action=upgrade-theme&amp;theme=bizznis', 'upgrade-theme_bizznis' ),
		esc_js( __( 'Upgrading Bizznis will overwrite the current installed version of Bizznis. Are you sure you want to upgrade?. "Cancel" to stop, "OK" to upgrade.', 'bizznis' ) )
	);
	echo '</div>';
}

/**
 * Sends out update notification email.
 *
 * Does several checks before finally sending out a notification email to the
 * specified email address, alerting it to a Bizznis update available for that install.
 *
 * @since 1.0.0
 */
add_action( 'init', 'bizznis_update_email' );
function bizznis_update_email() {
	# Pull email options from DB
	$email_on = bizznis_get_option( 'update_email' );
	$email    = bizznis_get_option( 'update_email_address' );
	# If we're not supposed to send an email, or email is blank / invalid, stop!
	if ( ! $email_on || ! is_email( $email ) ) {
		return;
	}
	# Check for updates
	$update_check = bizznis_update_check();
	# If no new version is available, stop!
	if ( ! $update_check ) {
		return;
	}
	# If we've already sent an email for this version, stop!
	if ( get_option( 'bizznis-update-email' ) == $update_check['new_version'] ) {
		return;
	}
	# Let's send an email!
	$subject  = sprintf( __( 'Bizznis %s is available for %s', 'bizznis' ), esc_html( $update_check['new_version'] ), home_url() );
	$message  = sprintf( __( 'Bizznis %s is now available. We have provided 1-click updates for this theme, so please log into your dashboard and update at your earliest convenience.', 'bizznis' ), esc_html( $update_check['new_version'] ) );
	$message .= "\n\n" . wp_login_url();
	# Update the option so we don't send emails on every pageload!
	update_option( 'bizznis-update-email', $update_check['new_version'], TRUE );
	# Send that puppy!
	wp_mail( sanitize_email( $email ), $subject, $message );
}

/**
 * Converts array of keys from Bizznis options to vestigial options.
 * This is done for backwards compatibility.
 *
 * @since 1.0.0
 */
function _bizznis_vestige( $keys = array(), $setting = BIZZNIS_SETTINGS_FIELD ) {
	# If no $keys passed, do nothing
	if ( ! $keys ) {
		return;
	}
	# Pull options
	$options = get_option( $setting );
	$vestige = get_option( 'bizznis-vestige' );
	# Cycle through $keys, creating new vestige array
	$new_vestige = array();
	foreach ( (array) $keys as $key ) {
		if ( isset( $options[$key] ) ) {
			$new_vestige[$key] = $options[$key];
			unset( $options[$key] );
		}
	}
	# If no new vestigial options being pushed, do nothing
	if ( ! $new_vestige ) {
		return;
	}
	# Merge the arrays, if necessary
	$vestige = $vestige ? wp_parse_args( $new_vestige, $vestige ) : $new_vestige;
	# Insert into options table
	update_option( 'bizznis-vestige', $vestige );
	update_option( $setting, $options );
}

/**
 * Disable WordPress theme update checks
 * If there is a theme in the repo with the same name, 
 * this prevents WP from prompting an update.
 *
 * @link http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/
 * @author Mark Jaquith
 * @since 1.0.0
 * 
 */
function bizznis_prevent_theme_update( $r, $url ) {
    if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) ) {
		return $r; # Not a theme update request. Bail immediately.
	}
	$themes = unserialize( $r['body']['themes'] );
	unset( $themes[ get_option( 'template' ) ] );
	unset( $themes[ get_option( 'stylesheet' ) ] );
	$r['body']['themes'] = serialize( $themes );
	return $r;
}