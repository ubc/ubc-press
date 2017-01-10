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
	<h3 class="widget-title"><svg class="ui-icon file" aria-hidden="true"><use xlink:href="#download"></use></svg> <?php echo esc_html( $widget_title ); ?></h3>
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

		$dashicon = '<svg class="ui-icon picture-icon" aria-hidden="true"><use xlink:href="#file-image"></use></svg>';

	break;

	case 'text/plain':
	case 'text/xml':
	case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
	case 'application/msword':

		$dashicon = '<svg class="ui-icon file" aria-hidden="true"><use xlink:href="#file-document"></use></svg>';

	break;

	case 'application/pdf':

		$dashicon = '<svg class="ui-icon file" aria-hidden="true"><use xlink:href="#file-pdf"></use></svg>';

		break;

	case 'text/csv':
	case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
	case 'application/x-excel':

		$dashicon = '<svg class="ui-icon file" aria-hidden="true"><use xlink:href="#file-excel"></use></svg>';

	break;

	case 'application/vnd.ms-powerpoint':

		$dashicon = '<svg class="ui-icon file" aria-hidden="true"><use xlink:href="#file-powerpoint"></use></svg>';

	break;

	case 'application/zip':

		$dashicon = '<svg class="ui-icon file" aria-hidden="true"><use xlink:href="#file-document"></use></svg>';

	break;

	default:

		$dashicon = '<svg class="ui-icon file" aria-hidden="true"><use xlink:href="#file-document"></use></svg>';

	break;

} ?>


	<li><a href="<?php echo esc_url( $file ); ?>"><?php echo $dashicon; ?> <?php echo esc_html( $attachment_title ); ?> </a></li>
<?php endforeach; ?>

</ul>

<?php endif;
