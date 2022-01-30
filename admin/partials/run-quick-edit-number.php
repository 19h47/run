<?php
/**
 * Quick edit number
 *
 * @since      1.0.0
 * @package    Run
 * @subpackage Run/admin/partials
 * @author     Jérémy Levron (https://19h47.fr) <jeremylevron@19h47.fr>
 */

?>
<fieldset class="inline-edit-col-left">
	<div class="inline-edit-col">
		<label>
			<span class="title">
				<?php echo esc_html( ucfirst( $column_name ) ); ?>
			</span>
			<span class="input-text-wrap">
				<input type="number" name="run_<?php echo esc_attr( $column_name ); ?>" class="" value="">
			</span>
		</label>
	</div>
</fieldset>
