<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Richtext Class
 *
 * This class renders a TinyMCE control using native WordPress functions.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Richtext_Form_Field' ) ) {

	if ( ! class_exists( '_WP_Editors' ) )
		require( ABSPATH . WPINC . '/class-wp-editor.php' );

	class Youxi_Richtext_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'tinymce' => array(
					'editor_class' => 'youxi-tmce'
				)
			));

			parent::__construct( $scope, $options, $allowed_hooks );

			add_filter( $this->get_ID_filter_name(), array( $this, 'filter_the_ID' ) );
		}

		/**
		 * Filter the ID attribute for compatibility with TinyMCE
		 *
		 * @param string The current ID attribute
		 *
		 * @return string The filtered ID attribute
		 */
		public function filter_the_ID( $id ) {
			return strtolower( preg_replace( '/[^a-zA-Z]/', '', $id ) );
		}

		/**
		 * Get the TinyMCE settings to pass on wp_editor
		 *
		 * @return array The TinyMCE settings
		 */
		public function get_tinymce_settings() {
			$editor_class = 'youxi-tmce';
			$textarea_name = $this->get_the_name();

			if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				$editor_class .= ' youxi-tmce-ajax';
			}

			$settings = compact( 'editor_class', 'textarea_name' );

			return apply_filters( 'youxi_richtext_form_field_args', wp_parse_args( $settings, $this->get_option( 'tinymce' ) ) );
		}

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_register_script(
					'youxi-richtext', 
					self::field_assets_url( "js/youxi.form.richtext{$suffix}.js" ), 
					array( 'youxi-form-manager' ), 
					YOUXI_CORE_VERSION, 
					true
				);
				wp_enqueue_script( 'youxi-richtext' );

				if ( is_admin() ) {
					add_action( 'admin_print_footer_scripts', array( $this, 'add_editor_settings' ), 40 );
				} else {
					add_action( 'wp_print_footer_scripts', array( $this, 'add_editor_settings' ), 40 );
				}				
			}
		}

		/**
		 * Setup an extra TinyMCE setting for use in AJAX editors
		 */
		public function add_editor_settings() {
			$ajax_settings = _WP_Editors::parse_settings( $this->get_the_ID(), $this->get_tinymce_settings() );
			_WP_Editors::editor_settings( $this->get_the_ID(), $ajax_settings );
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
			ob_start();
			wp_editor(
				$value, 
				$this->get_the_ID(), 
				$this->get_tinymce_settings()
			);
			return ob_get_clean();
		}
	}
}