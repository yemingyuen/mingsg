<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Text Domain
============================================================================= */

if( ! function_exists( 'helium_load_theme_textdomain' ) ):

function helium_load_theme_textdomain() {
	load_theme_textdomain( 'helium', get_template_directory() . '/languages' );
}
endif;
add_action( 'after_setup_theme', 'helium_load_theme_textdomain' );

/* ==========================================================================
	Theme Support
============================================================================= */

if( ! function_exists( 'helium_add_theme_support' ) ):

function helium_add_theme_support() {

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array( 'image', 'video', 'audio', 'gallery' ) );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

	// Add RSS feed links to <head> for posts and comments.
	add_theme_support( 'automatic-feed-links' );

	// Enable support for Post Thumbnails
	add_theme_support( 'post-thumbnails' );

	// This theme uses its own gallery styles.
	add_filter( 'use_default_gallery_style', '__return_false' );
}
endif;
add_action( 'init', 'helium_add_theme_support' );

/* ==========================================================================
	Pre WordPress 4.1 <title>
============================================================================= */

if( ! function_exists( '_wp_render_title_tag' ) ):

function helium_render_title() {
	echo '<title>' . wp_title( '|', false, 'right' ) . "</title>" . PHP_EOL;
}
add_action( 'wp_head', 'helium_render_title' );

function helium_wp_title( $title, $sep ) {
	global $page, $paged;

	if( empty( $title ) && ! is_feed() ) {
		$title .= get_bloginfo( 'name', 'display' );

		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) ) {
			$title .= " $sep $site_description";
		}

		if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() ) {
			$title .= " $sep " . sprintf( esc_html__( 'Page %s', 'helium' ), max( $paged, $page ) );
		}
	}

	return $title;
}
add_filter( 'wp_title', 'helium_wp_title', 10, 2 );
endif;

/* ==========================================================================
	Image Sizes
============================================================================= */

if( ! function_exists( 'helium_add_image_sizes' ) ):

function helium_add_image_sizes() {

	$image_sizes = apply_filters( 'helium_wp_image_sizes', array(
		'helium_square' => array(
			'width'  => 640, 
			'height' => 640, 
			'crop'   => true
		), 
		'helium_4by3' => array(
			'width'  => 400, 
			'height' => 300, 
			'crop'   => true
		), 
		'helium_16by9' => array(
			'width'  => 800, 
			'height' => 450, 
			'crop'   => true
		), 
		'helium_portfolio_thumb_4by3' => array(
			'width' => 720, 
			'height' => 540, 
			'crop' => true
		), 
		'helium_portfolio_thumb_square' => array(
			'width' => 720, 
			'height' => 720, 
			'crop' => true
		), 
		'helium_portfolio_thumb' => array(
			'width' => 720
		)
	));

	foreach( $image_sizes as $name => $size ) {

		/* Skip reserved names */
		if( preg_match( '/^((post-)?thumbnail|thumb|medium|large)$/', $name ) ) {
			continue;
		}

		$size = wp_parse_args( $size, array(
			'width'  => 0, 
			'height' => 0, 
			'crop'   => false
		));
		add_image_size( $name, $size['width'], $size['height'], $size['crop'] );
	}
}
endif;
add_action( 'init', 'helium_add_image_sizes' );

/* ==========================================================================
	Widgets
============================================================================= */

if( ! function_exists( 'helium_widgets_init' ) ):

function helium_widgets_init() {

	register_sidebar( array(
		'name'          => esc_html__( 'Header Widget Area', 'helium' ), 
		'id'            => 'header_widget_area', 
		'description'   => esc_html__( 'This is the header widget area.', 'helium' ), 
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>', 
		'before_title'  => '<h4 class="widget-title">', 
		'after_title'   => '</h4>'
	));
}
endif;
add_action( 'widgets_init', 'helium_widgets_init' );

/* ==========================================================================
	Automatic Theme Updates
============================================================================= */

function helium_check_theme_updates( $updates ) {

	if( isset( $updates->checked ) ) {

		/* Get Envato username and API key */
		$envato_username = Youxi()->option->get( 'envato_username' );
		$envato_apikey   = Youxi()->option->get( 'envato_api_key' );

		if( '' !== $envato_username && '' !== $envato_apikey ) {
			if( ! class_exists( 'Pixelentity_Themes_Updater' ) ) {
				require( get_template_directory() . '/lib/class-pixelentity-themes-updater.php' );
			}

			$updater = new Pixelentity_Themes_Updater( $envato_username, $envato_apikey );
			$updates = $updater->check( $updates );
		}
	}

	return $updates;
}
add_filter( 'pre_set_site_transient_update_themes', 'helium_check_theme_updates' );

/* ==========================================================================
	Other WP Filters
============================================================================= */

/**
 * Deregister Default WordPress MEJS Styles
 */
if( ! function_exists( 'helium_wp_mediaelement' ) ):

function helium_wp_mediaelement() {

	/* Dequeue default wp mediaelement style */
	wp_deregister_style( 'mediaelement' );
	wp_deregister_style( 'wp-mediaelement' );
}
endif;
add_action( 'wp_enqueue_scripts', 'helium_wp_mediaelement' );

/* ==========================================================================
	User Social Profiles
============================================================================= */

if( ! function_exists( 'helium_user_social_profiles' ) ):

function helium_user_social_profiles() {
	return array(
		'twitter'     => esc_html__( 'Twitter', 'helium' ), 
		'facebook'    => esc_html__( 'Facebook', 'helium' ), 
		'googleplus'  => esc_html__( 'Google+', 'helium' ), 
		'pinterest'   => esc_html__( 'Pinterest', 'helium' ), 
		'linkedin'    => esc_html__( 'LinkedIn', 'helium' ), 
		'youtube'     => esc_html__( 'YouTube', 'helium' ), 
		'vimeo'       => esc_html__( 'Vimeo', 'helium' ), 
		'tumblr'      => esc_html__( 'tumblr', 'helium' ), 
		'instagram'   => esc_html__( 'Instagram', 'helium' ), 
		'flickr'      => esc_html__( 'Flickr', 'helium' ), 
		'dribbble'    => esc_html__( 'dribbble', 'helium' ), 
		'foursquare'  => esc_html__( 'Foursquare', 'helium' ), 
		'forrst'      => esc_html__( 'Forrst', 'helium' ), 
		'vkontakte'   => esc_html__( 'VKontakte', 'helium' ), 
		'wordpress'   => esc_html__( 'WordPress', 'helium' ), 
		'stumbleupon' => esc_html__( 'StumbleUpon', 'helium' ), 
		'yahoo'       => esc_html__( 'Yahoo!', 'helium' ), 
		'blogger'     => esc_html__( 'Blogger', 'helium' ), 
		'soundcloud'  => esc_html__( 'SoundCloud', 'helium' )
	);
}
endif;

/**
 * User Contact Methods
 */
if( ! function_exists( 'helium_user_contactmethods' ) ):

function helium_user_contactmethods( $methods ) {
	return array_merge( $methods, helium_user_social_profiles() );
}
endif;
add_filter( 'user_contactmethods', 'helium_user_contactmethods' );

/* ==========================================================================
	Modify Stylesheet URI
============================================================================= */

if( ! function_exists( 'helium_stylesheet_uri' ) ):

function helium_stylesheet_uri( $stylesheet_uri, $stylesheet_dir_uri ) {

	if( ! is_child_theme() ) {
		if( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			return $stylesheet_dir_uri . "/assets/css/helium.css";
		}
		return $stylesheet_dir_uri . "/assets/css/helium.min.css";
	}

	return $stylesheet_uri;	
}
endif;
add_filter( 'stylesheet_uri', 'helium_stylesheet_uri', 10, 2 );

/* ==========================================================================
	WordPress Upgrades
============================================================================= */

/**
 * Whether the site is being previewed in the Customizer.
 *
 * @since 4.0.0
 *
 * @global WP_Customize_Manager $wp_customize Customizer instance.
 *
 * @return bool True if the site is being previewed in the Customizer, false otherwise.
 */
if( ! function_exists( 'is_customize_preview' ) ):

function is_customize_preview() {
	global $wp_customize;

	return is_a( $wp_customize, 'WP_Customize_Manager' ) && $wp_customize->is_preview();
}
endif;

/**
 * Try to convert an attachment URL into a post ID.
 *
 * @since 4.0.0
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @param string $url The URL to resolve.
 * @return int The found post ID.
 */
if( ! function_exists( 'attachment_url_to_postid' ) ):

function attachment_url_to_postid( $url ) {
	global $wpdb;

	$dir = wp_upload_dir();
	$path = $url;

	if ( 0 === strpos( $path, $dir['baseurl'] . '/' ) ) {
		$path = substr( $path, strlen( $dir['baseurl'] . '/' ) );
	}

	$sql = $wpdb->prepare(
		"SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wp_attached_file' AND meta_value = %s",
		$path
	);
	$post_id = $wpdb->get_var( $sql );
	if ( ! empty( $post_id ) ) {
		return (int) $post_id;
	}
}
endif;

/**
 * Verifies an attachment is of a given type.
 *
 * @since 4.2.0
 *
 * @param string      $type    Attachment type. Accepts 'image', 'audio', or 'video'.
 * @param int|WP_Post $post_id Optional. Attachment ID. Default 0.
 * @return bool True if one of the accepted types, false otherwise.
 */
if( ! function_exists( 'wp_attachment_is' ) ):

function wp_attachment_is( $type, $post_id = 0 ) {
	if ( ! $post = get_post( $post_id ) ) {
		return false;
	}

	if ( ! $file = get_attached_file( $post->ID ) ) {
		return false;
	}

	if ( 0 === strpos( $post->post_mime_type, $type . '/' ) ) {
		return true;
	}

	$check = wp_check_filetype( $file );
	if ( empty( $check['ext'] ) ) {
		return false;
	}

	$ext = $check['ext'];

	if ( 'import' !== $post->post_mime_type ) {
		return $type === $ext;
	}

	switch ( $type ) {
	case 'image':
		$image_exts = array( 'jpg', 'jpeg', 'jpe', 'gif', 'png' );
		return in_array( $ext, $image_exts );

	case 'audio':
		return in_array( $ext, wp_get_audio_extensions() );

	case 'video':
		return in_array( $ext, wp_get_video_extensions() );

	default:
		return $type === $ext;
	}
}
endif;

/* ==========================================================================
	Typekit JS rendering
============================================================================= */

if( ! function_exists( 'helium_typekit_wp_head' ) ):

function helium_typekit_wp_head() {

	/* Load Typekit only when it's used */
	if( Youxi_Font::has_typekit() ) : ?>
<script>
  (function(d) {
    var config = {
      kitId: '<?php echo esc_js( Youxi()->option->get( 'typekit_kit_id' ) ); ?>',
      scriptTimeout: 3000
    },
    h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='//use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
  })(document);
</script>
<?php endif;
}
endif;
add_action( 'wp_head', 'helium_typekit_wp_head', 6 );

/* ==========================================================================
	Scripts and Styles
============================================================================= */

if( ! function_exists( 'helium_wp_enqueue_script' ) ):

function helium_wp_enqueue_script() {
	
	/* Get theme version */
	$wp_theme = wp_get_theme();
	$theme_version = $wp_theme->exists() ? $wp_theme->get( 'Version' ) : false;

	/* Get script debug status */
	$script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;
	$suffix = $script_debug ? '' : '.min';

	/* Enqueue Core Styles */
	wp_enqueue_style( 'helium-bootstrap', get_template_directory_uri() . "/assets/bootstrap/css/bootstrap{$suffix}.css", array(), '3.3.5', 'screen' );
	wp_enqueue_style( 'helium-core', get_stylesheet_uri(), array( 'helium-bootstrap' ), $theme_version, 'screen' );

	/* Enqueue Google Fonts */
	if( $google_fonts_url = Youxi_Font::google_font_request_url() ) {
		wp_enqueue_style( 'helium-google-fonts', $google_fonts_url, array(), $theme_version, 'screen' );
	}

	/* Enqueue Icons */
	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', array(), '4.4.0', 'screen' );

	/* Make sure the LESS compiler exists */
	if( ! class_exists( 'Youxi_LESS_Compiler' ) ) {
		require_once( get_template_directory() . '/lib/framework/class-less-compiler.php' );
	}
	$less_compiler = Youxi_LESS_Compiler::get();

	/* Prepare variables */
	$theme_options_vars = array();

	/* Get the accent color setting */
	$brand_primary = Youxi()->option->get( 'accent_color', helium_default_accent_color() );

	/* Custom accent color styles */
	if( helium_default_accent_color() !== $brand_primary ) {
		wp_add_inline_style( 'helium-bootstrap', $less_compiler->compile( '/assets/less/mods/bootstrap.less', array( 'bs-override' => array( 'brand-primary' => $brand_primary ) ) ) );
		$theme_options_vars['brand-primary'] = $brand_primary;
	}

	/* Custom theme styles */
	if( $header_logo_height = absint( Youxi()->option->get( 'logo_height' ) ) ) {
		$theme_options_vars['logo-height'] = sprintf( '%dpx', $header_logo_height );
	}

	/* Add custom styles from theme options */
	$theme_options_css = $less_compiler->compile( '/assets/less/mods/theme-options.less', array(
		'theme-options' => $theme_options_vars
	));
	if( ! is_wp_error( $theme_options_css ) ) {
		wp_add_inline_style( 'helium-core', $theme_options_css );
	}

	/* Add custom fonts from theme options */
	$font_less_vars = Youxi_Font::get_less_vars();

	if( ! empty( $font_less_vars ) ) {
		$theme_fonts_css = $less_compiler->compile( '/assets/less/mods/theme-fonts.less', array(
			'theme-fonts' => $font_less_vars
		));
		if( ! is_wp_error( $theme_fonts_css ) ) {
			wp_add_inline_style( 'helium-core', $theme_fonts_css );
		}
	}

	/* Custom user styles */
	$custom_css = trim( Youxi()->option->get( 'custom_css' ) );
	if( $custom_css ) {
		wp_add_inline_style( 'helium-core', $custom_css );
	}

	/* Core */
	if( $script_debug ) {
		wp_enqueue_script( 'helium-plugins', get_template_directory_uri() . "/assets/js/helium.plugins.js", array( 'jquery' ), $theme_version, true );
		wp_enqueue_script( 'helium-gridlist', get_template_directory_uri() . "/assets/js/helium.gridlist.js", array( 'jquery' ), $theme_version, true );
		wp_enqueue_script( 'helium-core', get_template_directory_uri() . "/assets/js/helium.setup.js", array( 'jquery', 'helium-plugins', 'helium-gridlist' ), $theme_version, true );
	} else {
		wp_enqueue_script( 'helium-core', get_template_directory_uri() . "/assets/js/helium.min.js", array( 'jquery' ), $theme_version, true );
	}

	/* AJAX */
	$ajax_enabled = Youxi()->option->get( 'ajax_navigation' );

	if( $ajax_enabled ) {
		wp_enqueue_script( 'helium-ajax', get_template_directory_uri() . "/assets/js/helium.ajax{$suffix}.js", array( 'helium-core' ), $theme_version, true );
	}

	/* Enqueue wp-mediaelement if AJAX is enabled */
	if( $ajax_enabled ) {
		wp_enqueue_script( 'wp-mediaelement' );
	}

	/* Enqueue Magnific Popup */
	wp_enqueue_script( 'helium-mfp', get_template_directory_uri() . "/assets/plugins/mfp/jquery.mfp-1.0.0{$suffix}.js", array( 'jquery' ), '1.0.0', true );
	wp_enqueue_style( 'helium-mfp', get_template_directory_uri() . "/assets/plugins/mfp/mfp.css", array(), '1.0.0', 'screen' );

	/* Enqueue Isotope */
	wp_enqueue_script( 'helium-isotope', get_template_directory_uri() . "/assets/plugins/isotope/isotope.pkgd{$suffix}.js", array( 'jquery' ), '2.2.0', true );

	/* Enqueue RoyalSlider */
	wp_enqueue_script( 'helium-royalslider', get_template_directory_uri() . "/assets/plugins/royalslider/jquery.royalslider-9.5.7.min.js", array( 'jquery' ), '9.5.7', true );
	wp_enqueue_style( 'helium-royalslider', get_template_directory_uri() . "/assets/plugins/royalslider/royalslider{$suffix}.css", array(), '1.0.5', 'screen' );

	/* Enqueue Google Maps */
	wp_enqueue_script( 'helium-gmap3', get_template_directory_uri() . "/assets/plugins/gmap/gmap3{$suffix}.js", array( 'jquery' ), '6.0.0.', true );

	/* Pass configuration to frontend */
	wp_localize_script( 'helium-core', '_helium', apply_filters( 'helium_js_vars', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ), 
		'homeUrl' => home_url( '/' )
	)));

	/* Enqueue AddThis script on singular pages */
	if( is_singular( array( 'post', 'portfolio', 'download' ) ) || $ajax_enabled ) {

		$addthis_config = array( 'ui_delay' => 100 );
		if( $addthis_profile_id = Youxi()->option->get( 'addthis_profile_id' ) ) {
			$addthis_config['pubid'] = $addthis_profile_id;
		}
		wp_enqueue_script( 'helium-addthis', '//s7.addthis.com/js/300/addthis_widget.js', array(), 300, true );
		wp_localize_script( 'helium-addthis', 'addthis_config', $addthis_config );
	}

	/* Enqueue comment-reply */
	if( ( is_singular( array( 'post', 'portfolio', 'download' ) ) || $ajax_enabled ) && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
endif;
add_action( 'wp_enqueue_scripts', 'helium_wp_enqueue_script' );
