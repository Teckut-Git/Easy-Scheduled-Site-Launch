<?php
/**
 * Plugin Name: Easy Scheduled Site Launch
 * Description: Show a coming soon page with logo, custom message, countdown, and selectable template until your scheduled launch date.
 * Version: 1.0.0
 * Author: Teckut
 * Author URI: https://teckut.com/
 * Text Domain: easy-scheduled-site-launch
 * Domain Path: /languages
 *
 * Requires at least: 6.7.0
 * Tested up to: 6.8.3
 * WC requires at least: 6.5.0
 * WC tested up to: 10.2.2
 * Stable tag: 1.0.0
 * Requires PHP: 7.4
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Easy_Scheduled_Site_Launch
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initializes the Easy Scheduled Site Launch process.
 *
 * This function includes the main plugin class and runs.
 * the functionality for the scheduled site launch.
 *
 * @since 1.0.0
 */
function run_easy_scheduled_site_launch() {
	 require_once plugin_dir_path( __FILE__ ) . 'includes/class-easy-scheduled-site-launch.php';
}

run_easy_scheduled_site_launch();
