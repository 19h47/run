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
        
        $this->register_post_type();
        add_action( 'init', array( $this, 'register_post_type' ) );

        include __DIR__ . '/meta-box.php';

        new Meta_Box( $this->theme_name );

		add_filter( 'manage_run_posts_columns', array( $this, 'add_run_columns' ) );
		add_action( 'manage_run_posts_custom_column' , array( $this, 'run_custom_columns' ), 10, 2 );

		add_filter( 'manage_edit-run_sortable_columns', array( $this, 'sortable_run_column' ) );
		add_action( 'pre_get_posts', array( $this, 'steps_orderby' ) );
		add_action( 'pre_get_posts', array( $this, 'calories_orderby' ) ); 

		add_action( 'quick_edit_custom_box',  array( $this, 'add_quick_edit' ), 10, 2 );
		add_action( 'admin_print_scripts-edit.php', array( $this, 'enqueue_script_quick_edit' ) );
		add_action( 'save_post', array( $this, 'save_quick_edit' ), 10, 2 );
		add_action( 'wp_ajax_manage_wp_posts_using_bulk_quick_save_bulk_edit', array( $this, 'manage_quick_edit' ) );
     
	}

	
	/**
	 * Register Custom Post Type
	 */
	public function register_post_type() {
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
	 * add run columns
	 * 
	 * @param $columns
	 */
	public function add_run_columns( $columns ) {
	    
	    // unset( $columns['title'] );

	    return array_merge( $columns, 
	    	array( 
	    		'duration' 	=> 'Duration',
	    		'steps' 	=> 'Steps',
	    		'calories'	=> 'Calories'
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

		    case 'calories' :
		    
		        echo '<div id="run_calories-' . $post_id . '">';

		        echo ! empty( get_post_meta( $post_id, 'run_calories', true ) ) ? get_post_meta( $post_id, 'run_calories', true ) : '—';
		        
		        echo '</div>';
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
	    $sortable_columns['calories'] = 'run_calories';

	 
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
	 * steps orderby
	 * 
	 * @param $query
	 */
	function calories_orderby( $query ) {
	  	
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
	    	case 'run_calories':
	    				
				$query->set( 'meta_key', 'run_calories' );
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

		    case 'calories' :

        		?>
        		<fieldset class="inline-edit-col-left">
            		<div class="inline-edit-col">
		            	<label>
		    				<span class="title">Calories</span>
		    				<span class="input-text-wrap">
		    					<input type="number" name="run_calories" class="" value="">
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
			get_template_directory_uri() . '/inc/post-types/run/js/run.js', 
			array( 'jquery', 'inline-edit-post' ), 
			null, 
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
			
				$custom_fields = array( 'run_duration', 'run_steps', 'run_calories' );
				
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
			$custom_fields = array( 'run_duration', 'run_steps', 'run_calories' );
			
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
 * Get Run date
 *
 * @param 	string      	$format Optional. 	PHP date format defaults to the 
 *                             					date_format option if not 
 *                             					specified.
 * @param  	int|WP_Post 	$post 	Optional. 	Post ID or WP_Post object. 
 *                              				Default current post.
 * @author 	Jérémy Levron 	levronjeremy@19h47.fr
 */
function get_run_date( $format = '', $post_id = false ) {

	if( $format == '' ) {
		$format = 'j F Y G \h i \m\i\n';
	}

	return get_the_date( $format, $post_id );
}


/**
 * The Run date
 *
 * @param  boolean 					$post_id
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function the_run_date( $format = '', $post_id = false ) {
	
	echo get_run_date( $format, $post_id );	                                      
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


/**
 * Get Run calories
 * 
 * @param  	boolean 				$post_id
 * @return 	function 				get_run_meta
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function get_run_calories( $post_id = false ) {
	
	return get_run_meta( $post_id, 'run_calories' );
}


/**
 * The Run calories
 *
 * @param  boolean 					$post_id
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function the_run_calories( $post_id = false ) {
	
	echo get_run_calories( $post_id );	                                      
}