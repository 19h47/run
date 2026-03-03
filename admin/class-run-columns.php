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
	 * Meta key for current sort (posts_join + posts_orderby).
	 *
	 * @var string|null
	 * */
	private $orderby_meta_key = null;

	/**
	 * Meta type for current sort (posts_join + posts_orderby).
	 *
	 * @var string|null 'meta_value' (TIME) or 'meta_value_num'.
	 */
	private $orderby_meta_type = null;


	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	public function __construct( string $plugin_name, string $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		add_filter( 'manage_run_posts_columns', array( $this, 'add_run_columns' ) );
		add_action( 'manage_run_posts_custom_column', array( $this, 'run_custom_columns' ), 10, 2 );

		add_filter( 'manage_edit-run_sortable_columns', array( $this, 'sortable_run_column' ), 10, 1 );
		add_action( 'pre_get_posts', array( $this, 'pre_get_runs' ), 10, 1 );
		add_filter( 'posts_join', array( $this, 'posts_join_run_meta' ), 10, 2 );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby_run_meta' ), 10, 2 );
	}


	/**
	 * Add run columns
	 *
	 * @param array $columns Columns.
	 * @return array
	 */
	public function add_run_columns( array $columns ): array {
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
	 * Run custom columns
	 *
	 * @param string $column_name Column name.
	 * @param int    $post_id Post ID.
	 *
	 * @return void
	 */
	public function run_custom_columns( string $column_name, int $post_id ): void {
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
	 * Sortable run column
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
	public function pre_get_runs( WP_Query $query ) {

		if ( ! ( is_admin() && $query->is_main_query() ) ) {
			return $query;
		}

		if ( $query->get( 'post_type' ) !== 'run' ) {
			return $query;
		}

		$this->orderby_meta_key  = null;
		$this->orderby_meta_type = null;

		$orderby = $query->get( 'orderby' );

		switch ( $orderby ) {
			case $this->plugin_name . '_duration':
				$this->orderby_meta_key  = $this->plugin_name . '_duration';
				$this->orderby_meta_type = 'meta_value';
				break;
			case $this->plugin_name . '_steps':
				$this->orderby_meta_key  = $this->plugin_name . '_steps';
				$this->orderby_meta_type = 'meta_value_num';
				break;
			case $this->plugin_name . '_calories':
				$this->orderby_meta_key  = $this->plugin_name . '_calories';
				$this->orderby_meta_type = 'meta_value_num';
				break;
			case $this->plugin_name . '_weight':
				$this->orderby_meta_key  = $this->plugin_name . '_weight';
				$this->orderby_meta_type = 'meta_value_num';
				break;
			default:
				return $query;
		}

		$query->set( 'orderby', 'date' );
	}


	/**
	 * Only one postmeta join (run_meta_sort). Required when year filter + meta sort.
	 * Otherwise WordPress generates two postmeta joins → "Not unique table/alias".
	 *
	 * @param string   $join  Clause JOIN.
	 * @param WP_Query $query Requête.
	 * @return string
	 */
	public function posts_join_run_meta( $join, WP_Query $query ) {
		if ( ! is_admin() || $query->get( 'post_type' ) !== 'run' || ! $this->orderby_meta_key ) {
			return $join;
		}

		global $wpdb;

		$join .= $wpdb->prepare(
			" INNER JOIN {$wpdb->postmeta} AS run_meta_sort ON ({$wpdb->posts}.ID = run_meta_sort.post_id AND run_meta_sort.meta_key = %s) ",
			$this->orderby_meta_key
		);

		return $join;
	}


	/**
	 * ORDER BY run_meta_sort
	 *
	 * @param string   $orderby Clause ORDER BY.
	 * @param WP_Query $query   Requête.
	 * @return string
	 */
	public function posts_orderby_run_meta( $orderby, WP_Query $query ) {
		if ( ! is_admin() || $query->get( 'post_type' ) !== 'run' || ! $this->orderby_meta_key ) {
			return $orderby;
		}

		$order = strtoupper( $query->get( 'order' ) );
		if ( 'DESC' !== $order ) {
			$order = 'ASC';
		}

		if ( 'meta_value' === $this->orderby_meta_type ) {
			return 'run_meta_sort.meta_value ' . $order;
		}

		return 'run_meta_sort.meta_value+0 ' . $order;
	}
}
