<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Checkbox Class
 *
 * This class renders a checkbox.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Checkbox_Form_Field' ) ) {

	class Youxi_Checkbox_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor.
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'inline' => true
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
			$class = 'youxi-form-list';
			if( $this->get_option( 'inline' ) ) {
				$class .= ' inline';
			}

			if( isset( $attr['class'] ) ) {
				$attr['class'] = Youxi_Form::normalize_class( $class, $attr['class'] );
			}
			
			return parent::filter_field_attr( $attr );
		}

		/**
		 * Get the field's label
		 *
		 * @return string The label's HTML markup
		 */
		public function get_the_label( $attributes = array() ) {
			return '';
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

			$o = '<ul id="' . $this->get_the_ID() . '"' . Youxi_Form::render_attr( $attributes ) . '>';
				$o .= '<li>';
					$o .= '<label>';
						$o .= '<input type="hidden" name="' . $this->get_the_name() . '" value="0">';
						$o .= '<input type="checkbox" name="' . $this->get_the_name() . '" value="1" ' . checked( $value, 1, false ) . '>';
						$o .= ' ' . esc_html( $this->get_option( 'label' ) );
					$o .= '</label>';
				$o .= '</li>';
			$o .= '</ul>';
			
			return $o;
		}
	}
}