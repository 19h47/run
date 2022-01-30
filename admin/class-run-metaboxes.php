<?php
/**
 * Metaboxes
 *
 * @link       https://www.19h47.fr
 * @since      1.0.0
 *
 * @package    Run
 * @subpackage run/admin
 */

/**
 * Metaboxes
 *
 * @since      1.0.0
 * @package    Run
 * @subpackage run/adlin
 * @author     Jérémy Levron <jeremylevron@19h47.fr>
 */
class Run_Metaboxes {

	/**
	 * The plugin name
	 *
	 * @since       1.0.0
	 * @access      private
	 * @var         string $plugin_name The name of this plugin.
	 */
	private $plugin_name;


	/**
	 * The plugin version
	 *
	 * @since       1.0.0
	 * @access      private
	 * @var         string $plugin_version The version of this plugin.
	 */
	private $plugin_version;


	/**
	 * Constructor
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $plugin_version     The version of this plugin.
	 */
	public function __construct( string $plugin_name, string $plugin_version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $plugin_version;

		if ( is_admin() ) {
			add_action( 'load-post.php', array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );
		}
	}


	/**
	 * Meta box initialization
	 *
	 * @see https://generatewp.com/snippet/90jakpm/
	 */
	public function init_metabox() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
	}

	/**
	 * Add Meta Box
	 *
	 * $id, $title, $callback, $page, $context, $priority, $callback_args
	 *
	 * @see  https://developer.wordpress.org/reference/functions/add_meta_box/
	 */
	public function add_meta_box() {
		add_meta_box(
			'run_information',
			__( 'Information', 'run' ),
			array( $this, 'render_metabox' ),
			'run',
			'normal',
			'default'
		);
	}


	/**
	 * Renders the meta box
	 *
	 * @param WP_Post $post The post object.
	 */
	public function render_metabox( WP_Post $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'run_nonce_action', 'run_nonce' );

		// Retrieve an existing value from the database.
		$run_duration = get_run_duration( $post->ID );
		$run_steps    = get_run_steps( $post->ID );
		$run_calories = get_run_calories( $post->ID );

		// Set default values.
		if ( empty( $run_duration ) ) {
			$run_duration = '';
		}

		if ( empty( $run_steps ) ) {
			$run_steps = '';
		}

		if ( empty( $run_calories ) ) {
			$run_calories = '';
		}

		include plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-form.php';
	}

	/**
	 * Handles saving the meta box
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return null
	 */
	public function save_metabox( $post_id, $post ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['run_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['run_nonce'] ) ), 'run_nonce_action' ) ) {
			return;
		}

		// Sanitize user input.
		$run_duration = isset( $_POST['run_duration'] ) ? sanitize_text_field( wp_unslash( $_POST['run_duration'] ) ) : '';
		$run_steps    = isset( $_POST['run_steps'] ) ? sanitize_text_field( wp_unslash( $_POST['run_steps'] ) ) : '';
		$run_calories = isset( $_POST['run_calories'] ) ? sanitize_text_field( wp_unslash( $_POST['run_calories'] ) ) : '';

		// Update the meta field in the database.
		update_post_meta( $post_id, 'run_duration', $run_duration );
		update_post_meta( $post_id, 'run_steps', $run_steps );
		update_post_meta( $post_id, 'run_calories', $run_calories );
	}
}
