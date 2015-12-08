<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Repeater Class
 *
 * This class creates a nestable repeater field that can contain unlimited number of fields.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Repeater_Form_Field' ) ) {

	class Youxi_Repeater_Form_Field extends Youxi_Form_Field {

		/**
		 * @access protected
		 * @var string
		 */
		protected $index = 0;

		/**
		 * Constructor
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'max' => 0, 
				'min' => 1, 
				'std' => array(), 
				'fieldsets' => array(), 
				'fields' => array(), 
				'depth' => 0, 
				'add_text' => __( 'Add', 'youxi' ), 
				'edit_text' => __( 'Edit', 'youxi' ), 
				'delete_text' => __( 'Delete', 'youxi' ), 
				'preview_template' => false, 
				'confirm_delete' => __( 'Are you sure you want to remove this item?', 'youxi' )
			));

			parent::__construct( $scope, $options, $allowed_hooks );

			/* Construct the template fields */
			$fields = (array) $this->get_option( 'fields' );

			$depth = $this->get_option( 'depth' );
			$scope = "{$this->scope}[{$this->field_name}][{{ data.index_{$depth} }}]";

			$the_fields = array();
			foreach( $fields as $name => $atts ) {

				if( isset( $atts['type'] ) ) {
					
					$options = array_merge( compact( 'name' ), (array) $atts );

					/* Check for nested repeaters */
					if( 'repeater' == $options['type'] ) {
						$options['depth'] = $depth + 1;
					}

					/* Prevent a TinyMCE field added to the repeater */
					if( 'richtext' == $options['type'] ) {
						$options['type'] = 'textarea';
						unset( $options['tinymce'] );
					}
					
					$field = Youxi_Form_Field::factory( $scope, $options );
					
					if( is_a( $field, 'Youxi_Form_Field' ) ) {
						$the_fields[ $name ] = $field;
					}
				}
			}

			$this->set_option( 'fields', $the_fields );
		}

		/**
		 * Get a repeater row HTML output
		 *
		 * @param array The set of fields to render in the repeater
		 * @param array The values for the fields
		 */
		protected function get_template( $values = null, $template = true ) {
			$form = new Youxi_Form( $this->get_option( 'fields' ), array(
				'form_tag' => 'div', 
				'form_attr' => array(
					'class' => 'youxi-repeater-row-content'
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
				), 
				'fieldsets' => $this->get_option( 'fieldsets' )
			));

			$t = '<div class="youxi-repeater-row">';
				$t .= '<div class="youxi-repeater-row-header">';
					$t .= '<div class="youxi-repeater-row-title"></div>';
					$t .= '<div class="youxi-repeater-row-controls">';
						$t .= '<button type="button" class="button button-large" data-action="edit">';
							$t .= '<i class="dashicons dashicons-edit"></i>';
							$t .= esc_attr( $this->get_option( 'edit_text' ) );
						$t .= '</button>';
						$t .= '<button type="button" class="button button-large" data-action="remove">';
							$t .= '<i class="dashicons dashicons-trash"></i>';
							$t .= esc_attr( $this->get_option( 'delete_text' ) );
						$t .= '</button>';
					$t .= '</div>';
				$t .= '</div>';
				$t .= $form->compile( $values )->render( false );
			$t .= '</div>';

			$depth = $this->get_option( 'depth' );
			return $template ? $t : str_replace( "{{ data.index_{$depth} }}", $this->index, $t );
		}

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script(
					'youxi-repeater', 
					self::field_assets_url( "js/youxi.form.repeater{$suffix}.js" ), 
					array( 'youxi-form-manager', 'jquery-ui-widget', 'media-models' ), 
					YOUXI_CORE_VERSION, 
					true
				);
				wp_enqueue_style(
					'youxi-repeater', 
					self::field_assets_url( "css/youxi.form.repeater{$suffix}.css" ), 
					array( 'youxi-form' ), 
					YOUXI_CORE_VERSION
				);

				wp_enqueue_style( 'dashicons' );

				foreach( (array) $this->get_option( 'fields' ) as $field ) {
					if( is_a( $field, 'Youxi_Form_Field' ) ) {
						$field->enqueue( $hook );
					}
				}
			}
		}

		/**
		 * Parse a field's value, by default it loads the default if value is empty
		 * 
		 * @param mixed The value to parse
		 *
		 * @return mixed The parsed value
		 */
		public function parse_value( $value ) {
			$maximum_rows = $this->get_option( 'max' );
			return array_slice( (array) ( $value ? $value :  NULL ), 0, $maximum_rows ? $maximum_rows : NULL );
		}

		/**
		 * Sanitize the user submitted data
		 * 
		 * @param mixed The data to sanitize
		 *
		 * @return mixed The sanitized data
		 */
		public function sanitize( $data ) {
			return array_map( array( $this, 'sanitize_helper' ), (array) $data );
		}

		/**
		 * Sanitize helper thats checks and let the repeater field instances sanitize the data.
		 * 
		 * @param mixed The data to sanitize
		 *
		 * @return mixed The sanitized data
		 */
		public function sanitize_helper( $data ) {
			foreach( (array) $this->get_option( 'fields' ) as $field ) {
				$field_name = $field->get_option( 'name' );
				if( isset( $data[ $field_name ] ) ) {
					$data[ $field_name ] = $field->sanitize( $data[ $field_name ] );
				}
			}

			return $data;
		}

		/**
		 * Get the templates including all nested repeater templates recursively
		 * 
		 * @return string The templates
		 */
		public function get_templates() {
			$t = '<script type="text/html" id="tmpl-' . $this->get_template_id() . '">' . $this->get_template() . '</script>';
			if( $this->get_option( 'preview_template' ) ) {
				$t .= '<script type="text/html" id="tmpl-' . $this->get_template_id() . '-preview">' . $this->get_option( 'preview_template' ) . '</script>';
			}
			foreach( (array) $this->get_option( 'fields' ) as $field ) {
				if( is_a( $field, get_class() ) ) {
					$t .= $field->get_templates();
				}
			}
			return $t;
		}

		/**
		 * Get the sanitized template id for use in nested repeaters
		 * 
		 * @return string The template id
		 */
		public function get_template_id() {
			return preg_replace( '/_?\{\{\s*data.index_\d+\s*\}\}_?/', '_', $this->get_the_ID() );
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
			$fields = $this->get_option( 'fields' );

			if( ! is_array( $fields ) || empty( $fields ) )
				return '';

			$minimum_rows = $this->get_option( 'min' );
			$maximum_rows = $this->get_option( 'max' );

			$attributes = array_merge( $attributes, array(
				'class'                 => esc_attr( 'youxi-repeater' ), 
				'data-max'              => esc_attr( $maximum_rows ), 
				'data-min'              => esc_attr( $minimum_rows ), 
				'data-depth'            => esc_attr( $this->get_option( 'depth' ) ), 
				'data-template-id'      => esc_attr( $this->get_template_id() ), 
				'data-confirm-remove'   => esc_attr( $this->get_option( 'confirm_delete' ) )
			));

			$t = '<div id="' . $this->get_the_ID() . '"' . Youxi_Form::render_attr( $attributes ) . '>';
				if( ! $this->get_option( 'depth' ) ):
				$t .= $this->get_templates();
				endif;
				$t .= '<fieldset>';
					$t .= '<div class="youxi-repeater-fields">';
						for( $this->index = 0, $count = count( $value ); $this->index < $count; $this->index++ ):
							$t .= $this->get_template( $value[ $this->index ], false );
						endfor; 
						for( ; $this->index < $minimum_rows; $this->index++ ):
							$t .= $this->get_template( null, false );
						endfor;
					$t .= '</div>';
					$t .= '<div class="youxi-repeater-controls">';
						$t .= '<button type="button" class="button button-large button-primary" data-action="add">';
							$t .= esc_attr( '+ ' . $this->get_option( 'add_text' ) );
						$t .= '</button>';
					$t .= '</div>';
				$t .= '</fieldset>';
			$t .= '</div>';
			$t .= '<div style="clear: both;"></div>';
			
			return $t;
		}
	}
}
