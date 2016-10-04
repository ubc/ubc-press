<?php
/**
 * Instructor widget template
 *
 * @since 1.0.0
 *
 */

$widget_title 			= ! empty( $instance['widget_title'] ) ? $instance['widget_title'] : '';
$handout_id 			= ! empty( $instance['handouts_id'] ) ? $instance['handouts_id'] : '';
$handout_desc_check		= $instance['description'];
$handout_file_lists 	= get_post_meta( $handout_id, '_handout_details_file_list', true );
$handout_description 	= get_post_meta( $handout_id, '_handout_details_description', true );

?>

<?php if ( $handout_id > 0  ) : ?>

<header>
	<h4><span class="dashicons dashicons-category"></span> <?php echo esc_html( $widget_title ); ?></h4>
</header>


<?php if ( 'on' === $handout_desc_check ) : ?>
<div>
	<?php echo wp_kses_post( $handout_description ); ?>
</div>
<?php endif; ?>

<ul class="no-bullet">

<?php foreach ( $handout_file_lists as $value => $file ) : ?>

	<?php $attachment_title = get_the_title( $value ); ?>

	<?php $attachment_type  = get_post_mime_type( $value ); ?>

<?php switch ( $attachment_type ) {
	// Set icon for file type!
	case 'image/jpeg':
	case 'image/png':
	case 'image/gif':

		$dashicon = 'dashicons-format-image';

	break;

	case 'text/plain':
	case 'text/xml':
	case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
	case 'application/pdf':
	case 'application/msword':

		$dashicon = 'dashicons-media-text';

	break;

	case 'text/csv':
	case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
	case 'application/x-excel':

		$dashicon = 'dashicons-media-spreadsheet';

	break;

	case 'application/vnd.ms-powerpoint':

		$dashicon = 'dashicons-media-interactive';

	break;

	case 'application/zip':

		$dashicon = 'dashicons-media-archive';

	break;

	default:

		$dashicon = 'dashicons-media-default';

	break;

} ?>


	<li><a href="<?php echo esc_url( $file ); ?>"><span aria-hidden="true" class="dashicons <?php echo esc_attr( $dashicon ); ?>"></span> <?php echo esc_html( $attachment_title ); ?> </a></li>
<?php endforeach; ?>

</ul>

<?php endif;

