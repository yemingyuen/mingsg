<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/* Filter widget instances through WPML filter */
add_filter( 'youxi_widgets_quote-widget_new_instance', 'youxi_widgets_icl_register_string_quote', 10, 2 );
add_filter( 'youxi_widgets_rotating-quotes-widget_new_instance', 'youxi_widgets_icl_register_string_rotating_quotes', 10, 2 );
add_filter( 'youxi_widgets_social-widget_new_instance', 'youxi_widgets_icl_register_string_social', 10, 2 );

add_filter( 'youxi_widgets_quote-widget_instance', 'youxi_widgets_icl_t_quote', 10, 2 );
add_filter( 'youxi_widgets_rotating-quotes-widget_instance', 'youxi_widgets_icl_t_rotating_quotes', 10, 2 );
add_filter( 'youxi_widgets_social-widget_instance', 'youxi_widgets_icl_t_social', 10, 2 );

/* WPML callbacks */
function youxi_widgets_icl_register_string_quote( $instance, $id ) {
	icl_register_string( 'Youxi Widgets', sprintf( "[%s] content", $id ), $instance['content'] );
	icl_register_string( 'Youxi Widgets', sprintf( "[%s] name", $id ), $instance['name'] );
	icl_register_string( 'Youxi Widgets', sprintf( "[%s] source", $id ), $instance['source'] );

	return $instance;
}

function youxi_widgets_icl_register_string_rotating_quotes( $instance, $id ) {
	if( isset( $instance['quotes'] ) ) {
		foreach( $instance['quotes'] as $idx => &$quote ) {
			icl_register_string( 'Youxi Widgets', sprintf( "[%s] quote-text-%d", $id, $idx ), $quote['text'] );
			icl_register_string( 'Youxi Widgets', sprintf( "[%s] quote-author-%d", $id, $idx ), $quote['author'] );
			icl_register_string( 'Youxi Widgets', sprintf( "[%s] quote-source-%d", $id, $idx ), $quote['source'] );
		}
	}
	return $instance;
}

function youxi_widgets_icl_register_string_social( $instance, $id ) {
	if( isset( $instance['items'] ) ) {
		foreach( $instance['items'] as $idx => &$item ) {
			icl_register_string( 'Youxi Widgets', sprintf( "[%s] item-title-%d", $id, $idx ), $item['title'] );
		}
	}
	return $instance;
}

function youxi_widgets_icl_t_quote( $instance, $id ) {
	$instance['content'] = icl_t( 'Youxi Widgets', sprintf( "[%s] content", $id ), $instance['content'] );
	$instance['name']    = icl_t( 'Youxi Widgets', sprintf( "[%s] name", $id ), $instance['name'] );
	$instance['source']  = icl_t( 'Youxi Widgets', sprintf( "[%s] source", $id ), $instance['source'] );

	return $instance;
}

function youxi_widgets_icl_t_rotating_quotes( $instance, $id ) {
	if( isset( $instance['quotes'] ) ) {
		foreach( $instance['quotes'] as $idx => &$quote ) {
			$quote['text']   = icl_t( 'Youxi Widgets', sprintf( "[%s] quote-text-%d", $id, $idx ), $quote['text'] );
			$quote['author'] = icl_t( 'Youxi Widgets', sprintf( "[%s] quote-author-%d", $id, $idx ), $quote['author'] );
			$quote['source'] = icl_t( 'Youxi Widgets', sprintf( "[%s] quote-source-%d", $id, $idx ), $quote['source'] );
		}
	}
	return $instance;
}

function youxi_widgets_icl_t_social( $instance, $id ) {
	if( isset( $instance['items'] ) ) {
		foreach( $instance['items'] as $idx => &$item ) {
			$item['title'] = icl_t( 'Youxi Widgets', sprintf( "[%s] item-title-%d", $id, $idx ), $item['title'] );
		}
	}
	return $instance;
}