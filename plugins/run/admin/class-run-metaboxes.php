<?php

/**
 * Metaboxes
 *
 * @link       http://www.19h47.fr
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
 * @author     Levron Jérémy <levronjeremy@19h47.fr>
 */
class Run_Metaboxes {

	/**
	 * The ID of this plugin.
	 *
	 * @since       1.0.0
	 * @access      private
	 * @var         string          $plugin_name        The ID of this plugin.
	 */
	private $plugin_name;


	/**
	 * The version of this plugin.
	 *
	 * @since       1.0.0
	 * @access      private
	 * @var         string          $version            The current version of this plugin.
	 */
	private $version;


	/**
	 * Constructor
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

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
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
	}

	/**
	 * Adds the meta box
	 *
	 *
	 * $id, $title, $callback, $page, $context, $priority, $callback_args
	 * @see  https://developer.wordpress.org/reference/functions/add_meta_box/
	 */
	public function add_metabox() {
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
	 */
	public function render_metabox( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'custom_nonce_action', 'custom_nonce' );

		// Retrieve an existing value from the database
		$run_duration = get_post_meta( $post->ID, 'run_duration', true );
		$run_steps    = get_post_meta( $post->ID, 'run_steps', true );
		$run_calories = get_post_meta( $post->ID, 'run_calories', true );

		// Set default values
		if ( empty( $run_duration ) ) {
			$run_duration = '';
		}

		if ( empty( $run_steps ) ) {
			$run_steps = '';
		}

		if ( empty( $run_calories ) ) {
			$run_calories = '';
		}

		include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-form.php' );
	}

	/**
	 * Handles saving the meta box
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 * @return null
	 */
	public function save_metabox( $post_id, $post ) {
		// Add nonce for security and authentication.
		$nonce_name   = isset( $_POST['custom_nonce'] ) ? $_POST['custom_nonce'] : '';
		$nonce_action = 'custom_nonce_action';

		// Check if nonce is set.
		if ( ! isset( $nonce_name ) ) {
			return;
		}

		// Check if nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}

		// Check if user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Sanitize user input.
		$run_duration = isset( $_POST['run_duration'] ) ? sanitize_text_field( $_POST['run_duration'] ) : '';
		$run_steps    = isset( $_POST['run_steps'] ) ? sanitize_text_field( $_POST['run_steps'] ) : '';
		$run_calories = isset( $_POST['run_calories'] ) ? sanitize_text_field( $_POST['run_calories'] ) : '';

		// Update the meta field in the database.
		update_post_meta( $post_id, 'run_duration', $run_duration );
		update_post_meta( $post_id, 'run_steps', $run_steps );
		update_post_meta( $post_id, 'run_calories', $run_calories );
	}
}
