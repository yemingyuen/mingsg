<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Google Map Shortcode Handler
 */
function youxi_shortcode_google_map_cb( $atts, $content, $tag ) {

	preg_match_all( '/\((.+?),(.+?)\)/', $atts['markers'], $markers, PREG_SET_ORDER );

	foreach( $markers as $key => $marker ) {
		$markers[ $key ] = array(
			'lat' => isset( $marker[1] ) ? $marker[1] : 0.0, 
			'lng' => isset( $marker[2] ) ? $marker[2] : 0.0, 
		);
	}

	$gmap_style = '';
	if( is_string( $atts['aspect_ratio'] ) ) {
		$ar = explode( ':', $atts['aspect_ratio'] );
		if( isset( $ar[0], $ar[1] ) ) {
			$pad = 100 * max( 1, intval( $ar[1] ) ) / max( 1, intval( $ar[0] ) );
		} else {
			$pad = 100 * floatval( 9 / 16 );
		}
		$gmap_style = ' style="padding-bottom: ' . $pad . '%"';
	}

	if( is_string( $atts['controls'] ) ) {
		$controls = array();
		foreach( explode( ',', $atts['controls'] ) as $control ) {
			$controls[ $control ] = true;
		}
	} elseif( is_array( $atts['controls'] ) ) {
		$controls = $atts['controls'];
	} else {
		$controls = array();
	}

	$controls = shortcode_atts( array(
		'pan' => false, 
		'zoom' => false, 
		'map-type' => false, 
		'scale' => false, 
		'street-view' => false, 
		'overview-map' => false
	), $controls );

	$attributes = array();
	$attributes['data-widget']      = 'gmap';
	$attributes['data-scrollwheel'] = $atts['scrollwheel'];
	$attributes['data-center']      = implode( ',', array( $atts['center_lat'], $atts['center_lng'] ) );
	$attributes['data-map-type-id'] = $atts['map_type'];
	$attributes['data-monochrome']  = json_encode( (bool) $atts['monochrome'] );
	$attributes['data-markers']     = json_encode( (array) $markers );
	$attributes['data-zoom']        = intval( $atts['zoom'] );

	foreach( $controls as $id => $control ) {
		$attributes['data-' . $id . '-control'] = json_encode( $control );
	}

	$html = '';
	foreach( $attributes as $key => $val ) {
		$html .= " {$key}=\"" . esc_attr( $val ) . "\"";
	}

	return '<div class="google-maps-container"' . $gmap_style . '><div class="google-maps"' . $html . '></div></div>';
}

/**
 * Slider Shortcode Handler
 */
function youxi_shortcode_slider_cb( $atts, $content, $tag ) {

	extract( $atts, EXTR_SKIP );

	$controls  = (array) explode( ',', $controls );
	$behaviors = (array) explode( ',', $behaviors );

	$id = esc_attr( 'carousel-' . Youxi_Shortcode::read_counter( $tag ) );

	$attributes = array(
		'id' => $id, 
		'class' => 'carousel', 
		'data-interval' => $interval
	);

	if( in_array( 'pause', $behaviors ) ) {
		$attributes['data-pause'] = 'hover';
	}
	if( in_array( 'wrap', $behaviors ) ) {
		$attributes['data-wrap'] = true;
	}

	$html = '';
	foreach( $attributes as $key => $val ) {
		$html .= " {$key}=\"" . esc_attr( $val ) . "\"";
	}

	$o = "<div{$html}>";

		/* Reset slide index */
		Youxi_Shortcode::reset_counter( 'slide' );

		/* Carousel Content */
		$slide_content = '<div class="carousel-inner">';
			$slide_content .= do_shortcode( $content );
		$slide_content .= '</div>';

		$amount = Youxi_Shortcode::read_counter( 'slide' );

		/* Carousel Pagers */
		$pagers = '';
		$arrows = '';

		foreach( $controls as $control ):
			switch( $control ):
				case 'pagers':
					if( $amount > 0 ):
						$pagers .= '<ol class="carousel-indicators">';
						for( $i = 0; $i < $amount; $i++ ):
							$pagers .= '<li data-target="#' . esc_attr( $id ) . '" data-slide-to="' . esc_attr( $i ) . '"';
							if( 0 == $i ) {
								$pagers .= ' class="active"';
							}
							$pagers .= '></li>';
						endfor;
						$pagers .= '</ol>';
					endif;
					break;
				case 'arrows':
					$arrows .= '<a class="left carousel-control" href="#' . esc_attr( $id ) . '" data-slide="prev">';
						$arrows .= '<span class="glyphicon glyphicon-chevron-left"></span>';
					$arrows .= '</a>';
					$arrows .= '<a class="right carousel-control" href="#' . esc_attr( $id ) . '" data-slide="next">';
						$arrows .= '<span class="glyphicon glyphicon-chevron-right"></span>';
					$arrows .= '</a>';
			endswitch;
		endforeach;

		$o .= $pagers;
		$o .= $slide_content;
		$o .= $arrows;

	$o .= '</div>';

	return $o;
}

/**
 * Slide Shortcode Handler
 */
function youxi_shortcode_slide_cb( $atts, $content, $tag ) {

	$o = '<div class="item' . ( 0 == Youxi_Shortcode::read_counter( $tag ) ? esc_attr( ' active' ) : '' ) . '">';

		$o .= '<img src="' . esc_url( $atts['image'] ) . '" alt="' . esc_attr( $atts['title'] ) . '">';

		$o .= '<div class="carousel-caption">';

			$o .= '<h3>' . esc_html( $atts['title'] ) . '</h3>';
			$o .= wpautop( wp_kses_post( $content ) );

		$o .= '</div>';

	$o .= '</div>';

	return $o;
}

/**
 * Shortcode Definitions Callback
 */
function define_media_shortcodes( $manager ) {

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	/********************************************************************************
	 * Media category
	 ********************************************************************************/
	$manager->add_category( 'media', array(
		'label' => __( 'Media Shortcodes', 'youxi' ), 
		'priority' => 40
	));

	/********************************************************************************
	 * WordPress audio shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'audio', array(
		'label' => __( 'Audio', 'youxi' ), 
		'category' => 'media', 
		'priority' => 0, 
		'icon' => 'fa fa-music', 
		'third_party' => true, 
		'atts' => array(
			'src' => array(
				'type' => 'upload', 
				'label' => __( 'Source', 'youxi' ), 
				'description' => __( 'Choose here the audio source.', 'youxi' ), 
				'always_return_url' => true, 
				'library_type' => 'audio'
			), 
			'loop' => array(
				'type' => 'switch', 
				'label' => __( 'Loop', 'youxi' ), 
				'description' => __( 'Switch whether the audio will start over again, every time it is finished.', 'youxi' )
			), 
			'autoplay' => array(
				'type' => 'switch', 
				'label' => __( 'Autoplay', 'youxi' ), 
				'description' => __( 'Switch whether the audio will start playing as soon as it is ready.', 'youxi' )
			), 
			'preload' => array(
				'type' => 'select', 
				'label' => __( 'Preload', 'youxi' ), 
				'description' => __( 'Choose part of the audio to preload when the page loads.', 'youxi' ), 
				'choices' => array(
					'none' => __( 'None', 'youxi' ), 
					'auto' => __( 'Auto', 'youxi' ), 
					'metadata' => __( 'Metadata', 'youxi' )
				), 
				'std' => 'none'
			)
		)
	));

	/********************************************************************************
	 * Embed Shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'embed', array(
		'label' => __( 'Embed', 'youxi' ), 
		'category' => 'media', 
		'priority' => 10, 
		'icon' => 'fa fa-code', 
		'insert_nl' => false, 
		'third_party' => true, 
		'content' => array(
			'type' => 'text', 
			'label' => __( 'Embed URL', 'youxi' ), 
			'description' => __( 'Enter here the embed URL. See <a href="http://codex.wordpress.org/Embeds" target="_blank">http://codex.wordpress.org/Embeds</a> for a list of supported providers.', 'youxi' )
		)
	));

	/********************************************************************************
	 * Google Maps shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'google_map', array(
		'label' => __( 'Google Map', 'youxi' ), 
		'category' => 'media', 
		'priority' => 20, 
		'icon' => 'fa fa-map-marker', 
		'scripts' => array(
			'gmap3' => array(
				'src' => YOUXI_SHORTCODE_URL . "frontend/plugins/gmap/gmap3{$suffix}.js", 
				'deps' => array( 'jquery' ), 
				'ver' => '6.0.0', 
				'in_footer' => true
			), 
			'youxi-gmap' => array(
				'src' => YOUXI_SHORTCODE_URL . "frontend/assets/js/youxi.gmap{$suffix}.js", 
				'deps' => array( 'gmap3' ), 
				'ver' => '1.0', 
				'in_footer' => true
			)
		), 
		'atts' => array(
			'center_lat' => array(
				'type' => 'text', 
				'label' => __( 'Center Latitude', 'youxi' ), 
				'description' => __( 'Enter here the map center latitude.', 'youxi' ), 
				'std' => 0
			), 
			'center_lng' => array(
				'type' => 'text', 
				'label' => __( 'Center Longitude', 'youxi' ), 
				'description' => __( 'Enter here the map center longitude.', 'youxi' ), 
				'std' => 0
			), 
			'zoom' => array(
				'type' => 'uispinner', 
				'label' => __( 'Zoom', 'youxi' ), 
				'description' => __( 'Enter here the map zoom level.', 'youxi' ), 
				'widgetopts' => array(
					'min' => 0, 
					'max' => 20, 
					'step' => 1
				), 
				'std' => 0
			), 
			'map_type' => array(
				'type' => 'select', 
				'label' => __( 'Map Type', 'youxi' ), 
				'description' => __( 'Choose here the map type.', 'youxi' ), 
				'std' => 'ROADMAP', 
				'choices' => array(
					'HYBRID' => 'HYBRID', 
					'ROADMAP' => 'ROADMAP', 
					'SATELLITE' => 'SATELLITE', 
					'TERRAIN' => 'TERRAIN'
				)
			), 
			'aspect_ratio' => array(
				'type' => 'tabular', 
				'label' => __( 'Aspect Ratio', 'youxi' ), 
				'description' => __( 'Enter here the aspect ratio of the map.', 'youxi' ), 
				'columns' => array( __( 'Width', 'youxi' ), __( 'Height', 'youxi' ) ), 
				'rows' => 1, 
				'mode' => 'text', 
				'std' => array(
					'cells' => array(
						array( 16, 9 )
					)
				), 
				'serialize' => 'js:function( data ) {
					if( _.has( data, "cells" ) && data.cells.length && data.cells[0].length > 1 ) {
						data = data.cells[0][0] + ":" + data.cells[0][1];
					}
					return data;
				}', 
				'deserialize' => 'js:function( data ) {
					var splitted = ( data + "" ).split( ":" );
					if( splitted.length >= 2 ) {
						data = {
							cells: [[ splitted[0], splitted[1] ]]
						}
					}
					return data;
				}'
			), 
			'monochrome' => array(
				'type' => 'switch', 
				'label' => __( 'Monochrome', 'youxi' ), 
				'description' => __( 'Switch to display the google map in black and white.', 'youxi' ), 
				'std' => false
			), 
			'scrollwheel' => array(
				'type' => 'switch', 
				'label' => __( 'Enable Scrollwheel', 'youxi' ), 
				'description' => __( 'Switch whether the map should react to mouse scrolwheel events.', 'youxi' ), 
				'std' => false
			), 
			'controls' => array(
				'type' => 'checkboxlist', 
				'label' => __( 'Controls', 'youxi' ), 
				'description' => __( 'Choose here the controls to display on the map.', 'youxi' ), 
				'choices' => array(
					'pan' => __( 'Pan', 'youxi' ), 
					'zoom' => __( 'Zoom', 'youxi' ), 
					'map-type' => __( 'Map Type', 'youxi' ), 
					'scale' => __( 'Scale', 'youxi' ), 
					'street-view' => __( 'Street View', 'youxi' ), 
					'overview-map' => __( 'Overview Map', 'youxi' )
				), 
				'std' => array(), 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join(",");
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split(",");
				}'
			), 
			'markers' => array(
				'type' => 'repeater', 
				'label' => __( 'Markers', 'youxi' ), 
				'description' => __( 'Enter here the markers to display on the map.', 'youxi' ), 
				'min' => 0, 
				'fields' => array(
					'lat' => array(
						'type' => 'text', 
						'label' => __( 'Latitude', 'youxi' ), 
						'description' => __( 'Enter here the marker\'s latitude.', 'youxi' )
					), 
					'lng' => array(
						'type' => 'text', 
						'label' => __( 'Longitude', 'youxi' ), 
						'description' => __( 'Enter here the marker\'s longitude.', 'youxi' )
					)
				), 
				'preview_template' => '{{ data.lat }}, {{ data.lng }}', 
				'serialize' => 'js:function( data ) {
					return $.map( data, function( data ) {
						if( _.has( data, "lat" ) && _.has( data, "lng" ) ) {
							return "(" + data.lat + "," + data.lng + ")";
						}
					}).join( "," );
				}', 
				'deserialize' => 'js:function( data ) {
					var matches = [], 
						match, 
						regex = /\((.+?),(.+?)\)/g, 
						string = $.trim( data );

					while( match = regex.exec( string ) ) {
						matches.push({
							lat: match[1] || 0.0, 
							lng: match[2] || 0.0
						});
					}
					return matches;
				}'
			)
		), 
		'callback' => 'youxi_shortcode_google_map_cb'
	));

	/********************************************************************************
	 * Slider shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'slider', array(
		'label' => __( 'Slider', 'youxi' ), 
		'category' => 'media', 
		'priority' => 30, 
		'icon' => 'fa fa-photo', 
		'atts' => array(
			'controls' => array(
				'type' => 'checkboxlist', 
				'label' => __( 'Controls', 'youxi' ), 
				'description' => __( 'Choose here the slider controls to display.', 'youxi' ), 
				'choices' => array(
					'pagers' => __( 'Pagers', 'youxi' ), 
					'arrows' => __( 'Arrows', 'youxi' )
				), 
				'std' => array( 'pagers' , 'arrows' ), 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join(",");
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split(",");
				}'
			), 
			'interval' => array(
				'type' => 'uislider', 
				'label' => __( 'Interval', 'youxi' ), 
				'description' => __( 'Enter the amount of time to delay between automatically cycling an item.', 'youxi' ), 
				'std' => 500, 
				'widgetopts' => array(
					'min' => 100, 
					'max' => 4000, 
					'step' => 100
				)
			), 
			'behaviors' => array(
				'type' => 'checkboxlist', 
				'label' => __( 'Behaviors', 'youxi' ), 
				'description' => __( 'Choose here the behaviors of the slider.', 'youxi' ), 
				'choices' => array(
					'pause' => __( 'Pause on Hover', 'youxi' ), 
					'wrap'  => __( 'Allow Wrapping', 'youxi' )
				), 
				'std' => array( 'wrap' ), 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join(",");
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split(",");
				}'
			)
		), 
		'content' => array(
			'type' => 'repeater', 
			'label' => __( 'Content', 'youxi' ), 
			'description' => __( 'Enter here the slider contents.', 'youxi' ), 
			'min' => 0, 
			'fields' => array( array( $manager, 'get_shortcode_fields' ), 'slide' ), 
			'preview_template' => '{{ data.title }}', 
			'serialize' => 'js:function( data ) {
				return this.construct( "slide", data );
			}', 
			'deserialize' => 'js:function( data ) {
				return this.deserializeArray( data );
			}'
		), 
		'callback' => 'youxi_shortcode_slider_cb'
	));
	$manager->add_shortcode( 'slide', array(
		'label' => __( 'Slide', 'youxi' ), 
		'category' => 'media', 
		'internal' => true, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => __( 'Title', 'youxi' ), 
				'title' => __( 'Enter here the title of the slide.', 'youxi' )
			), 
			'image' => array(
				'type' => 'image', 
				'label' => __( 'Image', 'youxi' ), 
				'description' => __( 'Choose an image for the slide.', 'youxi' ), 
				'return_type' => 'url', 
				'frame_title' => __( 'Choose an Image', 'youxi' ), 
				'frame_btn_text' => __( 'Insert URL', 'youxi' ), 
				'upload_btn_text' => __( 'Choose an Image', 'youxi' )
			), 
			'content' => array(
				'type' => 'text', 
				'label' => __( 'Caption', 'youxi' ), 
				'caption' => __( 'Enter here the caption of the slide.', 'youxi' )
			)
		), 	
		'callback' => 'youxi_shortcode_slide_cb'
	));

	/********************************************************************************
	 * Twitter shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'twitter', array(
		'label' => __( 'Twitter', 'youxi' ), 
		'category' => 'media', 
		'priority' => 40, 
		'icon' => 'fa fa-twitter', 
		'atts' => array(
			'username' => array(
				'type' => 'text', 
				'label' => __( 'Username', 'youxi' ), 
				'description' => __( 'Enter here the Twitter username.', 'youxi' )
			), 
			'count' => array(
				'type' => 'uislider', 
				'label' => __( 'Tweets Count', 'youxi' ), 
				'description' => __( 'Specify here the number of tweets to display.', 'youxi' ), 
				'widgetopts' => array(
					'min' => 1, 
					'max' => 10, 
					'step' => 1
				), 
				'std' => 3
			)
		), 

		// Must be overriden as Twitter needs API keys configuration
		'callback' => '__return_empty_string'
	));

	/********************************************************************************
	 * WordPress video shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'video', array(
		'label' => __( 'Video', 'youxi' ), 
		'category' => 'media', 
		'priority' => 50, 
		'icon' => 'fa fa-video-camera', 
		'third_party' => true, 
		'atts' => array(
			'src' => array(
				'type' => 'upload', 
				'label' => __( 'Source', 'youxi' ), 
				'description' => __( 'Choose here the video source.', 'youxi' ), 
				'always_return_url' => true, 
				'library_type' => 'video'
			), 
			'poster' => array(
				'type' => 'image', 
				'label' => __( 'Poster', 'youxi' ), 
				'description' => __( 'Choose an image to be shown while the video is downloading.', 'youxi' ), 
				'always_return_url' => true
			), 
			'loop' => array(
				'type' => 'switch', 
				'label' => __( 'Loop', 'youxi' ), 
				'description' => __( 'Switch whether the video will start over again, every time it is finished.', 'youxi' )
			), 
			'autoplay' => array(
				'type' => 'switch', 
				'label' => __( 'Autoplay', 'youxi' ), 
				'description' => __( 'Switch whether the video will start playing as soon as it is ready.', 'youxi' )
			), 
			'preload' => array(
				'type' => 'select', 
				'label' => __( 'Preload', 'youxi' ), 
				'description' => __( 'Choose part of the video to preload when the page loads.', 'youxi' ), 
				'choices' => array(
					'none' => __( 'None', 'youxi' ), 
					'auto' => __( 'Auto', 'youxi' ), 
					'metadata' => __( 'Metadata', 'youxi' )
				), 
				'std' => 'metadata'
			)
		)
	));
}

/**
 * Hook to 'youxi_shortcode_register'
 */
add_action( 'youxi_shortcode_register', 'define_media_shortcodes', 1 );