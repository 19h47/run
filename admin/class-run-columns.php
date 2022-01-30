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
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_filter( 'manage_run_posts_columns', array( $this, 'columns' ) );
		add_action( 'manage_run_posts_custom_column', array( $this, 'custom_column' ), 10, 2 );

		add_filter( 'manage_edit-run_sortable_columns', array( $this, 'sortable_columns' ) );

		add_action( 'pre_get_posts', array( $this, 'orderby' ) );
	}


	/**
	 * Columns
	 *
	 * @param array $post_columns An associative array of column headings.
	 *
	 * @return array
	 */
	public function columns( array $post_columns ) {
		return array_merge(
			$post_columns,
			array(
				'duration' => 'Duration',
				'steps'    => 'Steps',
				'calories' => 'Calories',
			)
		);
	}


	/**
	 * Custom Column
	 *
	 * @param string $column_name The name of the column to display.
	 * @param int    $post_id The current post ID.
	 */
	public function custom_column( string $column_name, int $post_id ) {

		switch ( $column_name ) {

			case 'duration':
			case 'steps':
			case 'calories':
				$data = get_post_meta( $post_id, 'run_' . $column_name, true );

				include plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-column.php';

				break;
		}
	}


	/**
	 * Sortable Columns
	 *
	 * @param  array $sortable_columns An array of sortable columns.
	 *
	 * @return array $sortable_columns
	 */
	public function sortable_columns( array $sortable_columns ) : array {
		$sortable_columns['duration'] = 'run_duration';
		$sortable_columns['steps']    = 'run_steps';
		$sortable_columns['calories'] = 'run_calories';

		return $sortable_columns;
	}


	/**
	 * Order by steps
	 *
	 * @param WP_Query $query The WP_Query instance (passed by reference).
	 */
	public function orderby( WP_Query $query ) {
		$orderby = $query->get( 'orderby' );

		if ( ! is_admin() ) {
			return;
		}

		if ( ! $query->is_main_query() ) {
			return;
		}

		switch ( $orderby ) {
			case 'run_steps':
			case 'run_calories':
				$query->set( 'meta_key', $orderby );
				$query->set( 'orderby', 'meta_value_num' );

				break;
			case 'run_duration':
				$query->set( 'meta_key', $orderby );
				$query->set( 'orderby', 'meta_value' );

				break;
		}
	}
}
