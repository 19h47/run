<?php
/**
 * Run
 *
 * @link              https://www.19h47.fr
 * @since             1.0.0
 * @package           Run
 *
 * @wordpress-plugin
 * Plugin Name: Run
 * Plugin URI: http://github.com/19h47/run
 * Description: Enables a Run post type, taxonomy and metaboxes.
 * Version: 2.0.0
 * Author: Jérémy Levron
 * Author URI: https://www.19h47.fr
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: run
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-run.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_run() {
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugin_data = get_plugin_data( __FILE__ );

	$plugin = new Run( $plugin_data['Version'] );
	$plugin->run();
}
run_run(); // Run, Forrest, run!
