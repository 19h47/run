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
		$this->plugin_name    = $plugin_name;
		$this->plugin_version = $plugin_version;

		// Add the Run post type
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_filter( 'dashboard_glance_items', array( $this, 'at_a_glance' ) );
		add_action( 'admin_head', array( $this, 'css' ) );
	}


	/**
	 * Register the custom post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	public function register_post_type() {

		$labels = array(
			'name'                  => __( 'Runs', 'run' ),
			'singular_name'         => __( 'Run', 'run' ),
			'menu_name'             => __( 'Runs', 'run' ),
			'name_admin_bar'        => __( 'Run', 'run' ),
			'archives'              => __( 'Run Archives', 'run' ),
			'attributes'            => __( 'Item Attributes', 'run' ),
			'parent_item_colon'     => __( 'Parent Run:', 'run' ),
			'all_items'             => __( 'All runs', 'run' ),
			'add_new_item'          => __( 'Add New Run', 'run' ),
			'add_new'               => __( 'Add New', 'run' ),
			'new_item'              => __( 'New Run', 'run' ),
			'edit_item'             => __( 'Edit run', 'run' ),
			'update_item'           => __( 'Update run', 'run' ),
			'view_item'             => __( 'View Run', 'run' ),
			'view_items'            => __( 'View Runs', 'run' ),
			'search_items'          => __( 'Search Run', 'run' ),
			'not_found'             => __( 'Not found', 'run' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'run' ),
			'featured_image'        => __( 'Featured Image', 'run' ),
			'set_featured_image'    => __( 'Set featured image', 'run' ),
			'remove_featured_image' => __( 'Remove featured image', 'run' ),
			'use_featured_image'    => __( 'Use as featured image', 'run' ),
			'insert_into_item'      => __( 'Insert into run', 'run' ),
			'uploaded_to_this_item' => __( 'Updloaded to this run', 'run' ),
			'items_list'            => __( 'Runs list', 'run' ),
			'items_list_navigation' => __( 'Runs list navigation', 'run' ),
			'filter_items_list'     => __( 'Filtrer runs list', 'run' ),
		);

		$supports   = array( 'title' );
		$taxonomies = array();

		$rewrite = array(
			'slug'       => 'runs',
			'with_front' => false,
		);

		$args = array(
			'label'               => __( 'Run', 'run' ),
			'description'         => __( 'Run description', 'run' ),
			'labels'              => $labels,
			'supports'            => $supports,
			'taxonomies'          => $taxonomies,
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 40,
			'menu_icon'           => 'dashicons-chart-area',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'rewrite'             => $rewrite,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);

		$args = apply_filters( 'run_post_type_args', $args );

		register_post_type( $this->post_type, $args );
	}


	/**
	 * "At a glance" items (dashboard widget): add the testimony.
	 *
	 * @param arr $items Items.
	 */
	public function at_a_glance( $items ) {
		$post_type   = 'run';
		$post_status = 'publish';
		$object      = get_post_type_object( $post_type );

		$num_posts = wp_count_posts( $post_type );
		if ( ! $num_posts || ! isset( $num_posts->{ $post_status } ) || 0 === (int) $num_posts->{ $post_status } ) {
			return $items;
		}

		$text = sprintf(
			/* translators: %1$s: number posts %2$s: singular name %3$s: name %4$s: pending */
			_n( '%1$s %4$s%2$s', '%1$s %4$s%3$s', $num_posts->{ $post_status } ), // phpcs:ignore
			number_format_i18n( $num_posts->{ $post_status } ),
			strtolower( $object->labels->singular_name ),
			strtolower( $object->labels->name ),
			'pending' === $post_status ? 'Pending ' : ''
		);

		if ( current_user_can( $object->cap->edit_posts ) ) {
			$items[] = sprintf(
				'<a class="%1$s-count" href="edit.php?post_status=%2$s&post_type=%1$s">%3$s</a>',
				$post_type,
				$post_status,
				$text
			);
		} else {
			$items[] = sprintf( '<span class="%1$s-count">%s</span>', $text );
		}

		return $items;
	}


	/**
	 * CSS
	 *
	 */
	public function css() {
		echo '<style>#dashboard_right_now .run-count:before { content: "\f239"; }</style>';
	}
}
