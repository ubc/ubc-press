<?php
/**
 * Instructor widget form
 *
 * @since 1.0.0
 *
 */

$widget_title 	= ! empty( $instance['widget_title'] ) ? $instance['widget_title'] : 'Course components';

// $show_empty = ! empty( $instance['description'] ) ? 1 : 0;

// $post_types = array( 'handout', 'reading', 'link', 'hiddenquiz', 'assignment', 'lecture' );


?>
<p>
	Show all sub sections by component types. For example reading, quizzes, discussions
</p>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>"><?php echo esc_html( 'Widget title:' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_title' ) ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>">
</p>
<!-- widget title -->
