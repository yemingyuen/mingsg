<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Accordion Shortcode Handler
 */
function youxi_shortcode_accordion_cb( $atts, $content, $tag ) {

	/* Store accordion behavior */
	$accordion_id = Youxi_Shortcode::uniqid( $tag );
	$GLOBALS[ $accordion_id . '-behavior' ] = $atts['behavior'];

	$o = '<div class="panel-group" id="' . esc_attr( $accordion_id ) . '">';
		$o .= do_shortcode( $content );
	$o .= '</div>';

	/* Remove accordion behavior */
	unset( $GLOBALS[ $accordion_id . '-behavior' ] );

	return $o;
}

/**
 * Accordion Group Shortcode Handler
 */
function youxi_shortcode_accordion_group_cb( $atts, $content, $tag ) {

	$parent_id = Youxi_Shortcode::uniqid( 'accordion' );
	$accordion_id = Youxi_Shortcode::uniqid( $tag );

	/* Read accordion behavior */
	if( isset( $GLOBALS[ $parent_id . '-behavior' ] ) ) {
		$behavior = $GLOBALS[ $parent_id . '-behavior' ];
	}

	if( ! isset( $behavior ) || ! in_array( $behavior, array( 'accordion', 'toggle' ) ) ) {
		$behavior = 'accordion';
	}
	
	$o = '<div class="panel panel-default">';

		$o .= '<div class="panel-heading">';

			$o .= '<h4 class="panel-title">';

				$o .= '<a class="collapsed" data-toggle="collapse"' . ( 'accordion' == $behavior ? ' data-parent="#' . esc_attr( $parent_id ) . '"' : '' ) . ' href="#' . esc_attr( $accordion_id ) . '">';

				$o .= $atts['title'];

				$o .= '</a>';

			$o .= '</h4>';

		$o .= '</div>';

		$o .= '<div id="' . esc_attr( $accordion_id ) . '" class="panel-collapse collapse">';
			$o .= '<div class="panel-body">';
				$o .= wpautop( Youxi_Shortcode_Manager::get()->shortcode_unautop( do_shortcode( wp_kses_post( $content ) ) ) );
			$o .= '</div>';
		$o .= '</div>';

	$o .= '</div>';
	
	return $o;
}

/**
 * Alert Shortcode Handler
 */
function youxi_shortcode_alert_cb( $atts, $content, $tag ) {
	extract( $atts, EXTR_SKIP );

	$classes = array( 'alert' );
	if( ! empty( $type ) ) {
		$classes[] = "alert-{$type}";
	}
	
	$o = '<div class="' . join( ' ', $classes ) . '">';

		$o .= '<a class="close" data-dismiss="alert" href="#" aria-hidden="true">&times;</a>';

		if( $title ):
		$o .= '<h4>' . $title . '</h4>';
		endif;

		$o .= wp_kses_post( $content );

	$o .= '</div>';

	return $o;
}

/**
 * Call to Action Shortcode Handler
 */
function youxi_shortcode_call_to_action_cb( $atts, $content, $tag ) {
	extract( $atts, EXTR_SKIP );

	/* Compile button classes */
	$btn_classes = array( 'btn' );
	if( $btn_size ) {
		$btn_classes[] = sanitize_html_class( 'btn-' . $btn_size );
	}
	if( $btn_type ) {
		$btn_classes[] = sanitize_html_class( 'btn-' . $btn_type );
	}

	switch( $btn_action ) {
		case 'page':
			$url = get_permalink( $post_id );
			$url = $url ? $url : '#';
			break;
	}
	
	$o = '<div class="hero-unit">';

		$o .= '<h1>' . $title . '</h1>';
		$o .= wpautop( wp_kses_post( $content ) );

		$o .= '<p>';
			$o .= '<a href="' . esc_url( $url ) . '" class="' . esc_attr( join( ' ', $btn_classes ) ) . '">';
				$o .= $btn_text;
			$o .= '</a>';
		$o .= '</p>';

	$o .= '</div>';
	
	return $o;
}

/**
 * Clients Shortcode Handler
 */
function youxi_shortcode_clients_cb( $atts, $content, $tag ) {
	return '<ul>' . do_shortcode( $content ) . '</ul>';
}

/**
 * Client Shortcode Handler
 */
function youxi_shortcode_client_cb( $atts, $content, $tag ) {

	extract( $atts, EXTR_SKIP );

	$o = '<li>';
		if( '' != $url ):
		$o .= '<a href="' . esc_url( $url ) . '" title="' . esc_attr( $name ) . '">';
			$o .= '<img src="' . esc_url( $logo ) . '" alt="' . esc_attr( $name ) . '">';
		$o .= '</a>';
		else:
		$o .= '<img src="' . esc_url( $logo ) . '" alt="' . esc_attr( $name ) . '">';
		endif;
	$o .= '</li>';

	return $o;
}

/**
 * Fetch available Contact form 7
 */
if( defined( 'WPCF7_VERSION' ) ) {

	function youxi_shortcode_cf7_forms() {
		$array = array();
		$forms = WPCF7_ContactForm::find();
		foreach( $forms as $form ) {
			if( version_compare( WPCF7_VERSION, '3.9' ) >= 0 ) {
				$array[ $form->id() ] = $form->title();
			} else {
				$array[ $form->id ] = $form->title;
			}
		}

		return $array;
	}
}

/**
 * Heading Shortcode Handler
 */
function youxi_shortcode_heading_cb( $atts, $content, $tag ) {
	return '<' . $atts['element'] . '>' . wp_kses_post( $content ) . '</' . $atts['element'] . '>';
}

/**
 * Pricing Tables Shortcode Handler
 */
function youxi_shortcode_pricing_tables_cb( $atts, $content, $tag ) {

	// Keep the current tables count
	$num_tables = Youxi_Shortcode::read_counter( 'pricing_table' );

	// Render the content
	$content = do_shortcode( $content );

	// Compute the number of tables
	$num_tables = Youxi_Shortcode::read_counter( 'pricing_table' ) - $num_tables;

	$list_classes = array( 'one', 'two', 'three', 'four', 'five' );
	$the_class = $list_classes[ min( max( $num_tables, 1 ), 5 ) - 1 ];

	$o = '<div class="pricing-tables ' . esc_attr( $the_class ) . '-tables">';
		$o .= $content;
	$o .= '</div>';

	return $o;
}

/**
 * Pricing Table Shortcode Handler
 */
function youxi_shortcode_pricing_table_cb( $atts, $content, $tag ) {
	extract( $atts, EXTR_SKIP );

	switch( $btn_action ) {
		case 'page':
			$url = get_permalink( $post_id );
			$url = $url ? $url : '#';
			break;
	}

	$o = '<div class="pricing-table' . ( $featured ? ' featured' : '' ) . '">';

		$o .= '<div class="table-header">';
			$o .= '<div class="name">' . $title . '</div>';
		$o .= '</div>';

		if( $show_price ):

			$o .= '<div class="table-price">';

				$o .= '<div class="price text-' . esc_attr( $color ) . '">';
					$o .= '<span>' . esc_html( $currency ) . '</span>';
					$o .= esc_html( $price );
				$o .= '</div>';

				$o .= '<div class="price-description">';
					$o .= esc_html( $price_description );
				$o .= '</div>';

			$o .= '</div>';

		endif;

		$o .= '<div class="table-features">';
			$o .= $content;
		$o .= '</div>';

		if( $show_btn ):

			$o .= '<div class="table-footer">';
				$o .= '<a href="' . esc_url( $url ) . '" class="btn btn-' . esc_attr( $color ) . '">' . $btn_text . '</a>';
			$o .= '</div>';

		endif;

	$o .= '</div>';

	return $o;
}

/**
 * Service Shortcode Handler
 */
function youxi_shortcode_service_cb( $atts, $content, $tag ) {

	extract( $atts, EXTR_SKIP );

	switch( $btn_action ) {
		case 'page':
			$url = get_permalink( $post_id );
			$url = $url ? $url : '#';
			break;
	}
	
	$o = '<div class="thumbnail">';

		$o .= '<div class="caption">';

			$o .= '<h3>' . $title . '</h3>';
			$o .= wpautop( wp_kses_post( $content ) );

			if( $show_btn ):

				/* Compile button classes */
				$btn_classes = array( 'btn' );
				if( $btn_size ) {
					$btn_classes[] = sanitize_html_class( 'btn-' . $btn_size );
				}
				if( $btn_type ) {
					$btn_classes[] = sanitize_html_class( 'btn-' . $btn_type );
				}

			$o .= '<p>';
				$o .= '<a href="' . esc_url( $url ) . '" class="' . esc_attr( join( ' ', $btn_classes ) ) . '">';
					$o .= $btn_text;
				$o .= '</a>';
			$o .= '</p>';

			endif;

		$o .= '</div>';

	$o .= '</div>';

	return $o;
}

/**
 * Table Shortcode Handler
 */
function youxi_shortcode_table_cb( $atts, $content, $tag ) {
	$tags = array(
		'table' => array(
			'tag' => 'table', 
			'allowed_tags' => array(
				'thead' => true, 
				'tbody' => true, 
				'tr' => true, 
				'td' => true, 
				'th' => true, 
				'a' => array(
					'href' => true, 
					'target' => true, 
					'title' => true
				), 
				'i' => true, 
				'em' => true, 
				'b' => true, 
				'strong' => true, 
				'strike' => true, 
				'ul' => true, 
				'ol' => true, 
				'li' => true
			)
		), 
		'table_head' => array(
			'tag' => 'thead', 
			'allowed_tags' => array(
				'tr' => true, 
				'th' => true
			)
		), 
		'table_body' => array(
			'tag' => 'tbody', 
			'allowed_tags' => array(
				'tbody' => true, 
				'tr' => true, 
				'td' => true, 
				'a' => array(
					'href' => true, 
					'target' => true, 
					'title' => true
				), 
				'i' => true, 
				'em' => true, 
				'b' => true, 
				'strong' => true, 
				'strike' => true, 
				'ul' => true, 
				'ol' => true, 
				'li' => true
			)
		), 
		'table_row' => array(
			'tag' => 'tr', 
			'allowed_tags' => array(
				'td' => true, 
				'th' => true
			)
		), 
		'table_cell' => array(
			'tag' => 'td', 
			'allowed_tags' => array(
				'a' => array(
					'href' => true, 
					'target' => true, 
					'title' => true
				), 
				'i' => true, 
				'em' => true, 
				'b' => true, 
				'strong' => true, 
				'strike' => true, 
				'ul' => true, 
				'ol' => true, 
				'li' => true
			)
		), 
		'table_header' => array(
			'tag' => 'th', 
			'allowed_tags' => array()
		)
	);

	if( isset( $tags[ $tag ] ) ) {

		extract( $tags[ $tag ] );

		$html = '';

		if( 'table' == $tag ) {

			$attributes['class'] = 'table';

			if( ! empty( $atts['styles'] ) ) {
				foreach( explode( ',',  $atts['styles'] ) as $style ) {
					$attributes['class'] .= " table-{$style}";
				}
			}

			foreach( $attributes as $key => $val ) {
				$html .= " {$key}=\"" . esc_attr( $val ) . "\"";
			}
		}

		$o = '<' . $tag . $html . '>';

		$o .= do_shortcode( wp_kses_post( $content ) );

		$o .= '</' . $tag . '>';

		return $o;
	}
}

/**
 * Tabs Shortcode Handler
 */
function youxi_shortcode_tabs_cb( $atts, $content, $tag ) {

	$o = '';

	$tabs = Youxi_Shortcode::to_array( $content, true );
	
	if( is_array( $tabs ) && ! empty( $tabs ) ) {

		/* Reset the tab_content index */
		Youxi_Shortcode::reset_counter( 'tab' );

		$o .= '<ul class="nav nav-' . esc_attr( $atts['type'] ) . '">';
			
		foreach( $tabs as $index => $tab ) {

			if( isset( $tab['tag'], $tab['atts'] ) && Youxi_Shortcode::prefix( 'tab' ) == $tab['tag'] ) {

				extract( $tab['atts'] );

				$tab_id = sanitize_key( $title . Youxi_Shortcode::read_counter( $tag ) . $index );
				
				$o .= '<li' . ( $index == 0 ? ' class="active"' : '' ) . '>';
					$o .= '<a href="#' . esc_attr( $tab_id ) . '" data-toggle="tab">';
						$o .= $title;
					$o .= '</a>';
				$o .= '</li>';
			}
		}

		$o .= '</ul>';

		$o .= '<div class="tab-content">';

			$o .= do_shortcode( $content );

		$o .= '</div>';
	}

	return $o;
}

/**
 * Tab Shortcode Handler
 */
function youxi_shortcode_tab_cb( $atts, $content, $tag ) {

	$tab_id = sanitize_key( $atts['title'] . Youxi_Shortcode::read_counter( 'tabs' ) . Youxi_Shortcode::read_counter( $tag ) );
	$class  = 'tab-pane fade';

	if( 0 == Youxi_Shortcode::read_counter( $tag ) ) {
		$class .= ' active in';
	}

	$o = '<div id="' . esc_attr( $tab_id ) . '" class="' . esc_attr( trim( $class ) ) . '">';
		$o .= wpautop( Youxi_Shortcode_Manager::get()->shortcode_unautop( do_shortcode( wp_kses_post( $content ) ) ) );
	$o .= '</div>';
	
	return $o;
}

/**
 * Team Shortcode Handler
 */
function youxi_shortcode_team_cb( $atts, $content, $tag ) {
	
	$o = '<div class="thumbnail">';

		$o .= '<img src="' . esc_url( $atts['photo'] ) . '" alt="' . esc_attr( $atts['name'] ) . '">';

		$o .= '<div class="caption">';
			$o .= '<h4>' . $atts['name'] . ( $atts['role']? '<br><small>' . $atts['role'] . '</small>' : '' ) . '</h4>';
			$o .= wpautop( wp_kses_post( $content ) );
		$o .= '</div>';

	$o .= '</div>';

	return $o;
}

/**
 * Testimonials Shortcode Handler
 */
function youxi_shortcode_testimonials_cb( $atts, $content, $tag ) {
	return do_shortcode( $content );
}

/**
 * Testimonial Shortcode Handler
 */
function youxi_shortcode_testimonial_cb( $atts, $content, $tag ) {

	extract( $atts, EXTR_SKIP );

	$o = '<blockquote>';

		$o .= wpautop( $content );

		$o .= '<small>';

			$o .= esc_html( $author );

			if( '' !== $source_url ):
			$o .= ', <cite><a href="' . esc_url( $source_url ) . '">' . esc_html( $source ) . '</a></cite>';
			else:
			$o .= ', <cite>' . esc_html( $source ) . '</cite>';
			endif;

		$o .= '</small>';

	$o .= '</blockquote>';

	return $o;
}

/**
 * Text Widget Shortcode Handler
 */
function youxi_shortcode_text_widget_cb( $atts, $content, $tag ) {
	return do_shortcode( Youxi_Shortcode_Manager::get()->shortcode_unautop( wpautop( $content ) ) );
}

/**
 * Widget Area Shortcode Handler
 */
function youxi_shortcode_widget_area_cb( $atts, $content, $tag ) {
	
	$o = '';

	if( is_dynamic_sidebar( $atts['id'] ) ):
		ob_start();
		dynamic_sidebar( $atts['id'] );
		$o = ob_get_clean();
	endif;

	return $o;
}

/**
 * Helper Functions
 */
function youxi_shortcode_page_choices() {
	$page_objects = apply_filters( 'youxi_shortcode_recognized_sidebars', get_pages() );
	$pages = array();

	foreach( $page_objects as $page ) {
		$pages[ $page->ID ] = $page->post_title;
	}

	return $pages;
}

function youxi_shortcode_post_categories() {
	$result = array();
	foreach( get_categories( array( 'hide_empty' => false ) ) as $category ) {
		$result[ $category->term_id ] = $category->name;
	}
	return $result;
}

function youxi_shortcode_post_tags() {	
	$result = array();
	foreach( get_tags( array( 'hide_empty' => false ) ) as $tag ) {
		$result[ $tag->term_id ] = $tag->name;
	}
	return $result;
}

function youxi_shortcode_widget_area_choices() {
	global $wp_registered_sidebars;
	return apply_filters( 'youxi_shortcode_recognized_widget_areas', wp_list_pluck( $wp_registered_sidebars, 'name' ) );
}

/**
 * Shortcode Definitions Callback
 */
function define_content_shortcodes( $manager ) {

	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

	/********************************************************************************
	 * Content category
	 ********************************************************************************/
	$manager->add_category( 'content', array(
		'label' => __( 'Content Shortcodes', 'youxi' ), 
		'priority' => 20
	));

	/********************************************************************************
	 * Accordion shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'accordion', array(
		'label' => __( 'Accordion', 'youxi' ), 
		'category' => 'content', 
		'priority' => 10, 
		'icon' => 'fa fa-stack-overflow', 
		'atts' => array(
			'behavior' => array(
				'type' => 'radio', 
				'label' => __( 'Mode', 'youxi' ), 
				'description' => __( 'Choose the behavior of the accordion.', 'youxi' ), 
				'choices' => array(
					'accordion' => __( 'Accordion', 'youxi' ), 
					'toggle' => __( 'Toggle', 'youxi' ), 
				), 
				'std' => 'accordion'
			)
		), 
		'content' => array(
			'type' => 'repeater', 
			'label' => __( 'Groups', 'youxi' ), 
			'description' => __( 'Enter here the title and content of each accordion.', 'youxi' ), 
			'fields' => array( array( $manager, 'get_shortcode_fields' ), 'accordion_group' ), 
			'preview_template' => '{{ data.title }}', 
			'serialize' => 'js:function( data ) {
				return this.construct( "accordion_group", data );
			}', 
			'deserialize' => 'js:function( data ) {
				return this.deserializeArray( data );
			}'
		), 
		'callback' => 'youxi_shortcode_accordion_cb'
	));
	$manager->add_shortcode( 'accordion_group', array(
		'label' => __( 'Accordion Group', 'youxi' ), 
		'category' => 'content', 
		'internal' => true, 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => __( 'Title', 'youxi' ), 
				'description' => __( 'Enter here the accordion title.', 'youxi' )
			)
		), 
		'content' => array(
			'type' => 'textarea', 
			'label' => __( 'Content', 'youxi' ), 
			'description' => __( 'Enter here the accordion content.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_accordion_group_cb'
	));

	/********************************************************************************
	 * Alert shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'alert', array(
		'label' => __( 'Alert', 'youxi' ), 
		'category' => 'content', 
		'priority' => 20, 
		'icon' => 'fa fa-warning', 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => __( 'Title', 'youxi' ), 
				'description' => __( 'Enter here the alert\'s title.', 'youxi' )
			), 
			'type' => array(
				'type' => 'select', 
				'label' => __( 'Alert Type', 'youxi' ), 
				'description' => __( 'Choose the alert type.', 'youxi' ), 
				'choices' => array(
					'success' => __( 'Success', 'youxi' ), 
					'warning' => __( 'Warning', 'youxi' ), 
					'danger' => __( 'Danger', 'youxi' ), 
					'info' => __( 'Info', 'youxi' )
				), 
				'std' => 0
			)
		), 
		'content' => array(
			'type' => 'richtext', 
			'label' => __( 'Content', 'youxi' ), 
			'description' => __( 'Enter here the alert\'s content.', 'youxi' ), 
			'tinymce' => array(
				'media_buttons' => false, 
				'tinymce' => false
			)
		), 
		'callback' => 'youxi_shortcode_alert_cb'
	));

	/********************************************************************************
	 * Call to Action shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'call_to_action', array(
		'label' => __( 'Call to Action', 'youxi' ), 
		'category' => 'content', 
		'priority' => 30, 
		'icon' => 'fa fa-hand-o-right', 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => __( 'Title', 'youxi' ), 
				'description' => __( 'Enter the title for this call to action box.', 'youxi' ), 
				'std' => 'Call to Action Box'
			), 
			'btn_text' => array(
				'type' => 'text', 
				'label' => __( 'Button Text', 'youxi' ), 
				'description' => __( 'Enter the text to display on the button.', 'youxi' ), 
				'std' => 'Button'
			), 
			'btn_size' => array(
				'type' => 'select', 
				'label' => __( 'Button Size', 'youxi' ), 
				'description' => __( 'Choose the size of the button.', 'youxi' ), 
				'choices' => array(
					0 => __( 'Default', 'youxi' ), 
					'lg' => __( 'Large', 'youxi' ), 
					'sm' => __( 'Small', 'youxi' ), 
					'xs' => __( 'Extra Small', 'youxi' )
				), 
				'std' => 0
			), 
			'btn_type' => array(
				'type' => 'select', 
				'label' => __( 'Button Type', 'youxi' ), 
				'description' => __( 'Choose the type of the button.', 'youxi' ), 
				'choices' => array(
					'default' => __( 'Default', 'youxi' ), 
					'primary' => __( 'Primary', 'youxi' ), 
					'success' => __( 'Success', 'youxi' ), 
					'warning' => __( 'Warning', 'youxi' ), 
					'danger' => __( 'Danger', 'youxi' ), 
					'info' => __( 'Info', 'youxi' )
				), 
				'std' => 'default'
			), 
			'btn_action' => array(
				'type' => 'radio', 
				'label' => __( 'Button Action', 'youxi' ), 
				'description' => __( 'Choose the action to execute after clicking the button.', 'youxi' ), 
				'choices' => array(
					'url' => __( 'Go to URL', 'youxi' ), 
					'page' => __( 'Go to Page', 'youxi' )
				), 
				'std' => 'url'
			), 
			'post_id' => array(
				'type' => 'select', 
				'label' => __( 'Page', 'youxi' ), 
				'description' => __( 'Choose the page to view after clicking the button.', 'youxi' ), 
				'choices' => 'youxi_shortcode_page_choices', 
				'criteria' => 'btn_action:is(page)'
			), 
			'url' => array(
				'type' => 'text', 
				'label' => __( 'URL', 'youxi' ), 
				'description' => __( 'Enter the URL to go to after clicking the button.', 'youxi' ), 
				'std' => '#', 
				'criteria' => 'btn_action:is(url)'
			)
		), 
		'content' => array(
			'type' => 'richtext', 
			'label' => __( 'Description', 'youxi' ), 
			'description' => __( 'Enter the content of this call to action box.', 'youxi' ), 
			'tinymce' => array(
				'media_buttons' => false, 
				'tinymce' => false
			)
		), 
		'callback' => 'youxi_shortcode_call_to_action_cb'
	));

	/********************************************************************************
	 * Clients shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'clients', array(
		'label' => __( 'Clients', 'youxi' ), 
		'category' => 'content', 
		'priority' => 40, 
		'icon' => 'fa fa-group', 
		'content' => array(
			'type' => 'repeater', 
			'label' => __( 'Clients', 'youxi' ), 
			'description' => __( 'Enter here the client\'s data.', 'youxi' ), 
			'min' => 1, 
			'preview_template' => '<a href="{{ data.url }}" target="_blank">{{ data.name }}</a>', 
			'fields' => array( array( $manager, 'get_shortcode_fields' ), 'client' ), 
			'serialize' => 'js:function( data ) {
				return this.construct( "client", data );
			}', 
			'deserialize' => 'js:function( data ) {
				return this.deserializeArray( data );
			}'
		), 
		'callback' => 'youxi_shortcode_clients_cb'
	));
	$manager->add_shortcode( 'client', array(
		'label' => __( 'Client', 'youxi' ), 
		'category' => 'content', 
		'priority' => 40, 
		'icon' => 'fa fa-group', 
		'internal' => true, 
		'atts' => array(
			'name' => array(
				'type' => 'text', 
				'label' => __( 'Name', 'youxi' ), 
				'description' => __( 'Enter here the client\'s name.', 'youxi' )
			), 
			'url' => array(
				'type' => 'url', 
				'label' => __( 'URL', 'youxi' ), 
				'description' => __( 'Enter here the client\'s URL.', 'youxi' )
			), 
			'logo' => array(
				'type' => 'image', 
				'label' => __( 'Logo', 'youxi' ), 
				'description' => __( 'Choose here the client\'s logo image.', 'youxi' ), 
				'return_type' => 'url', 
				'frame_title' => __( 'Choose an Image', 'youxi' ), 
				'frame_btn_text' => __( 'Insert Image', 'youxi' ), 
				'upload_btn_text' => __( 'Choose an Image', 'youxi' )
			)
		), 
		'callback' => 'youxi_shortcode_client_cb'
	));

	/********************************************************************************
	 * Contact Form 7
	 ********************************************************************************/
	if( defined( 'WPCF7_VERSION' ) ) {
		$manager->add_shortcode( 'contact-form-7', array(
			'label' => __( 'Contact Form 7', 'youxi' ), 
			'category' => 'content', 
			'priority' => 50, 
			'icon' => 'fa fa-envelope-o', 
			'third_party' => true, 
			'atts' => array(
				'title' => array(
					'type' => 'text', 
					'label' => __( 'Title', 'youxi' ), 
					'description' => __( 'Enter here the title of the form.', 'youxi' )
				), 
				'id' => array(
					'type' => 'select', 
					'label' => __( 'Contact Form 7', 'youxi' ), 
					'description' => __( 'Choose a Contact Form 7 to display.', 'youxi' ), 
					'choices' => 'youxi_shortcode_cf7_forms'
				)
			)
		));
	}

	/********************************************************************************
	 * Heading shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'heading', array(
		'label' => __( 'Heading', 'youxi' ), 
		'category' => 'content', 
		'priority' => 60, 
		'icon' => 'fa fa-font', 
		'insert_nl' => false, 
		'atts' => array(
			'element' => array(
				'type' => 'select', 
				'label' => __( 'Heading Element', 'youxi' ), 
				'description' => __( 'Choose the HTML element to use for the heading.', 'youxi' ), 
				'choices' => array(
					'h1' => 'H1', 
					'h2' => 'H2', 
					'h3' => 'H3', 
					'h4' => 'H4', 
					'h5' => 'H5', 
					'h6' => 'H6'
				), 
				'std' => 'h1'
			)
		), 
		'content' => array(
			'type' => 'text', 
			'label' => __( 'Text', 'youxi' ), 
			'description' => __( 'Enter the heading text.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_heading_cb'
	));

	/********************************************************************************
	 * Icon Box shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'icon_box', array(
		'label' => __( 'Icon Box', 'youxi' ), 
		'category' => 'content', 
		'priority' => 70, 
		'icon' => 'fa fa-smile-o', 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => __( 'Title', 'youxi' ), 
				'description' => __( 'Enter here the icon box\'s title.', 'youxi' )
			), 
			'icon' => array(
				'type' => 'iconchooser', 
				'label' => __( 'Icon', 'youxi' ), 
				'description' => __( 'Choose here the icon to display on the icon box.', 'youxi' ), 
				'assets' => YOUXI_SHORTCODE_URL . "frontend/bootstrap/css/bootstrap-icons{$suffix}.css", 
				'choices' => array(
					'glyphicon glyphicon-adjust' => 'adjust', 
					'glyphicon glyphicon-align-center' => 'align-center', 
					'glyphicon glyphicon-align-justify' => 'align-justify', 
					'glyphicon glyphicon-align-left' => 'align-left', 
					'glyphicon glyphicon-align-right' => 'align-right', 
					'glyphicon glyphicon-arrow-down' => 'arrow-down', 
					'glyphicon glyphicon-arrow-left' => 'arrow-left', 
					'glyphicon glyphicon-arrow-right' => 'arrow-right', 
					'glyphicon glyphicon-arrow-up' => 'arrow-up', 
					'glyphicon glyphicon-asterisk' => 'asterisk', 
					'glyphicon glyphicon-backward' => 'backward', 
					'glyphicon glyphicon-ban-circle' => 'ban-circle', 
					'glyphicon glyphicon-barcode' => 'barcode', 
					'glyphicon glyphicon-bell' => 'bell', 
					'glyphicon glyphicon-bold' => 'bold', 
					'glyphicon glyphicon-book' => 'book', 
					'glyphicon glyphicon-bookmark' => 'bookmark', 
					'glyphicon glyphicon-briefcase' => 'briefcase', 
					'glyphicon glyphicon-bullhorn' => 'bullhorn', 
					'glyphicon glyphicon-calendar' => 'calendar', 
					'glyphicon glyphicon-camera' => 'camera', 
					'glyphicon glyphicon-certificate' => 'certificate', 
					'glyphicon glyphicon-check' => 'check', 
					'glyphicon glyphicon-chevron-down' => 'chevron-down', 
					'glyphicon glyphicon-chevron-left' => 'chevron-left', 
					'glyphicon glyphicon-chevron-right' => 'chevron-right', 
					'glyphicon glyphicon-chevron-up' => 'chevron-up', 
					'glyphicon glyphicon-circle-arrow-down' => 'circle-arrow-down', 
					'glyphicon glyphicon-circle-arrow-left' => 'circle-arrow-left', 
					'glyphicon glyphicon-circle-arrow-right' => 'circle-arrow-right', 
					'glyphicon glyphicon-circle-arrow-up' => 'circle-arrow-up', 
					'glyphicon glyphicon-cloud' => 'cloud', 
					'glyphicon glyphicon-cloud-download' => 'cloud-download', 
					'glyphicon glyphicon-cloud-upload' => 'cloud-upload', 
					'glyphicon glyphicon-cog' => 'cog', 
					'glyphicon glyphicon-collapse-down' => 'collapse-down', 
					'glyphicon glyphicon-collapse-up' => 'collapse-up', 
					'glyphicon glyphicon-comment' => 'comment', 
					'glyphicon glyphicon-compressed' => 'compressed', 
					'glyphicon glyphicon-copyright-mark' => 'copyright-mark', 
					'glyphicon glyphicon-credit-card' => 'credit-card', 
					'glyphicon glyphicon-cutlery' => 'cutlery', 
					'glyphicon glyphicon-dashboard' => 'dashboard', 
					'glyphicon glyphicon-download' => 'download', 
					'glyphicon glyphicon-download-alt' => 'download-alt', 
					'glyphicon glyphicon-earphone' => 'earphone', 
					'glyphicon glyphicon-edit' => 'edit', 
					'glyphicon glyphicon-eject' => 'eject', 
					'glyphicon glyphicon-envelope' => 'envelope', 
					'glyphicon glyphicon-euro' => 'euro', 
					'glyphicon glyphicon-exclamation-sign' => 'exclamation-sign', 
					'glyphicon glyphicon-expand' => 'expand', 
					'glyphicon glyphicon-export' => 'export', 
					'glyphicon glyphicon-eye-close' => 'eye-close', 
					'glyphicon glyphicon-eye-open' => 'eye-open', 
					'glyphicon glyphicon-facetime-video' => 'facetime-video', 
					'glyphicon glyphicon-fast-backward' => 'fast-backward', 
					'glyphicon glyphicon-fast-forward' => 'fast-forward', 
					'glyphicon glyphicon-file' => 'file', 
					'glyphicon glyphicon-film' => 'film', 
					'glyphicon glyphicon-filter' => 'filter', 
					'glyphicon glyphicon-fire' => 'fire', 
					'glyphicon glyphicon-flag' => 'flag', 
					'glyphicon glyphicon-flash' => 'flash', 
					'glyphicon glyphicon-floppy-disk' => 'floppy-disk', 
					'glyphicon glyphicon-floppy-open' => 'floppy-open', 
					'glyphicon glyphicon-floppy-remove' => 'floppy-remove', 
					'glyphicon glyphicon-floppy-save' => 'floppy-save', 
					'glyphicon glyphicon-floppy-saved' => 'floppy-saved', 
					'glyphicon glyphicon-folder-close' => 'folder-close', 
					'glyphicon glyphicon-folder-open' => 'folder-open', 
					'glyphicon glyphicon-font' => 'font', 
					'glyphicon glyphicon-forward' => 'forward', 
					'glyphicon glyphicon-fullscreen' => 'fullscreen', 
					'glyphicon glyphicon-gbp' => 'gbp', 
					'glyphicon glyphicon-gift' => 'gift', 
					'glyphicon glyphicon-glass' => 'glass', 
					'glyphicon glyphicon-globe' => 'globe', 
					'glyphicon glyphicon-hand-down' => 'hand-down', 
					'glyphicon glyphicon-hand-left' => 'hand-left', 
					'glyphicon glyphicon-hand-right' => 'hand-right', 
					'glyphicon glyphicon-hand-up' => 'hand-up', 
					'glyphicon glyphicon-hd-video' => 'hd-video', 
					'glyphicon glyphicon-hdd' => 'hdd', 
					'glyphicon glyphicon-header' => 'header', 
					'glyphicon glyphicon-headphones' => 'headphones', 
					'glyphicon glyphicon-heart' => 'heart', 
					'glyphicon glyphicon-heart-empty' => 'heart-empty', 
					'glyphicon glyphicon-home' => 'home', 
					'glyphicon glyphicon-import' => 'import', 
					'glyphicon glyphicon-inbox' => 'inbox', 
					'glyphicon glyphicon-indent-left' => 'indent-left', 
					'glyphicon glyphicon-indent-right' => 'indent-right', 
					'glyphicon glyphicon-info-sign' => 'info-sign', 
					'glyphicon glyphicon-italic' => 'italic', 
					'glyphicon glyphicon-leaf' => 'leaf', 
					'glyphicon glyphicon-link' => 'link', 
					'glyphicon glyphicon-list' => 'list', 
					'glyphicon glyphicon-list-alt' => 'list-alt', 
					'glyphicon glyphicon-lock' => 'lock', 
					'glyphicon glyphicon-log-in' => 'log-in', 
					'glyphicon glyphicon-log-out' => 'log-out', 
					'glyphicon glyphicon-magnet' => 'magnet', 
					'glyphicon glyphicon-map-marker' => 'map-marker', 
					'glyphicon glyphicon-minus' => 'minus', 
					'glyphicon glyphicon-minus-sign' => 'minus-sign', 
					'glyphicon glyphicon-move' => 'move', 
					'glyphicon glyphicon-music' => 'music', 
					'glyphicon glyphicon-new-window' => 'new-window', 
					'glyphicon glyphicon-off' => 'off', 
					'glyphicon glyphicon-ok' => 'ok', 
					'glyphicon glyphicon-ok-circle' => 'ok-circle', 
					'glyphicon glyphicon-ok-sign' => 'ok-sign', 
					'glyphicon glyphicon-open' => 'open', 
					'glyphicon glyphicon-paperclip' => 'paperclip', 
					'glyphicon glyphicon-pause' => 'pause', 
					'glyphicon glyphicon-pencil' => 'pencil', 
					'glyphicon glyphicon-phone' => 'phone', 
					'glyphicon glyphicon-phone-alt' => 'phone-alt', 
					'glyphicon glyphicon-picture' => 'picture', 
					'glyphicon glyphicon-plane' => 'plane', 
					'glyphicon glyphicon-play' => 'play', 
					'glyphicon glyphicon-play-circle' => 'play-circle', 
					'glyphicon glyphicon-plus' => 'plus', 
					'glyphicon glyphicon-plus-sign' => 'plus-sign', 
					'glyphicon glyphicon-print' => 'print', 
					'glyphicon glyphicon-pushpin' => 'pushpin', 
					'glyphicon glyphicon-qrcode' => 'qrcode', 
					'glyphicon glyphicon-question-sign' => 'question-sign', 
					'glyphicon glyphicon-random' => 'random', 
					'glyphicon glyphicon-record' => 'record', 
					'glyphicon glyphicon-refresh' => 'refresh', 
					'glyphicon glyphicon-registration-mark' => 'registration-mark', 
					'glyphicon glyphicon-remove' => 'remove', 
					'glyphicon glyphicon-remove-circle' => 'remove-circle', 
					'glyphicon glyphicon-remove-sign' => 'remove-sign', 
					'glyphicon glyphicon-repeat' => 'repeat', 
					'glyphicon glyphicon-resize-full' => 'resize-full', 
					'glyphicon glyphicon-resize-horizontal' => 'resize-horizontal', 
					'glyphicon glyphicon-resize-small' => 'resize-small', 
					'glyphicon glyphicon-resize-vertical' => 'resize-vertical', 
					'glyphicon glyphicon-retweet' => 'retweet', 
					'glyphicon glyphicon-road' => 'road', 
					'glyphicon glyphicon-save' => 'save', 
					'glyphicon glyphicon-saved' => 'saved', 
					'glyphicon glyphicon-screenshot' => 'screenshot', 
					'glyphicon glyphicon-sd-video' => 'sd-video', 
					'glyphicon glyphicon-search' => 'search', 
					'glyphicon glyphicon-send' => 'send', 
					'glyphicon glyphicon-share' => 'share', 
					'glyphicon glyphicon-share-alt' => 'share-alt', 
					'glyphicon glyphicon-shopping-cart' => 'shopping-cart', 
					'glyphicon glyphicon-signal' => 'signal', 
					'glyphicon glyphicon-sort' => 'sort', 
					'glyphicon glyphicon-sort-by-alphabet' => 'sort-by-alphabet', 
					'glyphicon glyphicon-sort-by-alphabet-alt' => 'sort-by-alphabet-alt', 
					'glyphicon glyphicon-sort-by-attributes' => 'sort-by-attributes', 
					'glyphicon glyphicon-sort-by-attributes-alt' => 'sort-by-attributes-alt', 
					'glyphicon glyphicon-sort-by-order' => 'sort-by-order', 
					'glyphicon glyphicon-sort-by-order-alt' => 'sort-by-order-alt', 
					'glyphicon glyphicon-sound-5-1' => 'sound-5-1', 
					'glyphicon glyphicon-sound-6-1' => 'sound-6-1', 
					'glyphicon glyphicon-sound-7-1' => 'sound-7-1', 
					'glyphicon glyphicon-sound-dolby' => 'sound-dolby', 
					'glyphicon glyphicon-sound-stereo' => 'sound-stereo', 
					'glyphicon glyphicon-star' => 'star', 
					'glyphicon glyphicon-star-empty' => 'star-empty', 
					'glyphicon glyphicon-stats' => 'stats', 
					'glyphicon glyphicon-step-backward' => 'step-backward', 
					'glyphicon glyphicon-step-forward' => 'step-forward', 
					'glyphicon glyphicon-stop' => 'stop', 
					'glyphicon glyphicon-subtitles' => 'subtitles', 
					'glyphicon glyphicon-tag' => 'tag', 
					'glyphicon glyphicon-tags' => 'tags', 
					'glyphicon glyphicon-tasks' => 'tasks', 
					'glyphicon glyphicon-text-height' => 'text-height', 
					'glyphicon glyphicon-text-width' => 'text-width', 
					'glyphicon glyphicon-th' => 'th', 
					'glyphicon glyphicon-th-large' => 'th-large', 
					'glyphicon glyphicon-th-list' => 'th-list', 
					'glyphicon glyphicon-thumbs-down' => 'thumbs-down', 
					'glyphicon glyphicon-thumbs-up' => 'thumbs-up', 
					'glyphicon glyphicon-time' => 'time', 
					'glyphicon glyphicon-tint' => 'tint', 
					'glyphicon glyphicon-tower' => 'tower', 
					'glyphicon glyphicon-transfer' => 'transfer', 
					'glyphicon glyphicon-trash' => 'trash', 
					'glyphicon glyphicon-tree-conifer' => 'tree-conifer', 
					'glyphicon glyphicon-tree-deciduous' => 'tree-deciduous', 
					'glyphicon glyphicon-unchecked' => 'unchecked', 
					'glyphicon glyphicon-upload' => 'upload', 
					'glyphicon glyphicon-usd' => 'usd', 
					'glyphicon glyphicon-user' => 'user', 
					'glyphicon glyphicon-volume-down' => 'volume-down', 
					'glyphicon glyphicon-volume-off' => 'volume-off', 
					'glyphicon glyphicon-volume-up' => 'volume-up', 
					'glyphicon glyphicon-warning-sign' => 'warning-sign', 
					'glyphicon glyphicon-wrench' => 'wrench', 
					'glyphicon glyphicon-zoom-in' => 'zoom-in', 
					'glyphicon glyphicon-zoom-out' => 'zoom-out'
				)
			)
		), 
		'content' => array(
			'type' => 'richtext', 
			'label' => __( 'Content', 'youxi' ), 
			'description' => __( 'Enter here the icon box\'s content.', 'youxi' ), 
			'tinymce' => array(
				'media_buttons' => false, 
				'tinymce' => false
			)
		), 
		'callback' => '__return_empty_string'
	));

	/********************************************************************************
	 * Posts shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'posts', array(
		'label' => __( 'Posts', 'youxi' ), 
		'category' => 'content', 
		'priority' => 80, 
		'icon' => 'fa fa-file-text-o', 
		'atts' => array(
			'category__not_in' => array(
				'type' => 'checkboxlist', 
				'label' => __( 'Excluded Categories', 'youxi' ), 
				'description' => __( 'Choose here the post categories to exclude.', 'youxi' ), 
				'choices' => 'youxi_shortcode_post_categories', 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join( "," );
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split( "," )
				}'
			), 
			'tag__not_in' => array(
				'type' => 'checkboxlist', 
				'label' => __( 'Excluded Tags', 'youxi' ), 
				'description' => __( 'Choose here the post tags to exclude.', 'youxi' ), 
				'choices' => 'youxi_shortcode_post_tags', 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join( "," );
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split( "," )
				}'
			), 
			'posts_per_page' => array(
				'type' => 'uispinner', 
				'label' => __( 'Posts Per Page' ), 
				'description' => __( 'Choose how many posts to retrieve.', 'youxi' ), 
				'widgetopts' => array(
					'min' => -1, 
					'step' => 1
				), 
				'std' => -1
			), 
			'orderby' => array(
				'type' => 'select', 
				'label' => __( 'Order By', 'youxi' ), 
				'description' => __( 'Choose which parameter to use for ordering the retrieved posts.', 'youxi' ), 
				'choices' => array(
					'none' => __( 'None', 'youxi' ), 
					'ID' => __( 'Post ID', 'youxi' ), 
					'title' => __( 'Post Title', 'youxi' ), 
					'name' => __( 'Post Slug', 'youxi' ), 
					'date' => __( 'Date', 'youxi' ), 
					'rand' => __( 'Random Order', 'youxi' )
				), 
				'std' => 'date'
			), 
			'order' => array(
				'type' => 'radio', 
				'label' => __( 'Order', 'youxi' ), 
				'description' => __( 'Choose the ascending/descending order of the orderby parameter.', 'youxi' ), 
				'choices' => array(
					'ASC' => __( 'Ascending', 'youxi' ), 
					'DESC' => __( 'Descending', 'youxi' )
				), 
				'std' => 'DESC'
			)
		), 
		'callback' => '__return_empty_string'
	));

	/********************************************************************************
	 * Pricing Tables shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'pricing_tables', array(
		'label' => __( 'Pricing Tables', 'youxi' ), 
		'category' => 'content', 
		'priority' => 90, 
		'icon' => 'fa fa-usd', 
		'content' => array(
			'type' => 'repeater', 
			'label' => __( 'Tables', 'youxi' ), 
			'description' => __( 'Enter here the pricing tables.', 'youxi' ), 
			'preview_template' => '{{ data.title }}: {{ data.currency }}{{ data.price }} {{ data.price_description }}', 
			'button_text' => __( 'Add Pricing Table', 'youxi' ), 
			'max' => 5, 
			'fields' => array( array( $manager, 'get_shortcode_fields' ), 'pricing_table' ), 
			'serialize' => 'js:function( data ) {
				return this.construct( "pricing_table", data );
			}', 
			'deserialize' => 'js:function( data ) {
				return this.deserializeArray( data );
			}'
		), 
		'callback' => 'youxi_shortcode_pricing_tables_cb'
	));
	$manager->add_shortcode( 'pricing_table', array(
		'label' => __( 'Pricing Table', 'youxi' ), 
		'category' => 'content', 
		'priority' => 90, 
		'icon' => 'fa fa-usd', 
		'internal' => true, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => __( 'Title', 'youxi' ), 
				'description' => __( 'Enter here the table title.', 'youxi' )
			), 
			'show_price' => array(
				'type' => 'switch', 
				'label' => __( 'Show Price', 'youxi' ), 
				'description' => __( 'Switch to hide/show the price.', 'youxi' ), 
				'std' => 1
			), 
			'currency' => array(
				'type' => 'text', 
				'label' => __( 'Currency Symbol', 'youxi' ), 
				'description' => __( 'Enter here the currency symbol.', 'youxi' ), 
				'std' => '$', 
				'criteria' => 'show_price:is(1)'
			), 
			'price' => array(
				'type' => 'text', 
				'label' => __( 'Price', 'youxi' ), 
				'description' => __( 'Enter here the price value.', 'youxi' ), 
				'std' => 0.0, 
				'criteria' => 'show_price:is(1)'
			), 
			'price_description' => array(
				'type' => 'text', 
				'label' => __( 'Price Description', 'youxi' ), 
				'description' => __( 'Enter here a little note to display below the price.', 'youxi' ), 
				'std' => 'per month', 
				'criteria' => 'show_price:is(1)'
			), 
			'show_btn' => array(
				'type' => 'switch', 
				'label' => __( 'Show Button', 'youxi' ), 
				'description' => __( 'Switch to hide/show the button.', 'youxi' ), 
				'std' => 1
			), 
			'btn_action' => array(
				'type' => 'radio', 
				'label' => __( 'Button Action', 'youxi' ), 
				'description' => __( 'Choose the action to execute after clicking the button.', 'youxi' ), 
				'choices' => array(
					'url' => __( 'Go to URL', 'youxi' ), 
					'page' => __( 'Go to Page', 'youxi' )
				), 
				'std' => 'url', 
				'criteria' => 'show_btn:is(1)'
			), 
			'post_id' => array(
				'type' => 'select', 
				'label' => __( 'Page', 'youxi' ), 
				'description' => __( 'Choose the page to view after clicking the button.', 'youxi' ), 
				'choices' => 'youxi_shortcode_page_choices', 
				'criteria' => array(
					'operator' => 'and', 
					'condition' => array( 'btn_action:is(page)', 'show_btn:is(1)' )
				)
			), 
			'url' => array(
				'type' => 'text', 
				'label' => __( 'URL', 'youxi' ), 
				'description' => __( 'Enter the URL to go to after clicking the button.', 'youxi' ), 
				'std' => '#', 
				'criteria' => array(
					'operator' => 'and', 
					'condition' => array( 'btn_action:is(url)', 'show_btn:is(1)' )
				)
			), 
			'btn_text' => array(
				'type' => 'text', 
				'label' => __( 'Button Text', 'youxi' ), 
				'description' => __( 'Enter here the text to display on the button.', 'youxi' ), 
				'std' => 'Sign Up', 
				'criteria' => 'show_btn:is(1)'
			), 
			'color' => array(
				'type' => 'select', 
				'label' => __( 'Color Scheme', 'youxi' ), 
				'description' => __( 'Choose the base color of the pricing table.', 'youxi' ), 
				'choices' => array(
					'default' => __( 'Default', 'youxi' ), 
					'primary' => __( 'Primary', 'youxi' ), 
					'success' => __( 'Success', 'youxi' ), 
					'warning' => __( 'Warning', 'youxi' ), 
					'danger' => __( 'Danger', 'youxi' ), 
					'info' => __( 'Info', 'youxi' )
				), 
				'std' => 'default'
			), 
			'featured' => array(
				'type' => 'switch', 
				'label' => __( 'Featured', 'youxi' ), 
				'description' => __( 'Switch to display the pricing table as featured.', 'youxi' ), 
				'std' => 0
			)
		), 
		'content' => array(
			'type' => 'repeater', 
			'label' => __( 'Features', 'youxi' ), 
			'description' => __( 'Enter here the features to display on this pricing table.', 'youxi' ), 
			'preview_template' => '{{ data.name }}', 
			'button_text' => __( 'Add Feature', 'youxi' ), 
			'fields' => array(
				'name' => array(
					'type' => 'text', 
					'label' => __( 'Name', 'youxi' )
				)
			), 
			'serialize' => 'js:function( data ) {
				var li = $("<li/>"), ul = $("<ul/>");
				ul.append( $.map( data || [], function(v) {
					if( v.hasOwnProperty("name") ) {
						return li.clone().text( v.name );
					}
				}));
				return ul[0].outerHTML;
			}', 
			'deserialize' => 'js:function( data ) {
				return $( data ).children("li").map(function() {
					return { name: $( this ).html() };
				}).get();
			}'
		), 
		'callback' => 'youxi_shortcode_pricing_table_cb'
	));

	/********************************************************************************
	 * Service shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'service', array(
		'label' => __( 'Service', 'youxi' ), 
		'category' => 'content', 
		'priority' => 100, 
		'icon' => 'fa fa-magic', 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => __( 'Title', 'youxi' ), 
				'description' => __( 'Enter here the service name.', 'youxi' )
			), 
			'show_btn' => array(
				'type' => 'switch', 
				'label' => __( 'Show Button', 'youxi' ), 
				'description' => __( 'Switch to hide/show the button.', 'youxi' ), 
				'std' => 1
			), 
			'btn_text' => array(
				'type' => 'text', 
				'label' => __( 'Button Text', 'youxi' ), 
				'description' => __( 'Enter the text to display on the button.', 'youxi' ), 
				'criteria' => 'show_btn:is(1)'
			), 
			'btn_size' => array(
				'type' => 'select', 
				'label' => __( 'Button Size', 'youxi' ), 
				'description' => __( 'Choose the size of the button.', 'youxi' ), 
				'choices' => array(
					0 => __( 'Default', 'youxi' ), 
					'lg' => __( 'Large', 'youxi' ), 
					'sm' => __( 'Small', 'youxi' ), 
					'xs' => __( 'Extra Small', 'youxi' )
				), 
				'std' => 0
			), 
			'btn_type' => array(
				'type' => 'select', 
				'label' => __( 'Button Type', 'youxi' ), 
				'description' => __( 'Choose the type of the button.', 'youxi' ), 
				'choices' => array(
					'default' => __( 'Default', 'youxi' ), 
					'primary' => __( 'Primary', 'youxi' ), 
					'success' => __( 'Success', 'youxi' ), 
					'warning' => __( 'Warning', 'youxi' ), 
					'danger' => __( 'Danger', 'youxi' ), 
					'info' => __( 'Info', 'youxi' )
				), 
				'std' => 'primary', 
				'criteria' => 'show_btn:is(1)'
			), 
			'btn_action' => array(
				'type' => 'radio', 
				'label' => __( 'Button Action', 'youxi' ), 
				'description' => __( 'Choose the action to execute after clicking the button.', 'youxi' ), 
				'choices' => array(
					'url' => 'Go to URL', 
					'page' => 'Go to Page'
				), 
				'std' => 'url', 
				'criteria' => 'show_btn:is(1)'
			), 
			'post_id' => array(
				'type' => 'select', 
				'label' => __( 'Page', 'youxi' ), 
				'description' => __( 'Choose the page to view after clicking the button.', 'youxi' ), 
				'choices' => 'youxi_shortcode_page_choices', 
				'criteria' => array(
					'condition' => array( 'btn_action:is(page)', 'show_btn:is(1)' )
				)
			), 
			'url' => array(
				'type' => 'text', 
				'label' => __( 'URL', 'youxi' ), 
				'description' => __( 'Enter the URL to go to after clicking the button.', 'youxi' ), 
				'std' => '#', 
				'criteria' => array(
					'condition' => array( 'btn_action:is(url)' , 'show_btn:is(1)' )
				)
			)
		), 
		'content' => array(
			'type' => 'richtext', 
			'label' => __( 'Description', 'youxi' ), 
			'description' => __( 'Enter the description of this service.', 'youxi' ), 
			'tinymce' => array(
				'media_buttons' => false, 
				'tinymce' => false
			)
		), 
		'callback' => 'youxi_shortcode_service_cb'
	));

	/********************************************************************************
	 * Table shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'table', array(
		'label' => __( 'Table', 'youxi' ), 
		'priority' => 110, 
		'icon' => 'fa fa-table', 
		'category' => 'content', 
		'atts' => array(
			'styles' => array(
				'type' => 'checkboxlist', 
				'label' => __( 'Table Styles', 'youxi' ), 
				'description' => __( 'Choose here the table styles to display.', 'youxi' ), 
				'choices' => array(
					'striped' => __( 'Striped', 'youxi' ), 
					'bordered' => __( 'Bordered', 'youxi' ), 
					'hover' => __( 'Hoverable', 'youxi' )
				), 
				'serialize' => 'js:function( data ) {
					return ( data || [] ).join(",");
				}', 
				'deserialize' => 'js:function( data ) {
					return ( data + "" ).split(",");
				}'
			)
		), 
		'content' => array(
			'type' => 'tabular', 
			'label' => __( 'Content', 'youxi' ), 
			'serialize' => 'js:function( data ) {
				var t = this, 
					tags = {
						headers: "table_head", 
						cells: "table_body"
					};
				return $.map( tags, function( tag, key ) {
					if( _.has( data, key ) ) {
						return t.construct( tag, { content: data[ key ] } );
					}
				}).join( "\n\n" );
			}', 
			'deserialize' => 'js:function( data ) {
				var t = this, 
					table = {}, 
					tags = ["table_head", "table_body"], 
					keys = ["headers", "cells"];

				_.each( data, function( data, index ) {
					table[ keys[ index ] ] = t.deserialize( tags[ index ], "content", data );
				});
				return $.extend( true, {}, table );
			}'
		), 	
		'callback' => 'youxi_shortcode_table_cb'
	));
	$manager->add_shortcode( 'table_head', array(
		'label' => __( 'Table Head', 'youxi' ), 
		'internal' => true, 
		'content' => array(
			'serialize' => 'js:function( data ) {
				var headers = _.map( data, function( header ) {
					return { content: header };
				});
				data = this.construct( "table_header", headers );
				return this.construct( "table_row", { content: data } );
			}', 
			'deserialize' => 'js:function( data ) {
				if( _.has( data, "content" ) && _.isArray( data.content ) ) {
					var mapped = _.map( data.content, function( data ) {
						return this.deserialize( data.tag, "content", data );
					}, this );

					// Take only the first <tr> in <thead>
					data = mapped.length ? mapped[0] : data;
				}
				return data;
			}'
		), 
		'callback' => 'youxi_shortcode_table_cb'
	));
	$manager->add_shortcode( 'table_body', array(
		'label' => __( 'Table Body', 'youxi' ), 
		'internal' => true, 
		'content' => array(
			'serialize' => 'js:function( data ) {
				if( _.isArray( data ) ) {
					var rows = _.map( data, function( cell ) {
						cell = _.map( cell, function( content ) {
							return { content: content };
						});
						return { content: this.construct( "table_cell", cell ) };
					}, this );

					data = this.construct( "table_row", rows );
				}
				return data;
			}', 
			'deserialize' => 'js:function( data ) {
				if( _.has( data, "content" ) && _.isArray( data.content ) ) {
					data = _.map( data.content, function( data ) {
						return this.deserialize( data.tag, "content", data );
					}, this );
				}
				return data;
			}'
		), 
		'callback' => 'youxi_shortcode_table_cb'
	));
	$manager->add_shortcode( 'table_header', array(
		'label' => __( 'Table Header', 'youxi' ), 
		'internal' => true, 
		'insert_nl' => false, 
		'callback' => 'youxi_shortcode_table_cb'
	));
	$manager->add_shortcode( 'table_cell', array(
		'label' => __( 'Table Cell', 'youxi' ), 
		'internal' => true, 
		'insert_nl' => false, 
		'callback' => 'youxi_shortcode_table_cb'
	));
	$manager->add_shortcode( 'table_row', array(
		'label' => __( 'Table Row', 'youxi' ), 
		'internal' => true, 
		'content' => array(
			'deserialize' => 'js:function( data ) {
				if( _.has( data, "content" ) && _.isArray( data.content ) ) {
					data = $.map( data.content, function( data ) {
						if( _.has( data, "content" ) ) {
							return data.content;
						}
					});
				}
				return data;
			}'
		), 
		'callback' => 'youxi_shortcode_table_cb'
	));

	/********************************************************************************
	 * Tabs shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'tabs', array(
		'label' => __( 'Tabs', 'youxi' ), 
		'category' => 'content', 
		'priority' => 120, 
		'icon' => 'fa fa-folder-o', 
		'atts' => array(
			'type' => array(
				'type' => 'select', 
				'label' => __( 'Tab Type', 'youxi' ), 
				'description' => __( 'Choose here the tabs type.', 'youxi' ), 
				'choices' => array(
					'tabs' => __( 'Tabs', 'youxi' ), 
					'pills' => __( 'Pills', 'youxi' )
				), 
				'std' => 'tabs'
			)
		), 
		'content' => array(
			'type' => 'repeater', 
			'label' => __( 'Tabs', 'youxi' ), 
			'description' => __( 'Enter here the title and content of each tab.', 'youxi' ), 
			'fields' => array( array( $manager, 'get_shortcode_fields' ), 'tab' ), 
			'preview_template' => '{{ data.title }}', 
			'serialize' => 'js:function( data ) {
				return this.construct( "tab", data );
			}', 
			'deserialize' => 'js:function( data ) {
				return this.deserializeArray( data );
			}'
		), 	
		'callback' => 'youxi_shortcode_tabs_cb'
	));
	$manager->add_shortcode( 'tab', array(
		'label' => __( 'Tab', 'youxi' ), 
		'category' => 'content', 
		'internal' => true, 
		'insert_nl' => false, 
		'atts' => array(
			'title' => array(
				'type' => 'text', 
				'label' => __( 'Title', 'youxi' ), 
				'description' => __( 'Enter here the tab title.', 'youxi' )
			)
		), 
		'content' => array(
			'type' => 'textarea', 
			'label' => __( 'Content', 'youxi' ), 
			'description' => __( 'Enter here the tab content.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_tab_cb'
	));

	/********************************************************************************
	 * Team shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'team', array(
		'label' => __( 'Team', 'youxi' ), 
		'category' => 'content', 
		'priority' => 130, 
		'icon' => 'fa fa-user', 
		'insert_nl' => false, 
		'atts' => array(
			'name' => array(
				'type' => 'text', 
				'label' => __( 'Name', 'youxi' ), 
				'description' => __( 'Enter here the team member\'s name.', 'youxi' )
			), 
			'role' => array(
				'type' => 'text', 
				'label' => __( 'Role', 'youxi' ), 
				'description' => __( 'Enter here the role of the team member.', 'youxi' )
			), 
			'photo' => array(
				'type' => 'image', 
				'label' => __( 'Photo', 'youxi' ), 
				'description' => __( 'Choose a photo for the team member.', 'youxi' ), 
				'return_type' => 'url', 
				'frame_title' => __( 'Choose a Photo', 'youxi' ), 
				'frame_btn_text' => __( 'Insert URL', 'youxi' ), 
				'upload_btn_text' => __( 'Choose a Photo', 'youxi' )
			)
		), 
		'content' => array(
			'type' => 'textarea', 
			'label' => __( 'About', 'youxi' ), 
			'description' => __( 'Enter here a short description about the team member.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_team_cb'
	));

	/********************************************************************************
	 * Testimonials shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'testimonials', array(
		'label' => __( 'Testimonials', 'youxi' ), 
		'category' => 'content', 
		'priority' => 140, 
		'icon' => 'fa fa-comments', 
		'content' => array(
			'type' => 'repeater', 
			'label' => __( 'Testimonials', 'youxi' ), 
			'description' => __( 'Enter here the testimonials to display.', 'youxi' ), 
			'fields' => array( array( $manager, 'get_shortcode_fields' ), 'testimonial' ), 
			'preview_template' => '{{ data.author }}', 
			'serialize' => 'js:function( data ) {
				return this.construct( "testimonial", data );
			}', 
			'deserialize' => 'js:function( data ) {
				return this.deserializeArray( data );
			}'
		), 
		'callback' => 'youxi_shortcode_testimonials_cb'
	));

	/********************************************************************************
	 * Testimonial shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'testimonial', array(
		'label' => __( 'Testimonial', 'youxi' ), 
		'category' => 'content', 
		'priority' => 150, 
		'icon' => 'fa fa-comment', 
		'insert_nl' => false, 
		'atts' => array(
			'author' => array(
				'type' => 'text', 
				'label' => __( 'Author', 'youxi' ), 
				'description' => __( 'Enter here the testimonial author.', 'youxi' )
			), 
			'source' => array(
				'type' => 'text', 
				'label' => __( 'Source', 'youxi' ), 
				'description' => __( 'Enter here the testimonial source.', 'youxi' )
			), 
			'source_url' => array(
				'type' => 'url', 
				'label' => __( 'Source URL', 'youxi' ), 
				'description' => __( 'Enter here the testimonial source URL.', 'youxi' ), 
				'std' => '#'
			)
		), 
		'content' => array(
			'type' => 'textarea', 
			'label' => __( 'Content', 'youxi' ), 
			'description' => __( 'Enter here the testimonial content.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_testimonial_cb'
	));

	/********************************************************************************
	 * Text widget shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'text_widget', array(
		'label' => __( 'Text Widget', 'youxi' ), 
		'category' => 'content', 
		'priority' => 160, 
		'icon' => 'fa fa-paragraph', 
		'content' => array(
			'type' => 'richtext', 
			'label' => __( 'Content', 'youxi' ), 
			'description' => __( 'Enter here the text to display.', 'youxi' )
		), 
		'callback' => 'youxi_shortcode_text_widget_cb'
	));

	/********************************************************************************
	 * Widget area shortcode
	 ********************************************************************************/
	$manager->add_shortcode( 'widget_area', array(
		'label' => __( 'Widget Area', 'youxi' ), 
		'category' => 'content', 
		'priority' => 170, 
		'icon' => 'fa fa-columns', 
		'atts' => array(
			'id' => array(
				'type' => 'select', 
				'label' => __( 'Widget Area', 'youxi' ), 
				'description' => __( 'Choose the widget area to display.', 'youxi' ), 
				'choices' => 'youxi_shortcode_widget_area_choices'
			)
		), 
		'callback' => 'youxi_shortcode_widget_area_cb'
	));
}

/**
 * Hook to 'youxi_shortcode_register'
 */
add_action( 'youxi_shortcode_register', 'define_content_shortcodes', 1 );