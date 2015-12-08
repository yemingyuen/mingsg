<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Form Class
 *
 * This class takes a set of form fields configuration and compile everything into a form.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Form' ) ) {

	class Youxi_Form {

		private $_args;

		private $_form_objects;

		private $_compiled;

		protected $form_tag;

		protected $form_attr;

		protected $form_action;

		protected $form_method;

		protected $group_tag;

		protected $group_attr;

		protected $control_tag;

		protected $control_attr;

		protected $label_attr;

		protected $field_attr;

		protected $before_fields;

		protected $after_fields;

		protected $button_row_tag;

		protected $button_row_attr;

		protected $fieldsets;

		protected $buttons;

		protected static $default_args = array(
			'form_tag' => 'form', 
			'form_attr' => array(), 
			'form_action' => '', 
			'form_method' => 'POST', 
			'group_tag' => 'div', 
			'group_attr' => array(), 
			'control_tag' => 'div', 
			'control_attr' => array(), 
			'label_attr' => array(), 
			'field_attr' => array(), 
			'before_fields' => '', 
			'after_fields' => '', 
			'button_row_tag' => 'div', 
			'button_row_attr' => array(), 
			'fieldsets' => array(), 
			'buttons' => array()
		);

		/**
		 * Constructor
		 */
		public function __construct( $fields, $args = array() ) {

			$this->parse_args( $args );
			$this->_args = $args;

			$this->process_fields( $fields );
		}

		/**
		 * Process the form fields converting them to form objects
		 * 
		 * @param array The form fields
		 */
		public function process_fields( $fields ) {

			/* Get the fieldset settings */
			$fieldsets = (array) $this->fieldsets;

			/* Get auto fieldset settings */
			if( isset( $fieldsets['auto'] ) ) {
				$auto_fieldset = $fieldsets['auto'];
				unset( $fieldsets['auto'] );
			} else {
				$auto_fieldset = self::auto_fieldset();
			}

			/* Prepend auto fieldset to the fieldset settings */
			$fieldsets = array_merge( array( 'auto' => $auto_fieldset ), $fieldsets );

			/* Cleanup fieldset settings */
			foreach( $fieldsets as $key => $fieldset ) {

				/* Remove irrelevant settings */
				foreach( (array) $fieldset as $id => $setting ) {
					if( ! in_array( $id, array( 'id', 'title', 'fields' ) ) ) {
						unset( $fieldsets[ $key ][ $id ] );
					}
				}

				/* Make sure fieldsets has the following properties defined */
				$fieldsets[ $key ] = wp_parse_args( $fieldsets[ $key ], array(
					'id' => $key . '_' . md5( uniqid( rand(), true ) ), 
					'title' => ucfirst( $key ), 
					'fields' => array()
				));
			}

			/* Loop through all the fields grouping fields into fieldsets */
			foreach( $fields as $field ) {

				if( is_a( $field, 'Youxi_Form_Field' ) ) {

					/* Get the field's fieldset name */
					$fieldset = $field->get_option( 'fieldset' );

					/* If no fieldset is defined or the fieldset doesn't exists */
					if( '' == $fieldset || ! isset( $fieldsets[ $fieldset ] ) ) {
						/* Assign it to the auto fieldset */
						$fieldset = 'auto';
					}

					/* Assign the field to the currently selected fieldset */
					$fieldsets[ $fieldset ]['fields'][] = $field;
				}
			}

			/* Cleanup empty fieldsets */
			foreach( $fieldsets as $key => $fieldset ) {
				if( empty( $fieldsets[ $key ]['fields'] ) ) {
					unset( $fieldsets[ $key ] );
				}
			}

			/* Check if there's only one single fieldset */
			if( 1 == count( $fieldsets ) ) {
				$fieldsets = reset( $fieldsets );
				$fieldsets = isset( $fieldsets['fields'] ) ? $fieldsets['fields'] : array();
			}

			/* Gotcha! */
			$this->_form_objects = array_values( $fieldsets );
		}

		/**
		 * Parse the form arguments
		 * 
		 * @param array The form arguments
		 */
		public function parse_args( $args ) {

			$args = wp_parse_args( $args, self::$default_args );
			$keys = array_keys( get_class_vars( __CLASS__ ) );

			foreach( $keys as $key ) {
				if( '_' != substr( $key, 0, 1 ) && isset( $args[ $key ] ) ) {
					$this->{$key} = $args[ $key ];
				}
			}

			// Make sure the form_attr contains the youxi-form class
			if( is_array( $this->form_attr ) ) {
				if( isset( $this->form_attr['class'] ) ) {
					$this->form_attr['class'] = self::normalize_class( 'youxi-form', $this->form_attr['class'] );
				} else {
					$this->form_attr['class'] = self::normalize_class( 'youxi-form' );
				}
			}

			// Ensure proper form method
			if( ! empty( $this->form_method ) && ! in_array( strtolower( $this->form_method ), array( 'post', 'get' ) ) ) {
				$this->form_method = 'POST';
			}
		}

		/**
		 * Function to determine whether we have fieldsets
		 */
		public function has_fieldsets() {
			return is_array( $this->_form_objects ) && 
				! empty( $this->_form_objects ) && 
				$this->is_fieldset( $this->_form_objects[0] );
		}

		/**
		 * Compile this form and cache the result
		 * 
		 * @param array The values for populating the form fields
		 */
		public function compile( $values = array(), $args = null ) {

			/* We'll compile using an external args if it's specified */
			if( is_array( $args ) ) {
				$args_modified = true;
				$this->parse_args( $args );
			}

			/* Opening form tag */

			$c = '<' . $this->form_tag;

			/* Form tag attributes */
			if( 'form' == $this->form_tag ) {

				/* Form action if not empty */
				if( ! empty( $this->form_action ) ) {
					$c .= ' action="' . esc_attr( $this->form_action ) . '"';
				}

				/* Form method */
				if( ! empty( $this->form_method ) ) {
					$c .= ' method="' . esc_attr( $this->form_method ) . '"';
				}

			}

			/* Additional form attributes */
			$c .= ' ' . self::render_attr( $this->form_attr );

			/* Opening form tag closing bracket */
			$c .=  '>';

			/* Optional before fields markup */
			$c .= $this->before_fields;

			/* Render tabs if there are fieldsets */
			if( $this->has_fieldsets() ) {

				$c .= '<div class="youxi-form-tabs">';

					$c .= $this->render_tabs_nav();

					$c .= '<div class="youxi-form-tabs-content">';

						/* Loop through each form object */
						foreach( (array) $this->_form_objects as $object ) {
							if( $this->is_fieldset( $object ) ) {
								$c .= $this->render_fieldset( $object, $values );
							}
						}
						/* End loop */

					$c .= '</div>';

				$c .= '</div>';

			} else {

				/* Loop through each form object */
				foreach( (array) $this->_form_objects as $object ) {
					if( $this->is_field( $object ) ) {
						$c .= $this->render_field( $object, $values );
					}
				}
				/* End loop */

			}

			/* Optional after fields markup */
			$c .= $this->after_fields;

			/* Button Row */
			if( ! empty( $this->buttons ) ) {

				$c .= '<' . $this->button_row_tag . self::render_attr( $this->button_row_attr ) . '>';

				foreach( $this->buttons as $button ) {

					$button = wp_parse_args( (array) $button, array( 'attr' => array(), 'text' => '' ));

					$c .= '<button' . self::render_attr( $button['attr'] ) . '>';
					$c .= esc_html( $button['text'] );
					$c .= '</button>';

				}

				$c .= '</' . $this->button_row_tag . '>';

			}

			/* Closing form tag */
			$c .= '</' . $this->form_tag . '>';

			/* Save the compiled form */
			$this->_compiled = $c;

			/* Restore the args if modified */
			if( isset( $args_modified ) && $args_modified ) {
				$this->parse_args( $this->_args );
			}

			return $this;
		}

		function render_tabs_nav() {

			$output = '<div class="youxi-form-tabs-nav">';

				$output .= '<ul>';

				foreach( $this->_form_objects as $object ) {

					if( $this->is_fieldset( $object ) ) {
						$output .= '<li><a href="#' . esc_attr( $object['id'] ) . '">';
						$output .= esc_html( $object['title'] );
						$output .= '</a></li>';
					}

				}

				$output .= '</ul>';

			$output .= '</div>';

			return $output;
		}

		/**
		 * Render a Youxi Form Field
		 * 
		 * @param Youxi Form Field
		 */
		public function render_field( $field, $values ) {

			if( ! $this->is_field( $field ) ) {
				return '';
			}
			
			/* The opening group tag */
			$output = $field->get_the_group_open( $this->group_tag, self::parse_attr( $this->group_attr, $field ) );

				/* The form label */
				$output .= $field->get_the_label( self::parse_attr( $this->label_attr, $field ) );

				/* The opening control tag */;
				$output .= $field->get_the_control_open( $this->control_tag, self::parse_attr( $this->control_attr, $field ) );

					/* Fetch the field's value */
					$name  = $field->get_option( 'name' );

					/* Parse the value */
					$value = isset( $values[ $name ] ) ? $values[ $name ] : null;
					$value = $field->parse_value( $value );

					/* Parse the attributes */
					$attr = self::parse_attr( $this->field_attr, $field );
					$attr = apply_filters( $field->get_field_filter_name(), $attr );

					/* The field */
					$output .= $field->get_the_field( $value, $attr );

					/* The description */
					$output .= $field->get_the_description();

				/* The closing control tag */
				$output .= $field->get_the_control_close();

			/* The closing group tag */
			$output .= $field->get_the_group_close();

			return $output;
		}

		/**
		 * Render a fieldset containing form fields
		 * 
		 * @param array Fieldset parameters
		 */
		public function render_fieldset( $fieldset, $values ) {

			if( ! $this->is_fieldset( $fieldset ) ) {
				return '';
			}

			extract( $fieldset, EXTR_SKIP );

			$output = '<fieldset id="' . $id . '">';

			foreach( (array) $fields as $field ) {

				if( is_a( $field, 'Youxi_Form_Field' ) ) {
					$output .= $this->render_field( $field, $values );
				}
			}

			$output .= '</fieldset>';

			return $output;
		}

		/**
		 * Enqueue the required assets for each registered field
		 *
		 * @param string The current screen hook
		 * @param array The objects to enqueue
		 */
		public function enqueue( $hook, $objects = null ) {
			if( is_null( $objects ) ) {
				$objects = $this->_form_objects;
			}

			foreach( $objects as $object ) {
				if( $this->is_field( $object ) ) {
					$object->enqueue( $hook );
				} else if( $this->is_fieldset( $object ) ) {
					$this->enqueue( $hook, $object['fields'] );
				}
			}
		}

		/**
		 * Helper function to render/get the form's HTML
		 * 
		 * @param bool Whether to output the form directly
		 */
		public function render( $echo = true ) {
			if( $echo ) {
				echo $this->_compiled;
			}

			return $this->_compiled;
		}

		/**
		 * Sanitize the data using the registered form fields
		 *
		 * @param mixed The data to sanitize
		 * @param array Objects to sanitize
		 */
		public function sanitize( $data, $objects = null ) {
			if( is_null( $objects ) ) {
				$objects = $this->_form_objects;
			}

			$sanitized = array();
			foreach( $objects as $object ) {
				if( $this->is_field( $object ) ) {
					$field_name = $object->get_option( 'name' );
					if( isset( $data[ $field_name ] ) ) {
						$sanitized[ $field_name ] = $object->sanitize( $data[ $field_name ] );
					}
				} else if( $this->is_fieldset( $object ) ) {
					$sanitized = array_merge( $sanitized, $this->sanitize( $data, $object['fields'] ) );
				}
			}

			return $sanitized;
		}

		/**
		 * Validates a Youxi Form Field object
		 *
		 * @param mixed Object to validate
		 */
		public function is_field( $object ) {
			return is_a( $object, 'Youxi_Form_Field' );
		}

		/**
		 * Validates a fieldset
		 *
		 * @param mixed Object to validate
		 */
		public function is_fieldset( $object ) {
			return is_array( $object ) && isset( $object['id'], $object['title'], $object['fields'] );
		}

		/**
		 * Parse attributes using a form field for conditional checking
		 * 
		 * @param array The HTML attributes in array
		 * @param Youxi_Form_Field The form field object to use for 
		 *
		 * @return string The HTML attributes compiled into a string
		 */
		public static function parse_attr( $attributes, $field ) {

			if( ! is_array( $attributes ) || ! is_a( $field, 'Youxi_Form_Field' ) )
				return $attributes;

			/* Create an empty array for keeping the parsed attributes */
			$parsed = array();

			/* Loop through each attributes */
			foreach( $attributes as $attribute_name => $attribute_value ) {

				/* Initialize an empty array for the attribute name */
				$parsed[ $attribute_name ] = array();

				if( is_string( $attribute_value ) ) {

					/* If the value is a string, just directly add it to the array */
					$parsed[ $attribute_name ][] = $attribute_value;

				} elseif( is_array( $attribute_value ) ) {

					/* If the value is an array, loop through it */
					foreach( $attribute_value as $index_or_val => $val_or_condition ) {

						/* If the value is a string, just directly add it to the array */
						if( is_string( $val_or_condition ) ) {

							$parsed[ $attribute_name ][] = $val_or_condition;

						} elseif( is_array( $val_or_condition ) ) {

							/* If it's an array of conditions */
							foreach( $val_or_condition as $option => $condition ) {

								/* Make sure the condition is an array */
								$condition = (array) $condition;

								/* If the related field's option value fulfills the condition, this attribute pass the test */
								if( in_array( $field->get_option( $option ), $condition ) ) {

									$parsed[ $attribute_name ][] = $index_or_val;
									break;

								}

							}

						}

					}

				}

			}

			return $parsed;
		}

		/**
		 * Add a HTML class to an existing HTML class string
		 * Uses the logic from jQuery.fn.addClass() 
		 * 
		 * @param string The class names to add
		 * @param mixed An array or string containing class names
		 *
		 * @return string The normalized HTML class string
		 */
		public static function normalize_class( $class, $cur = '' ) {

			if( is_string( $class ) && $class ) {

				if( is_array( $cur ) ) {
					$cur = array_filter( $cur, 'is_scalar' );
					$cur = join( ' ', $cur );
				} else if( ! is_scalar( $cur ) ) {
					$cur = '';
				}

				$cur = ' ' . preg_replace( '/[\t\r\n\f]/', ' ', (string) $cur ) . ' ';

				if( preg_match_all( '/\S+/', $class, $matches, PREG_SET_ORDER ) ) {

					foreach( $matches as $match ) {
						if( false === strpos( $cur, ' ' . $match[0] . ' ' ) ) {
							$cur .= $match[0] . ' ';
						}
					}

				}

			}

			return is_string( $cur ) ? trim( $cur ) : '';
		}

		/**
		 * Return a fallback ID and title for auto fieldsets
		 *
		 * @return array The ID and title for auto fieldsets
		 */
		public static function auto_fieldset() {
			return apply_filters( 'youxi_form_auto_fieldset', array(
				'id' => 'general', 
				'title' => __( 'General', 'youxi' )
			));
		}

		/**
		 * Join and return HTML attributes from an array
		 * 
		 * @param array The HTML attributes in array
		 * @param bool Whether to echo the attributes
		 * @param bool Whether to render as HTML5 attributes
		 *
		 * @return string The HTML attributes compiled into a string
		 */
		public static function render_attr( $attributes, $echo = false, $html5 = true ) {

			/* Taken from Yii PHP Framework */
			static $special_attr = array(
				'async', 
				'autofocus', 
				'autoplay', 
				'checked', 
				'controls', 
				'declare', 
				'default', 
				'defer', 
				'disabled', 
				'formnovalidate', 
				'hidden', 
				'ismap', 
				'loop', 
				'multiple', 
				'muted', 
				'nohref', 
				'noresize', 
				'novalidate', 
				'open', 
				'readonly', 
				'required', 
				'reversed', 
				'scoped', 
				'seamless', 
				'selected', 
				'typemustmatch'
			);

			$html = '';

			/* Loop through each attribute */
			foreach( $attributes as $name => $value ) {

				$value = join( ' ', (array) $value );

				if( in_array( $name, $special_attr ) ) {
					
					if( $value ) {
						$html .= " {$name}";
						if( ! $html5 ) {
							$html .= "=\"{$name}\"";
						}
					}
				} elseif( ! is_null( $value ) ) {
					$html .= " {$name}=\"" . esc_attr( trim( $value ) ) . "\"";
				}
			}

			if( $echo ) {
				echo $html;
			}

			return $html;
		}
	}
}