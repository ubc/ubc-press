<?php
/**
 * Instructor widget form
 *
 * @since 1.0.0
 *
 */

$get_bloginfo_email = get_bloginfo( 'admin_email' );

// Search for users with sites primary email address!
$blogusers = get_users( array( 'search' => $get_bloginfo_email ) );

// Now that we have the user with the matching email address get the users id!
foreach ( $blogusers as $username ) {

		$instructor_id	= $username->ID;
}

// Lets find out MORE about that user!
$users_info = get_userdata( intval( $instructor_id ) );
// Here the more part(s)!
$instructor_name 			= $users_info->display_name;
$instructor_email			= $users_info->user_email;
$instructor_website 		= $users_info->user_url;

// Here are defaults for the widgets!
$title 			= ! empty( $instance['title'] ) ? $instance['title'] : __( 'Course Instructor', 'ubc-press' );

$name 			= ! empty( $instance['name'] ) ? $instance['name'] : __( $instructor_name, 'ubc-press' );

$email 			= ! empty( $instance['email'] ) ? $instance['email'] : __( $instructor_email, 'ubc-press' );

$website		= ! empty( $instance['website'] ) ? $instance['website'] : __( $instructor_website, 'ubc-press' );

$telephone 		= ! empty( $instance['telephone'] ) ? $instance['telephone'] : __( '000 000 0000', 'ubc-press' );

// $office_hours 	= ! empty( $instance['office_hours'] ) ? $instance['office_hours'] : array();

?>

<!-- Widget title -->
<fieldset>
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>"><?php echo esc_html( 'Widget title:' ); ?></label>
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
	</p>
</fieldset>
<hr>
<fieldset>
	<legend>
		<strong>Contact Information</strong><br>
		<small>Set instructor name, email, website and telephone.</small>
	</legend>

	<!-- Instructor name -->
	<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>"><?php echo esc_html( 'Name:' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'name' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'name' ) ); ?>" type="text" value="<?php echo esc_attr( $name ); ?>">
	<small>(defaults to primary site user)</small>
	</p>
	<!-- end Instructor name -->

	<!-- Instructor email -->
	<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>"><?php echo esc_html( 'Email:' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" type="email" value="<?php echo esc_attr( $email ); ?>">
	<small>(defaults to primary site user email)</small>
	</p>
	<!-- end Instructor email -->

	<!-- website -->
	<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'website' ) ); ?>"><?php echo esc_html( 'Website:' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'website' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'website' ) ); ?>" type="url" value="<?php echo esc_url( $website ); ?>">
	<small>(defaults to primary users website if one has been added to their profile)</small>
	</p>
	<!-- end website -->

	<!-- telephone -->
	<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'telephone' ) ); ?>"><?php echo esc_html( 'Phone number:' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'telephone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'telephone' ) ); ?>" type="tel" value="<?php echo esc_attr( $telephone ); ?>">
	<small>(defaults to primary users website if one has been added to their profile)</small>
	</p>
	<!-- end telephone -->
</fieldset>
