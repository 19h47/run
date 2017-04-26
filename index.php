<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta charset="UTF-8"/>
		<title>Stepper</title>
		<?php wp_head() ?>
	</head>

	<body>
		
		<p>Title: <?php echo get_the_title( 15 ) ?></p>
		<p>Duration: <?php the_run_duration( 15 ) ?></p>
		<p>Steps: <?php the_run_steps( 15 ) ?></p>
		<p>Date: <?php echo get_the_date( 'j F Y G \h i \m\i\n', 15 ) ?></p>

		<?php wp_footer() ?>
	</body>

</html>