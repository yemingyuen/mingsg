<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Text Field Class
 *
 * This class renders a basic text field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Text_Form_Field' ) ) {

	class Youxi_Text_Form_Field extends Youxi_Form_Field {

		/**
		 * Get the field's HTML markup
		 *
		 * @param mixed The field's current value (if it exists)
		 * @param array The HTML attributes to be added on the field
		 *
		 * @return string The field's HTML markup
		 */
		public function get_the_field( $value, $attributes = array() ) {
			return '<input id="' . $this->get_the_ID() . '" name="' . $this->get_the_name() . '" type="text" value="' . esc_attr( $value ) . '"' . Youxi_Form::render_attr( $attributes ) . '>';
		}
	}
}