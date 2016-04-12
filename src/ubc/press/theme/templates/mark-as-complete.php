<?php

/**
 * Templatge for the 'mark as complete' button that is placed for each component
 *
 * @since 1.0.0
 *
 */

$data 					= get_query_var( 'template_data' );

$get_the_title			= get_the_title();
$get_the_id				= get_the_id();
$button_size			= 'medium';

$button_text 			= ( isset( $data['completed'] ) && true === $data['completed'] ) ? __( 'Mark Uncomplete', \UBC\Press::get_text_domain() ) : __( 'Mark Complete', \UBC\Press::get_text_domain() );
$tooltip_text 			= ( isset( $data['completed'] ) && true === $data['completed'] ) ? __( 'Mark Uncomplete?', \UBC\Press::get_text_domain() ) : __( 'Mark Complete?', \UBC\Press::get_text_domain() );
$when_completed_text	= ( isset( $data['when_completed'] ) ) ? human_time_diff( $data['when_completed'], current_time( 'timestamp' ) ) : false;
$tooltip_when_completed	= ( isset( $data['when_completed'] ) ) ? sprintf( _x( 'Completed %s ago', '%s = human-readable time difference', \UBC\Press::get_text_domain() ), $when_completed_text ) : 'Mark as Complete';
$button_class 			= ( isset( $data['completed'] ) && true === $data['completed'] ) ? 'success' : 'secondary hollow';
$mark_incomplete		= ( isset( $data['completed'] ) && true === $data['completed'] ) ? __( 'Mark incomplete', \UBC\Press::get_text_domain() ) : '';
$nonce 					= wp_create_nonce( 'mark_as_complete' );
$dashicon 				= ( ! $when_completed_text ) ? 'dashicons dashicons-yes onhover' : 'dashicons dashicons-no onhover';


// Default URL. As a failsafe if JS is broken
$url 					= \UBC\Press\Ajax\Utils::get_ubc_press_ajax_action_url( 'mark_as_complete', true, $nonce, array( 'post_id' => $data['post_id'], 'post_type' => $data['post_type'] ) );
?>

<div class="mark-as-complete-wrapper clearfix">
	<div class="row">
		<div class="title small-10 medium-7 columns">
			<header>
				<h3><?php echo esc_html( $get_the_title ); ?> </h3>
			</header>
		</div>
		<?php // Kiiinda pointless for someone who isn't signed in
		if ( is_user_logged_in() ) : ?>
		<div class="buttons small-2 medium-5 columns">
			<div class="text-right" data-responsive-toggle="mark-as-complete-<?php echo esc_attr( $get_the_id ); ?>">
				<button id="actionbar-<?php echo esc_attr( $get_the_id ); ?>" class="mobile-button button <?php echo esc_attr( $button_class ); ?> tiny" type="button" data-toggle=" actionbar-<?php echo esc_attr( $get_the_id ); ?> hamburger-<?php echo esc_attr( $get_the_id ); ?>" data-toggler=".active">
					<span class="hamburger dots" id="hamburger-<?php echo esc_attr( $get_the_id ); ?>" data-toggler=".dots"></span>
				</button>
			</div>
			<ul id="mark-as-complete-<?php echo esc_attr( $get_the_id ); ?>" class="button-group section-button-group small-horizontal menu float-right">
				<li>
					<a
					role="button"
					aria-label="<?php echo esc_html( $button_text ); ?>"
					href="<?php echo esc_url( $url ); ?>"
					data-nonce="<?php echo esc_html( $nonce ); ?>"
					data-post_id="<?php echo absint( $data['post_id'] ); ?>"
					class="<?php echo esc_html( $button_size ); ?> button round mark-as-complete <?php echo esc_attr( $button_class ); ?>"
					data-click-open="false"
					data-tooltip
					aria-haspopup="true"
					data-disable-hover="false"
					data-fade-out-duration="600"
					tabindex="1"
					title="<?php echo esc_html( $tooltip_when_completed ); ?>"
					>

						<span class="button-text"><?php echo esc_html( $button_text ); ?></span>
						<span class="dashicons dashicons-yes"></span>
					</a><!-- .mark as-complete -->
				</li>
				<li>
					<a role="button" href="#" class="button hollow <?php echo esc_html( $button_size ); ?> heart alert" data-tooltip aria-haspopup="true" data-disable-hover="false" tabindex="1" title="Save for later">
						<span class="dashicons dashicons-heart"></span>
					</a><!-- save for later -->
					<!-- <p class="show-for-small-only"><small>Save for Later</small></p> -->
				</li>
				<li>
					<a role="button" href="#" class="button hollow <?php echo esc_html( $button_size ); ?> share warning" data-tooltip aria-haspopup="true" data-disable-hover="false" tabindex="1" title="Share with group.">
						<span class="dashicons dashicons-share"></span>
					</a><!-- share -->
				</li>
				<li>
					<a role="button" href="#" class="button hollow <?php echo esc_html( $button_size ); ?> feedback" data-tooltip aria-haspopup="true" data-disable-hover="false" tabindex="1" title="Provide feedback.">
						<span class="dashicons dashicons-clipboard"></span>
					</a><!-- feedback -->
				</li>
			</ul>
		</div>
		<!-- end .button-bar -->
		<?php endif; ?>
	</div>
	<!-- end .row -->
</div>
