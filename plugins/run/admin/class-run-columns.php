<?php

/**
 * Columns
 *
 * @link       http://www.19h47.fr
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
 * @author     Levron Jérémy <levronjeremy@19h47.fr>
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
		add_action( 'pre_get_posts', array( $this, 'steps_orderby' ) );
		add_action( 'pre_get_posts', array( $this, 'calories_orderby' ) );

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
				'duration' => 'Duration',
				'steps'    => 'Steps',
				'calories' => 'Calories',
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

			case 'duration':
				$data = get_post_meta( $post_id, 'run_' . $column_name, true );

				include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-column.php' );

				break;

			case 'steps':
				$data = get_post_meta( $post_id, 'run_' . $column_name, true );

				include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-column.php' );

				break;

			case 'calories':
				$data = get_post_meta( $post_id, 'run_' . $column_name, true );

				include( plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-column.php' );

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

		if ( $query->get( 'orderby' ) !== $orderby ) {
			return;
		}

		switch ( $orderby ) {

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

		if ( $query->get( 'orderby' ) !== $orderby ) {
			return;
		}

		switch ( $orderby ) {
			case 'run_calories':
				$query->set( 'meta_key', 'run_calories' );
				$query->set( 'orderby', 'meta_value_num' );

				break;

		}
	}
}
