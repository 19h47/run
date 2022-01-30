(function ($) {
	// we create a copy of the WP inline edit post function
	var inlineEditPostEdit = inlineEditPost.edit;

	// and then we overwrite the function with our own code
	inlineEditPost.edit = function (id) {
		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		inlineEditPostEdit.apply(this, arguments);

		// now we take care of our business

		// get the post ID
		let postId = 0;

		if (typeof id === "object") {
			postId = parseInt(this.getId(id));
		}

		// define the edit row
		const $edit = document.getElementById("edit-" + postId);

		["duration", "steps", "calories"].forEach((field) => {
			const value = document.getElementById(`run_${field}-${postId}`).innerHTML.trim();

			$edit.querySelector(`input[name="run_${field}"]`).value = value;
		});
	};

	$("#bulk_edit").on("click", function () {
		// define the bulk edit row
		var $bulk_row = $("#bulk-edit");

		// get the selected post ids that are being edited
		var $post_ids = [];

		$bulk_row
			.find("#bulk-titles")
			.children()
			.each(function () {
				$post_ids.push(
					$(this)
						.attr("id")
						.replace(/^(ttle)/i, "")
				);
			});

		// get the custom fields
		const run_duration = $bulk_row.find('input[name="run_duration"]').val();
		const run_steps = $bulk_row.find('input[name="run_steps"]').val();
		const run_calories = $bulk_row.find('input[name="run_calories"]').val();

		// save the data
		$.ajax({
			url: ajaxurl, // this is a variable that WordPress has already defined for us
			type: "POST",
			async: false,
			cache: false,
			data: {
				action: "manage_wp_posts_using_bulk_quick_save_bulk_edit", // this is the name of our WP AJAX function that we'll set up next
				post_ids: $post_ids, // and these are the 2 parameters we're passing to our function
				run_duration,
				run_steps,
				run_calories,
			},
		});
	});
})(jQuery);
