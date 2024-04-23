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

// If uninstall not called form WordPress, exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Delete the options from the {wp_prefix}_options table.
if ( is_multisite() ) {
	delete_site_option( 'digitalpilot_settings' );
} else {
	delete_option( 'digitalpilot_settings' );
}
