<?php
/**
 * Dashboard widget view for Run.
 *
 * @package Run
 * @subpackage run/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="run-dashboard-widget" aria-label="<?php echo isset( $widget_aria_label ) ? esc_attr( $widget_aria_label ) : esc_attr__( 'Recent runs summary', 'run' ); ?>">
	<div class="run-dashboard-header">
		<div class="run-dashboard-header-main">
			<p class="run-dashboard-period">
				<?php echo esc_html( $this->get_period_label( $days_window ) ); ?>
			</p>
			<p class="run-dashboard-primary">
				<?php
				printf(
					/* translators: %d: number of sessions. */
					esc_html( _n( '%d session', '%d sessions', $total_sessions, 'run' ) ),
					(int) $total_sessions
				);
				?>
			</p>
		</div>
	</div>

	<nav class="run-dashboard-tabs" aria-label="<?php esc_attr_e( 'Period', 'run' ); ?>">
		<?php foreach ( $this->allowed_ranges as $range ) : ?>
			<?php
			$base_url = admin_url( 'index.php' );
			$url      = add_query_arg( array( 'run_dashboard_range' => $range ), $base_url );
			$label    = $this->get_period_label( $range );
			$current  = $range === $days_window;
			?>
			<a href="<?php echo esc_url( $url ); ?>"
				class="run-dashboard-tab <?php echo $current ? 'run-dashboard-tab--current' : ''; ?>"
				<?php echo $current ? ' aria-current="true"' : ''; ?>>
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endforeach; ?>
	</nav>

	<?php if ( 0 < $total_steps || 0 < $total_duration || null !== $weight ) : ?>
		<div class="run-dashboard-stats-wrap">
			<ul class="run-dashboard-stats" aria-label="<?php esc_attr_e( 'Totals for the period', 'run' ); ?>">
				<?php if ( 0 < $total_steps ) : ?>
					<li class="run-dashboard-stat">
						<?php
						printf(
							/* translators: %s: formatted number of steps. */
							esc_html__( '%s steps', 'run' ),
							esc_html( number_format_i18n( $total_steps ) )
						);
						?>
					</li>
				<?php endif; ?>
				<?php if ( 0 < $total_duration ) : ?>
					<li class="run-dashboard-stat">
						<?php echo esc_html( $this->format_duration( $total_duration ) ); ?>
						<?php if ( $total_sessions > 0 && isset( $avg_duration_per_session ) && $avg_duration_per_session > 0 ) : ?>
							<?php
							printf(
								/* translators: %s: formatted average duration per run. */
								' ' . esc_html__( '(~ %s per run)', 'run' ),
								esc_html( $this->format_duration( $avg_duration_per_session ) )
							);
							?>
						<?php endif; ?>
					</li>
				<?php endif; ?>
				<?php if ( null !== $weight ) : ?>
					<li class="run-dashboard-stat">
						<?php
						printf(
							/* translators: %s: weight value. */
							esc_html__( '%s kg', 'run' ),
							esc_html( number_format_i18n( (float) $weight, 1 ) )
						);
						?>
						<?php if ( isset( $weight_delta ) && 0.0 !== (float) $weight_delta ) : ?>
							<?php
							$delta_sign = $weight_delta > 0 ? '+' : '';
							printf(
								/* translators: %s: delta weight (e.g. +0.5 kg). */
								' ' . esc_html__( '(Δ %s kg)', 'run' ),
								esc_html( $delta_sign . number_format_i18n( abs( $weight_delta ), 1 ) )
							);
							?>
						<?php endif; ?>
					</li>
				<?php endif; ?>
				<?php if ( $total_sessions > 0 && isset( $avg_steps_per_session ) && $avg_steps_per_session > 0 ) : ?>
					<li class="run-dashboard-stat">
						<?php
						printf(
							/* translators: %s: average steps per session. */
							esc_html__( '~ %s steps / session', 'run' ),
							esc_html( number_format_i18n( $avg_steps_per_session ) )
						);
						?>
					</li>
				<?php endif; ?>
			</ul>
			<?php
			$period_runs_url = add_query_arg(
				array(
					'post_type'           => 'run',
					'run_dashboard_range' => $days_window,
				),
				admin_url( 'edit.php' )
			);
			?>
			<a class="run-dashboard-link" href="<?php echo esc_url( $period_runs_url ); ?>">
				<?php esc_html_e( 'View runs for this period', 'run' ); ?>
			</a>
		</div>
	<?php endif; ?>

	<?php
	$graph_class_base = 'run-dashboard-graph';
	if ( 365 === $days_window ) {
		$graph_class_base .= ' run-dashboard-graph--months';
	}
	$total_bars = count( $data['days'] );
	$label_step = ( $total_bars > 12 ) ? max( 1, (int) floor( $total_bars / 6 ) ) : 1;

	$series = array(
		'sessions' => array(
			'key'        => 'count',
			'max'        => $max_count,
			'title'      => __( 'Sessions', 'run' ),
			'fill_class' => 'run-graph-bar-fill--sessions',
		),
		'steps'    => array(
			'key'        => 'steps',
			'max'        => $max_steps,
			'title'      => __( 'Steps', 'run' ),
			'fill_class' => 'run-graph-bar-fill--steps',
		),
		'duration' => array(
			'key'        => 'duration',
			'max'        => $max_duration,
			'title'      => __( 'Duration', 'run' ),
			'fill_class' => 'run-graph-bar-fill--duration',
		),
		'weight'   => array(
			'key'        => 'weight',
			'max'        => $max_weight,
			'title'      => __( 'Weight', 'run' ),
			'fill_class' => 'run-graph-bar-fill--weight',
		),
	);

	/**
	 * Filter the metric series displayed in the dashboard widget.
	 *
	 * @param array $series      Associative array of series definitions.
	 * @param int   $days_window Current period in days.
	 * @param array $data        Raw data returned by get_runs_window_data().
	 */
	$series = apply_filters( 'run_dashboard_series', $series, $days_window, $data );
	?>

	<?php foreach ( $series as $series_key => $config ) : ?>
		<?php if ( (int) $config['max'] <= 0 ) : ?>
			<?php continue; ?>
		<?php endif; ?>

		<?php
		// Compute the peak label for this series.
		$peak_label   = '';
		$peak_value   = null;
		$peak_ts      = null;
		$peak_caption = '';
		foreach ( $data['days'] as $day_for_peak ) {
			$val = isset( $day_for_peak[ $config['key'] ] ) ? (float) $day_for_peak[ $config['key'] ] : 0.0;
			if ( 'weight' === $series_key ) {
				// For weight we care about the lowest (non-zero) value over the period.
				if ( $val > 0 && ( null === $peak_value || $val < $peak_value ) ) {
					$peak_value = $val;
					$peak_ts    = $day_for_peak['timestamp'];
				}
			} elseif ( $val > $peak_value ) {
					$peak_value = $val;
					$peak_ts    = $day_for_peak['timestamp'];
			}
		}
		if ( null !== $peak_value && $peak_value > 0 && null !== $peak_ts ) {
			// Human label depends on the current window: full date for 7/30 days,
			// month + year for longer ranges.
			if ( 7 === $days_window || 30 === $days_window ) {
				$peak_label = date_i18n( get_option( 'date_format' ), $peak_ts );
			} else {
				$peak_label = date_i18n( 'F Y', $peak_ts );
			}

			// Choose a caption depending on the metric. %2$s is a human period label
			// (date for short ranges, month+year for longer ranges).
			if ( 'sessions' === $series_key ) {
				/* translators: 1: number of sessions, 2: period label. */
				$peak_caption = __( 'Most sessions: %1$s (%2$s)', 'run' );
			} elseif ( 'steps' === $series_key ) {
				/* translators: 1: number of steps, 2: period label. */
				$peak_caption = __( 'Most steps: %1$s (%2$s)', 'run' );
			} elseif ( 'duration' === $series_key ) {
				/* translators: 1: formatted duration, 2: period label. */
				$peak_caption = __( 'Longest duration: %1$s (%2$s)', 'run' );
			} elseif ( 'weight' === $series_key ) {
				/* translators: 1: lowest weight value, 2: period label. */
				$peak_caption = __( 'Lowest weight: %1$s kg (%2$s)', 'run' );
			}
		}
		?>

		<div class="run-dashboard-graph-wrapper" role="img"
			aria-label="<?php echo esc_attr( $config['title'] ); ?>">
			<p class="run-dashboard-graph-title">
				<?php echo esc_html( $config['title'] ); ?>
			</p>
			<div class="<?php echo esc_attr( $graph_class_base ); ?>" style="height: <?php echo (int) $chart_height_px; ?>px;">
				<?php foreach ( $data['days'] as $index => $day ) : ?>
					<?php
					$label      = date_i18n( get_option( 'date_format' ), $day['timestamp'] );
					$show_label = ( 1 === $label_step || 0 === $index % $label_step );

					$value      = isset( $day[ $config['key'] ] ) ? (float) $day[ $config['key'] ] : 0.0;
					$bar_height = 0;
					if ( $value > 0 && $config['max'] > 0 ) {
						$bar_height = max( 2, (int) round( ( $value / $config['max'] ) * $chart_height_px ) );
					}

					$label_text = '';
					if ( 7 === $days_window || 30 === $days_window ) {
						$label_text = date_i18n( _x( 'D', 'day of week short', 'run' ), $day['timestamp'] );
					} elseif ( 180 === $days_window ) {
						$label_text = 'S' . date_i18n( 'W', $day['timestamp'] );
					} else {
						$label_text = date_i18n( 'M', $day['timestamp'] );
					}

					$tooltip_value = '';
					if ( 'sessions' === $series_key ) {
						$tooltip_value = number_format_i18n( (int) $value );
					} elseif ( 'steps' === $series_key ) {
						$tooltip_value = number_format_i18n( (int) $value );
					} elseif ( 'duration' === $series_key ) {
						$tooltip_value = $this->format_duration( (int) $value );
					} elseif ( 'weight' === $series_key && 0 < $value ) {
						$tooltip_value = number_format_i18n( $value, 1 ) . ' kg';
					}

					$tooltip = trim( $config['title'] . ( '' !== $tooltip_value ? ': ' . $tooltip_value : '' ) );
					?>
					<?php
					$is_peak = false;
					if ( 'weight' === $series_key && null !== $peak_value ) {
						$is_peak = ( $value === (float) $peak_value );
					} elseif ( 'weight' !== $series_key && $config['max'] > 0 ) {
						$is_peak = ( $value === (float) $config['max'] );
					}
					?>
					<div class="run-graph-bar <?php echo $is_peak ? 'run-graph-bar--peak' : ''; ?>" aria-label="<?php echo esc_attr( $label ); ?>"
						<?php if ( '' !== $tooltip_value ) : ?>
							title="<?php echo esc_attr( $tooltip ); ?>"
						<?php endif; ?>
					>
						<div class="run-graph-bar-fill <?php echo esc_attr( $config['fill_class'] ); ?>"
							style="height: <?php echo (int) $bar_height; ?>px;"
							aria-hidden="true"></div>
						<span class="run-graph-bar-label" aria-hidden="true">
							<?php echo $show_label ? esc_html( $label_text ) : '&nbsp;'; ?>
						</span>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( '' !== $peak_label && '' !== $peak_caption ) : ?>
				<p class="run-dashboard-graph-peak">
					<?php
					$peak_value_display = '';
					if ( 'sessions' === $series_key ) {
						$peak_value_display = number_format_i18n( (int) $peak_value );
					} elseif ( 'steps' === $series_key ) {
						$peak_value_display = number_format_i18n( (int) $peak_value );
					} elseif ( 'duration' === $series_key ) {
						$peak_value_display = $this->format_duration( (int) $peak_value );
					} elseif ( 'weight' === $series_key && null !== $peak_value ) {
						$peak_value_display = number_format_i18n( $peak_value, 1 );
					}

					if ( '' !== $peak_value_display ) {
						// All captions expect two placeholders: value and date.
						printf(
							esc_html( $peak_caption ),
							esc_html( $peak_value_display ),
							esc_html( $peak_label )
						);
					}
					?>
				</p>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</div>
