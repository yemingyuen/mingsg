<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Switch Class
 *
 * This class renders an ios7 switch.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Switch_Form_Field' ) ) {

	class Youxi_Switch_Form_Field extends Youxi_Form_Field {

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script(
					'switchery', 
					self::field_assets_url( "plugins/switchery/switchery{$suffix}.js" ), 
					array( 'jquery' ), 
					'0.7.0', 
					true
				);
				wp_enqueue_style(
					'switchery', 
					self::field_assets_url( "plugins/switchery/switchery{$suffix}.css" ), 
					array(), 
					'0.7.0'
				);

				wp_enqueue_script(
					'youxi-switch', 
					self::field_assets_url( "js/youxi.form.switch{$suffix}.js" ), 
					array( 'youxi-form-manager' ), 
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
				$attr['class'] = Youxi_Form::normalize_class( 'js-switch', $attr['class'] );
			} else {
				$attr['class'] = Youxi_Form::normalize_class( 'js-switch' );
			}

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
			$o = '<input type="hidden" name="' . $this->get_the_name() . '" value="0">';
			$o .= '<input id="' . $this->get_the_ID() . '" name="' . $this->get_the_name() . '" type="checkbox" value="1"' . Youxi_Form::render_attr( $attributes ) . checked( $value, 1, false ) . '>';

			return $o;
		}
	}
}