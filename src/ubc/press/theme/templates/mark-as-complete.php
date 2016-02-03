<?php

/**
 * Templatge for the 'mark as complete' button that is placed for each component
 *
 * @since 1.0.0
 *
 */

$data 					= get_query_var( 'template_data' );

$get_the_title			= get_the_title();
$button_size			= 'medium';

$button_text 			= ( isset( $data['completed'] ) && true === $data['completed'] ) ? __( 'Mark Uncomplete', \UBC\Press::get_text_domain() ) : __( 'Mark Complete', \UBC\Press::get_text_domain() );
$tooltip_text 			= ( isset( $data['completed'] ) && true === $data['completed'] ) ? __( 'Mark Uncomplete?', \UBC\Press::get_text_domain() ) : __( 'Mark Complete?', \UBC\Press::get_text_domain() );
$when_completed_text	= ( isset( $data['when_completed'] ) ) ? human_time_diff( $data['when_completed'], current_time( 'timestamp' ) ) : false;
$tooltip_when_completed	= ( isset( $data['when_completed'] ) ) ? sprintf( _x( 'Completed %s ago', '%s = human-readable time difference', \UBC\Press::get_text_domain() ), $when_completed_text ) : 'Mark as Complete';
$button_class 			= ( isset( $data['completed'] ) && true === $data['completed'] ) ? 'success' : 'secondary';
$mark_incomplete		= ( isset( $data['completed'] ) && true === $data['completed'] ) ? __( 'Mark incomplete', \UBC\Press::get_text_domain() ) : '';
$nonce 					= wp_create_nonce( 'mark_as_complete' );
$dashicon 				= ( ! $when_completed_text ) ? 'dashicons dashicons-yes onhover' : 'dashicons dashicons-no onhover';


// Default URL. As a failsafe if JS is broken
$url 					= \UBC\Press\Ajax\Utils::get_ubc_press_ajax_action_url( 'mark_as_complete', true, $nonce, array( 'post_id' => $data['post_id'], 'post_type' => $data['post_type'] ) );
?>

<div class="mark-as-complete-wrapper">
	<div class="row">
		<div class="medium-9 large-8 columns">
			<header>
				<h3><?php echo esc_html( $get_the_title ); ?></h3>
			</header>
		</div>
		<div class="medium-3 large-4 columns">
			<ul class="button-group">
				<li>
					<a role="button" aria-label="<?php echo esc_html( $button_text ); ?>" href="<?php echo esc_url( $url ); ?>" data-nonce="<?php echo esc_html( $nonce ); ?>" data-post_id="<?php echo absint( $data['post_id'] ); ?>" class="<?php echo esc_html( $button_size ); ?> button round mark-as-complete <?php echo esc_html( $button_class ); ?> hint--bottom-left" data-hint="<?php echo esc_html( $tooltip_when_completed ); ?>">
						<span class="button-text"><?php echo esc_html( $button_text ); ?></span><span class="dashicons dashicons-yes"></span>
					</a>
				</li>
				<li>
					<a role="button" href="#" class="button <?php echo esc_html( $button_size ); ?> heart hint--bottom" data-hint="Save for later"><span class="dashicons dashicons-heart"></span></a>
				</li>
				<li>
					<a role="button" href="#" class="button <?php echo esc_html( $button_size ); ?> share hint--bottom" data-hint="Share with group"><span class="dashicons dashicons-share"></span></a>
				</li>
				<li>
					<a role="button" href="#" class="button <?php echo esc_html( $button_size ); ?> feedback hint--bottom" data-hint="Provide feedback"><span class="dashicons dashicons-clipboard"></span></a>
				</li>
			</ul>
		</div>
		<!-- end .button-bar -->
	</div>
	<!-- end .row -->
</div>
