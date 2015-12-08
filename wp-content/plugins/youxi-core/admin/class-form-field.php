<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Form Field Class
 *
 * This class is the base class for all form fields.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Form_Field' ) ) {

	abstract class Youxi_Form_Field {

		/**
		 * The group tag name of the field
		 *
		 * @access private
		 * @var string
		 */
		private $group_tag;

		/**
		 * The control tag name of the field
		 *
		 * @access private
		 * @var string
		 */
		private $control_tag;

		/**
		 * The options of the field
		 *
		 * @access private
		 * @var array
		 */
		private $options;

		/**
		 * Makes sure the field type isn't altered
		 *
		 * @access protected
		 * @var string
		 */
		protected $field_type;

		/**
		 * Makes sure the field name isn't altered
		 *
		 * @access protected
		 * @var string
		 */
		protected $field_name;

		/**
		 * The fieldset name of the field
		 *
		 * @access protected
		 * @var string
		 */
		protected $fieldset;

		/**
		 * The scope name of this field
		 *
		 * @access protected
		 * @var string
		 */
		protected $scope;

		/**
		 * The unique id of this field
		 *
		 * @access protected
		 * @var string
		 */
		protected $field_uid;

		/**
		 * Keeps the number of fields that have been instantiated
		 *
		 * @access protected
		 * @var int
		 */
		protected static $counter = 0;

		/**
		 * @access protected
		 * @var array
		 */
		protected $default_options = array(
			'type' => 'text', 
			'name' => '', 
			'fieldset' => '', 
			'label' => '', 
			'description' => '', 
			'std' => '', 
			'mode' => 'display', 
			'criteria' => false
		);

		/**
		 * Constructor
		 */
		public function __construct( $scope, $options ) {
			$this->scope   = $scope;
			$this->options = wp_parse_args( $options, $this->default_options );

			/* Prevent changing the name and type */
			$this->field_name = $options['name'];
			$this->field_type = $options['type'];

			/* Remove the name and type from the options array */
			unset( $this->options['type'] );
			unset( $this->options['name'] );

			/* Continue if this field's mode is not enqueue */
			if( 'enqueue' !== $this->options['mode'] ) {
				$this->field_uid = self::$counter++;

				add_filter( $this->get_field_filter_name(), array( $this, 'ensure_field_attr' ), 1 );
				add_filter( $this->get_field_filter_name(), array( $this, 'filter_field_attr' ) );
			}
		}

		/**
		 * Generate a field group opening tag
		 */
		public function get_the_group_open( $tag = 'div', $attributes = array() ) {
			
			$this->group_tag = $tag;

			$attributes['id'] = $this->get_the_ID() . '_group';
			$attributes['data-field-scope'] = $this->get_the_scope();

			if( $this->get_option( 'criteria' ) ) {
				$criteria = $this->get_option( 'criteria' );
				if( is_string( $criteria ) ) {
					$criteria = array( 'condition' => (array) $criteria );
				}
				$criteria = wp_parse_args( $criteria, array(
					'condition' => array(), 
					'operator' => 'and'
				));
				$attributes['data-criteria'] = join( ',', (array) $criteria['condition'] );
				$attributes['data-criteria-operator'] = strtolower( $criteria['operator'] );
			}

			return '<' . $this->group_tag . Youxi_Form::render_attr( $attributes ) . '>';
		}

		/**
		 * Helper method to render the field group opening tag
		 */
		public function the_group_open( $tag = 'div', $attributes = array() ) {
			echo $this->get_the_group_open( $tag, $attributes );
		}

		/**
		 * Generate a field group closing tag
		 */
		public function get_the_group_close() {
			return '</' . $this->group_tag . '>';
		}

		/**
		 * Helper method to render the field group closing tag
		 */
		public function the_group_close() {
			echo $this->get_the_group_close();
		}

		/**
		 * Generate a field control opening tag
		 */
		public function get_the_control_open( $tag = 'div', $attributes = array() ) {

			$this->control_tag = $tag;
			
			return '<' . $this->control_tag . Youxi_Form::render_attr( $attributes ) . '>';
		}

		/**
		 * Helper method to render the field control opening tag
		 */
		public function the_control_open( $tag = 'div', $attributes = array() ) {
			echo $this->get_the_control_open( $tag, $attributes );
		}

		/**
		 * Generate a field control closing tag
		 */
		public function get_the_control_close() {
			return '</' . $this->control_tag . '>';
		}

		/**
		 * Helper method to render the control item closing tag
		 */
		public function the_control_close() {
			echo $this->get_the_control_close();
		}

		/**
		 * Helper method to render the field's label
		 */
		public function the_label( $attributes = array() ) {
			echo $this->get_the_label( $attributes );
		}

		/**
		 * Get the field's label
		 *
		 * @return string The label's HTML markup
		 */
		public function get_the_label( $attributes = array() ) {
			return '<label for="' . $this->get_the_ID() . '"' . Youxi_Form::render_attr( $attributes ) . '>' . $this->get_option( 'label' ) . '</label>';
		}
		
		/**
		 * Helper method to render the control's HTML markup
		 */
		public function the_field( $value, $attributes = array() ) {
			echo $this->get_the_field( $value, $attributes );
		}

		/**
		 * Abstract method to get the field's HTML markup
		 *
		 * @param mixed The field's current value (if it exists)
		 * @param array The HTML attributes to be added on the field
		 *
		 * @return string The field's HTML markup
		 */
		abstract public function get_the_field( $value, $attributes = array() );

		/**
		 * Helper method to output the description's HTML markup
		 */
		public function the_description() {
			echo $this->get_the_description();
		}

		/**
		 * Get the actual HTML description
		 *
		 * @return string The field's description
		 */
		public function get_the_description() {
			return $this->get_option( 'description' ) ? '<p class="description">' . $this->get_option( 'description' ) . '</p>' : '';
		}

		/**
		 * Helper method to output the field name
		 */
		public function the_name() {
			echo $this->get_the_name();
		}

		/**
		 * Get the actual field HTML name attribute
		 * 
		 * @return string The name attribute of the field
		 */
		public function get_the_name() {
			return esc_attr( apply_filters( $this->get_name_filter_name(), self::generate_name( $this->scope, $this->field_name ) ) );
		}

		/**
		 * Helper method to output the field ID
		 */
		public function the_ID() {
			echo $this->get_the_ID();
		}

		/**
		 * Get the actual field HTML id attribute
		 *
		 * @return string The ID attribute of the field
		 */
		public function get_the_ID() {
			return esc_attr( apply_filters( $this->get_ID_filter_name(), self::generate_ID( $this->scope, $this->field_name ) ) );
		}

		/**
		 * Get the field's scope name
		 *
		 * @return string The scope name of the field
		 */
		public function get_the_scope() {
			return esc_attr( apply_filters( $this->get_scope_filter_name(), self::generate_ID( $this->scope, '' ) ) );
		}

		/**
		 * Enqueue form field required assets
		 *
		 * @param string The current hooks page name
		 */
		public function enqueue( $hook ) {

			/* For now, we'll restrict form fields only to post.php or post-new.php */
			if( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
			
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script(
					'youxi-form-manager', 
					self::field_assets_url( "js/youxi.form.manager{$suffix}.js" ), 
					array( 'jquery', 'underscore' ), 
					YOUXI_CORE_VERSION, 
					true
				);

				if( ! wp_style_is( 'font-awesome', 'registered' ) ) {
					wp_register_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), '4.3', 'screen' );
				}

				wp_enqueue_style(
					'youxi-form', 
					self::field_assets_url( "css/youxi.form{$suffix}.css" ), 
					array(), 
					YOUXI_CORE_VERSION, 
					'screen'
				);

				wp_enqueue_style( 'font-awesome' );

				return true;
			}
		}

		/**
		 * Helper function to get the current field's ID filter name
		 * 
		 * @return string The field's ID filter name
		 */
		public function get_ID_filter_name() {
			return "youxi_form_field_ID_" . $this->get_field_unique() . "_attr";
		}

		/**
		 * Helper function to get the current field's name filter name
		 * 
		 * @return string The field's name filter name
		 */
		public function get_name_filter_name() {
			return "youxi_form_field_name_" . $this->get_field_unique() . "_attr";
		}

		/**
		 * Helper function to get the current field's field filter name
		 * 
		 * @return string The field's field filter name
		 */
		public function get_field_filter_name() {
			return "youxi_form_field_" . $this->get_field_unique() . "_attr";
		}

		/**
		 * Helper function to get the current field's scope filter name
		 * 
		 * @return string The field's scope filter name
		 */
		public function get_scope_filter_name() {
			return "youxi_form_field_scope" . $this->get_field_unique() . "_attr";	
		}

		/**
		 * Get field unique id consisting of the scope, name and type
		 * 
		 * @return string The unique ID of this field
		 */
		public function get_field_unique() {
			return sprintf( '%s_%d', $this->get_option( 'type' ), $this->field_uid );
		}

		/**
		 * Helper method to ensure that attributes is an array
		 * 
		 * @param mixed The attributes to check
		 *
		 * @return mixed The attributes, or an empty array
		 */
		public function ensure_field_attr( $attr ) {
			return is_array( $attr ) ? $attr : array();
		}

		/**
		 * Helper method to filter an array of attributes before using it on a field
		 * 
		 * @param mixed The attributes to filter
		 *
		 * @return mixed The filtered attributes
		 */
		public function filter_field_attr( $attr ) {
			return $attr;
		}

		/**
		 * Parse a field's value, by default it loads the default if value is empty
		 * 
		 * @param mixed The value to parse
		 *
		 * @return mixed The parsed value
		 */
		public function parse_value( $value ) {
			return is_null( $value ) ? $this->get_option( 'std' ) : $value;
		}

		/**
		 * Sanitize the user submitted data
		 * 
		 * @param mixed The data to sanitize
		 *
		 * @return mixed The sanitized data
		 */
		public function sanitize( $data ) {
			return $data;
		}

		/**
		 * Set an option value
		 * 
		 * @param string The option name to set
		 * @param string The option value
		 */
		public function set_option( $key, $value ) {
			if( ! empty( $key ) && 'type' !== $key && 'name' !== $key ) {
				$this->options[ $key ] = $value;
			}
		}

		/**
		 * Helper method to get an option value for this field
		 * 
		 * @param string The name of the requested option
		 *
		 * @return mixed The requested option value or false if the key doesn't exists
		 */
		public function get_option( $key = '' ) {

			switch( $key ) {
				case '':
					return $this->options;
				case 'choices':
				case 'fields':
					if( isset( $this->options[ $key ] ) ) {
						$choices = $this->options[ $key ];
						if( is_callable( $choices ) ) {
							return call_user_func( $choices );
						} elseif( is_array( $choices ) ) {
							if( isset( $choices[0] ) && is_callable( $choices[0] ) ) {
								return call_user_func_array( $choices[0], array_slice( $choices, 1 ) );
							}
							return $choices;
						} else {
							return array();
						}
					}
				case 'name':
				case 'type':
					return $this->{'field_'.$key};
				default:
					if( isset( $this->options[ $key ] ) ) {
						return $this->options[ $key ];
					}
			}

			return false;
		}

		/**
		 * Helper method to enforce enqueueing a field's assets.
		 * This can be useful if a field must be registered, but not known beforehand whether it will be used or not
		 * 
		 * @param string The scope to pass when enqueueing the field
		 * @param array The options of the field to enqueue
		 */
		public static function force_enqueue( $scope, $options, $hook ) {
			$options['mode'] = 'enqueue';
			$field = self::factory( $scope, $options );

			if( is_a( $field, 'Youxi_Form_Field' ) ) {
				$field->enqueue( $hook );
			}
		}

		/**
		 * Static factory method to generate form fields
		 * 
		 * @param string The scope to pass when enqueueing the field
		 * @param array The options of the field to enqueue
		 *
		 * @return Youxi_Form_Field
		 */
		public static function factory( $scope, $options ) {
			
			if( isset( $options['type'] ) ) {

				$field_type = $options['type'];
				
				switch( $field_type ) {
					case 'checkbox':
						if( ! class_exists( 'Youxi_Checkbox_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-checkbox.php' );
						}
						return new Youxi_Checkbox_Form_Field( $scope, $options );
					case 'checkboxlist':
						if( ! class_exists( 'Youxi_Checkbox_List_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-checkbox-list.php' );
						}
						return new Youxi_Checkbox_List_Form_Field( $scope, $options );
					case 'colorpicker':
						if( ! class_exists( 'Youxi_Colorpicker_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-colorpicker.php' );
						}
						return new Youxi_Colorpicker_Form_Field( $scope, $options );
					case 'gallery':
						if( ! class_exists( 'Youxi_Gallery_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-gallery.php' );
						}
						return new Youxi_Gallery_Form_Field( $scope, $options );
					case 'iconchooser':
						if( ! class_exists( 'Youxi_Icon_Chooser_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-icon-chooser.php' );
						}
						return new Youxi_Icon_Chooser_Form_Field( $scope, $options );
					case 'image':
						if( ! class_exists( 'Youxi_Image_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-image.php' );
						}
						return new Youxi_Image_Form_Field( $scope, $options );
					case 'radio':
						if( ! class_exists( 'Youxi_Radio_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-radio.php' );
						}
						return new Youxi_Radio_Form_Field( $scope, $options );
					case 'repeater':
						if( ! class_exists( 'Youxi_Repeater_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-repeater.php' );
						}
						return new Youxi_Repeater_Form_Field( $scope, $options );
					case 'select':
						if( ! class_exists( 'Youxi_Select_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-select.php' );
						}
						return new Youxi_Select_Form_Field( $scope, $options );
					case 'multiselect':
						if( ! class_exists( 'Youxi_Multiselect_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-multiselect.php' );
						}
						return new Youxi_Multiselect_Form_Field( $scope, $options );
					case 'tabular':
						if( ! class_exists( 'Youxi_Tabular_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-tabular.php' );
						}
						return new Youxi_Tabular_Form_Field( $scope, $options );
					case 'text':
						if( ! class_exists( 'Youxi_Text_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-text.php' );
						}
						return new Youxi_Text_Form_Field( $scope, $options );
					case 'textarea':
						if( ! class_exists( 'Youxi_Textarea_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-textarea.php' );
						}
						return new Youxi_Textarea_Form_Field( $scope, $options );
					case 'code':
						if( ! class_exists( 'Youxi_Code_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-code.php' );
						}
						return new Youxi_Code_Form_Field( $scope, $options );
					case 'togglable_fieldsets':
						if( ! class_exists( 'Youxi_Togglable_Fieldsets_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-togglable-fieldsets.php' );
						}
						return new Youxi_Togglable_Fieldsets_Form_Field( $scope, $options );
					case 'richtext':
						if( ! class_exists( 'Youxi_Richtext_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-richtext.php' );
						}
						return new Youxi_Richtext_Form_Field( $scope, $options );
					case 'url':
						if( ! class_exists( 'Youxi_URL_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-url.php' );
						}
						return new Youxi_URL_Form_Field( $scope, $options );
					case 'switch':
						if( ! class_exists( 'Youxi_Switch_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-switch.php' );
						}
						return new Youxi_Switch_Form_Field( $scope, $options );
					case 'uispinner':
						if( ! class_exists( 'Youxi_JUISpinner_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-jui-spinner.php' );
						}
						return new Youxi_JUISpinner_Form_Field( $scope, $options );
					case 'uislider':
						if( ! class_exists( 'Youxi_JUISlider_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-jui-slider.php' );
						}
						return new Youxi_JUISlider_Form_Field( $scope, $options );
					case 'upload':
						if( ! class_exists( 'Youxi_Upload_Form_Field' ) ) {
							require( plugin_dir_path( __FILE__ ) . 'fields/class-upload.php' );
						}
						return new Youxi_Upload_Form_Field( $scope, $options );
					default:
						if( class_exists( $field_type ) ) {
							return new $field_type( $scope, $options );
						}
				}
			}
		}

		/**
		 * Helper method to generate the field name
		 * 
		 * @param string The scope to add to the name attribute, can be used if we don't need scoping on the field
		 * @param string The name of this field
		 *
		 * @return string The generated field name
		 */
		public static function generate_name( $scope, $name ) {
			return is_null( $scope )? $name : sprintf( '%s[%s]', $scope, $name );
		}

		/**
		 * Helper method to generate the field ID
		 * 
		 * @param string The scope to add to the ID attribute, can be used if we don't need scoping on the field
		 * @param string The name of this field
		 *
		 * @return string The generated field ID
		 */
		public static function generate_ID( $scope, $name ) {
			return join( '_', array_filter( preg_split( '/(\[|\])/', self::generate_name( $scope, $name ) ) ) );
		}

		/**
		 * Helper method to get the URL of assets.
		 * This is required by derived classes to get the clean correct path to the assets directory.
		 *
		 * @return string The path to the assets dir relative to this file
		 */
		public static function field_assets_url( $path = '' ) {
			return path_join( YOUXI_CORE_URL . 'admin/field_assets/', $path );
		}
	}
}