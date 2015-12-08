<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi jQuery-UI Slider Class
 *
 * This class renders a jQuery-UI Slider control.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_JUISlider_Form_Field' ) ) 
{
	class Youxi_JUISlider_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor.
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'widgetopts' => array()
			));

			parent::__construct( $scope, $options, $allowed_hooks );
		}

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_style(
					'youxi-jui-slider', 
					self::field_assets_url( "css/youxi.form.jui-slider{$suffix}.css" ), 
					array(), 
					YOUXI_CORE_VERSION
				);
				wp_enqueue_script(
					'youxi-form-jui-slider', 
					self::field_assets_url( "js/youxi.form.jui-slider{$suffix}.js" ), 
					array( 'youxi-form-manager', 'jquery-ui-slider' ), 
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
			if( isset( $attr['class'] ) ) {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-ui-slider', $attr['class'] );
			} else {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-ui-slider' );
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
		public function sanitize( $data ) {
			$widgetopts = $this->get_option( 'widgetopts' );
			$max = isset( $widgetopts['max'] ) ? $widgetopts['max'] : 100;
			$min = isset( $widgetopts['min'] ) ? $widgetopts['min'] : 0;

			return min( max( $data, $min ), $max );
		}

		/**
		 * Generate jQuery-UI widget options as HTML data-* attributes
		 *
		 * @return string The widget options compiled into HTML5 data-* attributes
		 */
		public function get_widget_options_html() {

			static $valid_options = array(
				'animate', 'disabled', 'max', 
				'min', 'orientation', 'range', 
				'step', 'value', 'values'
			);

			$options = array_intersect_key( $this->get_option( 'widgetopts' ), array_flip( $valid_options ) );

			/* Make sure it's a horizontal slider */
			if( isset( $options['orientation'] ) ) {
				$options['orientation'] = 'horizontal';
			}

			$output = '';
			foreach( $options as $key => $option ) {
				if( false === $option || is_null( $option ) ) {
					continue;
				} elseif( is_bool( $option ) && $option ) {
					$output .= ' data-' . trim( "{$key}" );
				} else {
					$output .= " data-{$key}=\"" . esc_attr( trim( $option ) ) . '"';
				}
			}

			return $output;
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

			$o = '<div' . Youxi_Form::render_attr( $attributes ) . '>';
				$o .= '<input id="' . $this->get_the_ID() . '" name="' . $this->get_the_name() . '" type="text" class="youxi-slider-input" value="' . esc_attr( $value ) . '" readonly>';
				$o .= '<div class="youxi-slider-widget-wrap">';
					$o .= '<div class="youxi-slider-widget"' . $this->get_widget_options_html() . '></div>';
				$o .= '</div>';
			$o .= '</div>';

			return $o;
		}
	}
}
