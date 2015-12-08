<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Entry Pagination
============================================================================= */

if( ! function_exists( 'helium_entry_pagination' ) ):

function helium_entry_pagination( $pagination_type = 'numbered', $query = null ) {

	$before = '<div class="content-box clearfix">';
	$before .= '<div class="content-wrap-inner no-padding">';
	$before .= '<nav class="content-nav" role="navigation" itemscope itemtype="http://schema.org/SiteNavigationElement">';

	$after = '</nav></div></div>';

	if( 'infinite' == $pagination_type || 'ajax' == $pagination_type || 'numbered' == $pagination_type ) {

		echo Youxi()->pagination->paginate_links( $before, $after, array(
			'list_class'         => 'plain-list', 
			'list_item_class'    => 'content-nav-link', 
			'show_all'           => ( 'infinite' == $pagination_type || 'ajax' == $pagination_type ), 
			'prev_text'          => '<span class="content-nav-link-wrap"><i class="fa fa-chevron-left"></i></span>', 
			'next_text'          => '<span class="content-nav-link-wrap"><i class="fa fa-chevron-right"></i></span>', 
			'before_page_number' => '<span class="content-nav-link-wrap">', 
			'after_page_number'  => '</span>'
		), $query );

	} else {

		$next_posts_link_label = 
			'<span class="content-nav-link-wrap">' . 
				'<span class="fa fa-chevron-left"></span>' .
				'<span class="content-nav-link-label">' . esc_html__( 'Older Posts', 'helium' ) . '</span>' . 
			'</span>';

		$previous_posts_link_label = 
			'<span class="content-nav-link-wrap">' . 
				'<span class="content-nav-link-label">' . esc_html__( 'Newer Posts', 'helium' ) . '</span>' . 
				'<span class="fa fa-chevron-right"></span>' . 
			'</span>';

		echo Youxi()->pagination->posts_link( $before, $after, array(
			'next_posts_link_label'     => $next_posts_link_label, 
			'previous_posts_link_label' => $previous_posts_link_label, 
			'list_class'                => 'plain-list', 
			'list_item_class'           => 'content-nav-link', 
		), $query );

	}
}
endif;

/* ==========================================================================
	http://schema.org integration for `comments_popup_link`
============================================================================= */

if( ! function_exists( 'helium_comments_popup_link_attributes' ) ):

function helium_comments_popup_link_attributes( $attributes ) {
	return 'itemprop="discussionUrl"';
}
endif;
add_filter( 'comments_popup_link_attributes', 'helium_comments_popup_link_attributes' );
