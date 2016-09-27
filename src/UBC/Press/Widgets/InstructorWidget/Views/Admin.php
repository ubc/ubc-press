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

// Days of the week array!
$days = array(
	'Monday' 	=> 'Monday',
	'Tuesday' 	=> 'Tuesday',
	'Wednesday' => 'Wednesday',
	'Thrusday' 	=> 'Thrusday',
	'Friday' 	=> 'Friday',
	'Saturday' 	=> 'Saturday',
	'Sunday' 	=> 'Sunday',
);

// Here are defaults for the widgets!
$title 			= ! empty( $instance['title'] ) ? $instance['title'] : __( 'Course Instructor', 'widget_textdomain' );

$name 			= ! empty( $instance['name'] ) ? $instance['name'] : __( $instructor_name, 'widget_textdomain' );

$email 			= ! empty( $instance['email'] ) ? $instance['email'] : __( $instructor_email, 'widget_textdomain' );

$website		= ! empty( $instance['website'] ) ? $instance['website'] : __( $instructor_website, 'widget_textdomain' );

$telephone 		= ! empty( $instance['telephone'] ) ? $instance['telephone'] : __( '000 000 0000', 'widget_textdomain' );

$office_hours 	= ! empty( $instance['office_hours'] ) ? $instance['office_hours'] : array();

?>

<script type="text/javascript">
	// We need to allow the instructor to use multiple days and times so lets give them that option
	jQuery(document).ready(function(){

		// Find our object we'd like to duplicate and do it a click of button
		jQuery( 'a#add-row' ).off().click( function() {
			var row = jQuery( '.repeater.empty-row.screen-reader-text' ).eq( 0 );
			row.clone(true);
			row.insertBefore( '#repeater-container hr' );
			row.removeClass( 'empty-row screen-reader-text' );
			return false;
		});

		// Lets also allow them to remove as well.
		jQuery( '.remove-row' ).off( 'click' ).click( function() {
			jQuery(this).parents('.repeater').remove();

			return false;
		});
	});
</script>

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
<hr>
<fieldset id="repeater-container">
	<legend><strong>Office hours</strong></legend>
	<small>Select multiple days by holding command(Mac) or control(Win) and button click with the mouse</small>

<?php if ( is_array( $office_hours ) || is_object( $office_hours ) ) : ?>

	<?php foreach ( $office_hours as $office_hour ) { ?>
	<!-- office hours -->
	TOP
	<div class="repeater">
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'days' ) ); ?>"><?php esc_html( 'Select day(s):' ); ?></label> 
		<select id="<?php echo esc_attr( $this->get_field_id( 'days' ) ); ?>" class="widefat" name="<?php echo esc_attr( $office_hour['days'] ); ?>" multiple>

		<?php foreach ( $days as $day => $value ) : ?>

			<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $office_hour['select'], $day ); ?>><?php echo esc_html( $day ); ?></option>

		<?php endforeach; ?>

		</select>
		<div class="widefat">
			<label for="<?php echo esc_attr( $this->get_field_id( 'start-time' ) ); ?>"><?php esc_html( 'Hours:' ); ?></label>
			<input name="<?php echo esc_attr( $this->get_field_id( 'start-time' ) ); ?>" value="<?php echo esc_attr( $office_hour['start-time'] ); ?>" type="time"> - <input name="<?php echo esc_attr( $this->get_field_id( 'end-time' ) ); ?>" type="time" value="<?php echo esc_attr( $office_hour['end-time'] ); ?>">
		</div>
		</p>
		<p><a class="button remove-row" href="#">Remove</a></p>
	</div>
	<!-- .repeater -->
	<!-- end office hours -->

	<?php } ?>

<?php else : ?>
	<!-- office hours -->
	<div class="repeater">
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'days' ) ); ?>"><?php esc_html( 'Select day(s):' ); ?></label> 
		<select id="<?php echo esc_attr( $this->get_field_id( 'days' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_id( 'days' ) ); ?>" multiple>

		<?php foreach ( $days as $day => $value ) : ?>

			<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $office_hour['select'], $day ); ?>><?php echo esc_html( $day ); ?></option>

		<?php endforeach; ?>

		</select>
		<div class="widefat">
			<label for="<?php echo esc_attr( $this->get_field_id( 'start-time' ) ); ?>"><?php esc_html( 'Hours:' ); ?></label>
			<input name="<?php echo esc_attr( $this->get_field_id( 'start-time' ) ); ?>" value="<?php echo esc_attr( $office_hour['start-time'] ); ?>" type="time"> - <input name="<?php echo esc_attr( $this->get_field_id( 'end-time' ) ); ?>" type="time" value="<?php echo esc_attr( $office_hour['end-time'] ); ?>">
		</div>
		</p>
		<p><a class="button remove-row" href="#">Remove</a></p>
	</div>
	<!-- .repeater -->
	<!-- end office hours -->

<?php endif; ?>

	<div class="repeater empty-row screen-reader-text">

			no go
		<!-- office hours -->
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'days' ) ); ?>"><?php esc_html( 'Select day(s):' ); ?></label> 
		<select id="<?php echo esc_attr( $this->get_field_id( 'days' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_id( 'days' ) ); ?>" multiple>

		<?php foreach ( $days as $day => $value ) : ?>

			<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $office_hour['select'], $day ); ?>><?php echo esc_html( $day ); ?></option>

		<?php endforeach; ?>

		</select>
		<div class="widefat">
			<label for="<?php echo esc_attr( $this->get_field_id( 'start-time' ) ); ?>"><?php esc_html( 'Hours:' ); ?></label>
			<input name="<?php echo esc_attr( $this->get_field_id( 'start-time' ) ); ?>" value="<?php echo esc_attr( $office_hour['start-time'] ); ?>" type="time"> - <input name="<?php echo esc_attr( $this->get_field_id( 'end-time' ) ); ?>" type="time" value="<?php echo esc_attr( $office_hour['end-time'] ); ?>">
		</div>
		</p>
		<p><a class="button remove-row" href="#">Remove</a></p>
	</div>
	<!-- .repeater -->
	<!-- end office hours -->
	<hr>
	<p><a id="add-row" class="button" href="#">Add another</a></p>

</fieldset>
