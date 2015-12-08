<?php

// Make sure the plugin is active
if( ! defined( 'YOUXI_CORE_VERSION' ) ) {
	return;
}

/* ==========================================================================
	Add metabox to pages
============================================================================= */

if( ! function_exists( 'helium_add_page_metabox' ) ) {

	function helium_add_page_metabox() {

		$metaboxes = array();

		/* Layout */
		$metaboxes['layout'] = array(
			'title' => esc_html__( 'Layout', 'helium' ), 
			'fields' => array(
				'page_layout' => array(
					'type' => 'select', 
					'label' => esc_html__( 'Page Layout', 'helium' ), 
					'description' => esc_html__( 'Specify the layout of the page (does not have any effect on custom page templates).', 'helium' ), 
					'choices' => array(
						'fullwidth' => esc_html__( 'Fullwidth', 'helium' ), 
						'boxed' => esc_html__( 'Boxed', 'helium' )
					), 
					'std' => 'boxed'
				), 
				'wrap_content' => array(
					'type' => 'switch', 
					'label' => esc_html__( 'Wrap Content', 'helium' ), 
					'description' => esc_html__( 'Switch to automatically wrap the post content inside a container. Switch off to use advanced row layouts.', 'helium' ), 
					'std' => true
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
add_action( 'init', 'helium_add_page_metabox' );
