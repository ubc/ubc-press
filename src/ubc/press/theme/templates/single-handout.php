<?php

/**
 * Single template for handouts. Used when someone hits up a single
 * handout URL or when an handout component is added to a section.
 * Already in the_loop
 *
 * @since 1.0.0
 *
 */

$post_id 		= get_the_ID();
$content 		= \UBC\Press\Utils::get_handout_content( $post_id );

$description 	= ( isset( $content['fields']['_handout_details_description'] ) ) ? $content['fields']['_handout_details_description'] : false;
$files 			= isset( $content['fields']['_handout_details_file_list'] ) ? $content['fields']['_handout_details_file_list'] : array();
?>

<div class="component-wrapper component-handout">

	<h3><?php echo get_the_title( $post_id ); ?></h3>

	<div class="handout-content">

		<?php if ( $description ) : ?>
			<div class="handout-description">
				<?php echo wp_kses_post( $description ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $files ) ) : ?>
			<ul class="hanfout-file-list">
				<?php foreach ( $files as $id => $file_path ) : ?>
					<li><a href="<?php echo esc_url( $file_path ); ?>" title="<?php echo esc_url( $file_path ); ?>"><?php echo esc_url( $file_path ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div><!-- .assignment-content -->

</div><!-- .component-wrapper -->
