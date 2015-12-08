<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Meta Box Class
 *
 * This class is a helper wrapper class for easily adding meta boxes in WordPress admin.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Metabox' ) ) {

	class Youxi_Metabox {

		/**
		 * ID of the metabox
		 *
		 * @access private
		 * @var string
		 */
		private $id;

		/**
		 * Title to be displayed on the metabox
		 *
		 * @access private
		 * @var string
		 */
		private $title;

		/**
		 * Callback for the metabox content
		 *
		 * @access private
		 * @var mixed
		 */
		private $callback;

		/**
		 * Context of the metabox
		 *
		 * @access private
		 * @var string
		 */
		private $context;

		/**
		 * Priority of the metabox
		 *
		 * @access private
		 * @var string
		 */
		private $priority;

		/**
		 * Whether to store the field values in an array
		 *
		 * @access private
		 * @var bool
		 */
		private $as_array;

		/**
		 * Form fields to be displayed on the metabox
		 *
		 * @access private
		 * @var array
		 */
		private $fields;

		/**
		 * Form fieldset configuration
		 *
		 * @access private
		 * @var array
		 */
		private $fieldsets;

		/**
		 * Page template of the metabox
		 *
		 * @access private
		 * @var string
		 */
		private $page_template;

		/**
		 * List of HTML classes to be added to the metabox
		 *
		 * @access private
		 * @var array
		 */
		private $html_classes = array( 'youxi-metabox' );

		/**
		 * Cache for keeping generated forms
		 *
		 * @access private
		 * @var array
		 */
		private $form_cache = array();

		/**
		 * Default arg values
		 *
		 * @access private
		 * @var array
		 */
		private static $defaults = array(
			'title' => '', 
			'callback' => null, 
			'context' => 'normal', 
			'priority' => 'default', 
			'as_array' => true, 
			'fields' => array(), 
			'fieldsets' => array(), 
			'page_template' => null
		);

		/**
		 * Constructor
		 *
		 * @param string The ID of the metabox
		 * @param array The arguments to pass to add_meta_box
		 * @param array The list of HTML classes to add on the metabox
		 */
		public function __construct( $id, $args, $html_classes = array() ) {

			/* Assign default properties */
			$this->id = sanitize_key( $id );
			$this->callback = array( $this, 'meta_box_callback' );

			/* Parse the passed args */
			$args = wp_parse_args( $args, self::$defaults );

			/* Assign the properties */
			$keys = array_keys( get_object_vars( $this ) );
			foreach( $keys as $key ) {
				if( isset( $args[ $key ] ) ) {
					switch( $key ) {
						case 'id':
						case 'form_cache':
							break;
						case 'callback':
							if( is_callable( $args[ $key ] ) ) {
								$this->{$key} = $args[ $key ];
							}
							break;
						case 'fields':
							$this->{$key} = $this->parse_field_args( $args[ $key ] );
							break;
						default:
							$this->{$key} = $args[ $key ];
							break;
					}
				}
			}

			/* Tidy up the html classes */
			$this->html_classes = array_merge( $this->html_classes, (array) $html_classes );
			$this->html_classes = array_map( 'sanitize_html_class', $this->html_classes );
			$this->html_classes = array_unique( $this->html_classes );
		}

		/**
		 * Register the meta box on a specific post type
		 *
		 * @param string The post type to register the metabox
		 */
		public function register( $post_type ) {

			/* Register the meta box */
			add_meta_box(
				$this->id, 
				$this->title, 
				$this->callback, 
				$post_type, 
				$this->context, 
				$this->priority
			);

			/* Add the metabox classes filter */
			add_filter( "postbox_classes_{$post_type}_{$this->id}", array( $this, 'postbox_classes' ) );

			if( 'page' == $post_type && ! is_null( $this->page_template ) ) {
				add_filter( "postbox_classes_page_{$this->id}", array( $this, 'postbox_classes_page_template' ) );				
			}

			/* Register hook for enqueuing the registered field assets */
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Parse and validate the field arguments passed to this metabox
		 *
		 * @param array The field arguments
		 *
		 * @return array The parsed field arguments
		 */
		public function parse_field_args( $fields ) {

			$parsed = array();
			foreach( (array) $fields as $id => $field ) {

				/*  Skip if the type is not specified */
				if( ! isset( $field['type'] ) ) {
					continue;
				}

				/* Take the field name from the id */
				$field['name'] = $id;

				/* Set scalar to false if not supplied */
				if( ! isset( $field['scalar'] ) ) {
					$field['scalar'] = false;
				}

				/* Prevent a TinyMCE field added to the metabox */
				if( 'richtext' == $field['type'] ) {
					if( ! isset( $field['tinymce'] ) || ! is_array( $field['tinymce'] ) ) {
						$field['tinymce'] = array();
					}
					$field['tinymce'] = array_merge( $field['tinymce'], array(
						'media_buttons' => false, 
						'tinymce' => false
					));
				}

				/* Parsed! */
				$parsed[ $id ] = $field;
			}

			return $parsed;
		}

		/**
		 * Enqueue the required assets for metabox fields
		 *
		 * @param string The current screen hook
		 */
		public function admin_enqueue_scripts( $hook ) {

			if( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {

				$post_type = get_post_type();

				$this->get_form( $post_type )->enqueue( $hook );

				if( 'page' == $post_type && ! is_null( $this->page_template ) ) {

					wp_enqueue_script( 
						'youxi-page-template', 
						YOUXI_CORE_URL . 'admin/assets/js/youxi.page-template.js', 
						array( 'jquery' ), 
						YOUXI_CORE_VERSION, 
						true
					);

				}

			}

		}

		/**
		 * Save metabox fields
		 *
		 * @param string The current post ID
		 * @param array The data of the fields
		 */
		public function save_fields( $post_id, $data ) {
			$post_type = get_post_type( $post_id );
			$metabox_id = "{$post_type}_{$this->id}";

			if( check_admin_referer( "youxi_mb_{$metabox_id}", "{$metabox_id}_nonce" ) ) {

				if( isset( $data[ $this->id ] ) ) {

					// Make sure only data present in the fields are processed
					$data = array_intersect_key( $data[ $this->id ], $this->fields );

					// Sanitize the data
					$data = $this->get_form( $post_type )->sanitize( $data );

					// If saving as array
					if( $this->as_array ) {

						// First check for scalar values
						foreach( $data as $key => $value ) {

							// Found a scalar value
							if( $this->fields[ $key ]['scalar'] ) {

								// Update scalar value
								update_post_meta( $post_id, $key, $value );
								unset( $data[ $key ] );
							}
						}

						// Update the other values
						if( ! empty( $data ) ) {
							update_post_meta( $post_id, $this->id, $data );
						}
					} else {

						// Update all values as single post meta
						foreach( $data as $key => $value ) {
							update_post_meta( $post_id, $key, $value );
						}
					}
				} else {

					// Delete all possible meta fields
					foreach( $this->fields as $id => $field ) {
						delete_post_meta( $post_id, $id );
					}
					delete_post_meta( $post_id, $this->id );
				}
			}

			return $post_id;
		}

		/**
		 * Metabox callback function to render the form
		 *
		 * @param WP_Post the current post object
		 * @param array The passed metabox arguments
		 */
		public function meta_box_callback( $post ) {

			/* Get all existing post meta */
			$existing_meta = get_post_meta( $post->ID );

			/* Get the post meta data */
			if( $this->as_array ) {

				/* Get this metabox's post meta and make sure it's an array */
				$the_meta = get_post_meta( $post->ID, $this->id, true );
				$the_meta = $the_meta && is_array( $the_meta ) ? $the_meta : array();

				/* Retrieve the scalar values and merge them into the array */
				foreach( $this->fields as $id => $field ) {
					if( $field['scalar'] && isset( $existing_meta[ $id ] ) ) {
						$the_meta[ $id ] = get_post_meta( $post->ID, $id, true );
					}
				}
			} else {

				// Prepare an empty array for the post meta
				$the_meta = array();

				// Retrieve all post meta
				foreach( $this->fields as $id => $field ) {
					if( isset( $existing_meta[ $id ] ) ) {
						$the_meta[ $id ] = get_post_meta( $post->ID, $id, true );
					}
				}
			}

			/* Get the form */
			$form = $this->get_form( $post->post_type );

			/* Compile and output the form */
			$form->compile( $the_meta );
			$form->render();
		}

		/**
		 * Helper method to generate the form object
		 *
		 * @param string The current metabox post type name
		 *
		 * @return Youxi_Form The generated form instance
		 */
		public function get_form( $post_type ) {

			$metabox_id = "{$post_type}_{$this->id}";

			if( isset( $this->form_cache[ $metabox_id ] ) && is_a( $this->form_cache[ $metabox_id ], 'Youxi_Form' ) ) {
				return $this->form_cache[ $metabox_id ];
			}

			/* Instantiate the form fields */
			$fields = array();
			foreach( $this->fields as $id => $field ) {
				$fields[ $id ] = Youxi_Form_Field::factory( "{$post_type}[{$this->id}]", $field );
			}

			/* Generate the form */
			$form = new Youxi_Form( $fields, array(
				'form_tag' => 'div', 
				'form_attr' => array(
					'class' => 'youxi-form youxi-form-inline'
				), 
				'before_fields' => wp_nonce_field( "youxi_mb_{$metabox_id}", "{$metabox_id}_nonce", true, false ), 
				'group_attr' => array(
					'class' => array(
						'youxi-form-row', 
						'youxi-form-block' => array(
							'type' => array( 'checkbox', 'gallery' )
						)
					)
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
							'type' => array( 'text', 'textarea', 'code', 'url', 'select', 'iconchooser' )
						)
					)
				), 
				'fieldsets' => $this->fieldsets
			));

			/* Cache the form object */
			$this->form_cache[ $metabox_id ] = $form;

			return $form;
		}

		/**
		 * Metabox class names filter
		 *
		 * @param array The current classes of the metabox
		 */
		public function postbox_classes( $classes ) {
			return array_merge( $classes, $this->html_classes );
		}

		/**
		 * Metabox class names filter for page templates
		 *
		 * @param array The current classes of the metabox
		 */
		public function postbox_classes_page_template( $classes ) {
			return array_merge( $classes, array( 'youxi_page_template_' . preg_replace( '/\W/', '_', $this->page_template ) ) );
		}

		/**
		 * Get an array containing field names and labels
		 *
		 * @return array The collection of field <-> label
		 */
		public function get_visible_field_list() {
			$labels = array();
			foreach( $this->fields as $field ) {
				$field = wp_parse_args( $field, array(
					'show_admin_column' => false, 
					'label' => ''
				));
				if( $field['show_admin_column'] && '' !== $field['label'] ) {
					$labels[ $field['name'] ] = $field['label'];
				}
			}

			return $labels;
		}

		/**
		 * Get the value of a metabox field fetched from post meta
		 *
		 * @param string The field name
		 * @param string The post ID
		 *
		 * @return string The filtered value of the post meta
		 */
		public function get_field_value( $name, $post_id ) {
			if( $this->as_array ) {
				$meta  = get_post_meta( $post_id, $this->id, true );
				return isset( $meta[ $name ] ) ? $meta[ $name ] : '';
			}
			return get_post_meta( $post_id, $name, true );
		}
	}
}