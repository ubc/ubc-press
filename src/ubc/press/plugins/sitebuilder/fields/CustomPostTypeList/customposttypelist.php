<?php

namespace UBC\Press\Plugins\SiteBuilder\Fields\CustomPostTypeList;

class CustomPostTypeList extends \SiteOrigin_Widget_Field_Base {

	protected function render_field( $value, $instance ) {
		?>
		<input type="checkbox" name="<?php echo esc_attr( $this->element_name ) ?>" id="<?php echo esc_attr( $this->element_id ) ?>" class="siteorigin-widget-input" <?php checked( ! empty( $value ) ) ?> />
		<?php
	}

	protected function render_field_label() {
		echo 'Test Label';
	}

	protected function sanitize_field_input( $value ) {
		return $value;
	}

}
