<!DOCTYPE html>
<html lang="en">
	
	<head>
		<meta charset="UTF-8"/>
		<title>Stepper</title>
		<?php wp_head() ?>
	</head>

	<body>
		
		<p>Title: <?php echo get_the_title( 4 ) ?></p>
		<p>Duration: <?php the_run_duration( 4 ) ?></p>
		<p>Steps: <?php the_run_steps( 4 ) ?></p>
		<p>Date: <?php the_run_date( 'j F Y G \h i', 4 ) ?></p>
		<p>Calories: <?php echo ! empty( get_run_calories( 4 ) ) ? get_run_calories( 4 ) : '—' ?></p>
		<br>
		<p>Title: <?php echo get_the_title( 5 ) ?></p>
		<p>Duration: <?php the_run_duration( 5 ) ?></p>
		<p>Steps: <?php the_run_steps( 5 ) ?></p>
		<p>Date: <?php the_run_date( 'j F Y G \h i', 5 ) ?></p>
		<p>Calories: <?php echo ! empty( get_run_calories( 5 ) ) ? get_run_calories( 5 ) : '—' ?></p>

		<?php wp_footer() ?>
	</body>

</html>