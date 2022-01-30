<?php
/**
 * Form fields
 *
 * @since      1.0.0
 * @package    Run
 * @subpackage Run/admin/partials
 *
 * @author     Jérémy Levron (https://19h47.fr) <jeremylevron@19h47.fr>
 */

?>

<table class="form-table">

	<!-- Duration -->
	<tr>
		<th>
			<label for="run_duration" class="run_duration_label">
				<?php esc_html_e( 'Duration', 'run' ); ?>
			</label>
		</th>
		<td>
			<input
					type="time"
					id="run_duration"
					name="run_duration"
					class="run_duration_field"
					placeholder=""
					value="<?php echo esc_attr( $run_duration ); ?>"
			>
		</td>
	</tr>

	<!-- Steps -->
	<tr>
		<th>
			<label for="run_steps" class="run_steps_label">
				<?php esc_html_e( 'Steps', 'run' ); ?>
			</label>
		</th>
		<td>
			<input
				type="number"
				id="run_steps"
				name="run_steps"
				class="run_steps_field"
				placeholder=""
				value="<?php echo esc_attr( $run_steps ); ?>"
			>
		</td>
	</tr>

	<!-- Calories -->
	<tr>
		<th>
			<label for="run_steps" class="run_calories_label">
				<?php esc_html_e( 'Calories', 'run' ); ?>
			</label>
		</th>
		<td>
			<input
				type="number"
				id="run_steps"
				name="run_calories"
				class="run_calories_field"
				placeholder=""
				value="<?php echo esc_attr( $run_calories ); ?>"
			>
		</td>
	</tr>
</table>
