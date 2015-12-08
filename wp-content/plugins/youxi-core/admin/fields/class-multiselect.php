<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi MultiSelect Class
 *
 * This class renders a drag and drop dropdown list.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Multiselect_Form_Field' ) ) {

	class Youxi_Multiselect_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor.
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'choices' => array()
			));

			parent::__construct( $scope, $options, $allowed_hooks );
		}

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script(
					'jquery-multiselect', 
					self::field_assets_url( "plugins/multiselect/jquery.bsmselect{$suffix}.js" ), 
					array( 'jquery', 'jquery-ui-sortable' ), 
					'1.4.6', 
					true
				);
				wp_enqueue_style(
					'jquery-multiselect', 
					self::field_assets_url( 'plugins/multiselect/jquery.bsmselect.css' ), 
					array(), 
					'1.4.6'
				);
				wp_enqueue_script(
					'youxi-multiselect', 
					self::field_assets_url( "js/youxi.form.multiselect{$suffix}.js" ), 
					array( 'youxi-form-manager', 'jquery-multiselect' ), 
					YOUXI_CORE_VERSION, 
					true
				);
			}
		}

		/**
		 * Apply form item attributes filtering
		 * 
		 * @param array The current attributes of the field
		 *
		 * @return array The filtered attributes of the field
		 */
		public function filter_field_attr( $attr ) {
			$attr['multiple'] = true;

			if( isset( $attr['class'] ) ) {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-multiselect', $attr['class'] );
			} else {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-multiselect' );
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
			return (array) $value;
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

			// Parse the values to keep ordering
			$choices  = (array) $this->get_option( 'choices' );
			$selected = array();
			foreach( $value as $val ) {
				if( array_key_exists( $val, $choices ) ) {
					$selected[ $val ] = $choices[ $val ];
				}
			}
			$unselected = array_diff( $choices, $selected );

			$o = '<select id="' . $this->get_the_ID() . '" name="' . $this->get_the_name() . '[]"' . Youxi_Form::render_attr( $attributes ) . '>';

				foreach( (array) $unselected as $key => $choice ):
				$o .= '<option value="' . esc_attr( $key ) . '">';
					$o .= esc_html( $choice );
				$o .= '</option>';
				endforeach;

				foreach( (array) $selected as $key => $choice ):
				$o .= '<option value="' . esc_attr( $key ) . '" selected>';
					$o .= esc_html( $choice );
				$o .= '</option>';
				endforeach;

			$o .= '</select>';

			return $o;
		}
	}
}