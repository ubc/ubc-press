<?php
/**
 * Instructor widget form
 *
 * @since 1.0.0
 *
 */

$url = admin_url();

$add_handouts_url 	= $url .'edit.php?post_type=handout';

$widget_title 	= ! empty( $instance['widget_title'] ) ? $instance['widget_title'] : 'Handouts';
$handout_id 	= ! empty( $instance['handouts_id'] ) ? $instance['handouts_id'] : 0;

$description_check = ! empty( $instance['description'] ) ? 1 : 0;

$handout_args = array(

	'posts_per_page' => 50,
	'post_type'		 	 => 'handout',
	'post_status' 	 => 'publish',

);

?>
<p>
	<label for="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>"><?php echo esc_html( 'Widget title:' ); ?></label>
	<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'widget_title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'widget_title' ) ); ?>" type="text" value="<?php echo esc_attr( $widget_title ); ?>">
</p>
<!-- widget title -->

<?php $hasposts = get_posts( $handout_args );

if ( ! $hasposts ) {

	echo '<p> Sorry, there are no handouts available but you can easily add some <a href="' . esc_url( $add_handouts_url ) . '">here</a>.';

	return;
}
?>

<p>
	<input class="checkbox" type="checkbox" <?php echo esc_attr( checked( $description_check, 1 ) ); ?> id="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'description' ) ); ?>" />
	    <label for="<?php echo esc_attr( $this->get_field_id( 'description' ) ); ?>">Show handout description</label>
</p>
<!-- description checkbox -->

<p>

	<label for="<?php echo esc_attr( $this->get_field_id( 'handouts_id' ) ); ?>"><?php echo esc_html( 'Select handout' ); ?></label>

	<select id="<?php echo esc_attr( $this->get_field_id( 'handouts_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'handouts_id' ) ); ?>">

		<?php $get_handouts = get_posts( $handout_args ); ?>

			<?php foreach ( $get_handouts as $the_handout ) { ?>

				<?php $handouts_id = $the_handout->ID; ?>

				<?php $selected = ( $the_handout->ID == $handout_id ) ? 'selected' : ''; ?>

				<?php if ( strlen( get_the_title( $handouts_id ) ) > 30 ) { ?>

					<?php $title = substr( get_the_title( $handouts_id ), 0, 27 ) . '...'; ?>

				<?php } else { ?>

					<?php $title = get_the_title( $handouts_id ); ?>

				<?php } ?>

		<option value="<?php echo esc_attr( $the_handout->ID ); ?>" <?php echo $selected; ?>><?php echo esc_html( $title ); ?></option>


			<?php } ?>

	</select>

</p>
<!-- handout select -->
