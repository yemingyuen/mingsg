<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Shortcode_Editor {

	/**
	 * The tag name of the shortcode to display on the editor
	 *
	 * @access private
	 * @var Youxi_Shortcode
	 */
	private $shortcode;

	/**
	 * The array containing Youxi_Form_Field objects
	 *
	 * @access private
	 * @var array
	 */
	private $fields;

	/**
	 * The array containing the values of the form fields (if it is defined)
	 *
	 * @access private
	 * @var array
	 */
	private $form_data;

	/**
	 * Constructor
	 */
	public function __construct( $shortcode, $form_data ) {
		$this->shortcode = $shortcode;
		$this->form_data = $form_data;
		$this->fields = array();

		/* Loop through the shortcode attributes generating field objects */
		foreach( array_merge( $shortcode->atts, array( 'content' => $shortcode->content ) ) as $name => $atts ) {
			$options = array_merge( compact( 'name' ), (array) $atts );
			$field   = Youxi_Form_Field::factory( $shortcode->tag, $options );

			if( is_a( $field, 'Youxi_Form_Field' ) ) {
				$this->fields[] = $field;
			}
		}
	}

	/**
	 * Get the HTML output for the shortcode popup content
	 *
	 * @return string The popup content HTML markup
	 */
	public function get_html() {

		$form = new Youxi_Form( $this->fields, array(
			'form_tag' => 'div', 
			'form_attr' => array(
				'class' => 'youxi-form'
			), 
			'form_method' => '', 
			'group_attr' => array(
				'class' => 'youxi-form-row'
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
			'fieldsets' => $this->shortcode->fieldsets
		) );

		return $form->compile( $this->form_data )->render( false );
	}
}