<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Shortcode_Category {

	/**
	 * The id of the category
	 *
	 * @access public
	 * @var string
	 */
	public $id;

	/**
	 * The label of the category
	 *
	 * @access public
	 * @var string
	 */
	public $label;

	/**
	 * The priority of this category
	 *
	 * @access public
	 * @var int
	 */
	public $priority;

	/**
	 * The array containing Youxi_Shortcode objects
	 *
	 * @access public
	 * @var array
	 */
	public $shortcodes;

	/**
	 * Constructor.
	 *
	 * @param Youxi_Shortcode_Manager $manager
	 * @param string $id An specific ID of the category.
	 * @param array $args Category arguments.
	 */
	public function __construct( $id, $args ) {
		$keys = array_keys( get_class_vars( __CLASS__ ) );
		foreach( $keys as $key ) {
			if( isset( $args[ $key ] ) ) {
				$this->{$key} = $args[ $key ];
			}
		}

		$this->id = $id;
		$this->shortcodes = array();
	}

	/**
	 * Returns the category arguments
	 *
	 * @return array The arguments of the category
	 */
	public function get_args( $include = '*', $single = false ) {
		
		$public_args = apply_filters( 'youxi_shortcode_allowed_category_args', array( 'label' ) );		

		/* Get all accessible object vars */
		$class_keys  = array_keys( get_object_vars( $this ) );

		/* Validate the included args */
		if( '*' == $include ) {
			$include = $public_args;
		} else {
			$include = (array) $include;
		}

		/* Intersect the includes to the allowed args */
		$include = array_intersect( $public_args, $include );

		$args = array();

		/* Loop through each included args */
		foreach( $include as $arg ) {
			if( in_array( $arg, $class_keys ) ) {
				$args[ $arg ] = $this->{$arg};
			}
		}

		return $single && isset( $args[0] ) ? $args[0] : $args;
	}
}