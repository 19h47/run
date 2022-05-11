<?php
/**
 * Form fields
 *
 * @since      1.0.0
 * @package    Run
 * @subpackage Run/admin/partials
 * @author     Jérémy Levron <jeremylevron@19h47.fr>
 */
?>
<table class="form-table">

	<!-- Duration -->
	<tr>
		<th>
			<label for="run_duration" class="run_duration_label">
				<?php _e( 'Duration', 'run' ); ?>
			</label>
		</th>
		<td>
			<input
					type="time"
					id="run_duration"
					name="run_duration"
					class="run_duration_field"
					placeholder=""
					value="<?php echo esc_html( $run_duration ); ?>"
			>
		</td>
	</tr>

	<!-- Steps -->
	<tr>
		<th>
			<label for="run_steps" class="run_steps_label">
				<?php _e( 'Steps', 'run' ); ?>
			</label>
		</th>
		<td>
			<input
				type="number"
				id="run_steps"
				name="run_steps"
				class="run_steps_field"
				placeholder=""
				value="<?php echo esc_html( $run_steps ); ?>"
			>
		</td>
	</tr>

	<!-- Calories -->
	<tr>
		<th>
			<label for="run_steps" class="run_calories_label">
				<?php _e( 'Calories', 'run' ); ?>
			</label>
		</th>
		<td>
			<input
				type="number"
				id="run_steps"
				name="run_calories"
				class="run_calories_field"
				placeholder=""
				value="<?php echo esc_html( $run_calories ); ?>"
			>
		</td>
	</tr>

	<tr>
		<th>
			<label for="run_weight" class="run_weight_label">
				<?php _e( 'Weight', 'run' ); ?>
			</label>
		</th>
		<td>
			<input
				type="number"
				id="run_weight"
				name="run_weight"
				class="run_weight_field"
				placeholder=""
				step="0.01"
				value="<?php echo esc_html( $run_weight ); ?>"
			>
		</td>
	</tr>
</table>
