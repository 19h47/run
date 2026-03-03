<?php
/**
 * Dashboard widget for Run.
 *
 * @link       https://www.19h47.fr
 * @since      2.1.0
 *
 * @package    Run
 * @subpackage run/admin
 */

/**
 * Adds a native-feeling dashboard widget summarizing recent runs.
 *
 * @since      2.1.0
 * @package    Run
 * @subpackage run/admin
 * @author     Jérémy Levron <jeremylevron@19h47.fr>
 */
class Run_Dashboard {


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
	 * @var         string          $plugin_version        The version of this plugin.
	 */
	private $plugin_version;

	/**
	 * Default number of days to display in the chart.
	 *
	 * @var int
	 */
	private $days_window = 7;

	/**
	 * Allowed range values (days) for the period selector.
	 *
	 * @var int[]
	 */
	private $allowed_ranges = array( 7, 30, 180, 365 );

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name    Plugin name.
	 * @param string $plugin_version Plugin version.
	 */
	public function __construct( string $plugin_name, string $plugin_version ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
		$this->plugin_name    = $plugin_name;
		$this->plugin_version = $plugin_version;

		/**
		 * Filter the allowed dashboard ranges (in days).
		 *
		 * @param int[] $allowed_ranges Array of allowed day ranges.
		 */
		$this->allowed_ranges = (array) apply_filters( 'run_dashboard_allowed_ranges', $this->allowed_ranges );

		add_action( 'wp_dashboard_setup', array( $this, 'register_widget' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Enqueue dashboard assets.
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_assets( string $hook ): void {
		if ( 'index.php' !== $hook ) {
			return;
		}

		wp_enqueue_style(
			'run-dashboard',
			plugins_url( 'admin/css/run-dashboard.css', __DIR__ ),
			array(),
			'1.0.0'
		);
	}

	/**
	 * Get the effective days window (from request or user meta).
	 *
	 * @return int Number of days.
	 */
	private function get_effective_days_window(): int {
		$requested = isset( $_GET['run_dashboard_range'] ) ? (int) $_GET['run_dashboard_range'] : 0;

		if ( $requested && in_array( $requested, $this->allowed_ranges, true ) ) {
			if ( is_user_logged_in() ) {
				update_user_meta( get_current_user_id(), 'run_dashboard_range', $requested );
			}
			return $requested;
		}

		if ( is_user_logged_in() ) {
			$saved = (int) get_user_meta( get_current_user_id(), 'run_dashboard_range', true );
			if ( $saved && in_array( $saved, $this->allowed_ranges, true ) ) {
				return $saved;
			}
		}
		return $this->days_window;
	}

	/**
	 * Get period label for a given number of days.
	 *
	 * @param int $days Number of days.
	 * @return string
	 */
	private function get_period_label( int $days ): string {
		$labels = array(
			7   => __( '7 days', 'run' ),
			30  => __( '1 month', 'run' ),
			180 => __( '6 months', 'run' ),
			365 => __( '1 year', 'run' ),
		);
		if ( isset( $labels[ $days ] ) ) {
			return $labels[ $days ];
		}

		/* translators: %d: number of days. */
		$pattern = __( '%d days', 'run' );
		return sprintf( $pattern, $days );
	}

	/**
	 * Register the dashboard widget.
	 */
	public function register_widget(): void {
		wp_add_dashboard_widget(
			'run_dashboard_overview',
			__( 'Runs overview', 'run' ),
			array( $this, 'render_widget' )
		);
	}

	/**
	 * Render the dashboard widget content.
	 */
	public function render_widget(): void {
		$days_window    = $this->get_effective_days_window();
		$data           = $this->get_runs_window_data( $days_window );
		$total_sessions = array_sum( wp_list_pluck( $data['days'], 'count' ) );
		$total_steps    = array_sum( wp_list_pluck( $data['days'], 'steps' ) );
		$total_duration = (int) ( $data['total_duration_seconds'] ?? 0 );
		$weight         = isset( $data['weight_kg'] ) && $data['weight_kg'] > 0 ? $data['weight_kg'] : null;
		$max_count      = max( array( 1, $data['max_count'] ) );
		$max_steps      = max( array( 1, (int) ( $data['max_steps'] ?? 0 ) ) );
		$max_duration   = max( array( 1, (int) ( $data['max_duration'] ?? 0 ) ) );
		$max_weight     = max( array( 1, (int) round( $data['max_weight'] ?? 0 ) ) );
		// Derived metrics.
		$avg_steps_per_session    = $total_sessions > 0 ? (int) floor( $total_steps / $total_sessions ) : 0;
		$avg_duration_per_session = $total_sessions > 0 ? (int) floor( $total_duration / max( 1, $total_sessions ) ) : 0;

		// Compute weight delta over the period (first non-zero vs last non-zero).
		$first_weight = null;
		$last_weight  = null;
		foreach ( $data['days'] as $day ) {
			if ( isset( $day['weight'] ) && $day['weight'] > 0 ) {
				if ( null === $first_weight ) {
					$first_weight = $day['weight'];
				}
				$last_weight = $day['weight'];
			}
		}
		$weight_delta = null;
		if ( null !== $first_weight && null !== $last_weight ) {
			$weight_delta = $last_weight - $first_weight;
		}

		// Slightly smaller chart to avoid visual overflow.
		$chart_height_px = 44;

		// More descriptive aria label for assistive technologies.
		$duration_label = $this->format_duration( $total_duration );
		if ( '' === $duration_label ) {
			$duration_label = __( '0 m', 'run' );
		}

		if ( null !== $weight ) {
			/* translators: 1: period label, 2: number of sessions, 3: total duration, 4: average weight. */
			$widget_aria_label = sprintf(
				__( '%1$s: %2$d sessions, %3$s total, average weight %4$s kg', 'run' ),
				$this->get_period_label( $days_window ),
				(int) $total_sessions,
				$duration_label,
				number_format_i18n( (float) $weight, 1 )
			);
		} else {
			/* translators: 1: period label, 2: number of sessions, 3: total duration. */
			$widget_aria_label = sprintf(
				__( '%1$s: %2$d sessions, %3$s total', 'run' ),
				$this->get_period_label( $days_window ),
				(int) $total_sessions,
				$duration_label
			);
		}

		include plugin_dir_path( __FILE__ ) . 'partials/' . $this->plugin_name . '-widget.php';
	}

	/**
	 * Format duration in seconds to a human-readable string (e.g. "2h 15m" or "45m").
	 *
	 * @param int $seconds Total seconds.
	 * @return string
	 */
	private function format_duration( int $seconds ): string {
		if ( $seconds <= 0 ) {
			return '';
		}
		$days    = (int) floor( $seconds / DAY_IN_SECONDS );
		$hours   = (int) floor( ( $seconds % DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
		$minutes = (int) floor( ( $seconds % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );

		if ( $days > 0 ) {
			return sprintf(
				/* translators: 1: days, 2: hours, 3: minutes. */
				_x( '%1$d j %2$d h %3$02d m', 'duration with days, hours and minutes', 'run' ),
				$days,
				$hours,
				$minutes
			);
		}

		if ( $hours > 0 ) {
			return sprintf(
				/* translators: 1: hours, 2: minutes. */
				_x( '%1$d h %2$02d m', 'duration with hours and minutes', 'run' ),
				$hours,
				$minutes
			);
		}

		return sprintf(
			/* translators: %d: minutes. */
			_x( '%d m', 'duration with minutes', 'run' ),
			$minutes
		);
	}

	/**
	 * Parse duration meta value to seconds (supports integer seconds or "HH:MM" / "HH:MM:SS").
	 *
	 * @param mixed $value run_duration meta value.
	 * @return int Seconds.
	 */
	private function parse_duration_to_seconds( $value ): int {
		if ( is_numeric( $value ) ) {
			return (int) $value;
		}
		if ( is_string( $value ) && preg_match( '/^(\d+):(\d+)(?::(\d+))?$/', trim( $value ), $m ) ) {
			$h = (int) $m[1];
			$i = (int) $m[2];
			$s = isset( $m[3] ) ? (int) $m[3] : 0;
			return $h * 3600 + $i * 60 + $s;
		}
		return 0;
	}

	/**
	 * Collect run data for the last N days (or aggregated by week/month for long ranges).
	 *
	 * @param int $days_window Number of days (7, 30, 180, 365).
	 * @return array{days: array, max_count: int, total_duration_seconds: int, weight_kg: float|null}
	 */
	private function get_runs_window_data( int $days_window ): array {
		$days  = array();
		$today = time();

		$make_empty_bucket = static function ( int $timestamp ): array {
			return array(
				'timestamp'    => $timestamp,
				'count'        => 0,
				'steps'        => 0,
				'duration'     => 0,
				'weight_sum'   => 0.0,
				'weight_count' => 0,
			);
		};

		if ( 30 >= $days_window ) {
			$start_day = strtotime( '-' . ( $days_window - 1 ) . ' days', $today );
			for ( $i = 0; $i < $days_window; $i++ ) {
				$ts           = strtotime( '+' . $i . ' days', $start_day );
				$key          = gmdate( 'Y-m-d', $ts );
				$days[ $key ] = $make_empty_bucket( $ts );
			}
		} elseif ( 180 === $days_window ) {
			// Last 6 months, aggregated by month.
			for ( $m = 5; $m >= 0; $m-- ) {
				$ts           = strtotime( "first day of -{$m} months", $today );
				$key          = gmdate( 'Y-m', $ts );
				$days[ $key ] = $make_empty_bucket( $ts );
			}
		} else {
			// Last 12 months (1‑year view), aggregated by month.
			for ( $m = 11; $m >= 0; $m-- ) {
				$ts           = strtotime( "first day of -{$m} months", $today );
				$key          = gmdate( 'Y-m', $ts );
				$days[ $key ] = $make_empty_bucket( $ts );
			}
		}

		$total_duration_seconds = 0;
		$weight_sum             = 0.0;
		$weight_count           = 0;

		$after_date = gmdate( 'Y-m-d', strtotime( "-{$days_window} days", $today ) );
		$query      = new WP_Query(
			array(
				'post_type'           => 'run',
				'post_status'         => 'publish',
				'posts_per_page'      => -1,
				'no_found_rows'       => true,
				'ignore_sticky_posts' => true,
				'date_query'          => array(
					array(
						'column'    => 'post_date_gmt',
						'after'     => $after_date,
						'inclusive' => true,
					),
				),
				'fields'              => 'ids',
			)
		);

		if ( 30 >= $days_window ) {
			$key_format = 'Y-m-d';
		} else {
			// 6‑month and 1‑year views are both aggregated by month.
			$key_format = 'Y-m';
		}

		if ( $query->have_posts() ) {
			foreach ( $query->posts as $post_id ) {
				$ts = get_post_time( 'U', true, $post_id );
				if ( 'Y-m-d' === $key_format ) {
					$key = gmdate( 'Y-m-d', $ts );
				} else {
					$key = gmdate( 'Y-m', $ts );
				}

				if ( ! isset( $days[ $key ] ) ) {
					continue;
				}

				++$days[ $key ]['count'];

				$steps = function_exists( 'get_run_steps' ) ? (int) get_run_steps( $post_id ) : 0;
				if ( $steps > 0 ) {
					$days[ $key ]['steps'] += $steps;
				}

				$duration = get_run_duration( $post_id );

				if ( null !== $duration && '' !== $duration ) {
					$sec                       = $this->parse_duration_to_seconds( $duration );
					$days[ $key ]['duration'] += $sec;
					$total_duration_seconds   += $sec;
				}

				$w = get_post_meta( $post_id, 'run_weight', true );
				if ( is_numeric( $w ) && (float) $w > 0 ) {
					$days[ $key ]['weight_sum'] += (float) $w;
					++$days[ $key ]['weight_count'];
					$weight_sum += (float) $w;
					++$weight_count;
				}
			}
		}

		wp_reset_postdata();

		$max_count    = 0;
		$max_steps    = 0;
		$max_duration = 0;
		$max_weight   = 0;

		foreach ( $days as $key => $day ) {
			if ( $day['count'] > $max_count ) {
				$max_count = $day['count'];
			}
			if ( $day['steps'] > $max_steps ) {
				$max_steps = $day['steps'];
			}
			if ( $day['duration'] > $max_duration ) {
				$max_duration = $day['duration'];
			}

			if ( $day['weight_count'] > 0 ) {
				$avg_weight             = $day['weight_sum'] / $day['weight_count'];
				$days[ $key ]['weight'] = $avg_weight;
				if ( $avg_weight > $max_weight ) {
					$max_weight = $avg_weight;
				}
			} else {
				$days[ $key ]['weight'] = 0;
			}

			// These fields are only needed internally for averaging.
			unset( $days[ $key ]['weight_sum'], $days[ $key ]['weight_count'] );
		}

		$weight_kg = $weight_count > 0 ? round( $weight_sum / $weight_count, 1 ) : null;

		return array(
			'days'                   => array_values( $days ),
			'max_count'              => $max_count,
			'max_steps'              => $max_steps,
			'max_duration'           => $max_duration,
			'max_weight'             => $max_weight,
			'total_duration_seconds' => $total_duration_seconds,
			'weight_kg'              => $weight_kg,
		);
	}
}
