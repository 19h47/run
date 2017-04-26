<?php
/**
 * Run class
 */
class Run {
	
	/**
     * The unique identifier of this theme.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this theme.
     */
    protected $theme_name;
    

    /**
     * The version of the theme.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this theme.
     */
    private $theme_version;
	

	/**
	 * Construct function
	 *
	 * @access public
	 */
	public function __construct( $theme_name, $theme_version ) {
		$this->theme_name = $theme_name;
        $this->theme_version = $theme_version;
        
        $this->register_run_post_type();
        add_action( 'init', array( &$this, 'register_post_type' ) );
        
        if ( is_admin() ) {
			add_action( 'load-post.php', array( &$this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( &$this, 'init_metabox' ) );
		}

		add_filter( 'manage_run_posts_columns', array( &$this, 'add_run_columns' ) );
		add_action( 'manage_run_posts_custom_column' , array( &$this, 'run_custom_columns' ), 10, 2 );

		add_filter( 'manage_edit-run_sortable_columns', array( &$this, 'sortable_run_column' ) );
		add_action( 'pre_get_posts', array( &$this, 'steps_orderby' ) ); 

		add_action( 'quick_edit_custom_box',  array( &$this, 'add_quick_edit' ), 10, 2 );
		add_action( 'admin_print_scripts-edit.php', array( &$this, 'enqueue_script_quick_edit' ) );
		add_action( 'save_post', array( &$this, 'save_quick_edit' ), 10, 2 );
		add_action( 'wp_ajax_manage_wp_posts_using_bulk_quick_save_bulk_edit', array( &$this, 'manage_quick_edit' ) );
        
	}


	/**
	 * init metabox description
	 * 
	 * @see https://generatewp.com/snippet/90jakpm/
	 */
	public function init_metabox() {

		add_action( 'add_meta_boxes', array( &$this, 'add_metabox' ) );
		add_action( 'save_post', array( &$this, 'save_metabox' ), 10, 2 );

	}
	
	/**
	 * Register Custom Post Type
	 */
	public function register_run_post_type() {
		$labels = array(
			'name'                  => __( 'Runs', $this->theme_name ),
			'singular_name'         => __( 'Run', $this->theme_name ),
			'menu_name'             => __( 'Runs', $this->theme_name ),
			'name_admin_bar'        => __( 'Run', $this->theme_name ),
			'archives'              => __( 'Run Archives', $this->theme_name ),
			'attributes'			=> __( 'Item Attributes', $this->theme_name ),
			'parent_item_colon'     => __( 'Parent Run:', $this->theme_name ),
			'all_items'             => __( 'All runs', $this->theme_name ),
			'add_new_item'          => __( 'Add New Run', $this->theme_name ),
			'add_new'               => __( 'Add New', $this->theme_name ),
			'new_item'              => __( 'New Run', $this->theme_name ),
			'edit_item'             => __( 'Edit run', $this->theme_name ),
			'update_item'           => __( 'Update run', $this->theme_name ),
	        'view_item'             => __( 'View Run', $this->theme_name ),
			'view_items'            => __( 'View Runs', $this->theme_name ),
			'search_items'          => __( 'Search Run', $this->theme_name ),
			'not_found'             => __( 'Not found', $this->theme_name ),
			'not_found_in_trash'    => __( 'Not found in Trash', $this->theme_name ),
			'featured_image'        => __( 'Featured Image', $this->theme_name ),
			'set_featured_image'    => __( 'Set featured image', $this->theme_name ),
			'remove_featured_image' => __( 'Remove featured image', $this->theme_name ),
			'use_featured_image'    => __( 'Use as featured image', $this->theme_name ),
			'insert_into_item'      => __( 'Insert into run', $this->theme_name ),
			'uploaded_to_this_item' => __( 'Updloaded to this run', $this->theme_name ),
			'items_list'            => __( 'Runs list', $this->theme_name ),
			'items_list_navigation' => __( 'Runs list navigation', $this->theme_name ),
			'filter_items_list'     => __( 'Filtrer runs list', $this->theme_name ),
		);

		$rewrite = array(
	        'slug'          => 'runs',
	        'with_front'    => false,
	    );

		$args = array(
			'label'                 => __( 'Run', $this->theme_name ),
			'description'           => __( 'Run description', $this->theme_name ),
			'labels'                => $labels,
			'supports'              => array(),
			'taxonomies'            => array( 'post_tag' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 40,
			'menu_icon'             => 'dashicons-chart-area',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'rewrite'				=> $rewrite,		
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
		);

		register_post_type( 'run', $args );
	}


	/**
	 * Add the meta box container
	 *
	 * $id, $title, $callback, $page, $context, $priority, $callback_args
	 * @see  https://developer.wordpress.org/reference/functions/add_meta_box/
	 */
	public function add_metabox() {
		add_meta_box(
			'run_information', 
			__( 'Information', $this->theme_name ), 
			array( &$this, 'render_metabox' ),
			'run', 
			'side', 
			'default'
		);
	}


	/**
	 * Render metabox description
	 * 
	 * @param $post 
	 */
	public function render_metabox( $post ) {

		// Add nonce for security and authentication.
		wp_nonce_field( 'run_nonce_action', 'run_nonce' );

		// Retrieve an existing value from the database.
		$run_duration = get_post_meta( $post->ID, 'run_duration', true );
		$run_steps = get_post_meta( $post->ID, 'run_steps', true );

		// Set default values.
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
	 * Save the meta when the post is saved
     *
     * @param 	int 	$post_id 	The ID of the post being saved.
	 */
	public function save_metabox( $post_id ) {

		// Add nonce for security and authentication.
		$nonce_name   = $_POST['run_nonce'];
		$nonce_action = 'run_nonce_action';

		// Check if a nonce is set.
		if ( ! isset( $nonce_name ) )
			return;

		// Check if a nonce is valid.
		if ( ! wp_verify_nonce( $nonce_name, $nonce_action ) )
			return;

		// Check if the user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return;

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) )
			return;

		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) )
			return;

		// Sanitize user input.
		$run_duration = isset( $_POST[ 'run_duration' ] ) ? sanitize_text_field( $_POST[ 'run_duration' ] ) : '';
		$run_steps = isset( $_POST[ 'run_steps' ] ) ? sanitize_text_field( $_POST[ 'run_steps' ] ) : '';

		// Update the meta field in the database.
		update_post_meta( $post_id, 'run_duration', $run_duration );
		update_post_meta( $post_id, 'run_steps', $run_steps );

	}


	/**
	 * add run columns
	 * 
	 * @param $columns
	 */
	public function add_run_columns( $columns ) {
	    
	    // unset( $columns['title'] );

	    return array_merge( $columns, 
	    	array( 
	    		'duration' => 'Duration',
	    		'steps' =>'Steps'
	    	) 
	    );
	}


	/**
	 * run custom columns
	 * 
	 * @param $column_name 
	 * @param $post_id     
	 */
	public function run_custom_columns( $column_name, $post_id ) {

	    switch ( $column_name ) {

		    case 'duration' :
		    
		        echo '<div id="run_duration-' . $post_id . '">' . get_post_meta( $post_id, 'run_duration', true ) . '</div>';
		        break;

		    case 'steps' :
		    
		        echo '<div id="run_steps-' . $post_id . '">' . get_post_meta( $post_id, 'run_steps', true ) . '</div>';
		        break;
	    }
	}


	/**
	 * sortable_run_column description
	 * 
	 * @param 	$sortable_columns
	 */
	public function sortable_run_column( $sortable_columns ) {

	    $sortable_columns['duration'] = 'run_duration';
	    $sortable_columns['steps'] = 'run_steps';

	 
	    return $sortable_columns;
	}


	/**
	 * steps orderby
	 * 
	 * @param $query
	 */
	function steps_orderby( $query ) {
	  	
	  	if ( ! is_admin() ) {
	    	return;
	  	}

	    if ( ! $query->is_main_query() ) {
	    	return;
	    } 

	    if ( ! $orderby = $query->get( 'orderby' ) ) {
	    	return;
	    }

	    switch( $orderby ) {
	    	case 'run_steps':
	    				
				$query->set( 'meta_key', 'run_steps' );
				$query->set( 'orderby', 'meta_value_num' );
				
				break;
					
	    }
	}


	/**
	 * add quick edit
	 * 
	 * @param $column_name 
	 * @param $post_type   
	 */
	function add_quick_edit( $column_name, $post_type ) {
	        
        switch ( $column_name ) {

    	    case 'steps' :

		        ?>
				<fieldset class="inline-edit-col-left">
			        <div class="inline-edit-col">
			        	<label>
							<span class="title">Steps</span>
							<span class="input-text-wrap">
								<input type="number" name="run_steps" class="" value="">
							</span>
						</label>
			        </div>
		        </fieldset><?php

        		break;

		    case 'duration' :

        		?>
        		<fieldset class="inline-edit-col-left">
            		<div class="inline-edit-col">
		            	<label>
		    				<span class="title">Duration</span>
		    				<span class="input-text-wrap">
		    					<input type="time" name="run_duration" class="" value="">
		    				</span>
		    			</label>
		            </div>
	         	</fieldset><?php

	            break;
	    }
	}


	function enqueue_script_quick_edit() {
		
		wp_enqueue_script( 
			'manage-wp-posts-using-bulk-quick-edit', 
			trailingslashit( get_bloginfo( 'stylesheet_directory' ) ) . 'inc/post-types/run.js', 
			array( 'jquery', 'inline-edit-post' ), 
			'', 
			true 
		);
		
	}


	function save_quick_edit( $post_id, $post ) {
		
		// pointless if $_POST is empty (this happens on bulk edit)
		if ( empty( $_POST ) )
			return $post_id;
			
		// verify quick edit nonce
		if ( isset( $_POST[ '_inline_edit' ] ) && ! wp_verify_nonce( $_POST[ '_inline_edit' ], 'inlineeditnonce' ) )
			return $post_id;
				
		// don't save for autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
			
		// dont save for revisions
		if ( isset( $post->post_type ) && $post->post_type == 'revision' )
			return $post_id;
			
		switch( $post->post_type ) {
		
			case 'run':
			
				/**
				 * Because this action is run in several places, checking for the array key
				 * keeps WordPress from editing data that wasn't in the form, i.e. if you had
				 * this post meta on your "Quick Edit" but didn't have it on the "Edit Post" screen.
				 */
				$custom_fields = array( 'run_duration', 'run_steps' );
				
				foreach( $custom_fields as $field ) {
				
					if ( array_key_exists( $field, $_POST ) )
						update_post_meta( $post_id, $field, $_POST[ $field ] );
						
				}
					
				break;	
		}
	}


	/**
	 * Manage quick edit
	 */
	function manage_quick_edit() {
		
		// we need the post IDs
		$post_ids = ( isset( $_POST[ 'post_ids' ] ) && ! empty( $_POST[ 'post_ids' ] ) ) ? $_POST[ 'post_ids' ] : NULL;
			
		// if we have post IDs
		if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
		
			// get the custom fields
			$custom_fields = array( 'run_duration', 'run_steps' );
			
			foreach( $custom_fields as $field ) {
				
				// if it has a value, doesn't update if empty on bulk
				if ( isset( $_POST[ $field ] ) && ! empty( $_POST[ $field ] ) ) {
				
					// update for each post ID
					foreach( $post_ids as $post_id ) {
						update_post_meta( $post_id, $field, $_POST[ $field ] );
					}
					
				}
				
			}
			
		}
		
	}
}


/**
 * Get Run key
 *
 * Retrieve the value of a given $key in Run post type
 * 
 * @param  	boolean 				$post_id
 * @param  	string  				$key   
 * @return 	string					$meta
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr          
 */
function get_run_meta( $post_id = false, $key ) {
	
	if( ! $key ) {
		return;
	}

	$meta = get_post_meta( $post_id, $key, true );

	if( empty( $meta ) ) {
		return;
	}

	return $meta;
}

/**
 * Get Run steps
 * 
 * @param  	boolean 				$post_id
 * @return 	function 				get_run_meta
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function get_run_steps( $post_id = false ) {
	
	return get_run_meta( $post_id, 'run_steps' );
}


/**
 * The Run steps
 *
 * @param  boolean 					$post_id
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function the_run_steps( $post_id = false ) {
	
	echo get_run_steps( $post_id );	                                      
}

/**
 * Get Run duration
 * 
 * @param  	boolean 				$post_id
 * @return 	function 				get_run_meta
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function get_run_duration( $post_id = false ) {
	
	return get_run_meta( $post_id, 'run_duration' );
}


/**
 * The Run duration
 *
 * @param  boolean 					$post_id
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function the_run_duration( $post_id = false ) {
	
	echo get_run_duration( $post_id );	                                      
}