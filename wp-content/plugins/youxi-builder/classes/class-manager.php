<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

class Youxi_Builder_Manager {

	/**
	 * The singleton instance
	 *
	 * @access private
	 * @var Youxi_Builder_Manager
	 */
	private static $instance;

	/**
	 * Constructor
	 * 
	 * @return void
	 */
	private function __construct() {
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );
	}

	/**
	 * Hook to add_meta_boxes on after_setup_theme so we can pre-initialize the page builder.
	 * We're using the after_setup_theme hook so configurations can be modified through the theme.
	 */
	public function after_setup_theme() {
		add_action( 'add_meta_boxes', array( $this, 'prepare' ) );
	}

	/**
	 * Public method to get the singleton instance
	 *
	 * @return Youxi_Builder_Manager
	 */
	public static function instance() {
		if( ! self::$instance ) {
			self::$instance = new Youxi_Builder_Manager();
		}

		return self::$instance;
	}

	/**
	 * Initialize the page builder by setting up hooks if the current post type is whitelisted.
	 */
	public function prepare( $post_type ) {
		if( in_array( $post_type, apply_filters( 'youxi_builder_post_types', array( 'page' ) ) ) ) {

			/* Add hook to enqueue external assets for use by the TinyMCE plugin */
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			/* Add the builder toggle button besides the WP 'Add Media' button */
			add_action( 'media_buttons', array( $this, 'builder_media_buttons' ), 9 );

			/* Print the builder templates */
			add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );

			/* Tell the shortcodes plugin to enqueue all admin assets */
			add_filter( 'youxi_shortcode_admin_enqueue_scripts', '__return_true' );
		}
	}

	/**
	 * Add the button for switching between the builder and the default editor.
	 * This button will be moved into the .wp-editor-tools on DOM ready.
	 *
	 * @param string The TinyMCE textarea ID to attach the button
	 */
	public function builder_media_buttons( $editor_id ) {

		/* Make sure editor_id is in the list of allowed editors */
		if( ! in_array( $editor_id, apply_filters( 'youxi_builder_allowed_editors', array( 'content' ) ) ) )
			return;

		echo '<button type="button" id="' . esc_attr( $editor_id ) . '-ypbl" class="wp-switch-editor switch-youxi-builder" data-wp-editor-id="' . esc_attr( $editor_id ) . '" style="display: none;">' . __( 'Page Builder', 'youxi' ) . "</button>\n";
	}

	/**
	 * Return the builder settings array that will be used by the builder client script
	 *
	 * @return array The settings
	 */
	public function get_builder_settings() {

		$prefix = Youxi_Shortcode::prefix();

		/* Get container shortcodes */
		$container_shortcodes = Youxi_Shortcode::get_container_shortcodes();

		/* Get row shortcodes */
		$row_shortcode = Youxi_Shortcode::get_row_shortcode();

		/* Get separator shortcodes */
		$separator_shortcodes = Youxi_Shortcode::get_separator_shortcodes();

		/* Determine if we're using simple columns */
		$simple_columns = Youxi_Shortcode::use_simple_columns();

		/* Determine if container is enabled */
		$container_enabled = apply_filters( 'youxi_builder_enable_container', true );

		/* If any of the container shortcode doesn't exists, disable the container mode */
		foreach( $container_shortcodes as $shortcode ) {
			if( ! Youxi_Shortcode_Manager::get()->shortcode_exists( $shortcode ) ) {
				$container_enabled = false;
			}
		}

		/* Define column shortcodes */
		$column_sizes = Youxi_Shortcode::get_column_sizes();

		// Create size and tags array
		if( $simple_columns ) {
			$column_props = Youxi_Shortcode::get_simple_columns();
			$column_shortcodes = array_intersect_key( $column_props, array_combine( $column_sizes, $column_sizes ) );
			$column_shortcodes = array_values( $column_shortcodes );
		} else {
			$tag = Youxi_Shortcode::get_column_shortcode();
			$column_shortcodes = array();
			foreach( $column_sizes as $size ) {
				$column_shortcodes[] = compact( 'size', 'tag' );
			}
		}
		$column_tags = array_unique( wp_list_pluck( $column_shortcodes, 'tag' ) );

		/* Helper variables */
		$non_column_or_widget_shortcodes = array_merge( $container_shortcodes, (array) $row_shortcode );
		$non_widget_shortcodes = array_merge( $non_column_or_widget_shortcodes, $column_tags );
		$rows_and_separators = array_merge( (array) $row_shortcode, $separator_shortcodes );

		/* Define the builder rules */
		$rules = array();
		$rules[ $row_shortcode ] = array(
			'accept' => $column_tags, 
			'reject' => $non_column_or_widget_shortcodes
		);
		foreach( $column_tags as $tag ) {
			$rules[ $tag ] = array(
				'accept' => '*', 
				'reject' => $non_widget_shortcodes
			);
		}

		/* Adjust rules based on enable_container state */
		if( $container_enabled ) {

			$rules = array_merge( array(
				'root' => array(
					'accept' => $container_shortcodes
				), 
				'container' => array(
					'accept' => $rows_and_separators, 
					'reject' => $container_shortcodes
				), 
				'fullwidth' => array(
					'accept' => '*', 
					'reject' => $non_widget_shortcodes
				)
			), $rules );

		} else {

			$rules = array_merge( array(
				'root' => array(
					'accept' => $rows_and_separators, 
					'reject' => $container_shortcodes
				)
			), $rules );

		}

		/* Compact all in one setting array */
		$settings = array(
			'parseMethod'               => apply_filters( 'youxi_builder_parse_method', 'js' ), 
			'simpleColumns'             => $simple_columns, 
			'enableContainer'           => $container_enabled, 
			'containerShortcodes'       => $container_shortcodes, 
			'columnContainerShortcode'  => Youxi_Shortcode::get_column_container_shortcode(), 
			'rowShortcode'              => $row_shortcode, 
			'rowSize'                   => Youxi_Shortcode::get_column_count(), 
			'columnShortcodes'          => $column_shortcodes, 
			'rules'                     => $rules, 
			'uiIcons'                   => apply_filters( 'youxi_builder_ui_icons', array(
				'resizeLeft' => array(
					'icon' => 'dashicons dashicons-arrow-left', 
					'title' => __( 'Decrease Size', 'youxi' )
				), 
				'resizeRight' => array(
					'icon' => 'dashicons dashicons-arrow-right', 
					'title' => __( 'Increase Size', 'youxi' )
				), 
				'remove' => array(
					'icon' => 'dashicons dashicons-trash', 
					'title' => __( 'Remove', 'youxi' )
				), 
				'edit' => array(
					'icon' => 'dashicons dashicons-edit', 
					'title' => __( 'Edit', 'youxi' )
				), 
				'copy' => array(
					'icon' => 'dashicons dashicons-admin-page', 
					'title' => __( 'Copy', 'youxi' )
				)
			))
		);

		/* The builder languages strings */
		$l10n = array(
			'invalidColumnSize'      => __( 'The specified column size is invalid.', 'youxi' ), 
			'invalidRowSlotsSize'    => __( 'The specified row slots size is invalid.', 'youxi' ), 
			'notEnoughColumnSlots'   => __( 'The containing row does not have enough slots.', 'youxi' ), 
			'confirmRemoveContainer' => __( 'Are you sure you want to remove this container?', 'youxi' ), 
			'confirmRemoveRow'       => __( 'Are you sure you want to remove this row?', 'youxi' ), 
			'confirmRemoveColumn'    => __( 'Are you sure you want to remove this column?', 'youxi' ), 
			'confirmRemoveWidget'    => __( 'Are you sure you want to remove this widget?', 'youxi' ), 
			'columnTitlePrefix'      => __( 'Column ', 'youxi' )
		);

		return array_merge( $l10n, compact( 'settings' ) );
	}

	/**
	 * Print the builder templates that will be used by the builder client script
	 * to construct the builder views.
	 */
	public function print_media_templates() {
		?>

	<script type="text/html" id="tmpl-youxi-builder-wrapper">
		<div class="youxi-builder-header"></div>
		<div class="youxi-builder-content">
			<div class="youxi-builder-content-wrap"></div>
			<div class="youxi-builder-content-drop-notice">
				<div class="drop-icon">
					<i class="fa fa-arrow-down"></i>
				</div>
				<p><?php _e( 'Drop here a new component', 'youxi' ) ?></p>
			</div>
		</div>
	</script>

	<script type="text/html" id="tmpl-youxi-builder-menu">
		<ul class="youxi-builder-menu-tabs"></ul>
		<div class="youxi-builder-menu-items"></div>
	</script>

	<script type="text/html" id="tmpl-youxi-builder-menu-tab">
		<a href="#{{ data.href }}" title="{{ data.label }}">{{ data.label }}</a>
	</script>

	<script type="text/html" id="tmpl-youxi-builder-menu-tab-content">
		<ul class="youxi-builder-component-list"></ul>
	</script>

	<script type="text/html" id="tmpl-youxi-builder-shortcode-button">
		<# if( data.icon ) { #>
		<span class="youxi-builder-component-icon">
			<i class="{{ data.icon }}"></i>
		</span>
		<# } #>
		{{ data.label }}
	</script>

	<script type="text/html" id="tmpl-youxi-builder-panel">
		<div class="youxi-builder-panel-inner">
			<div class="youxi-builder-panel-header youxi-builder-{{ data.type }}-header"></div>
			<div class="youxi-builder-panel-contents youxi-builder-{{ data.type }}-contents"></div>
		</div>
	</script>

	<script type="text/html" id="tmpl-youxi-builder-controls">
		<ul></ul>
	</script>

	<script type="text/html" id="tmpl-youxi-builder-panel-control-button">
		<a href="#" title="{{ data.title }}" data-action="{{ data.action }}" data-message="{{ data.message }}" class="button button-primary">
			<span class="{{ data.icon }}"></span>
		</a>
	</script>
		<?php
	}
	
	/**
	 * Enqueue all builder required assets.
	 *
	 * @param string The current admin page name
	 */
	public function admin_enqueue_scripts( $hook ) {
		/* 
			If we're not on the post edit screen or the current post type does not support editor, 
			we can assume that TinyMCE is not present on the page.
			This currently does not take account other plugins/function calls the wp_editor, in that case just do not include the plugin.
		*/
		if( ! in_array( $hook, array( 'post.php', 'post-new.php' ) ) || ! post_type_supports( get_post_type(), 'editor' ) )
			return;

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if( ! wp_style_is( 'font-awesome', 'registered' ) ) {
			wp_register_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css', array(), '4.3', 'screen' );
		}

		wp_register_script( 'backbone-courier', YOUXI_BUILDER_URL . "admin/assets/plugins/backbone-courier/backbone.courier{$suffix}.js", array( 'jquery', 'backbone' ), '0.6.1', true );
		wp_register_script( 'youxi-builder-models', YOUXI_BUILDER_URL . "admin/assets/js/youxi.builder-models{$suffix}.js", array( 'jquery', 'media-views', 'shortcode' ), YOUXI_BUILDER_VERSION, true );
		wp_register_script( 'youxi-builder-views', YOUXI_BUILDER_URL . "admin/assets/js/youxi.builder-views{$suffix}.js", array( 'youxi-builder-models' ), YOUXI_BUILDER_VERSION, true );
		wp_register_script( 'youxi-builder', YOUXI_BUILDER_URL . "admin/assets/js/youxi.builder{$suffix}.js", array( 'youxi-shortcode', 'backbone-courier', 'youxi-builder-views', 'youxi-builder-models' ), YOUXI_BUILDER_VERSION, true );
		wp_register_style( 'youxi-builder', YOUXI_BUILDER_URL . "admin/assets/css/youxi.builder{$suffix}.css", array( 'youxi-shortcode' ), YOUXI_BUILDER_VERSION, 'screen' );

		wp_localize_script( 'youxi-builder-models', '_youxiBuilderL10n', $this->get_builder_settings() );
		
		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-droppable' );

		wp_enqueue_script( 'youxi-builder' );

		wp_enqueue_style( 'font-awesome' );
		wp_enqueue_style( 'youxi-builder' );
	}
}