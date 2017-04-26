<?php

/**
 * Register a meta box using a class.
 */
class Meta_Box {

	/**
     * The unique identifier of this theme.
     *
     * @since       1.0.0
     * @access      protected
     * @var         string          $plugin_name        The string used to 
     *                                                  uniquely identify this 
     *                                                  theme.
     */
    protected $theme_name;

 
    /**
     * Constructor
     */
    public function __construct() {
        $this->theme_name = $theme_name;
        
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
        	__( 'Information', $this->theme_name ), 
        	array( $this, 'render_metabox' ),
        	'run', 
        	'side', 
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
        $run_steps = get_post_meta( $post->ID, 'run_steps', true );

        // Set default values
        if( empty( $run_duration ) ) $run_duration = '';
        if( empty( $run_steps ) ) $run_steps = '';

        // Form fields.
        echo '<table class="form-table">';

        echo '<tr>';
        echo '<th><label for="run_duration" class="run_duration_label">';
        echo __( 'Duration', $this->theme_name );
        echo '</label></th>';
        echo '<td>';
        echo '<input type="time" id="run_duration" name="run_duration" class="run_duration_field" placeholder="' . esc_attr__( '', $this->theme_name ) . '" value="' . esc_attr__( $run_duration ) . '">';
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<th><label for="run_steps" class="run_steps_label">' . __( 'Steps', $this->theme_name ) . '</label></th>';
        echo '<td>';
        echo '<input type="number" id="run_steps" name="run_steps" class="run_steps_field" placeholder="' . esc_attr__( '', $this->theme_name ) . '" value="' . esc_attr__( $run_steps ) . '">';
        echo '</td>';
        echo '</tr>';

        echo '</table>';
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
        $run_duration = isset( $_POST[ 'run_duration' ] ) ? sanitize_text_field( $_POST[ 'run_duration' ] ) : '';
        $run_steps = isset( $_POST[ 'run_steps' ] ) ? sanitize_text_field( $_POST[ 'run_steps' ] ) : '';

        // Update the meta field in the database.
        update_post_meta( $post_id, 'run_duration', $run_duration );
        update_post_meta( $post_id, 'run_steps', $run_steps );
    }
}