<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Colorpicker Class
 *
 * This class renders a color picker control
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Colorpicker_Form_Field' ) ) 
{
	class Youxi_Colorpicker_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor.
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'std' => '#ffffff', 
				'palette' => true, 
				'hide' => true
			));

			parent::__construct( $scope, $options, $allowed_hooks );
		}

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-color-picker' );

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script( 
					'youxi-wp-color-picker', 
					self::field_assets_url( "js/youxi.form.colorpicker{$suffix}.js" ), 
					array( 'wp-color-picker' ), 
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
			$attr['maxlength'] = 7;
			$attr['data-hide'] = $this->get_option( 'hide' );
			$attr['data-palette'] = $this->get_option( 'palette' );
			$attr['data-default-color'] = $this->get_option( 'std' );

			if( isset( $attr['class'] ) ) {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-wp-color-picker', $attr['class'] );
			} else {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-wp-color-picker' );
			}

			return parent::filter_field_attr( $attr );
		}

		/**
		 * Sanitize the user submitted data
		 * 
		 * @param mixed The data to sanitize
		 *
		 * @return mixed The sanitized data
		 */
		public function sanitize( $color ) {
			if ( '' === $color )
				return '';

			// 3 or 6 hex digits, or the empty string.
			if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) )
				return $color;

			return null;
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
