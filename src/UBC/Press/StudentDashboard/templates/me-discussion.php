<div class="tabs-panel column" id="panel5v">

	<?php do_action( 'bbp_template_before_user_topics_created' ); ?>

	<div id="bbp-user-topics-started" class="bbp-user-topics-started">
		<h2 class="entry-title"><?php esc_html_e( 'Forum Topics Started', 'bbpress' ); ?></h2>
		<div class="bbp-user-section">

			<?php if ( bbp_get_user_topics_started( get_current_user_id() ) ) : ?>

				<?php bbp_get_template_part( 'pagination', 'topics' ); ?>

				<?php bbp_get_template_part( 'loop',       'topics' ); ?>

				<?php bbp_get_template_part( 'pagination', 'topics' ); ?>

			<?php else : ?>

				<p><?php bbp_is_user_home()
					? esc_html_e( 'You have not created any topics.',      'bbpress' )
					: esc_html_e( 'This user has not created any topics.', 'bbpress' );
				?></p>

			<?php endif; ?>

		</div>
	</div><!-- #bbp-user-topics-started -->

	<?php do_action( 'bbp_template_after_user_topics_created' ); ?>

	<?php do_action( 'bbp_template_before_user_replies' ); ?>

	<div id="bbp-user-replies-created" class="bbp-user-replies-created">
		<h2 class="entry-title"><?php esc_html_e( 'Forum Replies Created', 'bbpress' ); ?></h2>
		<div class="bbp-user-section">

			<?php if ( bbp_get_user_replies_created( get_current_user_id() ) ) : ?>

				<?php bbp_get_template_part( 'pagination', 'replies' ); ?>

				<?php bbp_get_template_part( 'loop',       'replies' ); ?>

				<?php bbp_get_template_part( 'pagination', 'replies' ); ?>

			<?php else : ?>

				<p><?php bbp_is_user_home()
					? esc_html_e( 'You have not replied to any topics.',      'bbpress' )
					: esc_html_e( 'This user has not replied to any topics.', 'bbpress' );
				?></p>

			<?php endif; ?>

		</div>
	</div><!-- #bbp-user-replies-created -->

	<?php do_action( 'bbp_template_after_user_replies' ); ?>

</div>
