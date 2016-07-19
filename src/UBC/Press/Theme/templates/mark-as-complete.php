<?php

/**
 * Templatge for the 'mark as complete' button that is placed for each component
 *
 * @since 1.0.0
 *
 */

global $wp_query;
$the_id 				= $wp_query->post->ID;

$data 					= get_query_var( 'template_data' );

$get_the_title			= get_the_title();
$get_the_id				= get_the_id();

$button_size			= 'medium';

$button_text 			= ( isset( $data['completed'] ) && true === $data['completed'] ) ? __( 'Mark Uncomplete', \UBC\Press::get_text_domain() ) : __( 'Mark Complete', \UBC\Press::get_text_domain() );
$when_completed_text	= ( isset( $data['when_completed'] ) ) ? human_time_diff( $data['when_completed'], current_time( 'timestamp' ) ) : false;
$tooltip_when_completed	= ( isset( $data['when_completed'] ) ) ? sprintf( _x( 'Completed %s ago', '%s = human-readable time difference', \UBC\Press::get_text_domain() ), $when_completed_text ) : 'Mark as Complete';
$mark_as_complete_classes = ( isset( $data['completed'] ) && true === $data['completed'] ) ? 'success' : 'secondary hollow';

$saved_for_later 		= ( isset( $data['saved_for_later'] ) ) ? $data['saved_for_later'] : false;
$saved_for_later_classes = ( isset( $data['saved_for_later'] ) && ( true === $data['saved_for_later'] ) ) ? '' : 'hollow';

$mark_as_complete_nonce = wp_create_nonce( 'mark_as_complete' );
$fav_nonce 				= wp_create_nonce( 'fav_sub_section' );


// Default URL. As a failsafe if JS is broken
$mark_as_complete_url 	= \UBC\Press\Ajax\Utils::get_ubc_press_ajax_action_url( 'mark_as_complete', true, $mark_as_complete_nonce, array( 'post_id' => $data['post_id'], 'post_type' => $data['post_type'] ) );
$fav_url 				= \UBC\Press\Ajax\Utils::get_ubc_press_ajax_action_url( 'fav_sub_section', true, $fav_nonce, array( 'post_id' => $data['post_id'], 'post_type' => $data['post_type'] ) );

?>
<div class="mark-as-complete-wrapper" data-sticky-container>
	<div class="mark-as-complete-wrapper-inside row expanded clearfix">
		<div class="title small-10 medium-7 columns">
			<header>
				<h2><?php echo esc_html( $get_the_title ); ?> </h2>
			</header>
		</div>
		<?php if ( is_user_logged_in() ) : ?>
		<div class="buttons columns">
			<div class="text-right" data-responsive-toggle="mark-as-complete-<?php echo esc_attr( $get_the_id ); ?>">
				<button id="actionbar-<?php echo esc_attr( $get_the_id ); ?>" class="mobile-button button <?php echo esc_attr( $mark_as_complete_classes ); ?> tiny" type="button" data-toggle=" actionbar-<?php echo esc_attr( $get_the_id ); ?> hamburger-<?php echo esc_attr( $get_the_id ); ?>" data-toggler=".active">
					<span class="dashicons dashicons-menu" id="hamburger-<?php echo esc_attr( $get_the_id ); ?>"></span>
				</button>
			</div>
			<ul id="mark-as-complete-<?php echo esc_attr( $get_the_id ); ?>" class="button-group section-button-group small-horizontal menu float-right">
				<li>
					<a
					role="button"
					aria-label="<?php echo esc_html( $button_text ); ?>"
					href="<?php echo esc_url( $mark_as_complete_url ); ?>"
					data-nonce="<?php echo esc_html( $mark_as_complete_nonce ); ?>"
					data-post_id="<?php echo absint( $data['post_id'] ); ?>"
					class="<?php echo esc_html( $button_size ); ?> button round mark-as-complete <?php echo esc_attr( $mark_as_complete_classes ); ?>"
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
					<a role="button" href="<?php echo esc_url( $fav_url ); ?>" class="button alert <?php echo esc_html( $button_size ); ?> <?php echo esc_attr( $saved_for_later_classes ); ?> heart save-for-later" data-tooltip aria-haspopup="true" data-disable-hover="false" tabindex="1" title="Save for later" href="<?php echo esc_url( $fav_url ); ?>" data-nonce="<?php echo esc_html( $fav_nonce ); ?>" data-post_id="<?php echo absint( $data['post_id'] ); ?>">
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
	<!-- .row -->
</div>
