<?php
/**
 * Uninstall plugin.
 *
 * @package     DigitalPilot
 * @subpackage  DigitalPilot Traking
 * @author      Valeriu Tihai
 * @author URI  https://vt9.agency/
 * @link        https://www.digitalpilot.app/
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 * @version     1.0.0
 * @since       1.0.0
 */

// If uninstall not called from WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete the options from the {wp_prefix}_options table.
if ( is_multisite() ) {
	$site_ids = get_sites(
		array(
			'fields' => 'ids',
		)
	);

	foreach ( $site_ids as $site_id ) {
		switch_to_blog( $site_id );
		delete_option( 'digitalpilot_settings' );
		restore_current_blog();
	}
} else {
	delete_option( 'digitalpilot_settings' );
}
