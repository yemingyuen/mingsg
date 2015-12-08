<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Portfolio shortcode handler.
 * do nothing and leave rendering of the portfolio completely to the theme
 */
function youxi_portfolio_shortcode_cb( $atts, $content, $tag ) {
	ob_start();
	$args = func_get_args();
	do_action_ref_array( 'youxi_portfolio_shortcode_output', $args );
	return ob_get_clean();
}

/**
 * Portfolio shortcode tag
 */
function youxi_portfolio_shortcode_tag() {
	return apply_filters( 'youxi_portfolio_shortcode_tag', 'portfolio_entries' );
}

/**
 * Register shortcode
 */
function youxi_portfolio_shortcode_register( $manager ) {

	if( ! apply_filters( 'youxi_portfolio_register_shortcode', true ) ) {
		return;
	}

	/* Add a hook to make registering another shortcode category possible */
	do_action( 'youxi_portfolio_shortcode_register' );

	/********************************************************************************
	 * Portfolio shortcode
	 ********************************************************************************/
	$manager->add_shortcode( youxi_portfolio_shortcode_tag(), array(
		'label' => apply_filters( 'youxi_portfolio_shortcode_label', __( 'Portfolio Entries', 'youxi' ) ), 
		'category' => apply_filters( 'youxi_portfolio_shortcode_category', 'content' ), 
		'priority' => 75, 
		'icon' => 'fa fa-suitcase', 
		'atts' => apply_filters( 'youxi_portfolio_shortcode_atts', array() ), 
		'callback' => 'youxi_portfolio_shortcode_cb'
	) );
}
add_action( 'youxi_shortcode_register', 'youxi_portfolio_shortcode_register' );
