<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi URL Field Class
 *
 * This class renders a textfield that is specialized for URLs.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */

if( ! class_exists( 'Youxi_Text_Form_Field' ) ) {
	require( 'class-text.php' );
}

if( ! class_exists( 'Youxi_URL_Form_Field' ) ) {

	class Youxi_URL_Form_Field extends Youxi_Text_Form_Field {

		/**
		 * Parse a field's value, by default it loads the default if value is empty
		 * 
		 * @param mixed The value to parse
		 *
		 * @return mixed The parsed value
		 */
		public function parse_value( $value ) {
			return esc_url( $value );
		}

		/**
		 * Sanitize the user submitted data
		 * 
		 * @param mixed The data to sanitize
		 *
		 * @return mixed The sanitized data
		 */
		public function sanitize( $data ) {
			return esc_url( $data );
		}

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