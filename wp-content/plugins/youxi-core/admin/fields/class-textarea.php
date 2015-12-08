<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Textarea Class
 *
 * This class renders a basic textarea.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Textarea_Form_Field' ) ) {

	class Youxi_Textarea_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'rows' => 8
			));

			parent::__construct( $scope, $options, $allowed_hooks );
		}

		/**
		 * Apply form item attributes filtering
		 * 
		 * @param array The current attributes of the field
		 *
		 * @return array The filtered attributes of the field
		 */
		public function filter_field_attr( $attr ) {
			$attr['rows'] = $this->get_option( 'rows' );
			return parent::filter_field_attr( $attr );
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
			return '<textarea id="' . $this->get_the_ID() . '" name="' . $this->get_the_name() . '"' . Youxi_Form::render_attr( $attributes ) . '>' . esc_textarea( $value ) . '</textarea>';
		}
	}
}