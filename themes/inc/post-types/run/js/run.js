(function($) {

	// we create a copy of the WP inline edit post function
	var $wp_inline_edit = inlineEditPost.edit;
	
	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {
	
		if ( $post_id < 0 ) {
			return;
		}

		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );
		
		// now we take care of our business
		
		// get the post ID
		var $post_id = 0;
		
		if ( typeof( id ) == 'object' ) {
			$post_id = parseInt( this.getId( id ) );
		}		
		
		// define the edit row
		var $edit_row = $( '#edit-' + $post_id );
		
		// get the run duration
		var $run_duration = $('#run_duration-' + $post_id ).text();
		
		// set the run duration
		$edit_row.find('input[name="run_duration"]').val( $run_duration );
		
		// get the run steps
		var $run_steps = $('#run_steps-' + $post_id ).text();
		
		// set the run steps
		$edit_row.find('input[name="run_steps"]' ).val( $run_steps );
		
		// get the run calories
		var $run_calories = $( '#run_calories-' + $post_id ).text();
		
		// set the run calories
		$edit_row.find('input[name="run_calories"]').val( $run_calories );
		
	};
	
	$( '#bulk_edit' ).live( 'click', function() {
	
		// define the bulk edit row
		var $bulk_row = $( '#bulk-edit' );
		
		// get the selected post ids that are being edited
		var $post_ids = new Array();
		$bulk_row.find( '#bulk-titles' ).children().each( function() {
			$post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});
		
		// get the custom fields
		var $run_duration = $bulk_row.find('input[name="run_duration"]').val();
		var $run_steps = $bulk_row.find('input[name="run_steps"]').val();
		var $run_calories = $bulk_row.find('input[name="run_calories"]').val();
		
		// save the data
		$.ajax({
			url: ajaxurl, // this is a variable that WordPress has already defined for us
			type: 'POST',
			async: false,
			cache: false,
			data: {
				action: 'manage_wp_posts_using_bulk_quick_save_bulk_edit', // this is the name of our WP AJAX function that we'll set up next
				post_ids: $post_ids, // and these are the 2 parameters we're passing to our function
				run_duration: $run_duration,
				run_steps: $run_steps,
				run_calories: $run_calories
			}
		});
		
	});
	
})(jQuery);