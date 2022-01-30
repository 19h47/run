<?php
/**
 * Column
 *
 * @since      1.0.0
 * @package    Run
 * @subpackage Run/includes
 * @author     Jérémy Levron (https://19h47.fr) <jeremylevron@19h47.fr>
 */

?>
<div id="run_<?php echo esc_attr( $column_name . '-' . $post_id ); ?>">
	<?php echo esc_html( empty( $data ) ? '—' : $data ); ?>
</div>
