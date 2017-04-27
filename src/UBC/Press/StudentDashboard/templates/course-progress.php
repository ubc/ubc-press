<?php

$completed_percentage = \UBC\Press\StudentDashboard\Utils::get_total_course_completion_percentage_for_student();
$completed_data = \UBC\Press\StudentDashboard\Utils::get_course_completion_data_for_student();

?>

<section class="course-progress">

	<h2>Course progress <small>(Completed: <?php echo absint( $completed_data['num_completed_components'] ); ?>/<?php echo absint( $completed_data['num_components'] ) ?>)*</small></h2>

	<div class="progress" role="progressbar" tabindex="0" aria-valuenow="<?php echo absint( $completed_percentage ); ?>" aria-valuemin="0" aria-valuetext="<?php echo absint( $completed_percentage ); ?> percent" aria-valuemax="100">

		<span class="progress-meter" style="width: <?php echo absint( $completed_percentage ); ?>%">
			<p class="progress-meter-text"><?php echo absint( $completed_percentage ); ?>%</p>
		</span><!-- .progress-meter -->

	</div><!-- .progress -->
	<p>
		<small>*Completed indicates the number of elements that have been marked as complete throughout the course material.</small>
	</p>
</section><!-- .course-progress -->
