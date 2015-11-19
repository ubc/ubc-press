<?php

/**
 * Templatge for the 'mark as complete' button that is placed for each component
 *
 * @since 1.0.0
 *
 */

$data 					= get_query_var( 'template_data' );

$button_text 			= ( isset( $data['completed'] ) && true === $data['completed'] ) ? __( 'Completed', \UBC\Press::get_text_domain() ) : __( 'Mark as complete', \UBC\Press::get_text_domain() );
$when_completed_text	= ( isset( $data['when_completed'] ) ) ? human_time_diff( $data['when_completed'], current_time( 'timestamp' ) ) : false;
$button_class 			= ( isset( $data['completed'] ) && true === $data['completed'] ) ? 'success' : 'secondary';
$nonce 					= wp_create_nonce( 'mark_as_complete' );

// Default URL. As a failsafe if JS is broken
$url 					= \UBC\Press\Ajax\Utils::get_ubc_press_ajax_action_url( 'mark_as_complete', true, $nonce, array( 'post_id' => $data['post_id'], 'post_type' => $data['post_type'] ) );
?>

<a role="button" aria-label="<?php echo esc_html( $button_text ); ?>" href="<?php echo esc_url( $url ); ?>" data-nonce="<?php echo esc_html( $nonce ); ?>" data-post_id="<?php echo absint( $data['post_id'] ); ?>" class="small button radius mark-as-complete <?php echo esc_html( $button_class ); ?>">
	<?php echo esc_html( $button_text ); ?>
</a>

<?php if ( $when_completed_text ) : ?>
<span class="when_completed"><?php echo esc_html( sprintf( _x( 'Completed %s ago', '%s = human-readable time difference', \UBC\Press::get_text_domain() ), $when_completed_text ) ); ?></span>
<?php endif; ?>
