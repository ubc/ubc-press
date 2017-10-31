<?php

$completed_percentage = \UBC\Press\StudentDashboard\Utils::get_total_course_completion_percentage_for_student();
$completed_data = \UBC\Press\StudentDashboard\Utils::get_course_completion_data_for_student();

?>

	<p class="lead">
		This is your overall progress for the course material.
	</p>
	<div class="course-progress" style="width: 100%" aria-hidden="true">
		<div class="progress" role="progressbar" tabindex="0" aria-valuenow="<?php echo absint( $completed_percentage ); ?>" aria-valuemin="0" aria-valuetext="<?php echo absint( $completed_percentage ); ?> percent" aria-valuemax="100">

			<span class="progress-meter" style="width: <?php echo absint( $completed_percentage ); ?>%">
				<p class="progress-meter-text">Completed <?php echo absint( $completed_percentage ); ?>% of all components</p>
			</span>

		</div>
	</div>
	<br />

	<p>
		You have completed <strong><i><?php echo absint( $completed_data['num_completed_components'] ); ?> of the <?php echo absint( $completed_data['num_components'] ) ?> components</i></strong> for this course.
	<br />
		This means that you have completed <strong><i><?php echo absint( $completed_percentage ); ?>%</i></strong> of the course material. *
	</p>
	<footer>
		<p>
			<small>* This is a tally of all the components that can be completed throughout the course material, such as readings, assignments, lectures, videos, and indicates how many you have completed.</small>
		</p>
	</footer>
