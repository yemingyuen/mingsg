<?php

// Make sure the plugin is active
if( ! defined( 'YOUXI_PORTFOLIO_VERSION' ) ) {
	add_filter( 'theme_page_templates', 'helium_remove_portfolio_page_template' );
	return;
}

/* ==========================================================================
	Modify portfolio slug
============================================================================= */

if( ! function_exists( 'helium_modify_portfolio_slug' ) ):

function helium_modify_portfolio_slug( $args ) {

	$slug = trim( Youxi()->option->get( 'portfolio_slug' ) );

	if( $slug && $slug != youxi_portfolio_cpt_name() ) {
		$args['query_var'] = $slug;
		$args['rewrite']   = compact( 'slug' );
	}

	return $args;
}
endif;
add_filter( 'youxi_portfolio_cpt_args', 'helium_modify_portfolio_slug' );

/* ==========================================================================
	Add Comments Support
============================================================================= */

if( ! function_exists( 'helium_portfolio_supports' ) ):

function helium_portfolio_supports( $args ) {

	if( isset( $args['supports'] ) && is_array( $args['supports'] ) ) {
		$args['supports'][] = 'comments';
	}

	return $args;
}
endif;
add_filter( 'youxi_portfolio_cpt_args', 'helium_portfolio_supports' );

/* ==========================================================================
	Portfolio General
============================================================================= */

if( ! function_exists( 'helium_modify_portfolio_tax' ) ):

function helium_modify_portfolio_tax( $taxonomies ) {
	if( isset( $taxonomies[ youxi_portfolio_tax_name() ] ) ) {
		$taxonomies[ youxi_portfolio_tax_name() ]['hierarchical'] = true;
	}
	return $taxonomies;
}
endif;
add_filter( 'youxi_portfolio_cpt_taxonomies', 'helium_modify_portfolio_tax' );

/**
 * Portfolio Metaboxes
 */
if( ! function_exists( 'helium_youxi_portfolio_cpt_metaboxes' ) ):

	function helium_youxi_portfolio_cpt_metaboxes( $metaboxes ) {

		$metaboxes['general'] = array(
			'title' => esc_html__( 'General', 'helium' ), 
			'fields' => array(
				'url' => array(
					'type' => 'url', 
					'label' => esc_html__( 'URL', 'helium' ), 
					'description' => esc_html__( 'Enter here the portfolio URL.', 'helium' ), 
					'show_admin_column' => true, 
					'std' => ''
				), 
				'client' => array(
					'type' => 'text', 
					'label' => esc_html__( 'Client', 'helium' ), 
					'description' => esc_html__( 'Enter here the portfolio client.', 'helium' ), 
					'std' => ''
				), 
				'client_url' => array(
					'type' => 'text', 
					'label' => esc_html__( 'Client URL', 'helium' ), 
					'description' => esc_html__( 'Enter here the portfolio client url.', 'helium' ), 
					'std' => ''
				), 
				'featured' => array(
					'type' => 'switch', 
					'label' => esc_html__( 'Featured', 'helium' ), 
					'description' => esc_html__( 'Switch to mark the portfolio as featured and show it on the portfolio slider.', 'helium' ), 
					'std' => false, 
					'scalar' => true
				)
			)
		);

		$metaboxes['layout'] = array(
			'title' => esc_html__( 'Layout', 'helium' ), 
			'fields' => array(
				'show_title' => array(
					'type' => 'switch', 
					'label' => esc_html__( 'Show Title', 'helium' ), 
					'description' => esc_html__( 'Switch to show/hide the portfolio title before the content.', 'helium' ), 
					'std' => true
				), 
				'page_layout' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Portfolio Page Layout', 'helium' ), 
					'description' => esc_html__( 'Specify the layout of the portfolio page.', 'helium' ), 
					'choices' => array(
						'boxed' => esc_html__( 'Boxed', 'helium' ), 
						'fullwidth' => esc_html__( 'Fullwidth', 'helium' )
					), 
					'std' => 'boxed', 
					'scalar' => true
				), 
				'archive_page' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Portfolio Archive Page', 'helium' ), 
					'description' => esc_html__( 'Choose the archive page of this portfolio item.', 'helium' ), 
					'choices' => 'helium_portfolio_pages', 
					'std' => 0, 
					'scalar' => true
				), 
				'media_position' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Portfolio Media Layout', 'helium' ), 
					'description' => esc_html__( 'Specify how the portfolio media is displayed.', 'helium' ), 
					'choices' => array(
						'top' => esc_html__( 'Top', 'helium' ), 
						'left' => esc_html__( 'Left', 'helium' ), 
						'right' => esc_html__( 'Right', 'helium' )
					), 
					'std' => 'top'
				), 
				'details_position' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Portfolio Details Layout', 'helium' ), 
					'description' => esc_html__( 'Specify how the portfolio details is displayed. Choosing left or right for media position will move the details to the bottom.', 'helium' ), 
					'choices' => array(
						'hidden' => esc_html__( 'Hidden', 'helium' ), 
						'left' => esc_html__( 'Left', 'helium' ), 
						'right' => esc_html__( 'Right', 'helium' )
					), 
					'std' => 'left'
				), 
				'details' => array(
					'type' => 'repeater', 
					'label' => esc_html__( 'Details', 'helium' ), 
					'description' => esc_html__( 'Specify the portfolio details to show.', 'helium' ), 
					'fields' => array(
						'type' => array(
							'type' => 'select', 
							'label' => esc_html__( 'Detail Type', 'helium' ), 
							'description' => esc_html__( 'Choose the portfolio detail type.', 'helium' ), 
							'choices' => array(
								'categories' => esc_html__( 'Categories', 'helium' ), 
								'url' => esc_html__( 'URL', 'helium' ), 
								'client' => esc_html__( 'Client', 'helium' ), 
								'share' => esc_html__( 'Share', 'helium' ), 
								'custom' => esc_html__( 'Custom', 'helium' )
							), 
							'std' => 'custom'
						), 
						'label' => array(
							'type' => 'text', 
							'label' => esc_html__( 'Label', 'helium' ), 
							'description' => esc_html__( 'Enter the detail label.', 'helium' ), 
							'std' => '', 
						), 
						'custom_value' => array(
							'type' => 'textarea', 
							'label' => esc_html__( 'Custom Value', 'helium' ), 
							'description' => esc_html__( 'Enter the custom detail value.', 'helium' ), 
							'std' => '', 
							'criteria' => 'type:is(custom)'
						)
					), 
					'min' => 0, 
					'preview_template' => '{{ data.label }}', 
					'std' => '', 
					'criteria' => 'details_position:not(hidden)'
				)
			)
		);

		$metaboxes['media'] = array(
			'title' => esc_html__( 'Media', 'helium' ), 
			'fields' => array(
				'type' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Media Type', 'helium' ), 
					'description' => esc_html__( 'Choose the type of media to display.', 'helium' ), 
					'choices' => array(
						'featured-image' => esc_html__( 'Featured Image', 'helium' ), 
						'stacked' => esc_html__( 'Stacked Images', 'helium' ), 
						'slider' => esc_html__( 'Slider', 'helium' ), 
						'justified-grids' => esc_html__( 'Justified Grids', 'helium' ), 
						'video' => esc_html__( 'Video', 'helium' ), 
						'audio' => esc_html__( 'Audio', 'helium' )
					), 
					'std' => 'featured-image'
				), 
				'autoHeight' => array(
					'type' => 'switch', 
					'label' => esc_html__( 'Slider: Auto Height', 'helium' ), 
					'description' => esc_html__( 'Switch to automatically update slider height based on each slide.', 'helium' ), 
					'std' => true, 
					'criteria' => 'type:is(slider)'
				), 
				'autoScaleSliderRatio' => array(
					'type' => 'aspect-ratio', 
					'label' => esc_html__( 'Slider: Aspect Ratio', 'helium' ), 
					'description' => esc_html__( 'Specify the slider aspect ratio when auto height is disabled.', 'helium' ), 
					'std' => array( 'width' => 4, 'height' => 3 ), 
					'criteria' => 'type:is(slider),autoHeight:is(0)'
				), 
				'imageScaleMode' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Slider: Image Scale Mode', 'helium' ), 
					'description' => esc_html__( 'Specify the slider image scaling mode.', 'helium' ), 
					'choices' => array(
						'fill' => esc_html__( 'Fill', 'helium' ), 
						'fit' => esc_html__( 'Fit', 'helium' )
					), 
					'std' => 'fill', 
					'criteria' => 'type:is(slider),autoHeight:is(0)'
				), 
				'controlNavigation' => array(
					'type' => 'switch', 
					'label' => esc_html__( 'Slider: Navigation Bullets', 'helium' ), 
					'description' => esc_html__( 'Switch to toggle the slider navigation bullets.', 'helium' ), 
					'std' => true, 
					'criteria' => 'type:is(slider)'
				), 
				'arrowsNav' => array(
					'type' => 'switch', 
					'label' => esc_html__( 'Slider: Navigation Arrows', 'helium' ), 
					'description' => esc_html__( 'Switch to toggle the slider navigation arrows.', 'helium' ), 
					'std' => true, 
					'criteria' => 'type:is(slider)'
				), 
				'loop' => array(
					'type' => 'switch', 
					'label' => esc_html__( 'Slider: Loop', 'helium' ), 
					'description' => esc_html__( 'Switch to allow the slider to go to the first from the last slide.', 'helium' ), 
					'std' => false, 
					'criteria' => 'type:is(slider)'
				), 
				'slidesOrientation' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Slider: Orientation', 'helium' ), 
					'description' => esc_html__( 'Specify the slider orientation.', 'helium' ), 
					'choices' => array(
						'vertical' => esc_html__( 'Vertical', 'helium' ), 
						'horizontal' => esc_html__( 'Horizontal', 'helium' )
					), 
					'std' => 'horizontal', 
					'criteria' => 'type:is(slider),autoHeight:is(0)'
				), 
				'transitionType' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Slider: Transition Type', 'helium' ), 
					'description' => esc_html__( 'Specify the slider transition type.', 'helium' ), 
					'choices' => array(
						'move' => esc_html__( 'Move', 'helium' ), 
						'fade' => esc_html__( 'Fade', 'helium' )
					), 
					'std' => 'move', 
					'criteria' => 'type:is(slider)'
				), 
				'transitionSpeed' => array(
					'type' => 'uislider', 
					'label' => esc_html__( 'Slider: Transition Speed', 'helium' ), 
					'description' => esc_html__( 'Specify the slider transition speed.', 'helium' ), 
					'widgetopts' => array(
						'min' => 100, 
						'max' => 5000, 
						'step' => 10
					), 
					'std' => 600, 
					'criteria' => 'type:is(slider)'
				), 
				'images' => array(
					'type' => 'gallery', 
					'label' => esc_html__( 'Images', 'helium' ), 
					'description' => esc_html__( 'Choose here the images to use.', 'helium' ), 
					'multiple' => 'add', 
					'criteria' => 'type:not(featured-image),type:not(video),type:not(audio)'
				), 
				'video_type' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Video Type', 'helium' ), 
					'description' => esc_html__( 'Choose here the video type.', 'helium' ), 
					'choices' => array(
						'embed' => esc_html__( 'Embedded (YouTube/Vimeo)', 'helium' ), 
						'hosted' => esc_html__( 'Hosted', 'helium' )
					), 
					'std' => 'hosted', 
					'criteria' => 'type:is(video)'
				), 
				'video_embed' => array(
					'type' => 'textarea', 
					'label' => esc_html__( 'Video Embed Code (YouTube/Vimeo)', 'helium' ), 
					'description' => esc_html__( 'Enter here the video embed code (YouTube/Vimeo).', 'helium' ), 
					'std' => '', 
					'criteria' => 'type:is(video),video_type:is(embed)'
				), 
				'video_src' => array(
					'type' => 'upload', 
					'label' => esc_html__( 'Video Source', 'helium' ), 
					'library_type' => 'video', 
					'description' => esc_html__( 'Choose here the hosted video source.', 'helium' ), 
					'criteria' => 'type:is(video),video_type:is(hosted)'
				), 
				'video_poster' => array(
					'type' => 'image', 
					'multiple' => false, 
					'label' => esc_html__( 'Video Poster', 'helium' ), 
					'description' => esc_html__( 'Upload here an image that will be used either as the poster or fallback for unsupported devices.', 'helium' ), 
					'criteria' => 'type:is(video),video_type:is(hosted)'
				), 
				'audio_type' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Audio Type', 'helium' ), 
					'description' => esc_html__( 'Choose here the audio type.', 'helium' ), 
					'choices' => array(
						'embed' => esc_html__( 'Embedded (SoundCloud)', 'helium' ), 
						'hosted' => esc_html__( 'Hosted', 'helium' )
					), 
					'std' => 'hosted', 
					'criteria' => 'type:is(audio)'
				), 
				'audio_embed' => array(
					'type' => 'textarea', 
					'label' => esc_html__( 'Embed Code (SoundCloud)', 'helium' ), 
					'description' => esc_html__( 'Enter here the audio embed code (SoundCloud).', 'helium' ), 
					'std' => '', 
					'criteria' => 'type:is(audio),audio_type:is(embed)'
				), 
				'audio_src' => array(
					'type' => 'upload', 
					'label' => esc_html__( 'Audio Source', 'helium' ), 
					'library_type' => 'audio', 
					'description' => esc_html__( 'Choose here the hosted audio source.', 'helium' ), 
					'criteria' => 'type:is(audio),audio_type:is(hosted)'
				)
			)
		);

		return $metaboxes;
	}
endif;
add_filter( 'youxi_portfolio_cpt_metaboxes', 'helium_youxi_portfolio_cpt_metaboxes' );

/* ==========================================================================
	Add portfolio archive metabox to pages
============================================================================= */

if( ! function_exists( 'helium_add_portfolio_metabox' ) ) {

	function helium_add_portfolio_metabox() {

		$metaboxes = array();

		/* Portfolio Archive Page Template */
		$metaboxes['portfolio_grid_settings'] = array(

			'title' => esc_html__( 'Page Template: Portfolio', 'helium' ), 

			'page_template' => 'archive-portfolio', 

			'fields' => array(
				'use_defaults' => array(
					'type' => 'switch', 
					'label' => esc_html__( 'Use Default Settings', 'helium' ), 
					'description' => esc_html__( 'Switch to use the default portfolio grid settings.', 'helium' ), 
					'std' => false
				), 
				'show_filter' => array(
					'type' => 'switch', 
					'label' => esc_html__( 'Show Filter', 'helium' ), 
					'description' => esc_html__( 'Switch to display the portfolio filter.', 'helium' ), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => true
				), 
				'pagination' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Pagination Type', 'helium' ), 
					'description' => esc_html__( 'Specify the portfolio pagination type.', 'helium' ), 
					'choices' => array(
						'ajax' => esc_html__( 'AJAX', 'helium' ), 
						'infinite' => esc_html__( 'Infinite', 'helium' ), 
						'numbered' => esc_html__( 'Numbered', 'helium' ), 
						'prev_next' => esc_html__( 'Prev/Next', 'helium' ), 
						'show_all' => esc_html__( 'None (Show all)', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => 'ajax'
				), 
				'ajax_button_text' => array(
					'type' => 'text', 
					'label' => esc_html__( 'AJAX Button Text', 'helium' ), 
					'description' => esc_html__( 'Specify the text to display on the AJAX load more button.', 'helium' ), 
					'std' => 'Load More', 
					'criteria' => 'pagination:is(ajax),use_defaults:is(0)'
				), 
				'ajax_button_complete_text' => array(
					'type' => 'text', 
					'label' => esc_html__( 'AJAX Button Complete Text', 'helium' ), 
					'description' => esc_html__( 'Specify the text to display on the AJAX load more button when there are no more items to load.', 'helium' ), 
					'std' => 'No More Items', 
					'criteria' => 'pagination:is(ajax),use_defaults:is(0)'
				), 
				'posts_per_page' => array(
					'type' => 'uislider', 
					'label' => esc_html__( 'Posts Per Page', 'helium' ), 
					'description' => esc_html__( 'Specify how many portfolio items to show per page.', 'helium' ), 
					'widgetopts' => array(
						'min' => 1, 
						'max' => 20, 
						'step' => 1
					), 
					'criteria' => 'use_defaults:is(0),pagination:not(show_all)', 
					'std' => 10
				), 
				'include' => array(
					'type' => 'checkboxlist', 
					'label' => esc_html__( 'Included Categories', 'helium' ), 
					'description' => esc_html__( 'Specify the portfolio categories to include (leave unchecked to include all).', 'helium' ), 
					'choices' => get_terms( youxi_portfolio_tax_name(), array( 'fields' => 'id=>name', 'hide_empty' => false ) ), 
					'criteria' => 'use_defaults:is(0)'
				), 
				'behavior' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Behavior', 'helium' ), 
					'description' => esc_html__( 'Specify the behavior when clicking the thumbnail image.', 'helium' ), 
					'choices' => array(
						'none' => esc_html__( 'None', 'helium' ), 
						'lightbox' => esc_html__( 'Show Image in Lightbox', 'helium' ), 
						'page' => esc_html__( 'Go to Detail Page', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0)'
				), 
				'orderby' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Order By', 'helium' ), 
					'description' => esc_html__( 'Specify in what order the items should be displayed.', 'helium' ), 
					'choices' => array(
						'date' => esc_html__( 'Date', 'helium' ), 
						'menu_order' => esc_html__( 'Menu Order', 'helium' ), 
						'title' => esc_html__( 'Title', 'helium' ), 
						'ID' => esc_html__( 'ID', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => 'date'
				), 
				'order' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Order', 'helium' ), 
					'description' => esc_html__( 'Specify how to order the items.', 'helium' ), 
					'choices' => array(
						'DESC' => esc_html__( 'Descending', 'helium' ), 
						'ASC' => esc_html__( 'Ascending', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0),orderby:not(menu_order)', 
					'std' => 'DESC'
				), 
				'layout' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Layout', 'helium' ), 
					'description' => esc_html__( 'Specify the portfolio layout.', 'helium' ), 
					'choices' => array(
						'classic'    => esc_html__( 'Classic', 'helium' ), 
						'masonry'    => esc_html__( 'Masonry', 'helium' ), 
						'justified'  => esc_html__( 'Justified', 'helium' )
					), 
					'criteria' => 'use_defaults:is(0)', 
					'std' => 'justified'
				), 
				'columns' => array(
					'type' => 'uislider', 
					'label' => esc_html__( 'Columns', 'helium' ), 
					'description' => esc_html__( 'Specify in how many columns the items should be displayed in the masonry/classic layout.', 'helium' ), 
					'widgetopts' => array(
						'min' => 3, 
						'max' => 5, 
						'step' => 1
					), 
					'std' => 4, 
					'criteria' => 'use_defaults:is(0),layout:not(justified)'
				)
			)
		);

		$metaboxes['portfolio_slider_settings'] = array(

			'title' => esc_html__( 'Page Template: Portfolio Slider', 'helium' ), 

			'page_template' => 'page-templates/portfolio-slider', 

			'fields' => array(
				'posts_per_page' => array(
					'type' => 'uislider', 
					'label' => esc_html__( 'Number of Slides', 'helium' ), 
					'description' => esc_html__( 'Specify how many portfolio items to show on the slider.', 'helium' ), 
					'widgetopts' => array(
						'min' => 1, 
						'max' => 10, 
						'step' => 1
					), 
					'std' => 5
				), 
				'orderby' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Order By', 'helium' ), 
					'description' => esc_html__( 'Specify in what order the items should be displayed.', 'helium' ), 
					'choices' => array(
						'date' => esc_html__( 'Date', 'helium' ), 
						'menu_order' => esc_html__( 'Menu Order', 'helium' ), 
						'title' => esc_html__( 'Title', 'helium' ), 
						'ID' => esc_html__( 'ID', 'helium' ), 
						'rand' => esc_html__( 'Random', 'helium' )
					), 
					'std' => 'date'
				), 
				'order' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Order', 'helium' ), 
					'description' => esc_html__( 'Specify how to order the items.', 'helium' ), 
					'choices' => array(
						'DESC' => esc_html__( 'Descending', 'helium' ), 
						'ASC' => esc_html__( 'Ascending', 'helium' )
					), 
					'std' => 'DESC', 
					'criteria' => 'orderby:not(menu_order)'
				)
			)
		);

		/* Create the 'page' post type object */
		$post_type_object = Youxi_Post_Type::get( 'page' );

		/* Add the metaboxes */
		foreach( $metaboxes as $metabox_id => $metabox ) {
			$post_type_object->add_meta_box( new Youxi_Metabox( $metabox_id, $metabox ) );
		}
	}
}
add_action( 'init', 'helium_add_portfolio_metabox' );

/* ==========================================================================
	Portfolio Shortcode
============================================================================= */

add_filter( 'youxi_portfolio_register_shortcode', '__return_false' );

/* ==========================================================================
	Remove Portfolio Page Template
============================================================================= */

function helium_remove_portfolio_page_template( $templates ) {
	unset( $templates['archive-portfolio.php'] );
	unset( $templates['page-templates/portfolio-slider.php'] );
	return $templates;
}
