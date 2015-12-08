<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Taxonomy Class
 *
 * This class is a helper wrapper class for easily registering taxonomies.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Taxonomy' ) ) {

	class Youxi_Taxonomy {

		/**
		 * @access private
		 * @var string The name of the taxonomy 
		 */
		private $taxonomy;

		/**
		 * @access private
		 * @var array The arguments of the taxonomy
		 */
		private $taxonomy_args;

		/**
		 * Constructor
		 *
		 * @param string The ID of the taxonomy
		 * @param array The arguments to pass to register_taxonomy
		 */
		public function __construct( $taxonomy, $args = array() ) {
			$this->taxonomy       = $taxonomy;
			$this->taxonomy_args  = $args;
		}

		/**
		 * Register the taxonomy, this should be called on the init hook
		 *
		 * @param string The name of the post type to register this taxonomy
		 */
		public function register( $post_type ) {
			if( ! taxonomy_exists( $this->taxonomy ) ) {
				register_taxonomy( $this->taxonomy, $post_type, $this->taxonomy_args );
			}
			register_taxonomy_for_object_type( $this->taxonomy, $post_type );
		}
	}
}