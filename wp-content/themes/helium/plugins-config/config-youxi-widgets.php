<?php

// Make sure the plugin is active
if( ! defined( 'YOUXI_WIDGETS_VERSION' ) ) {
	return;
}

/* ==========================================================================
	Youxi Widgets plugin config
============================================================================= */

/**
 * Disable Enqueuing Scripts
 */
add_filter( 'youxi_widgets_social-widget_enqueue_scripts', '__return_false' );
add_filter( 'youxi_widgets_google-maps-widget_enqueue_scripts', '__return_false' );
add_filter( 'youxi_widgets_allow_google-maps-widget_setup', '__return_false' );

/**
 * Disable Widgets temporarily
 */
add_filter( 'youxi_widgets_use_recent_posts', '__return_false' );
add_filter( 'youxi_widgets_use_video', '__return_false' );
add_filter( 'youxi_widgets_use_quote', '__return_false' );
add_filter( 'youxi_widgets_use_rotating_quotes', '__return_false' );

/**
 * Fetch Twitter Keys from Theme Options
 */
if( ! function_exists( 'helium_widgets_twitter_keys' ) ):

function helium_widgets_twitter_keys( $keys ) {
	return array(
		'consumer_key'       => trim( Youxi()->option->get( 'twitter_consumer_key' ) ), 
		'consumer_secret'    => trim( Youxi()->option->get( 'twitter_consumer_secret' ) ), 
		'oauth_token'        => trim( Youxi()->option->get( 'twitter_access_token' ) ), 
		'oauth_token_secret' => trim( Youxi()->option->get( 'twitter_access_token_secret' ) )
	);
}
endif;
add_filter( 'youxi_widgets_twitter_keys', 'helium_widgets_twitter_keys' );

/**
 * Set Widget Templates Directory
 */
if( ! function_exists( 'helium_widgets_template_dir' ) ):

function helium_widgets_template_dir( $path ) {
	return trailingslashit( 'widget-templates' );
}
endif;
add_filter( 'youxi_widgets_template_dir', 'helium_widgets_template_dir' );

/**
 * Recognized Social Icons
 */
if( ! function_exists( 'helium_youxi_widgets_social_icons' ) ):

function helium_youxi_widgets_social_icons( $icons ) {

	return array(
		'500px' => '500px', 
		'6' => '6', 
		'apple' => 'apple', 
		'bebo' => 'bebo', 
		'behance' => 'behance', 
		'blogger' => 'blogger', 
		'buffer' => 'buffer', 
		'chimein' => 'chimein', 
		'coderwall' => 'coderwall', 
		'dailymotion' => 'dailymotion', 
		'delicious' => 'delicious', 
		'deviantart' => 'deviantart', 
		'digg' => 'digg', 
		'disqus' => 'disqus', 
		'dribbble' => 'dribbble', 
		'envato' => 'envato', 
		'facebook' => 'facebook', 
		'feedburner' => 'feedburner', 
		'flattr' => 'flattr', 
		'flickr' => 'flickr', 
		'forrst' => 'forrst', 
		'foursquare' => 'foursquare', 
		'friendfeed' => 'friendfeed', 
		'github' => 'github', 
		'googleplus' => 'googleplus', 
		'grooveshark' => 'grooveshark', 
		'identica' => 'identica', 
		'instagram' => 'instagram', 
		'lanyrd' => 'lanyrd', 
		'lastfm' => 'lastfm', 
		'linkedin' => 'linkedin', 
		'myspace' => 'myspace', 
		'netcodes' => 'netcodes', 
		'newsvine' => 'newsvine', 
		'outlook' => 'outlook', 
		'pinterest' => 'pinterest', 
		'playstore' => 'playstore', 
		'reddit' => 'reddit', 
		'rss' => 'rss', 
		'skype' => 'skype', 
		'slideshare' => 'slideshare', 
		'soundcloud' => 'soundcloud', 
		'spotify' => 'spotify', 
		'steam' => 'steam', 
		'stumbleupon' => 'stumbleupon', 
		'technorati' => 'technorati', 
		'tripadvisor' => 'tripadvisor', 
		'tumblr' => 'tumblr', 
		'twitter' => 'twitter', 
		'viadeo' => 'viadeo', 
		'vimeo' => 'vimeo', 
		'vine' => 'vine', 
		'vkontakte' => 'vkontakte', 
		'wikipedia' => 'wikipedia', 
		'windows' => 'windows', 
		'wordpress' => 'wordpress', 
		'xbox' => 'xbox', 
		'xing' => 'xing', 
		'yahoo' => 'yahoo', 
		'yelp' => 'yelp', 
		'youtube' => 'youtube', 
		'zerply' => 'zerply', 
		'zynga' => 'zynga'
	);
}
endif;
add_filter( 'youxi_widgets_recognized_social_icons', 'helium_youxi_widgets_social_icons' );