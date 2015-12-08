<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Demo Content Importer
============================================================================= */

if( ! function_exists( 'helium_demo_content' ) ):

function helium_demo_content( $demo ) {

	return array_merge( $demo, array(
		'default' => array(
			'screenshot' => get_template_directory_uri() . '/screenshot.png', 
			'name' => esc_html__( 'Default', 'helium' ), 
			'content' => array(
				'wp' => array(
					'xml' => get_template_directory() . '/demo/helium.wordpress.2015-03-27.xml', 
					'attachments_baseurl' => 'http://pub.youxithemes.com/placeholders/helium', 
					'attachments_dir' => get_template_directory() . '/demo/attachments'
				), 
				'widgets' => '{"header_widget_area":{"text-1":{"title":"About","text":"We\u2019re Helium, a web design agency. We love design and we try to make the web a better place.","filter":false},"social-widget-1":{"title":"We\'re Social","items":[{"url":"#","title":"Facebook","icon":"facebook"},{"url":"#","title":"Twitter","icon":"twitter"},{"url":"#","title":"Google+","icon":"googleplus"},{"url":"#","title":"Pinterest","icon":"pinterest"},{"url":"#","title":"RSS","icon":"rss"}]},"flickr-widget-1":{"title":"My Flickr Feed","flickr_id":"","limit":8},"instagram-widget-1":{"title":"Instagram Feed","username":"kinfolk","count":8},"twitter-widget-1":{"title":"Recent Tweets","username":"envato","count":2}}}', 
				'frontpage_displays' => array(
					'show_on_front'  => 'page', 
					'page_on_front'  => 1411, 
					'page_for_posts' => 845
				), 
				'nav_menu_locations' => array(
					'main-menu' => 'the-menu'
				)
			)
		)
	));
}
endif;
add_filter( 'youxi_demo_importer_demos', 'helium_demo_content' );

if( ! function_exists( 'helium_demo_importer_tasks' ) ):

function helium_demo_importer_tasks( $tasks ) {
	unset( $tasks['customizer-options'], $tasks['theme-options'] );
	return $tasks;
}
endif;
add_filter( 'youxi_demo_importer_tasks', 'helium_demo_importer_tasks' );
