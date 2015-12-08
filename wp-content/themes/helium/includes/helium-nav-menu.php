<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Cheatin&#8217; uh?' );
}

/* ==========================================================================
	Nav Menus
============================================================================= */

if( ! function_exists( 'helium_register_nav_menus' ) ):

function helium_register_nav_menus() {

	$nav_menus = array(
		'main-menu' => esc_html__( 'Main Menu', 'helium' )
	);
	register_nav_menus( $nav_menus );
}
endif;
add_action( 'init', 'helium_register_nav_menus' );

/* ==========================================================================
	Default Walker Class
============================================================================= */

if( ! class_exists( 'Helium_Walker_Nav_Menu' ) ):

	class Helium_Walker_Nav_Menu extends Walker_Nav_Menu {

		function start_lvl( &$output, $depth = 0, $args = array() ) {
			$output .= '<span class="subnav-close"></span>';
			parent::start_lvl( $output, $depth, $args );
		}
	}
endif;

/* ==========================================================================
	Menu Fallback if none was specified
============================================================================= */

if( ! function_exists( 'helium_fallback_menu' ) ):

function helium_fallback_menu() {
	?>
	<ul class="menu">
		<li class="menu-item menu-item-home<?php if( is_front_page() ) echo esc_attr( ' current-menu-item' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'helium' ); ?></a>
		</li>
		<?php wp_list_pages( 'title_li=&sort_column=menu_order' ); ?>
	</ul>
	<?php
}
endif;
