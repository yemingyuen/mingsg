<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Counter Shortcode Handler
 */
function youxi_shortcode_counter_cb( $atts, $content, $tag ) {
	return esc_html( $content ) . '<br>' . esc_html( $atts['label'] );
}

/**
 * Progressbar Shortcode Handler
 */
function youxi_shortcode_progressbar_cb( $atts, $content, $tag ) {
	$container_classes = array( 'progress' );
	$bar_classes = array( 'progress-bar' );

	extract( $atts, EXTR_SKIP );

	if( $type ) {
		$bar_classes[] = "progress-bar-{$type}";
	}
	if( $striped ) {
		$container_classes[] = "progress-striped";
	}
	if( $active ) {
		$container_classes[] = 'active';
	}

	$o = '<div class="' . join( ' ', $container_classes ) . '">';
		$o .= '<div class="' . join( ' ', $bar_classes ) . '" role="progressbar" aria-valuenow="' . esc_attr( $value ) . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . esc_attr( $value ) . '%"></div>';
	$o .= '</div>';

	return $o;
}

/**
 * Shortcode Definitions Callback
 */
function define_statistic_shortcodes( $manager ) {

	/********************************************************************************
	 * Content category
	 ********************************************************************************/
	$manager->add_category( 'statistic', array(
		'label' => __( 'Statistic Shortcodes', 'youxi' ), 
		'priority' => 30
	));

	/********************************************************************************
	 * Counter shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'counter', array(
		'label' => __( 'Counter', 'youxi' ), 
		'category' => 'statistic', 
		'priority' => 10, 
		'icon' => 'fa fa-clock-o', 
		'insert_nl' => false, 
		'atts' => array(
			'label' => array(
				'type' => 'text', 
				'label' => __( 'Label', 'youxi' ), 
				'description' => __( 'Enter here the counter label.', 'youxi' )
			)
		), 
		'content' => array(
			'type' => 'uispinner', 
			'label' => __( 'Value', 'youxi' ), 
			'description' => __( 'Enter here the counter value.', 'youxi' ), 
			'widgetopts' => array(
				'min' => 0, 
				'step' => 0.1
			), 
			'std' => 0
		), 
		'callback' => 'youxi_shortcode_counter_cb'
	));

	/********************************************************************************
	 * Progressbar shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'progressbar', array(
		'label' => __( 'Progressbar', 'youxi' ), 
		'category' => 'statistic', 
		'priority' => 20, 
		'icon' => 'fa fa-tasks', 
		'atts' => array(
			'type' => array(
				'type' => 'select', 
				'label' => __( 'Type', 'youxi' ), 
				'description' => __( 'Choose the type of the progressbar.', 'youxi' ), 
				'choices' => array(
					0 => __( 'Default', 'youxi' ), 
					'success' => __( 'Success', 'youxi' ), 
					'info' => __( 'Info', 'youxi' ), 
					'warning' => __( 'Warning', 'youxi' ), 
					'danger' => __( 'Danger', 'youxi' )
				), 
				'std' => 0
			), 
			'value' => array(
				'type' => 'uislider', 
				'label' => __( 'Value', 'youxi' ), 
				'description' => __( 'Enter the value of the progressbar.', 'youxi' ), 
				'std' => '100'
			), 
			'striped' => array(
				'type' => 'switch', 
				'label' => __( 'Show Stripes', 'youxi' ), 
				'description' => __( 'Switch to show stripes on the progressbar.', 'youxi' ), 
				'std' => 1
			), 
			'active' => array(
				'type' => 'switch', 
				'label' => __( 'Animated', 'youxi' ), 
				'description' => __( 'Switch to animate the progressbar.', 'youxi' ), 
				'std' => 1
			)
		), 
		'callback' => 'youxi_shortcode_progressbar_cb'
	));
}

/**
 * Hook to 'youxi_shortcode_register'
 */
add_action( 'youxi_shortcode_register', 'define_statistic_shortcodes', 1 );