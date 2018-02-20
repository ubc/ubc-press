<?php
/**
 * The template for displaying the general dashboard.
 *
 * @package wp-clf-lite
 */


$get_component = ! empty( $wp_query->query_vars['component'] ) ? $wp_query->query_vars['component'] : '';
$get_sections = \UBC\Press\Utils::ubc_press_get_sections_by_component( esc_html( $get_component ) );
$miss_msg1 = 'Wait... What... there is nothing here... Run to the hills!';
$get_component_name = 'hiddenquiz' === $get_component ? 'quizze' : $get_component;
$section_type = UBC_WP_CLF_Lite::get_course_content_name_convention();
?>

<?php if ( is_array( $get_sections ) ) : ?>

<ul class="sub-section-list no-bullet">

<?php foreach ( $get_sections as $get_section ) :

	$get_id 			 		= $get_section->ID;
	$get_title 		 		= get_the_title( $get_id );
	$get_permalink 		= get_the_permalink( $get_id );
	$title_attr_args 	= array( 'before' => 'Read ', 'after' => '', true, $get_id );
	$get_ancestor 		= get_ancestors( $get_id, 'section', 'post_type' );
	$get_parent_id 		= $get_ancestor[0];
	$get_parent_title = get_the_title( $get_parent_id );
	$get_excerpt 			= get_the_excerpt();

	// Find out the number of completed components for this section. @TODO: AJAX call?
	$completed_comps_data		= \UBC\Press\Utils::completed_components_for_section( $get_id );

	// $completed_comps_data will contain ['completed_components'] and 'total_components'
	$num_total_components		= $completed_comps_data['total_components'];
	$num_completed_components	= count( $completed_comps_data['completed_components'] );

	// Find the percentage for completed components complete / total * 100. Assume 100 when no completable.
	$num_percentage				= ( 0 === $num_total_components || null === $num_total_components ) ? 100 : ( ($num_completed_components / $num_total_components) * 100 );

	// Set class for 100% complete.
	$numb_total_complete		= ( 100 === $num_percentage ) ? 'completed' : '';

	// Set class for when complete components is zero or another class when progress has started.
	$no_complete_components 	= ( 0 === $num_completed_components ) ? 'no-complete' : 'start-progress';

	$completed_text = sprintf( __( '%1$sCompleted: %2$s%3$s/%4$s', \UBC\Press::get_text_domain() ),
		'<span class="completed-text">',
		'</span>',
		'<span class="completed-components">' . absint( $num_completed_components ) . '</span>',
		'<span class="total-components">' . absint( $num_total_components ) . '</span>'
	);

	$no_completable_components_text = __( 'None to complete', 'ubc-wp-clf-lite' );

	$comp_text_to_output = ( $num_total_components > 0 ) ? $completed_text : $no_completable_components_text;

?>

<li class="align-items-center sub-section-<?php echo esc_html( $get_id ); ?>" data-post-id="<?php echo absint( $get_id ); ?>">
	<a href="<?php echo esc_url( $get_permalink ); ?>" title="<?php the_title_attribute( array( 'before' => 'Read more on ', 'after' => wp_kses_post( $comp_text_to_output ), true, absint( $get_id ) ) ); ?>" class="sub-section-link row">
		<?php if ( is_user_logged_in() && ! empty( $show_mark_complete_btn ) ) : ?>

			<span class="completed-components-details show-for-sr"><small>[<?php echo wp_kses_post( $comp_text_to_output ); ?>]</small></span>

			<?php if ( $num_total_components > 0 ) : ?>

			<div aria-hidden="true" class="progress <?php echo esc_html( $numb_total_complete ); ?> <?php echo esc_html( $no_complete_components ); ?>" role="progressbar" tabindex="0" aria-valuenow="<?php echo absint( $num_percentage ); ?>" aria-valuemin="0" aria-valuetext="<?php echo absint( $num_percentage ); ?> percent"  aria-valuemax="100">
				<div class="progress-meter" style="width: <?php echo absint( $num_percentage ); ?>%">
				</div>
				<p class="progress-meter-text"><span class="show-for-sr"><?php echo absint( $num_percentage ); ?>%</span> <svg class="ui-icon" aria-hidden="true"><use xlink:href="#checkmark-circle"></use></svg></p>
			</div>

		<?php else: ?>

			<span class="forum-icon">
				<svg class="ui-icon discussions" aria-hidden="true"><use xlink:href="#discussions"></use></svg>
			</span>

			<?php endif; ?>



		<?php endif; ?>
		<h4 class="sub-section-title">
			<?php  echo esc_html( $get_title ); ?>
		</h4>
	</a>

</li>

<?php endforeach; ?>

<?php else : ?>

	<p class="lead">
		<strong><?php echo esc_html( __( $miss_msg1, 'ubc-press' ) ); ?></strong>
	</p>
	<p>
		Sorry. There appears to be no <?php echo esc_html( $section_type ); ?>s available for "<i><?php echo esc_html( $get_component_name ); ?></i>".
	</p>

</ul>
<?php endif; ?>
