<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Select Class
 *
 * This class renders a HTML dropdown list.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Select_Form_Field' ) ) {

	class Youxi_Select_Form_Field extends Youxi_Form_Field {

		/**
		 * Constructor
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {
			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(
				'choices' => array()
			));

			parent::__construct( $scope, $options, $allowed_hooks );
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

			$o = '<select id="' . $this->get_the_ID() . '" name="' . $this->get_the_name() . '"' . Youxi_Form::render_attr( $attributes ) . '>';
				foreach( (array) $this->get_option( 'choices' ) as $key => $choice ):
				$o .= '<option value="' . esc_attr( $key ) . '" ' . selected( $key, $value, false ) . '>';
					$o .= esc_html( $choice );
				$o .= '</option>';
				endforeach;
			$o .= '</select>';

			return $o;
		}
	}
}