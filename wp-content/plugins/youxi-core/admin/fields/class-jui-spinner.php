<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi jQuery-UI Spinner Class
 *
 * This class renders a jQuery-UI Spinner control.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_JUISpinner_Form_Field' ) ) 
{
	class Youxi_JUISpinner_Form_Field extends Youxi_Form_Field {

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

				// wp_enqueue_style(
				// 	'youxi-jui-spinner', 
				// 	self::field_assets_url( "css/youxi.form.jui-spinner{$suffix}.css" ), 
				// 	array(), 
				// 	YOUXI_CORE_VERSION
				// );
				wp_enqueue_script(
					'jquery-mousewheel', 
					self::field_assets_url( "plugins/mousewheel/jquery.mousewheel{$suffix}.js" ), 
					array( 'jquery' ), 
					'3.1.12', 
					true
				);
				wp_enqueue_script(
					'youxi-form-jui-spinner', 
					self::field_assets_url( "js/youxi.form.jui-spinner{$suffix}.js" ), 
					array( 'youxi-form-manager', 'jquery-ui-spinner', 'jquery-mousewheel' ), 
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
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-ui-spinner', $attr['class'] );
			} else {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-ui-spinner' );
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

			if( isset( $widgetopts['max'] ) ) {
				$data = max( $data, intval( $widgetopts['max'] ) );
			}
			if( isset( $widgetopts['min'] ) ) {
				$data = min( $data, intval( $widgetopts['min'] ) );
			}
			
			return $data;
		}

		/**
		 * Generate jQuery-UI widget options as HTML data-* attributes
		 *
		 * @return string The widget options compiled into HTML5 data-* attributes
		 */
		public function get_widget_options_html() {

			static $valid_options = array(
				'culture', 'disabled', 'incremental', 
				'max', 'min', 'numberFormat', 
				'step', 'page'
			);

			$options = array_intersect_key( $this->get_option( 'widgetopts' ), array_flip( $valid_options ) );

			/* Make sure the number format is 'n' */
			if( isset( $options['numberFormat'] ) ) {
				$options['numberFormat'] = 'n';
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
				$o .= '<input id="' . $this->get_the_ID() . '" name="' . $this->get_the_name() . '" type="text" class="youxi-form-large youxi-spinner-input" value="' . esc_attr( $value ) . '" ' . $this->get_widget_options_html() . '>';
			$o .= '</div>';

			return $o;
		}
	}
}
