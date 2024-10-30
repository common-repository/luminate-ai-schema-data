<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.luminate.ai/
 * @since             1.0.0
 * @package           Lumaisd
 *
 * @wordpress-plugin
 * Plugin Name:       Luminate AI - Schema Data
 * Description:       Luminate AI - Schema Data is a simple and elegant content enrichment plugin that helps you set up structured data in minutes.
 * Version:           1.2.1
 * Author:            Luminate AI
 * Author URI:        https://www.luminate.ai/
 * Text Domain:       lumaisd
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
define( 'LUMAISD_NAME', 'lumaisd' );
define( 'LUMAISD_VERSION', '1.2.1' );
define( 'LUMAISD_API', 'http://gcp.luminate.ai/' );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-lumaisd-activator.php
 */
function activate_lumaisd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lumaisd-activator.php';
	Lumaisd_Activator::activate();
}
/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-lumaisd-deactivator.php
 */
function deactivate_lumaisd() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-lumaisd-deactivator.php';
	Lumaisd_Deactivator::deactivate();
}
register_activation_hook( __FILE__, 'activate_lumaisd' );
register_deactivation_hook( __FILE__, 'deactivate_lumaisd' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-lumaisd.php';
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_lumaisd() {
	$plugin = new Lumaisd();
	$plugin->run();
}
run_lumaisd();