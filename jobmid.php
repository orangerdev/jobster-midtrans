<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://ridwan-arifandi.com
 * @since             1.0.0
 * @package           Jobmid
 *
 * @wordpress-plugin
 * Plugin Name:       JobSter - Midtrans Payment Gateway
 * Plugin URI:        https://ridwan-arifandi.com
 * Description:       Integrate midtrans (veritrans) payment gateway to WPJobster marketplace theme (https://wpjobster.com)
 * Version:           1.0.0
 * Author:            Ridwan Arifandi
 * Author URI:        https://ridwan-arifandi.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       jobmid
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'JOBMID_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-jobmid-activator.php
 */
function activate_jobmid() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobmid-activator.php';
	Jobmid_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-jobmid-deactivator.php
 */
function deactivate_jobmid() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-jobmid-deactivator.php';
	Jobmid_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_jobmid' );
register_deactivation_hook( __FILE__, 'deactivate_jobmid' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-jobmid.php';
require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_jobmid() {

	$plugin = new Jobmid();
	$plugin->run();

}

if(!function_exists('__debug')) :

function __debug()
{
		$bt     = debug_backtrace();
		$caller = array_shift($bt);
		?><pre class='debug'><?php
		print_r([
			"file"  => $caller["file"],
			"line"  => $caller["line"],
			"args"  => func_get_args()
		]);
		?></pre><?php
}
endif;

run_jobmid();
