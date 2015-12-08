<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Togglable Fieldsets Class
 *
 * This class creates a group of togglable fieldsets
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Togglable_Fieldsets_Form_Field' ) ) {

	class Youxi_Togglable_Fieldsets_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'fieldsets' => array()
			));

			parent::__construct( $scope, $options, $allowed_hooks );

			/* Construct the template fieldsets */
			$fieldsets = (array) $this->get_option( 'fieldsets' );

			foreach( $fieldsets as $id => $fieldset ) {
				if( ! isset( $fieldset['fields'] ) ) {
					unset( $fieldsets[ $id ] );
					continue;
				}

				foreach( (array) $fieldset['fields'] as $name => $atts ) {

					if( isset( $atts['type'] ) ) {
						
						$options = array_merge( compact( 'name' ), (array) $atts );
						$field = Youxi_Form_Field::factory( "$this->scope[$this->field_name][$id]", $options );

						if( is_a( $field, 'Youxi_Form_Field' ) ) {
							$fieldsets[ $id ]['fields'][ $name ] = $field;
						}
					}
				}
			}

			$this->set_option( 'fieldsets', $fieldsets );
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
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-togglable-fieldsets', $attr['class'] );
			} else {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-togglable-fieldsets' );
			}

			return parent::filter_field_attr( $attr );
		}

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script(
					'youxi-togglable-fieldsets', 
					self::field_assets_url( "js/youxi.form.togglable-fieldsets{$suffix}.js" ), 
					array( 'youxi-form-manager' ), 
					YOUXI_CORE_VERSION, 
					true
				);

				wp_enqueue_style(
					'youxi-togglable-fieldsets', 
					self::field_assets_url( "css/youxi.form.togglable-fieldsets{$suffix}.css" ), 
					array( 'youxi-form' ), 
					YOUXI_CORE_VERSION
				);

				wp_enqueue_script(
					'switchery', 
					self::field_assets_url( "plugins/switchery/switchery{$suffix}.js" ), 
					array( 'jquery' ), 
					'0.6.3', 
					true
				);
				wp_enqueue_style(
					'switchery', 
					self::field_assets_url( "plugins/switchery/switchery{$suffix}.css" ), 
					array(), 
					'0.6.3'
				);

				foreach( (array) $this->get_option( 'fieldsets' ) as $fieldset ) {
					if( ! isset( $fieldset['fields'] ) ) {
						continue;
					}
					foreach( $fieldset['fields'] as $name => $field ) {
						if( is_a( $field, 'Youxi_Form_Field' ) ) {
							$field->enqueue( $hook );
						}
					}
				}
			}
		}

		/**
		 * Get the form to use inside the fieldsets
		 *
		 * @param array The field instances
		 *
		 * @return Youxi_Form
		 */
		public function get_form( $fields ) {
			return new Youxi_Form( $fields, array(
				'form_tag' => 'fieldset', 
				'form_attr' => array(
					'class' => 'youxi-togglable-fieldset-content'
				), 
				'group_attr' => array(
					'class' => 'youxi-form-row youxi-form-inline'
				), 
				'control_attr' => array(
					'class' => 'youxi-form-item'
				), 
				'label_attr' => array(
					'class' => 'youxi-form-label'
				), 
				'field_attr' => array(
					'class' => array(
						'youxi-form-large' => array(
							'type' => array( 'text', 'textarea', 'url', 'select', 'iconchooser' )
						)
					)
				)
			));
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
			$fieldsets = $this->get_option( 'fieldsets' );

			if( ! is_array( $fieldsets ) || empty( $fieldsets ) )
				return '';

			$t = '<div id="' . $this->get_the_ID() . '"' . Youxi_Form::render_attr( $attributes ) . '>';

				foreach( $fieldsets as $id => $fieldset ):

					$t .= '<div class="youxi-togglable-fieldset">';

						$t .= '<div class="youxi-togglable-fieldset-header">';

							$t .= '<label class="youxi-form-label">';

								$t .= esc_html( $fieldset['title'] );

							$t .= '</label>';

							$t .= '<div class="youxi-togglable-fieldset-toggle">';

								$t .= '<input type="checkbox" ' . checked( isset( $value[ $id ] ), true, false ) . '>';

							$t .= '</div>';

						$t .= '</div>';

						$t .= $this->get_form( $fieldset['fields'] )
							->compile( isset( $value[ $id ] ) ? $value[ $id ] : array() )
							->render( false );

					$t .= '</div>';

				endforeach;

			$t .= '</div>';
			
			return $t;
		}
	}
}