<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://www.19h47.fr
 * @since      1.0.0
 *
 * @package    Run
 * @subpackage run/admin
 */


/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @since      1.0.0
 * @package    Run
 * @subpackage run/admin
 * @author     Jérémy Levron <jeremylevron@19h47.fr>
 */
class Run_Admin {

	/**
	 * The name of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;


	/**
	 * The version of this plugin.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var string $plugin_version The current version of this plugin.
	 */
	private $plugin_version;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $plugin_version The version of this plugin.
	 */
	public function __construct( $plugin_name, $plugin_version ) {
		$this->plugin_name    = $plugin_name;
		$this->plugin_version = $plugin_version;

		$this->load_dependencies();
	}


	private function load_dependencies() {

		/**
		 * Registrations
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-run-registrations.php';

		new Run_Registrations( $this->plugin_name, $this->plugin_version );

		/**
		 * Metaxboxes
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-run-metaboxes.php';

		new Run_Metaboxes( $this->plugin_name, $this->plugin_version );

		/**
		 * Columns
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-run-columns.php';

		new Run_Columns( $this->plugin_name, $this->plugin_version );

		/**
		 * Quick edit
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-run-quick-edit.php';

		new Run_Quick_Edit( $this->plugin_name, $this->plugin_version );
	}
}
