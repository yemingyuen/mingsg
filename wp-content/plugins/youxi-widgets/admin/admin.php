<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

function youxi_widgets_enqueue_control_scripts( $hook ) {

	if( 'widgets.php' === $hook ) {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'youxi-widgets-repeater', YOUXI_WIDGETS_URL . "admin/assets/js/youxi.widgets.repeater{$suffix}.js", array( 'jquery', 'underscore' ), '1.0', true );
		wp_enqueue_style( 'youxi-widgets-repeater', YOUXI_WIDGETS_URL . 'admin/assets/css/youxi.widgets.repeater.css', array(), '1.0', 'screen' );
	}
}
add_action( 'admin_enqueue_scripts', 'youxi_widgets_enqueue_control_scripts' );