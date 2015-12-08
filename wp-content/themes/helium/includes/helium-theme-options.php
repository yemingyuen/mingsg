<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Theme options
============================================================================= */

function helium_option_defaults() {
	return array(
		'logo_image' => '', 
		'logo_height' => 25, 
		'show_search' => true, 
		'copyright_text' => esc_html__( '&copy; Youxi Themes. 2012-2014. All Rights Reserved.', 'helium' ), 
		'accent_color' => '#3dc9b3', 
		'body_font' => '', 
		'headings_1234_font' => '', 
		'headings_56_font' => '', 
		'menu_font' => '', 
		'blockquote_font' => '', 
		'gridlist_filter_font' => '', 
		'gridlist_title_font' => '', 
		'gridlist_subtitle_font' => '', 
		'content_title_font' => '', 
		'content_nav_font' => '', 
		'widget_title_font' => '', 
		'hidden_post_meta' => array(), 
		'blog_show_tags' => true, 
		'blog_sharing' => true, 
		'blog_show_author' => true, 
		'blog_related_posts' => true, 
		'blog_related_posts_count' => 3, 
		'blog_related_posts_behavior' => 'lightbox', 
		'blog_summary' => 'the_excerpt', 
		'blog_excerpt_length' => 100, 
		'blog_index_layout' => 'boxed', 
		'blog_archive_layout' => 'boxed', 
		'blog_single_layout' => 'boxed', 
		'blog_index_title' => esc_html__( 'Welcome to Our Blog', 'helium' ), 
		'blog_single_title' => esc_html__( 'Currently Reading', 'helium' ), 
		'blog_category_title' => esc_html__( 'Category: {category}', 'helium' ), 
		'blog_tag_title' => esc_html__( 'Posts Tagged &lsquo;{tag}&rsquo;', 'helium' ), 
		'blog_author_title' => esc_html__( 'Posts by {author}', 'helium' ), 
		'blog_date_title' => esc_html__( 'Archive for {date}', 'helium' ), 
		'portfolio_show_related_items' => true, 
		'portfolio_related_items_count' => 3, 
		'portfolio_related_items_behavior' => 'lightbox', 
		'portfolio_archive_page_title' => esc_html__( 'Portfolio Archive', 'helium' ), 
		'portfolio_grid_show_filter' => true, 
		'portfolio_grid_pagination' => 'ajax', 
		'portfolio_grid_ajax_button_text' => esc_html__( 'Load More', 'helium' ), 
		'portfolio_grid_ajax_button_complete_text' => esc_html__( 'No More Items', 'helium' ), 
		'portfolio_grid_posts_per_page' => 10, 
		'portfolio_grid_include' => array(), 
		'portfolio_grid_behavior' => 'lightbox', 
		'portfolio_grid_orderby' => 'date', 
		'portfolio_grid_order' => 'DESC', 
		'portfolio_grid_layout' => 'masonry', 
		'portfolio_grid_columns' => 4, 
		'edd_show_cart' => true, 
		'edd_show_categories' => true, 
		'edd_show_tags' => true, 
		'edd_show_sharing_buttons' => true, 
		'edd_show_related_items' => true, 
		'edd_related_items_count' => 3, 
		'edd_related_items_behavior' => 'lightbox', 
		'edd_archive_page_title' => esc_html__( 'Downloads Archive', 'helium' ), 
		'edd_grid_pagination' => 'ajax', 
		'edd_grid_ajax_button_text' => esc_html__( 'Load More', 'helium' ), 
		'edd_grid_ajax_button_complete_text' => esc_html__( 'No More Items', 'helium' ), 
		'edd_grid_posts_per_page' => 10, 
		'edd_grid_include' => array(), 
		'edd_grid_behavior' => 'lightbox', 
		'edd_grid_columns' => 4
	);
}
add_filter( 'youxi_option_defaults', 'helium_option_defaults' );

function helium_option_ot_keys() {
	return array(
		'addthis_sharing_buttons', 
		'addthis_profile_id', 
		'ajax_navigation', 
		'ajax_navigation_scroll_top', 
		'ajax_navigation_loading_text', 
		'ajax_exclude_urls', 
		'typekit_kit_id', 
		'typekit_cache', 
		'twitter_consumer_key', 
		'twitter_consumer_secret', 
		'twitter_access_token', 
		'twitter_access_token_secret', 
		'portfolio_slug', 
		'envato_username', 
		'envato_api_key', 
		'custom_css'
	);
}
add_filter( 'youxi_option_ot_keys', 'helium_option_ot_keys' );

function helium_option_ot_on_off() {
	return array(
		'ajax_navigation', 
		'ajax_navigation_scroll_top', 
		'typekit_cache'
	);
}
add_filter( 'youxi_option_ot_on_off', 'helium_option_ot_on_off' );
