<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Button Shortcode Handler
 */
function youxi_shortcode_button_cb( $atts, $content, $tag ) {
	extract( $atts, EXTR_SKIP );

	$classes = array( 'btn', "btn-{$type}" );
	if( $size ) {
		$classes[] = "btn-{$size}";
	}
	$html  = ' href="' . esc_url( $url ) . '"';
	$html .= ' class="' . esc_attr( join( ' ' , $classes ) ) . '"';

	return '<a' . $html . '>' . strip_tags( $content ) . '</a>';
}

/**
 * Dropcap Shortcode Handler
 */
function youxi_shortcode_dropcap_cb( $atts, $content, $tag ) {
	return '<strong>' . strip_tags( $content ) . '</strong>';
}

/**
 * Label Shortcode Handler
 */
function youxi_shortcode_label_cb( $atts, $content, $tag ) {

	extract( $atts, EXTR_SKIP );

	$classes = 'label';
	if( $type ) {
		$classes .= " label-{$type}";
	}

	return '<span class="' . $classes . '">' . strip_tags( $content ) . '</span>';
}

/**
 * Lead Text Shortcode Handler
 */
function youxi_shortcode_lead_text_cb( $atts, $content, $tag ) {

	/* Remove all <p> tags first */
	$content = preg_replace( '#</?p[^>]*>#', '', $content );

	/* Fix shortcodes */
	$content = Youxi_Shortcode_Manager::get()->shortcode_unautop( $content );

	/* do_shortcode */
	$content = do_shortcode( $content );

	/* apply wpautop */
	return wpautop( '<p class="lead">' . $content . '</p>' );
}

/**
 * Tooltip Shortcode Handler
 */
function youxi_shortcode_tooltip_cb( $atts, $content, $tag ) {

	extract( $atts, EXTR_SKIP );

	$tagname = 'link' == $type ? 'a' : 'span';

	$attributes = array(
		'rel' => 'tooltip', 
		'title' => $title, 
		'data-html' => true, 
		'data-trigger' => join( ' ', array_map( 'trim', explode( ',', $trigger ) ) ), 
		'data-placement' => $placement
	);
	if( 'link' == $type ) {
		$attributes['href'] = esc_url( $url );
	}

	$html = '';
	foreach( $attributes as $name => $value ) {
		$html .= " {$name}=\"" . esc_attr( $value ) . '"';
	}

	return '<' . $tagname . $html . '>' . strip_tags( $content ) . '</' . $tagname . '>';
}

/**
 * Shortcode Definitions Callback
 */
function define_uncategorized_shortcodes( $manager ) {

	/********************************************************************************
	 * Button shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'button', array(
		'label' => __( 'Button', 'youxi' ), 
		'priority' => 10, 
		'inline' => true, 
		'insert_nl' => false, 
		'atts' => array(
			'type' => array(
				'type' => 'select', 
				'label' => __( 'Type', 'youxi' ), 
				'description' => __( 'Choose here the button type.', 'youxi' ), 
				'choices' => array(
					'default' => __( 'Default', 'youxi' ), 
					'primary' => __( 'Primary', 'youxi' ), 
					'success' => __( 'Success', 'youxi' ), 
					'warning' => __( 'Warning', 'youxi' ), 
					'danger' => __( 'Danger', 'youxi' ), 
					'info' => __( 'Info', 'youxi' ), 
				), 
				'std' => 'default'
			), 
			'size' => array(
				'type' => 'select', 
				'label' => __( 'Size', 'youxi' ), 
				'description' => __( 'Choose here the button size.', 'youxi' ), 
				'choices' => array(
					0 => __( 'Default', 'youxi' ), 
					'lg' => __( 'Large', 'youxi' ), 
					'sm' => __( 'Small', 'youxi' ), 
					'xs' => __( 'Extra Small', 'youxi' )
				), 
				'std' => 0
			), 
			'url' => array(
				'type' => 'url', 
				'label' => __( 'URL', 'youxi' ), 
				'description' => __( 'Enter here the URL to visit after clicking the button.', 'youxi' ), 
				'std' => '#'
			)
		), 
		'content' => array(
			'type' => 'text', 
			'label' => __( 'Text', 'youxi' ), 
			'description' => __( 'Enter here the button text.', 'youxi' ), 
			'std' => 'Button'
		), 
		'callback' => 'youxi_shortcode_button_cb'
	));

	/********************************************************************************
	 * Dropcap shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'dropcap', array(
		'label' => __( 'Dropcap', 'youxi' ), 
		'priority' => 20, 
		'inline' => true, 
		'insert_nl' => false, 
		'content' => array(
			'type' => 'text', 
			'label' => __( 'Text', 'youxi' ), 
			'description' => __( 'Enter here the dropcap text.', 'youxi' )
		), 	
		'callback' => 'youxi_shortcode_dropcap_cb'
	));

	/********************************************************************************
	 * Label shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'label', array(
		'label' => __( 'Label', 'youxi' ), 
		'priority' => 30, 
		'inline' => true, 
		'insert_nl' => false, 
		'atts' => array(
			'type' => array(
				'type' => 'select', 
				'label' => __( 'Type', 'youxi' ), 
				'description' => __( 'Choose here the highlight type.', 'youxi' ), 
				'choices' => array(
					'default' => __( 'Default', 'youxi' ), 
					'primary' => __( 'Primary', 'youxi' ), 
					'success' => __( 'Success', 'youxi' ), 
					'info' => __( 'Info', 'youxi' ), 
					'warning' => __( 'Warning', 'youxi' ), 
					'danger' => __( 'Danger', 'youxi' )
				), 
				'std' => 'default'
			)
		), 
		'content' => array(
			'type' => 'text', 
			'label' => __( 'Text', 'youxi' ), 
			'description' => __( 'Enter here the label text.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_label_cb'
	));

	/********************************************************************************
	 * Lead text shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'lead_text', array(
		'label' => __( 'Lead Text', 'youxi' ), 
		'priority' => 40, 
		'callback' => 'youxi_shortcode_lead_text_cb'
	));

	/********************************************************************************
	 * Tooltip shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'tooltip', array(
		'label' => __( 'Tooltip', 'youxi' ), 
		'priority' => 50, 
		'inline' => true, 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => __( 'Title', 'youxi' ), 
				'description' => __( 'Enter here the tooltip title.', 'youxi' )
			), 
			'placement' => array(
				'type' => 'select', 
				'label' => __( 'Placement', 'youxi' ), 
				'description' => __( 'Choose here the tooltip position.', 'youxi' ), 
				'choices' => array(
					'top' => __( 'Top', 'youxi' ), 
					'bottom' => __( 'Bottom', 'youxi' ), 
					'left' => __( 'Left', 'youxi' ), 
					'right' => __( 'Right', 'youxi' )
				), 
				'std' => 'top'
			), 
			'trigger' => array(
				'type' => 'checkboxlist', 
				'label' => __( 'Trigger', 'youxi' ), 
				'description' => __( 'Choose here what action triggers the tooltip.', 'youxi' ), 
				'choices' => array(
					'click' => __( 'Click', 'youxi' ), 
					'hover' => __( 'Hover', 'youxi' ), 
					'focus' => __( 'Focus', 'youxi' )
				), 
				'std' => array( 'hover', 'focus' )
			), 
			'type' => array(
				'type' => 'select', 
				'label' => __( 'Type', 'youxi' ), 
				'description' => __( 'Choose here what element to use to display the tooltip.', 'youxi' ), 
				'choices' => array(
					'link' => __( 'Link', 'youxi' ), 
					'text' => __( 'Text', 'youxi' )
				), 
				'std' => 'text'
			), 
			'url' => array(
				'type' => 'url', 
				'label' => __( 'URL', 'youxi' ), 
				'description' => __( 'Enter here the URL for the link tooltip.', 'youxi' ), 
				'criteria' => array(
					'condition' => 'type:is(link)'
				)
			)
		), 
		'content' => array(
			'type' => 'text', 
			'label' => __( 'Content', 'youxi' ), 
			'description' => __( 'Enter the content of the tooltip.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_tooltip_cb'
	));
}

/**
 * Hook to 'youxi_shortcode_register'
 */
add_action( 'youxi_shortcode_register', 'define_uncategorized_shortcodes', 1 );