<?php
/**
 * Extend Bizznis core with WP CLI command
 *
 * Use this class to extend core functionality with
 * WP CLI commands
 *
 * @since 1.2.0
 */

class Bizznis_CLI_Command extends WP_CLI_Command {

	/**
     * Upgrade the Bizznis settings, usually after an upgrade.
     * 
     * ## EXAMPLES
     * 
     *     wp bizznis upgrade-db
     *
     */
	public function upgrade_db( $args, $assoc_args ) {

		// Call the upgrade function
		bizznis_upgrade();

		WP_CLI::success( __( 'Bizznis database upgraded.', 'bizznis' ) );

	}

}

WP_CLI::add_command( 'bizznis', 'Bizznis_CLI_Command' );