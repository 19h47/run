<?php
/**
 * Run Post Type
 *
 * @link       http://www.19h47.fr
 * @since      1.0.0
 *
 * @package    Run
 * @subpackage Run/includes
 */


/**
 * Register post types and taxonomies.
 *
 * @since      1.0.0
 * @package    Run
 * @subpackage Run/admin
 * @author     Levron Jérémy <levronjeremy@19h47.fr>
 */
class Run_Registrations {

	/**
	 * Post type name
	 * 
	 * @var string
	 */
	public $post_type = 'run';


	/**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;
    

    /**
     * The version of the plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $plugin_version;


	/**
	 * init
	 */
	public function __construct( $plugin_name, $plugin_version ) {
		$this->plugin_name = $plugin_name;
        $this->plugin_version = $plugin_version;

		// Add the Run post type
		add_action( 'init', array( $this, 'register_post_type' ) );
	}


	/**
	 * Register the custom post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public function register_post_type() {
		
		$labels = array(
			'name'                  => __( 'Runs', $this->plugin_name ),
			'singular_name'         => __( 'Run', $this->plugin_name ),
			'menu_name'             => __( 'Runs', $this->plugin_name ),
			'name_admin_bar'        => __( 'Run', $this->plugin_name ),
			'archives'              => __( 'Run Archives', $this->plugin_name ),
			'attributes'			=> __( 'Item Attributes', $this->plugin_name ),
			'parent_item_colon'     => __( 'Parent Run:', $this->plugin_name ),
			'all_items'             => __( 'All runs', $this->plugin_name ),
			'add_new_item'          => __( 'Add New Run', $this->plugin_name ),
			'add_new'               => __( 'Add New', $this->plugin_name ),
			'new_item'              => __( 'New Run', $this->plugin_name ),
			'edit_item'             => __( 'Edit run', $this->plugin_name ),
			'update_item'           => __( 'Update run', $this->plugin_name ),
	        'view_item'             => __( 'View Run', $this->plugin_name ),
			'view_items'            => __( 'View Runs', $this->plugin_name ),
			'search_items'          => __( 'Search Run', $this->plugin_name ),
			'not_found'             => __( 'Not found', $this->plugin_name ),
			'not_found_in_trash'    => __( 'Not found in Trash', $this->plugin_name ),
			'featured_image'        => __( 'Featured Image', $this->plugin_name ),
			'set_featured_image'    => __( 'Set featured image', $this->plugin_name ),
			'remove_featured_image' => __( 'Remove featured image', $this->plugin_name ),
			'use_featured_image'    => __( 'Use as featured image', $this->plugin_name ),
			'insert_into_item'      => __( 'Insert into run', $this->plugin_name ),
			'uploaded_to_this_item' => __( 'Updloaded to this run', $this->plugin_name ),
			'items_list'            => __( 'Runs list', $this->plugin_name ),
			'items_list_navigation' => __( 'Runs list navigation', $this->plugin_name ),
			'filter_items_list'     => __( 'Filtrer runs list', $this->plugin_name ),
		);


		$supports = array();
		$taxonomies = array();


		$rewrite = array(
	        'slug'          => 'runs',
	        'with_front'    => false,
	    );


		$args = array(
			'label'                 => __( 'Run', $this->plugin_name ),
			'description'           => __( 'Run description', $this->plugin_name ),
			'labels'                => $labels,
			'supports'              => $supports,
			'taxonomies'            => $taxonomies,
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

		$args = apply_filters( 'run_post_type_args', $args );

		register_post_type( $this->post_type, $args );
	}
}