<?php

/**
 * Run Post Type
 *
 * @link              http://www.19h47.fr
 * @since             1.0.0
 * @package           Run
 *
 * @wordpress-plugin
 * Plugin Name:       	Run
 * Plugin URI:        	http://stepper.19h47.fr/
 * Description:       	Enables a run post type, taxonomy and metaboxes.
 * Version:           	1.0.0
 * Author:            	Levron Jérémy
 * Author URI:        	http://www.19h47.fr
 * License:           	GPL-2.0+
 * License URI:       	http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       	run
 * Domain Path:       	/languages
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
 * @since 		1.0.0
 */
function run_Run() {
	$plugin = new Run();
	$plugin->run();
}
run_Run(); // Run, Forrest, run!