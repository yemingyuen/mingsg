<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Checkbox List Class
 *
 * This class renders a list of checkboxes.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Checkbox_List_Form_Field' ) ) {

	class Youxi_Checkbox_List_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor.
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'inline' => true, 
				'uncheckable' => false, 
				'choices' => array()
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
			} else {
				$attr['class'] = Youxi_Form::normalize_class( $class );
			}
			
			return parent::filter_field_attr( $attr );
		}

		/**
		 * Parse a field's value, by default it loads the default if value is empty
		 * 
		 * @param mixed The value to parse
		 *
		 * @return mixed The parsed value
		 */
		public function parse_value( $value ) {
			return (array) parent::parse_value( $value );
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

				foreach( (array) $this->get_option( 'choices' ) as $key => $choice ):

				$o .= '<li>';
					$o .= '<label>';

						if( $this->get_option( 'uncheckable' ) ):

						$o .= '<input type="hidden" name="' . $this->get_the_name() . esc_attr( "[$key]" ) . '" value="0">';
						$o .= '<input type="checkbox" name="' . $this->get_the_name() . esc_attr( "[$key]" ) . '" value="1"' . checked( isset( $value[ $key ] ) ? $value[ $key ] : false, true, false ) . '>';

						else:

						$o .= '<input type="checkbox" name="' . $this->get_the_name() . '[]" value="' . esc_attr( $key ) . '"' . checked( in_array( $key, $value ), true, false ) . '>';

						endif;

						$o .= ' ' . esc_html( $choice );

					$o .= '</label>';
				$o .= '</li>';

				endforeach;

			$o .= '</ul>';

			return $o;
		}
	}
}