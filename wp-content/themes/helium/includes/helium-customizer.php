<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

if( ! class_exists( 'Youxi_Customize_Manager' ) ) {
	require( get_template_directory() . '/lib/framework/customizer/class-manager.php' );
}

class Helium_Customize_Manager extends Youxi_Customize_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {

		parent::__construct();

		add_action( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_control_scripts' ) );

		add_action( 'customize_register', array( $this, 'site_customizer' ) );
		add_action( 'customize_register', array( $this, 'color_customizer' ) );
		add_action( 'customize_register', array( $this, 'typography_customizer' ) );
		add_action( 'customize_register', array( $this, 'blog_customizer' ) );

		if( defined( 'YOUXI_PORTFOLIO_VERSION' ) ) {
			add_action( 'customize_register', array( $this, 'portfolio_customizer' ) );
		}
		if( class_exists( 'Easy_Digital_Downloads' ) ) {
			add_action( 'customize_register', array( $this, 'edd_customizer' ) );
		}
	}

	public function enqueue_control_scripts() {

		/* Get theme version */
		$wp_theme = wp_get_theme();
		$theme_version = $wp_theme->exists() ? $wp_theme->get( 'Version' ) : false;

		wp_enqueue_script( 'helium-customize-controls', get_template_directory_uri() . '/assets/admin/js/helium.customize-controls.js', array( 'customize-controls' ), $theme_version, true );
		wp_localize_script( 'helium-customize-controls', '_heliumCustomizeControls', array( 'prefix' => $this->prefix() ) );
	}

	public function pre_customize( $wp_customize ) {

		parent::pre_customize( $wp_customize );

		/* Remove predefined sections and controls */
		$wp_customize->remove_section( 'nav' );
	}

	public function site_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Section: Header */

		$wp_customize->add_section( $prefix . '_header', array(
			'title' => esc_html__( 'Header', 'helium' ), 
			'priority' => 41
		));

		/* Header Settings */

		$wp_customize->add_setting( $prefix . '[logo_image]', array(
			'default' => '', 
			'sanitize_callback' => 'esc_url_raw'
		));
		$wp_customize->add_setting( $prefix . '[logo_height]', array(
			'default' => 25, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[show_search]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[copyright_text]', array(
			'default' => esc_html__( '&copy; Youxi Themes. 2012-2014. All Rights Reserved.', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));

		/* Header Controls */

		$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize, $prefix . '[logo_image]', array(
				'label' => esc_html__( 'Logo Image', 'helium' ), 
				'section' => $prefix . '_header', 
				'priority' => 1
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[logo_height]', array(
				'label' => esc_html__( 'Max Logo Height', 'helium' ), 
				'section' => $prefix . '_header', 
				'min' => 0, 
				'max' => 640, 
				'step' => 1, 
				'priority' => 2
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[show_search]', array(
				'label' => esc_html__( 'Show Search', 'helium' ), 
				'section' => $prefix . '_header', 
				'priority' => 3
			)
		));
		$wp_customize->add_control( $prefix . '[copyright_text]', array(
			'label' => esc_html__( 'Copyright Text', 'helium' ), 
			'section' => $prefix . '_header', 
			'type' => 'text', 
			'priority' => 4
		));
	}

	public function color_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Styling Settings */

		$wp_customize->add_setting( $prefix . '[accent_color]', array(
			'default' => '#3dc9b3', 
			'sanitize_callback' => 'sanitize_hex_color'
		));

		/* Styling Controls */

		$wp_customize->add_control( new WP_Customize_Color_Control(
			$wp_customize, $prefix . '[accent_color]', array(
				'label' => esc_html__( 'Accent Color', 'helium' ), 
				'section' => 'colors', 
				'priority' => 1
			)
		));
	}

	public function typography_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Section: Typography */

		$wp_customize->add_section( $prefix . '_typography', array(
			'title' => esc_html__( 'Typography', 'helium' ), 
			'priority' => 41
		));

		/* Typography Settings */

		$wp_customize->add_setting( $prefix . '[body_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[headings_1234_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[headings_56_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[menu_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[blockquote_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[gridlist_filter_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[gridlist_title_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[gridlist_subtitle_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[content_title_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[content_nav_font]', array(
			'default' => ''
		));
		$wp_customize->add_setting( $prefix . '[widget_title_font]', array(
			'default' => ''
		));


		/* Typography Controls */

		$priority = 0;

		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[body_font]', array(
				'label' => esc_html__( 'Body Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => ++$priority
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[headings_1234_font]', array(
				'label' => esc_html__( 'H1, H2, H3, H4 Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => ++$priority
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[headings_56_font]', array(
				'label' => esc_html__( 'H5, H6 Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => ++$priority
			)
		));		
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[menu_font]', array(
				'label' => esc_html__( 'Menu Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => ++$priority
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[blockquote_font]', array(
				'label' => esc_html__( 'Blockquote Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => ++$priority
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[gridlist_filter_font]', array(
				'label' => esc_html__( 'Gridlist Filter Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => ++$priority
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[gridlist_title_font]', array(
				'label' => esc_html__( 'Gridlist Title Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => ++$priority
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[gridlist_subtitle_font]', array(
				'label' => esc_html__( 'Gridlist Subtitle Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => ++$priority
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[content_title_font]', array(
				'label' => esc_html__( 'Content Title Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => ++$priority
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[content_nav_font]', array(
				'label' => esc_html__( 'Content Navigation Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => ++$priority
			)
		));
		$wp_customize->add_control( new Youxi_Customize_WebFont_Control(
			$wp_customize, $prefix . '[widget_title_font]', array(
				'label' => esc_html__( 'Widget Title Font', 'helium' ), 
				'section' => $prefix . '_typography', 
				'priority' => ++$priority
			)
		));
	}

	public function blog_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		/* Panel: Blog */

		if( method_exists( $wp_customize, 'add_panel' ) ) {
			$section_priority = 0;
			$section_title_prefix = '';
			$wp_customize->add_panel( $prefix . '_blog', array(
				'title' => esc_html__( 'Blog', 'helium' ), 
				'priority' => 42
			));
		} else {
			$section_priority = 42;
			$section_title_prefix = esc_html__( 'Blog', 'helium' ) . ' ';
		}

		/* Section: Entries */

		$wp_customize->add_section( $prefix . '_blog_entries', array(
			'title' => $section_title_prefix . esc_html__( 'Entries', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Entries Settings */

		$wp_customize->add_setting( $prefix . '[hidden_post_meta]', array(
			'default' => array()
		));

		/* Entries Controls */

		$wp_customize->add_control( new Youxi_Customize_Multicheck_Control(
			$wp_customize, $prefix . '[hidden_post_meta]', array(
				'label' => esc_html__( 'Hide Post Meta', 'helium' ), 
				'section' => $prefix . '_blog_entries', 
				'choices' => array(
					'author' => esc_html__( 'Author', 'helium' ), 
					'category' => esc_html__( 'Category', 'helium' ), 
					'tags' => esc_html__( 'Tags', 'helium' ), 
					'comments' => esc_html__( 'Comments', 'helium' ), 
					'permalink' => esc_html__( 'Permalink', 'helium' )
				), 
				'priority' => 2
			)
		));

		/* Section: Posts */

		$wp_customize->add_section( $prefix . '_blog_posts', array(
			'title' => $section_title_prefix . esc_html__( 'Posts', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Posts Settings */

		$wp_customize->add_setting( $prefix . '[blog_show_tags]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[blog_sharing]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[blog_show_author]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[blog_related_posts]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[blog_related_posts_count]', array(
			'default' => 3, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[blog_related_posts_behavior]', array(
			'default' => 'lightbox'
		));


		/* Posts Controls */

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_show_tags]', array(
				'label' => esc_html__( 'Show Tags', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => 3
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_sharing]', array(
				'label' => esc_html__( 'Show Sharing Buttons', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => 4
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_show_author]', array(
				'label' => esc_html__( 'Show Author', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => 5
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[blog_related_posts]', array(
				'label' => esc_html__( 'Show Related Posts', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'priority' => 6
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[blog_related_posts_count]', array(
				'label' => esc_html__( 'Related Posts Count', 'helium' ), 
				'section' => $prefix . '_blog_posts', 
				'min' => 3, 
				'max' => 4, 
				'step' => 1, 
				'priority' => 7
			)
		));
		$wp_customize->add_control( $prefix . '[blog_related_posts_behavior]', array(
			'label' => esc_html__( 'Related Posts Behavior', 'helium' ), 
			'section' => $prefix . '_blog_posts', 
			'type' => 'select', 
			'choices' => array(
				'lightbox' => esc_html__( 'Show Lightbox', 'helium' ), 
				'permalink' => esc_html__( 'Go to Post', 'helium' )
			), 
			'priority' => 8
		));

		/* Section: Summary */

		$wp_customize->add_section( $prefix . '_blog_summary', array(
			'title' => $section_title_prefix . esc_html__( 'Summary', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Summary Settings */

		$wp_customize->add_setting( $prefix . '[blog_summary]', array(
			'default' => 'the_excerpt'
		));
		$wp_customize->add_setting( $prefix . '[blog_excerpt_length]', array(
			'default' => 100, 
			'sanitize_callback' => 'absint'
		));

		/* Summary Controls */

		$wp_customize->add_control( $prefix . '[blog_summary]', array(
			'label' => esc_html__( 'Summary Display', 'helium' ), 
			'section' => $prefix . '_blog_summary', 
			'type' => 'radio', 
			'choices' => array(
				'the_excerpt' => esc_html__( 'Excerpt', 'helium' ), 
				'the_content' => esc_html__( 'More Tag', 'helium' ), 
			), 
			'priority' => 1
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[blog_excerpt_length]', array(
				'label' => esc_html__( 'Excerpt Length', 'helium' ), 
				'section' => $prefix . '_blog_summary', 
				'min' => 55, 
				'max' => 250, 
				'step' => 1, 
				'priority' => 2
			)
		));	

		/* Section: Layout */

		$wp_customize->add_section( $prefix . '_blog_layout', array(
			'title' => $section_title_prefix . esc_html__( 'Layout', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Layout Settings */

		$wp_customize->add_setting( $prefix . '[blog_index_layout]', array(
			'default' => 'boxed'
		));
		$wp_customize->add_setting( $prefix . '[blog_archive_layout]', array(
			'default' => 'boxed'
		));
		$wp_customize->add_setting( $prefix . '[blog_single_layout]', array(
			'default' => 'boxed'
		));

		/* Layout Controls */

		$wp_customize->add_control( $prefix . '[blog_index_layout]', array(
			'label' => esc_html__( 'Index', 'helium' ), 
			'section' => $prefix . '_blog_layout', 
			'type' => 'select', 
			'choices' => array(
				'boxed' => esc_html__( 'Boxed', 'helium' ), 
				'fullwidth' => esc_html__( 'Fullwidth', 'helium' ), 
			), 
			'priority' => 1
		));
		$wp_customize->add_control( $prefix . '[blog_archive_layout]', array(
			'label' => esc_html__( 'Archive', 'helium' ), 
			'section' => $prefix . '_blog_layout', 
			'type' => 'select', 
			'choices' => array(
				'boxed' => esc_html__( 'Boxed', 'helium' ), 
				'fullwidth' => esc_html__( 'Fullwidth', 'helium' ), 
			), 
			'priority' => 2
		));
		$wp_customize->add_control( $prefix . '[blog_single_layout]', array(
			'label' => esc_html__( 'Single', 'helium' ), 
			'section' => $prefix . '_blog_layout', 
			'type' => 'select', 
			'choices' => array(
				'boxed' => esc_html__( 'Boxed', 'helium' ), 
				'fullwidth' => esc_html__( 'Fullwidth', 'helium' ), 
			), 
			'priority' => 3
		));


		/* Section: Titles */

		$wp_customize->add_section( $prefix . '_blog_titles', array(
			'title' => $section_title_prefix . esc_html__( 'Titles', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_blog'
		));

		/* Titles Settings */

		$wp_customize->add_setting( $prefix . '[blog_index_title]', array(
			'default' => esc_html__( 'Welcome to Our Blog', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[blog_single_title]', array(
			'default' => esc_html__( 'Currently Reading', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[blog_category_title]', array(
			'default' => esc_html__( 'Category: {category}', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[blog_tag_title]', array(
			'default' => esc_html__( 'Posts Tagged &lsquo;{tag}&rsquo;', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[blog_author_title]', array(
			'default' => esc_html__( 'Posts by {author}', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[blog_date_title]', array(
			'default' => esc_html__( 'Archive for {date}', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));

		/* Titles Controls */

		$wp_customize->add_control( $prefix . '[blog_index_title]', array(
			'label' => esc_html__( 'Index', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'priority' => 1
		));
		$wp_customize->add_control( $prefix . '[blog_single_title]', array(
			'label' => esc_html__( 'Single', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => wp_kses( __( 'Use <strong>{title}</strong> for the post title.', 'helium' ), array( 'strong' => array() ) ), 
			'priority' => 2
		));
		$wp_customize->add_control( $prefix . '[blog_category_title]', array(
			'label' => esc_html__( 'Category Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => wp_kses( __( 'Use <strong>{category}</strong> for the category name.', 'helium' ), array( 'strong' => array() ) ), 
			'priority' => 3
		));
		$wp_customize->add_control( $prefix . '[blog_tag_title]', array(
			'label' => esc_html__( 'Tag Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => wp_kses( __( 'Use <strong>{tag}</strong> for the tag name.', 'helium' ), array( 'strong' => array() ) ), 
			'priority' => 4
		));
		$wp_customize->add_control( $prefix . '[blog_author_title]', array(
			'label' => esc_html__( 'Author Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => wp_kses( __( 'Use <strong>{author}</strong> for the author name.', 'helium' ), array( 'strong' => array() ) ), 
			'priority' => 5
		));
		$wp_customize->add_control( $prefix . '[blog_date_title]', array(
			'label' => esc_html__( 'Date Archive', 'helium' ), 
			'section' => $prefix . '_blog_titles', 
			'type' => 'text', 
			'description' => wp_kses( __( 'Use <strong>{date}</strong> for the date.', 'helium' ), array( 'strong' => array() ) ), 
			'priority' => 6
		));
	}

	public function portfolio_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		if( method_exists( $wp_customize, 'add_panel' ) ) {
			$section_priority  = 0;
			$section_title_prefix = '';
			$wp_customize->add_panel( $prefix . '_portfolio', array(
				'title' => esc_html__( 'Portfolio', 'helium' ), 
				'priority' => 46
			));
		} else {
			$section_priority = 46;
			$section_title_prefix = esc_html__( 'Portfolio', 'helium' ) . ' ';
		}

		/* Section: Single */

		$wp_customize->add_section( $prefix . '_portfolio_single', array(
			'title' => $section_title_prefix . esc_html__( 'Single Item', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_portfolio'
		));

		/* Single Settings */

		$wp_customize->add_setting( $prefix . '[portfolio_show_related_items]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[portfolio_related_items_count]', array(
			'default' => 3, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_related_items_behavior]', array(
			'default' => 'lightbox'
		));

		/* Single Controls */

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[portfolio_show_related_items]', array(
				'label' => esc_html__( 'Show Related Items', 'helium' ), 
				'section' => $prefix . '_portfolio_single', 
				'priority' => 1
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[portfolio_related_items_count]', array(
				'label' => esc_html__( 'Related Items Count', 'helium' ), 
				'section' => $prefix . '_portfolio_single', 
				'min' => 3, 
				'max' => 4, 
				'step' => 1, 
				'priority' => 2
			)
		));
		$wp_customize->add_control( $prefix . '[portfolio_related_items_behavior]', array(
			'label' => esc_html__( 'Related Items Behavior', 'helium' ), 
			'section' => $prefix . '_portfolio_single', 
			'type' => 'select', 
			'choices' => array(
				'lightbox' => esc_html__( 'Show Lightbox', 'helium' ), 
				'permalink' => esc_html__( 'Go to Post', 'helium' )
			), 
			'priority' => 3
		));


		/* Section: Archive */

		$wp_customize->add_section( $prefix . '_portfolio_archive', array(
			'title' => $section_title_prefix . esc_html__( 'Archive', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_portfolio'
		));

		/* Archive Settings */

		$wp_customize->add_setting( $prefix . '[portfolio_archive_page_title]', array(
			'default' => esc_html__( 'Portfolio Archive', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));

		/* Archive Controls */

		$wp_customize->add_control( $prefix . '[portfolio_archive_page_title]', array(
			'label' => esc_html__( 'Page Title', 'helium' ), 
			'section' => $prefix . '_portfolio_archive', 
			'type' => 'text', 
			'priority' => 1
		));

		/* Section: Grid */

		$wp_customize->add_section( $prefix . '_portfolio_grid', array(
			'title' => $section_title_prefix . esc_html__( 'Grid Settings', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_portfolio'
		));

		/* Grid Settings */

		$wp_customize->add_setting( $prefix . '[portfolio_grid_show_filter]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_pagination]', array(
			'default' => 'ajax'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_ajax_button_text]', array(
			'default' => esc_html__( 'Load More', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_ajax_button_complete_text]', array(
			'default' => esc_html__( 'No More Items', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_posts_per_page]', array(
			'default' => get_option( 'posts_per_page' ), 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_include]', array(
			'default' => array()
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_behavior]', array(
			'default' => 'lightbox'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_orderby]', array(
			'default' => 'date'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_order]', array(
			'default' => 'DESC'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_layout]', array(
			'default' => 'masonry'
		));
		$wp_customize->add_setting( $prefix . '[portfolio_grid_columns]', array(
			'default' => 4, 
			'sanitize_callback' => 'absint'
		));
		// $wp_customize->add_setting( $prefix . '[portfolio_grid_justified_min_height]', array(
		// 	'default' => 240
		// ));

		/* Grid Controls */

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[portfolio_grid_show_filter]', array(
				'label' => esc_html__( 'Show Filter', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'priority' => 2
			)
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_pagination]', array(
			'label' => esc_html__( 'Pagination', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'ajax' => esc_html__( 'AJAX', 'helium' ), 
				'infinite' => esc_html__( 'Infinite', 'helium' ), 
				'numbered' => esc_html__( 'Numbered', 'helium' ), 
				'prev_next' => esc_html__( 'Prev/Next', 'helium' ), 
				'show_all' => esc_html__( 'None (Show all)', 'helium' )
			), 
			'priority' => 3
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_ajax_button_text]', array(
			'label' => esc_html__( 'AJAX Button Text', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'text', 
			'priority' => 4
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_ajax_button_complete_text]', array(
			'label' => esc_html__( 'AJAX Button Complete Text', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'text', 
			'priority' => 5
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[portfolio_grid_posts_per_page]', array(
				'label' => esc_html__( 'Items per Page', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'min' => 1, 
				'max' => 20, 
				'step' => 1, 
				'priority' => 5.5
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Multicheck_Control(
			$wp_customize, $prefix . '[portfolio_grid_include]', array(
				'label' => esc_html__( 'Included Categories', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'choices' => get_terms( youxi_portfolio_tax_name(), array( 'fields' => 'id=>name' ) ), 
				'priority' => 5.6, 
				'description' => esc_html__( 'Uncheck all to include all categories.', 'helium' )
			)
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_behavior]', array(
			'label' => esc_html__( 'Behavior', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'none' => esc_html__( 'None', 'helium' ), 
				'lightbox' => esc_html__( 'Show Image in Lightbox', 'helium' ), 
				'page' => esc_html__( 'Go to Detail Page', 'helium' )
			), 
			'priority' => 6
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_orderby]', array(
			'label' => esc_html__( 'Order By', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'date' => esc_html__( 'Date', 'helium' ), 
				'menu_order' => esc_html__( 'Menu Order', 'helium' ), 
				'title' => esc_html__( 'Title', 'helium' ), 
				'ID' => esc_html__( 'ID', 'helium' )
			), 
			'priority' => 7
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_order]', array(
			'label' => esc_html__( 'Order', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'DESC' => esc_html__( 'Descending', 'helium' ), 
				'ASC' => esc_html__( 'Ascending', 'helium' )
			), 
			'priority' => 8
		));
		$wp_customize->add_control( $prefix . '[portfolio_grid_layout]', array(
			'label' => esc_html__( 'Layout', 'helium' ), 
			'section' => $prefix . '_portfolio_grid', 
			'type' => 'select', 
			'choices' => array(
				'masonry' => esc_html__( 'Masonry', 'helium' ), 
				'classic' => esc_html__( 'Classic', 'helium' ), 
				'justified' => esc_html__( 'Justified', 'helium' )
			), 
			'priority' => 9
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[portfolio_grid_columns]', array(
				'label' => esc_html__( 'Columns (Masonry/Classic)', 'helium' ), 
				'section' => $prefix . '_portfolio_grid', 
				'min' => 3, 
				'max' => 5, 
				'step' => 1, 
				'priority' => 10
			)
		));
	}

	public function edd_customizer( $wp_customize ) {

		$prefix = $this->prefix();

		if( method_exists( $wp_customize, 'add_panel' ) ) {
			$section_priority = 0;
			$section_title_prefix = '';
			$wp_customize->add_panel( $prefix . '_edd', array(
				'title' => esc_html__( 'Easy Digital Downloads', 'helium' ), 
				'priority' => 48
			));
		} else {
			$section_priority = 48;
			$section_title_prefix = esc_html__( 'EDD', 'helium' ) . ' ';
		}

		/* Section: General */

		$wp_customize->add_section( $prefix . '_edd_general', array(
			'title' => $section_title_prefix . esc_html__( 'General', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* General Settings */

		$wp_customize->add_setting( $prefix . '[edd_show_cart]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));

		/* General Controls */

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_cart]', array(
				'label' => esc_html__( 'Show Cart in Header', 'helium' ), 
				'section' => $prefix . '_edd_general', 
				'priority' => 1
			)
		));

		/* Section: Single */

		$wp_customize->add_section( $prefix . '_edd_single', array(
			'title' => $section_title_prefix . esc_html__( 'Single Downloads', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* Single Settings */
		
		$wp_customize->add_setting( $prefix . '[edd_show_categories]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[edd_show_tags]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[edd_show_sharing_buttons]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[edd_show_related_items]', array(
			'default' => true, 
			'sanitize_callback' => array( get_class(), 'sanitize_boolean' )
		));
		$wp_customize->add_setting( $prefix . '[edd_related_items_count]', array(
			'default' => 3, 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[edd_related_items_behavior]', array(
			'default' => 'lightbox'
		));
		

		/* Single Controls */

		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_categories]', array(
				'label' => esc_html__( 'Show Categories', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => 1
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_tags]', array(
				'label' => esc_html__( 'Show Tags', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => 2
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_sharing_buttons]', array(
				'label' => esc_html__( 'Show Sharing Buttons', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => 3
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Switch_Control(
			$wp_customize, $prefix . '[edd_show_related_items]', array(
				'label' => esc_html__( 'Show Related Items', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'priority' => 4
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[edd_related_items_count]', array(
				'label' => esc_html__( 'Related Items Count', 'helium' ), 
				'section' => $prefix . '_edd_single', 
				'min' => 3, 
				'max' => 4, 
				'step' => 1, 
				'priority' => 5
			)
		));
		$wp_customize->add_control( $prefix . '[edd_related_items_behavior]', array(
			'label' => esc_html__( 'Related Items Behavior', 'helium' ), 
			'section' => $prefix . '_edd_single', 
			'type' => 'select', 
			'choices' => array(
				'lightbox' => esc_html__( 'Show Lightbox', 'helium' ), 
				'permalink' => esc_html__( 'Go to Post', 'helium' )
			), 
			'priority' => 6
		));

		/* Section: Archive */

		$wp_customize->add_section( $prefix . '_edd_archive', array(
			'title' => $section_title_prefix . esc_html__( 'Archive', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* Archive Settings */

		$wp_customize->add_setting( $prefix . '[edd_archive_page_title]', array(
			'default' => esc_html__( 'Downloads Archive', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));

		/* Archive Controls */

		$wp_customize->add_control( $prefix . '[edd_archive_page_title]', array(
			'label' => esc_html__( 'Page Title', 'helium' ), 
			'section' => $prefix . '_edd_archive', 
			'type' => 'text', 
			'priority' => 1
		));

		/* Section: Grid */

		$wp_customize->add_section( $prefix . '_edd_grid', array(
			'title' => $section_title_prefix . esc_html__( 'Grid Settings', 'helium' ), 
			'priority' => ++$section_priority, 
			'panel' => $prefix . '_edd'
		));

		/* Grid Settings */

		$wp_customize->add_setting( $prefix . '[edd_grid_pagination]', array(
			'default' => 'ajax'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_ajax_button_text]', array(
			'default' => esc_html__( 'Load More', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_ajax_button_complete_text]', array(
			'default' => esc_html__( 'No More Items', 'helium' ), 
			'sanitize_callback' => 'sanitize_text_field'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_posts_per_page]', array(
			'default' => get_option( 'posts_per_page' ), 
			'sanitize_callback' => 'absint'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_include]', array(
			'default' => array()
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_behavior]', array(
			'default' => 'lightbox'
		));
		$wp_customize->add_setting( $prefix . '[edd_grid_columns]', array(
			'default' => 4, 
			'sanitize_callback' => 'absint'
		));

		/* Archive Controls */

		$wp_customize->add_control( $prefix . '[edd_grid_pagination]', array(
			'label' => esc_html__( 'Pagination', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'select', 
			'choices' => array(
				'ajax' => esc_html__( 'AJAX', 'helium' ), 
				'infinite' => esc_html__( 'Infinite', 'helium' ), 
				'numbered' => esc_html__( 'Numbered', 'helium' ), 
				'prev_next' => esc_html__( 'Prev/Next', 'helium' ), 
				'show_all' => esc_html__( 'None (Show all)', 'helium' )
			), 
			'priority' => 3
		));
		$wp_customize->add_control( $prefix . '[edd_grid_ajax_button_text]', array(
			'label' => esc_html__( 'AJAX Button Text', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'text', 
			'priority' => 4
		));
		$wp_customize->add_control( $prefix . '[edd_grid_ajax_button_complete_text]', array(
			'label' => esc_html__( 'AJAX Button Complete Text', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'text', 
			'priority' => 5
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[edd_grid_posts_per_page]', array(
				'label' => esc_html__( 'Items per Page', 'helium' ), 
				'section' => $prefix . '_edd_grid', 
				'min' => 1, 
				'max' => 20, 
				'step' => 1, 
				'priority' => 5.5
			)
		));
		$wp_customize->add_control( new Youxi_Customize_Multicheck_Control(
			$wp_customize, $prefix . '[edd_grid_include]', array(
				'label' => esc_html__( 'Included Categories', 'helium' ), 
				'section' => $prefix . '_edd_grid', 
				'choices' => get_terms( 'download_category', array( 'fields' => 'id=>name', 'hide_empty' => false ) ), 
				'priority' => 5.6, 
				'description' => esc_html__( 'Uncheck all to include all categories.', 'helium' )
			)
		));
		$wp_customize->add_control( $prefix . '[edd_grid_behavior]', array(
			'label' => esc_html__( 'Behavior', 'helium' ), 
			'section' => $prefix . '_edd_grid', 
			'type' => 'select', 
			'choices' => array(
				'none' => esc_html__( 'None', 'helium' ), 
				'lightbox' => esc_html__( 'Show Image in Lightbox', 'helium' ), 
				'page' => esc_html__( 'Go to Detail Page', 'helium' )
			), 
			'priority' => 6
		));
		$wp_customize->add_control( new Youxi_Customize_Range_Control(
			$wp_customize, $prefix . '[edd_grid_columns]', array(
				'label' => esc_html__( 'Number of Columns', 'helium' ), 
				'section' => $prefix . '_edd_grid', 
				'min' => 3, 
				'max' => 5, 
				'step' => 1, 
				'priority' => 7
			)
		));

		// foreach( $wp_customize->settings() as $setting ) {
		// 	if( preg_match( '/^helium_settings\[/', $setting->id ) ) {
		// 		printf( "'%s' => '%s', \n", $setting->id, $setting->default );
		// 	}
		// }
	}
}
new Helium_Customize_Manager();
