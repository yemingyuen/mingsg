<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Code Class
 *
 * This class renders a code editor form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Code_Form_Field' ) ) {

	class Youxi_Code_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'mode' => 'css'
			));

			if( isset( $options['mode'] ) ) {
				if( ! in_array( $options['mode'], array( 'css', 'javascript' ) ) ) {
					unset( $options['mode'] );
				}
			}

			parent::__construct( $scope, $options, $allowed_hooks );
		}

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_register_script(
					'codemirror', 
					self::field_assets_url( "plugins/codemirror/codemirror{$suffix}.js" ), 
					array(), '5.0.1', true
				);
				wp_register_script(
					'codemirror-javascript', 
					self::field_assets_url( "plugins/codemirror/mode/javascript{$suffix}.js" ), 
					array( 'codemirror' ), '5.0.1', true
				);
				wp_register_script(
					'codemirror-css', 
					self::field_assets_url( "plugins/codemirror/mode/css{$suffix}.js" ), 
					array( 'codemirror' ), '5.0.1', true
				);

				wp_enqueue_style(
					'codemirror', 
					self::field_assets_url( 'plugins/codemirror/codemirror.css' ), 
					array(), '5.0.1'
				);

				wp_enqueue_script(
					'youxi-code-editor', 
					self::field_assets_url( "js/youxi.form.code{$suffix}.js" ), 
					array( 'youxi-form-manager', 'codemirror-' . $this->get_option( 'mode' ) ), 
					YOUXI_CORE_VERSION, true
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
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-code-editor-textarea', $attr['class'] );
			} else {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-code-editor-textarea' );
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

			$attributes['data-editor-mode'] = esc_attr( $this->get_option( 'mode' ) );

			return '<textarea id="' . $this->get_the_ID() . '" name="' . $this->get_the_name() . '"' . Youxi_Form::render_attr( $attributes ) . '>' . esc_textarea( $value ) . '</textarea>';
		}
	}
}