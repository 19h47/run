<?php
/**
 * Globally-accessible functions
 *
 * @link       https://www.19h47.fr
 * @since      1.0.0
 *
 * @package    Run
 * @subpackage Run/includes
 */

/**
 * Get Run steps
 *
 * @param boolean $post_id Post ID.
 *
 * @author Jérémy Levron <jeremylevron@19h47.fr> (https://19h47.fr)
 */
function get_run_steps( $post_id = false ) {
	return get_post_meta( $post_id, 'run_steps', true );
}


/**
 * The Run steps
 *
 * @param boolean $post_id Post ID.
 *
 * @author Jérémy Levron <jeremylevron@19h47.fr> (https://19h47.fr)
 */
function the_run_steps( $post_id = false ) {
	echo get_run_steps( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Get Run date
 *
 * @param string      $format Optional. PHP date format defaults to the date_format option if not specified.
 * @param int|WP_Post $post_id Optional. Post ID or WP_Post object. Default current post.
 *
 * @author Jérémy Levron <jeremylevron@19h47.fr> (https://19h47.fr)
 */
function get_run_date( $format = '', $post_id = false ) {

	if ( '' === $format ) {
		$format = 'j F Y G \h i \m\i\n';
	}

	return get_the_date( $format, $post_id );
}


/**
 * The Run date
 *
 * @param string  $format PHP date format.
 * @param boolean $post_id Post ID.
 *
 * @author Jérémy Levron <jeremylevron@19h47.fr> (https://19h47.fr)
 */
function the_run_date( $format = '', $post_id = false ) {
	echo get_run_date( $format, $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Get Run duration
 *
 * @param boolean $post_id Post ID.
 *
 * @author Jérémy Levron <jeremylevron@19h47.fr> (https://19h47.fr)
 *
 * @return function get_run_meta
 */
function get_run_duration( $post_id = false ) {
	return get_post_meta( $post_id, 'run_duration', true );
}


/**
 * The Run duration
 *
 * @param boolean $post_id Post ID.
 *
 * @author Jérémy Levron <jeremylevron@19h47.fr> (https://19h47.fr)
 */
function the_run_duration( $post_id = false ) {
	echo get_run_duration( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}


/**
 * Get Run calories
 *
 * @param boolean $post_id Post ID.
 *
 * @author Jérémy Levron <jeremylevron@19h47.fr> (https://19h47.fr)
 *
 * @return function get_run_meta
 */
function get_run_calories( $post_id = false ) {
	return get_post_meta( $post_id, 'run_calories', true );
}


/**
 * The Run calories
 *
 * @param boolean $post_id Post ID.
 *
 * @author Jérémy Levron <jeremylevron@19h47.fr> (https://19h47.fr)
 */
function the_run_calories( $post_id = false ) {
	echo get_run_calories( $post_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
