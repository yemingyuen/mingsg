<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Icon Chooser Class
 *
 * This class helps creating a dropdown list for selecting icon fonts.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Icon_Chooser_Form_Field' ) ) {

	class Youxi_Icon_Chooser_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor.
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'assets' => array(), 
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

				foreach( (array) $this->get_option( 'assets' ) as $asset ) {
					$style_id = 'youxi-icon-chooser-' . sprintf( '%x', crc32( $asset ) );
					if( ! wp_style_is( $style_id, 'registered' ) ) {
						wp_register_style( $style_id, $asset );
					}
					wp_enqueue_style( $style_id );
				}

				wp_enqueue_script(
					'select2', 
					self::field_assets_url( "plugins/select2/select2{$suffix}.js" ), 
					array( 'jquery' ), 
					'3.5.2', 
					true
				);
				wp_enqueue_style(
					'select2', 
					self::field_assets_url( 'plugins/select2/select2.css' ), 
					array(), 
					'3.5.2'
				);

				wp_enqueue_script(
					'youxi-icon-chooser', 
					self::field_assets_url( "js/youxi.form.iconchooser{$suffix}.js" ), 
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
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-icon-chooser-select', $attr['class'] );
			} else {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-icon-chooser-select' );
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

			$choices = $this->icon_choices_flat();

			if( ! is_string( $data ) || ! array_key_exists( $data, $choices ) ) {
				return $this->get_option( 'std' );
			}
			return $data;
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

			$choices = $this->get_option( 'choices' );
			$choices = (array) ( is_callable( $choices )? call_user_func( $choices ) : $choices );

			$o = '<select id="' . $this->get_the_ID() . '" name="' . $this->get_the_name() . '"' . Youxi_Form::render_attr( $attributes ) . '>';
				foreach( $choices as $key => $choice ):
					if( is_array( $choice ) ):
						if( isset( $choice['icons'] ) ):
							$o .= '<optgroup label="' . ( isset( $choice['label'] ) ? $choice['label'] : $key ) . '">';
							foreach( $choice[ 'icons' ] as $icon_key => $icon ):
								$o .= $this->construct_option( $icon_key, $icon, $value );
							endforeach;
							$o .= '</optgroup>';
						endif;
					else:
						$o .= $this->construct_option( $key, $choice, $value );
					endif;
				endforeach;
			$o .= '</select>';

			return $o;
		}

		/**
		 * Return icon choices as a single dimensional array
		 *
		 * @return array The icons as a single dimensional array
		 */
		protected function icon_choices_flat() {

			$choices = $this->get_option( 'choices' );
			$choices = (array) ( is_callable( $choices ) ? call_user_func( $choices ) : $choices );

			$icons = array();
			foreach( $choices as $key => $choice ) {
				if( is_array( $choice ) ) {
					if( isset( $choice['icons'] ) ) {
						$icons = array_merge( $icons, $choice['icons'] );
					}
				} else {
					$icons[ $key ] = $choice;
				}
			}

			return $icons;
		}

		/**
		 * Construct an <option> HTML tag
		 *
		 * @param string The <option> value
		 * @param string The <option> label
		 * @param string The value used to determine the selected state
		 *
		 * @return string The <option> HTML tag
		 */
		protected function construct_option( $value, $label, $selected ) {
			return '<option value="' . esc_attr( $value ) . '" ' . selected( $value, $selected, false ) . '>' . esc_html( $label ) . '</option>';
		}
	}
}
