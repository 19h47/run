<?php

/**
 * Quick edit
 *
 * @link       https://www.19h47.fr
 * @since      1.0.0
 *
 * @package    Run
 * @subpackage Run/admin
 */


/**
 * Quick edit
 *
 * @since      1.0.0
 * @package    Run
 * @subpackage Run/includes
 * @author     Jérémy Levron <jeremylevron@19h47.fr>
 */
class Run_Quick_Edit {

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

		add_action( 'quick_edit_custom_box', array( $this, 'add_quick_edit_custom_box' ), 10, 3 );
		add_action( 'admin_print_scripts-edit.php', array( $this, 'enqueue_script_quick_edit' ) );
		add_action( 'save_post', array( $this, 'save_quick_edit' ), 10, 2 );
		add_action( 'wp_ajax_manage_wp_posts_using_bulk_quick_save_bulk_edit', array( $this, 'manage_quick_edit' ) );
	}


	/**
	 * Add quick edit custom box
	 *
	 * @param string $column_name Name of the column to edit.
	 * @param string $post_type The post type slug, or current screen name if this is a taxonomy list table.
	 * @param string $taxonomy The taxonomy name, if any.
	 */
	public function add_quick_edit_custom_box( string $column_name, string $post_type, string $taxonomy ) {

		switch ( $column_name ) {
			case 'steps':
				include plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-quick-edit-number.php';

				break;

			case 'duration':
				include plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-quick-edit-time.php';

				break;

			case 'calories':
				include plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-quick-edit-number.php';

				break;
		}
	}


	function enqueue_script_quick_edit() {

		wp_enqueue_script(
			'manage-wp-posts-using-bulk-quick-edit',
			plugin_dir_url( __FILE__ ) . 'js/' . $this->plugin_name . '.js',
			array( 'jquery', 'inline-edit-post' ),
			null,
			true
		);

	}


	function save_quick_edit( $post_id, $post ) {

		// pointless if $_POST is empty (this happens on bulk edit)
		if ( empty( $_POST ) ) {
			return $post_id;
		}

		// verify quick edit nonce
		if ( isset( $_POST['_inline_edit'] ) && ! wp_verify_nonce( $_POST['_inline_edit'], 'inlineeditnonce' ) ) {
			return $post_id;
		}

		// don't save for autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// dont save for revisions
		if ( isset( $post->post_type ) && 'revision' === $post->post_type ) {
			return $post_id;
		}

		switch ( $post->post_type ) {

			case 'run':
				$custom_fields = array( 'run_duration', 'run_steps', 'run_calories' );

				foreach ( $custom_fields as $field ) {

					if ( array_key_exists( $field, $_POST ) ) {
						update_post_meta( $post_id, $field, $_POST[ $field ] );
					}
				}

				break;
		}
	}


	/**
	 * Manage quick edit
	 */
	function manage_quick_edit() {

		// we need the post IDs
		$post_ids = ( isset( $_POST['post_ids'] ) && ! empty( $_POST['post_ids'] ) ) ? $_POST['post_ids'] : null;

		// if we have post IDs
		if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {

			// get the custom fields
			$custom_fields = array( 'run_duration', 'run_steps', 'run_calories' );

			foreach ( $custom_fields as $field ) {

				// if it has a value, doesn't update if empty on bulk
				if ( isset( $_POST[ $field ] ) && ! empty( $_POST[ $field ] ) ) {

					// update for each post ID
					foreach ( $post_ids as $post_id ) {
						update_post_meta( $post_id, $field, $_POST[ $field ] );
					}
				}
			}
		}
	}
}
