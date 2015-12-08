<?php

// Make sure the plugin is active
if( ! defined( 'YOUXI_POST_FORMAT_VERSION' ) ) {
	return;
}

if( ! function_exists( 'helium_youxi_post_format_gallery_metabox' ) ):

function helium_youxi_post_format_gallery_metabox( $metabox ) {

	if( is_array( $metabox ) && isset( $metabox['fields'] ) ) {
		$metabox['fields'] = array_merge( array(
			'type' => array(
				'type' => 'radio', 
				'label' => esc_html__( 'Gallery Type', 'helium' ), 
				'description' => esc_html__( 'Choose the gallery type for this post.', 'helium' ), 
				'choices' => array(
					'slider' => esc_html__( 'Slider', 'helium' ), 
					'justified' => esc_html__( 'Justified Gallery', 'helium' )
				), 
				'std' => 'slider'
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
			)
		), $metabox['fields'] );
	}

	return $metabox;
}
endif;
add_filter( 'youxi_post_format_gallery_metabox', 'helium_youxi_post_format_gallery_metabox' );