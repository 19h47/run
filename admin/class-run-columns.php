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

		add_filter( 'manage_edit-run_sortable_columns', array( $this, 'sortable_run_column' ) );
		add_action( 'pre_get_posts', array( $this, 'pre_get_runs' ) );

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
				'weight'   => __( 'weight', 'run' ),
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
	 * @param   $sortable_columns
	 */
	public function sortable_run_column( $sortable_columns ) {

		$sortable_columns['duration'] = 'run_duration';
		$sortable_columns['steps']    = 'run_steps';
		$sortable_columns['calories'] = 'run_calories';
		$sortable_columns['weight']   = 'run_weight';

		return $sortable_columns;
	}


	/**
	 * Pre get runs
	 *
	 * @param $query
	 */
	function pre_get_runs( $query ) {

		if ( ! is_admin() ) {
			return;
		}

		if ( ! $query->is_main_query() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );

		switch ( $orderby ) {
			case 'run_steps':
				$query->set( 'meta_key', 'run_steps' );
				$query->set( 'orderby', 'meta_value_num' );

				break;

			case 'run_calories':
				$query->set( 'meta_key', 'run_calories' );
				$query->set( 'orderby', 'meta_value_num' );

				break;

			case 'run_weight':
				$query->set( 'meta_key', 'run_weight' );
				$query->set( 'orderby', 'meta_value_num' );

				break;

		}
	}
}
