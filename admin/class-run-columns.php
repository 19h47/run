<?php

/**
 * Columns
 *
 * @link       https://www.19h47.fr
 * @since      1.0.0
 *
 * @package    Run
 * @subpackage Run/admin
 */


/**
 * Columns
 *
 * @since      1.0.0
 * @package    Run
 * @subpackage Run/includes
 * @author     Jérémy Levron <jeremylevron@19h47.fr>
 */
class Run_Columns {

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

		add_filter( 'manage_run_posts_columns', array( $this, 'add_run_columns' ) );
		add_action( 'manage_run_posts_custom_column', array( $this, 'run_custom_columns' ), 10, 2 );

		add_filter( 'manage_edit-run_sortable_columns', array( $this, 'sortable_run_column' ), 10, 1 );
		add_action( 'pre_get_posts', array( $this, 'pre_get_runs' ), 10, 1 );
	}


	/**
	 * add run columns
	 *
	 * @param $columns
	 */
	public function add_run_columns( $columns ) {

		// unset( $columns['title'] );

		return array_merge(
			$columns,
			array(
				'duration' => __( 'Duration', 'run' ),
				'steps'    => __( 'Steps', 'run' ),
				'calories' => __( 'Calories', 'run' ),
				'weight'   => __( 'Weight', 'run' ),
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
		$data = get_post_meta( $post_id, 'run_' . $column_name, true );

		switch ( $column_name ) {

			case 'duration':
				include plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-column.php';

				break;

			case 'steps':
				include plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-column.php';

				break;

			case 'calories':
				include plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-column.php';

				break;

			case 'weight':
				include plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-column.php';

				break;
		}
	}


	/**
	 * sortable_run_column description
	 *
	 * @see https://developer.wordpress.org/reference/hooks/manage_this-screen-id_sortable_columns/
	 *
	 * @param array $sortable_columns  An array of sortable columns.
	 *
	 * @return array
	 */
	public function sortable_run_column( array $sortable_columns ): array {

		$sortable_columns['duration'] = $this->plugin_name . '_duration';
		$sortable_columns['steps']    = $this->plugin_name . '_steps';
		$sortable_columns['calories'] = $this->plugin_name . '_calories';
		$sortable_columns['weight']   = $this->plugin_name . '_weight';

		return $sortable_columns;
	}


	/**
	 * Pre get runs
	 *
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 */
	function pre_get_runs( WP_Query $query ) {

		if ( ! ( is_admin() && $query->is_main_query() ) ) {
			return $query;
		}

		if ( $query->get( 'post_type' ) !== 'run' ) {
			return $query;
		}

		$orderby = $query->get( 'orderby' );

		switch ( $orderby ) {
			case $this->plugin_name . '_duration':
				$query->set( 'meta_key', $this->plugin_name . '_duration' );
				$query->set( 'meta_type', 'TIME' );
				$query->set( 'orderby', 'meta_value' );

				break;

			case $this->plugin_name . '_steps':
				$query->set( 'meta_key', $this->plugin_name . '_steps' );
				$query->set( 'orderby', 'meta_value_num' );

				break;

			case $this->plugin_name . '_calories':
				$query->set( 'meta_key', $this->plugin_name . '_calories' );
				$query->set( 'orderby', 'meta_value_num' );

				break;

			case $this->plugin_name . '_weight':
				$query->set( 'meta_key', $this->plugin_name . '_weight' );
				$query->set( 'orderby', 'meta_value_num' );

				break;

			default:
				break;

		}
	}
}
