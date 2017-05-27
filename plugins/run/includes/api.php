<?php
/**
 * Globally-accessible functions
 *
 * @link       http://www.19h47.fr
 * @since      1.0.0
 *
 * @package    Run
 * @subpackage Run/includes
 */


/**
 * Get Run key
 *
 * Retrieve the value of a given $key in Run post type
 * 
 * @param  	boolean 				$post_id
 * @param  	string  				$key   
 * @return 	string					$meta
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr          
 */
function get_run_meta( $post_id = false, $key ) {
	
	if( ! $key ) {
		return;
	}

	$meta = get_post_meta( $post_id, $key, true );

	if( empty( $meta ) ) {
		return;
	}

	return $meta;
}

/**
 * Get Run steps
 * 
 * @param  	boolean 				$post_id
 * @return 	function 				get_run_meta
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function get_run_steps( $post_id = false ) {
	
	return get_run_meta( $post_id, 'run_steps' );
}


/**
 * The Run steps
 *
 * @param  boolean 					$post_id
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function the_run_steps( $post_id = false ) {
	
	echo get_run_steps( $post_id );	                                      
}


/**
 * Get Run date
 *
 * @param 	string      	$format Optional. 	PHP date format defaults to the 
 *                             					date_format option if not 
 *                             					specified.
 * @param  	int|WP_Post 	$post 	Optional. 	Post ID or WP_Post object. 
 *                              				Default current post.
 * @author 	Jérémy Levron 	levronjeremy@19h47.fr
 */
function get_run_date( $format = '', $post_id = false ) {

	if( $format == '' ) {
		$format = 'j F Y G \h i \m\i\n';
	}

	return get_the_date( $format, $post_id );
}


/**
 * The Run date
 *
 * @param  boolean 					$post_id
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function the_run_date( $format = '', $post_id = false ) {
	
	echo get_run_date( $format, $post_id );	                                      
}

/**
 * Get Run duration
 * 
 * @param  	boolean 				$post_id
 * @return 	function 				get_run_meta
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function get_run_duration( $post_id = false ) {
	
	return get_run_meta( $post_id, 'run_duration' );
}


/**
 * The Run duration
 *
 * @param  boolean 					$post_id
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function the_run_duration( $post_id = false ) {
	
	echo get_run_duration( $post_id );	                                      
}


/**
 * Get Run calories
 * 
 * @param  	boolean 				$post_id
 * @return 	function 				get_run_meta
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function get_run_calories( $post_id = false ) {
	
	return get_run_meta( $post_id, 'run_calories' );
}


/**
 * The Run calories
 *
 * @param  boolean 					$post_id
 * @author 	Jérémy Levron 			levronjeremy@19h47.fr
 */
function the_run_calories( $post_id = false ) {
	
	echo get_run_calories( $post_id );	                                      
}