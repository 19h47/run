<?php
/**
 * Column
 *
 * @since      1.0.0
 * @package    Run
 * @subpackage Run/includes
 * @author     Levron Jérémy <levronjeremy@19h47.fr>
 */
?>
<div id="run_<?php echo $column_name . '-' . $post_id; ?>">
	<?php echo empty( $data ) ? '—' : $data; ?>
</div>
