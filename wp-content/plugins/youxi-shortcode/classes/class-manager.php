<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}

/**
 * Youxi Shortcode Manager class
 *
 * This class manages registration, setup and othing shortcode related things.
 *
 * @package   Youxi Shortcode
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2014, Mairel Theafila
 */
final class Youxi_Shortcode_Manager {

	/**
	 * The array containing Youxi_Category objects
	 *
	 * @access private
	 * @var array
	 */
	private $categories = array();

	/**
	 * The array containing oprhan shortcodes
	 *
	 * @access private
	 * @var array
	 */
	private $orphan_shortcodes = array();

	/**
	 * The array containing Youxi_Shortcode objects
	 *
	 * @access private
	 * @var array
	 */
	private $shortcodes = array();

	/**
	 * The TinyMCE manager instance
	 *
	 * @access private
	 * @var Youxi_Shortcode_TinyMCE_Manager
	 */
	private $tinymce_manager;

	/**
	 * The Singleton instance
	 *
	 * @access private
	 * @var Youxi_Shortcode_Manager
	 */
	private static $instance;

	/**
	 * Constructor
	 */
	private function __construct() {
		require( plugin_dir_path( __FILE__ ) . 'class-category.php' );
		require( plugin_dir_path( __FILE__ ) . 'class-shortcode.php' );
		require( plugin_dir_path( __FILE__ ) . 'class-shortcode-animation.php' );

		/* Hook to after_setup_theme where we can start registering shortcodes */
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );

		/* Register AJAX hook for getting the shortcode edit popup */
		add_action( 'wp_ajax_get_shortcode_editor', array( $this, 'get_shortcode_editor' ) );

		/* Register AJAX hook for parsing shortcode to json */
		add_action( 'wp_ajax_parse_shortcode_to_json', array( $this, 'shortcode_to_json' ) );
	}

	/**
	 * Public method to get the singleton instance
	 *
	 * @return Youxi_Shortcode_Manager
	 */
	public static function get() {
		if( ! self::$instance ) {
			self::$instance = new Youxi_Shortcode_Manager();
		}

		return self::$instance;
	}

	/**
	 * Hook to after_setup_theme and do the youxi_shortcode_register action
	 * so external scripts can register shortcodes and categories
	 */
	public function after_setup_theme() {

		/* Initialize animations if allowed */
		if( apply_filters( 'youxi_shortcode_enable_animation', false ) ) {
			Youxi_Shortcode_Animation::get()->init();
		}

		/* Register the shortcodes */
		do_action( 'youxi_shortcode_register', $this );

		/* Prepare the shortcodes */
		$this->prepare_shortcodes();

		if( is_admin() ) {

			/* Hook for enqueueing admin scripts */
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			/* Hook for printing shortcode serializers and deserializers */
			add_action( 'admin_print_footer_scripts', array( $this, 'admin_print_footer_scripts' ) );

			/* Register TinyMCE plugin if allowed */
			if( apply_filters( 'youxi_shortcode_allow_tinymce', true ) ) {
				if( ! class_exists( 'Youxi_Shortcode_TinyMCE_Manager' ) ) {
					require( plugin_dir_path( __FILE__ ) . 'class-mce-manager.php' );
				}

				$this->tinymce_manager = new Youxi_Shortcode_TinyMCE_Manager();
			}

		} else {

			/* Enqueue shortcode scripts and styles */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

			/* Non destructive fix for broken shortcodes caused by wpautop */
			add_filter( 'the_content', array( $this, 'shortcode_unautop' ) );

			/* Filter the shortcodes where unautop needs to be applied */
			add_filter( 'youxi_shortcode_unautop', array( $this, 'filter_unautop_tags' ) );
		}
	}

	/**
	 * Enhanced version of Wordpress shortcode_unautop.
	 * This function takes <br /> and nested shortcodes into account by filtering opening and closing tags separately.
	 *
	 * Based on the WordPress core shortcode_unautop regex
	 * "/<p>\s*+(\[(?:$tagregexp)(?![\w-])[^\]\/]*(?:\/(?!\])[^\]\/]*)*?(?:\/\]|\](?:[^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+\[\/\2\])?))\s*+<\/p>/s";
	 *
	 * Construct the opening tag regex
	 * 1. Remove content and closing tag regex -> (?:[^\[]*+(?:\[(?!\/\2\])[^\[]*+)*+\[\/\2\])?
	 * "/<p>\s*+(\[(?:$tagregexp)(?![\w-])[^\]\/]*(?:\/(?!\])[^\]\/]*)*?(?:\/\]|\]))\s*+<\/p>/s";
	 *
	 * 2. Change the regex to match zero or one <p> or <br> before the closing tag, but don't capture it.
	 * "/(?:<p>|<br[^>]*>)?\s*+(\[(?:$tagregexp)(?![\w-])[^\]\/]*(?:\/(?!\])[^\]\/]*)*?(?:\/\]|\]))\s*+<\/p>";
	 *
	 * 3. Change the regex to match zero or one </p> or <br> after the closing tag, but don't capture it.
	 * "/(?:<p>|<br[^>]*>)?\s*+(\[(?:$tagregexp)(?![\w-])[^\]\/]*(?:\/(?!\])[^\]\/]*)*?(?:\/\]|\]))\s*+(?:<\/p>|<br[^>]*>)?/s";
	 *
	 * 4. Make the whitespace and opening tag the capturing group we want
	 * "/(?:<p>|<br[^>]*>)?(\s*+(\[(?:$tagregexp)(?![\w-])[^\]\/]*(?:\/(?!\])[^\]\/]*)*?(?:\/\]|\]))\s*+)(?:<\/p>|<br[^>]*>)?/s";
	 *
	 *
	 * Construct the closing tag regex
	 * 1. Match any shortcodes closing tags
	 * "/(\[\/(?:$tagregexp)\])/s"
	 *
	 * 2. Match any whitespace characters before and after the closing tag
	 * "/\s*+(\[\/(?:$tagregexp)\])\s*+/s"
	 *
	 * 3. Match zero or one <p> or <br> before the closing tag, but do not capture it
	 * "/(?:<p>|<br[^>]*>)?\s*+(\[\/(?:$tagregexp)\])\s*+/s"
	 * 
	 * 4. Match zero or one </p> or <br> after the closing tag, but do not capture it
	 * "/(?:<p>|<br[^>]*>)?\s*+(\[\/(?:$tagregexp)\])\s*+(?:<\/p>|<br[^>]*>)?/s"
	 *
	 * 5. Make the whitespace and closing tag the capturing group we want
	 * "/(?:<p>|<br[^>]*>)?(\s*+(\[\/(?:$tagregexp)\])\s*+)(?:<\/p>|<br[^>]*>)?/s"
	 *
	 * @param string The unfiltered string from wpautop
	 */
	public function shortcode_unautop( $post_content ) {

		/* Get all shortcodes registered through this plugin */
		$shortcode_tags = array_keys( $this->shortcodes );

		if( empty( $shortcode_tags ) || ! is_array( $shortcode_tags ) ) {
			return $pee;
		}

		/* Construct the shortcode tags regex */
		$tagregexp = implode( '|', array_map( 'preg_quote', apply_filters( 'youxi_shortcode_unautop', array_keys( $shortcode_tags ) ) ) );

		/* Filter opening tags for <p> */
		$post_content = preg_replace( "/(?:<p>|<br[^>]*>)?(\s*+(\[(?:$tagregexp)(?![\w-])[^\]\/]*(?:\/(?!\])[^\]\/]*)*?(?:\/\]|\]))\s*+)(?:<\/p>|<br[^>]*>)?/s", '$1', $post_content );

		/* Filter closing tags */
		$post_content = preg_replace( "/(?:<p>|<br[^>]*>)?(\s*+(\[\/(?:$tagregexp)\])\s*+)(?:<\/p>|<br[^>]*>)?/s", '$1', $post_content );

		return $post_content;
	}

	/**
	 * Adds a shortcode category
	 *
	 * @param string $id A specific ID of the category.
	 * @param array $args Category arguments.
	 */
	public function add_category( $id, $args = array() ) {
		if( is_a( $id, 'Youxi_Shortcode_Category' ) ) {
			$category = $id;
		} else {
			$category = new Youxi_Shortcode_Category( $id, $args );
		}

		$this->categories[ $category->id ] = $category;
	}

	/**
	 * Prepare all shortcodes by registering them to the categories they belongs to
	 *
	 * @param string $id A specific ID of the category.
	 * @param array $args Category arguments.
	 */
	public function prepare_shortcodes() {

		$this->shortcodes = array_reverse( $this->shortcodes );
		$shortcodes = array();

		// Attach shortcodes to categories
		foreach( $this->shortcodes as $tag => $shortcode ) {
			if( ! isset( $this->categories[ $shortcode->category ] ) ) {
				$this->orphan_shortcodes[ $tag ] = $shortcode;
			} else {
				$this->categories[ $shortcode->category ]->shortcodes[ $tag ] = $shortcode;
			}
			$shortcodes[ $tag ] = $shortcode;
		}
		$this->shortcodes = $shortcodes;

		// Sort the shortcode categories
		$this->categories = array_reverse( $this->categories );
		uasort( $this->categories, array( $this, '_cmp_priority' ) );

		// Validate, sort and filter the shortcodes inside the categories
		$categories = array();
		foreach( $this->categories as $id => $category ) {
			if( ! $category->shortcodes ) {
				continue;
			}

			// Sort the shortcodes in this category based on priority
			uasort( $category->shortcodes, array( $this, '_cmp_priority' ) );

			// Add the category to the list
			$categories[] = $category;
		}
		$this->categories = $categories;

		// Sort the orphans
		$this->orphan_shortcodes = array_reverse( $this->orphan_shortcodes );
		uasort( $this->orphan_shortcodes, array( $this, '_cmp_priority' ) );
	}

	/**
	 * Add a shortcode
	 *
	 * @param string $tag The shortcode tag.
	 * @param array $args Shortcode arguments.
	 */
	public function add_shortcode( $tag, $args ) {
		if( is_a( $tag, 'Youxi_Shortcode' ) ) {
			$shortcode = $tag;
		} else {
			$shortcode = new Youxi_Shortcode( $tag, $args );
		}

		$this->shortcodes[ $shortcode->tag ] = $shortcode;
	}
	
	/**
	 * Retrieve a shortcode
	 *
	 * @param string $tag A specific tag of the shortcode.
	 * @return object The shortcode object.
	 */
	public function get_shortcode( $tag ) {
		if( isset( $this->shortcodes[ $tag ] ) ) {
			return $this->shortcodes[ $tag ];
		} else {
			$tag = Youxi_Shortcode::prefix( $tag );
			if( isset( $this->shortcodes[ $tag ] ) ) {
				return $this->shortcodes[ $tag ];
			}
		}
	}
	
	/**
	 * Check if a shortcode exists
	 *
	 * @param string $tag A specific tag of the shortcode.
	 * @return bool Whether the shortcode exists
	 */
	public function shortcode_exists( $tag ) {
		if( ! isset( $this->shortcodes[ $tag ] ) ) {
			$tag = Youxi_Shortcode::prefix( $tag );
		}
		return isset( $this->shortcodes[ $tag ] ) && shortcode_exists( $tag );
	}

	/**
	 * Remove a shortcode
	 *
	 * @param string $tag A specific ID of the shortcode.
	 * @return object The removed shortcode object.
	 */
	public function remove_shortcode( $tag ) {

		if( isset( $this->shortcodes[ $tag ] ) ) {
			$object = $this->shortcodes[ $tag ];
			unset( $this->shortcodes[ $tag ] );
		} else {
			$tag = Youxi_Shortcode::prefix( $tag );
			if( isset( $this->shortcodes[ $tag ] ) ) {
				$object = $this->shortcodes[ $tag ];
				unset( $this->shortcodes[ $tag ] );
			}
		}

		if( isset( $object ) ) {
			$object->remove();
			return $object;
		}
	}

	/**
	 * Get shortcodes based on a particular condition
	 *
	 * @param array $conditions An array of conditions to match
	 * @param string $operator The logic for matching shortcode conditions
	 *
	 * @return array The shortcode objects matching the specified conditions
	 */
	public function get_shortcodes( $conditions = array(), $operator = 'AND' ) {
		$shortcodes = array();
		foreach( $this->shortcodes as $tag => $shortcode ) {
			$shortcodes[ $tag ] = $shortcode->get_args();
		}

		return wp_list_filter( $shortcodes, $conditions, $operator );
	}

	/**
	 * Returns the shortcode arguments grouped by category
	 *
	 * @param mixed $include The shortcode arguments we want to include
	 *
	 * @return array The list of shortcode arguments
	 */
	public function get_shortcode_args( $include = '*', $include_orphans = true ) {
		$categories = array();
		$orphans    = array();

		foreach( $this->categories as $id => $category ) {
			$categories[ $id ] = array(
				'args' => $category->get_args(), 
				'shortcodes' => array()
			);
			foreach( $category->shortcodes as $tag => $shortcode ) {
				$categories[ $id ]['shortcodes'][ $tag ] = array_merge( $shortcode->get_args( $include ), array(
					'defaults' => $shortcode->get_defaults()
				));
			}
		}

		if( ! $include_orphans ) {
			return $categories;
		}

		foreach( $this->orphan_shortcodes as $tag => $shortcode ) {
			$orphans[ $tag ] = array_merge( $shortcode->get_args( $include ), array(
				'defaults' => $shortcode->get_defaults()
			));
		}

		return compact( 'categories', 'orphans' );
	}

	/**
	 * Method to retrieve the fields data of a shortcode
	 *
	 * @return array The shortcode fields (attributes and content)
	 */
	public function get_shortcode_fields( $tag = '' ) {

		$result = array();

		$prefixed = Youxi_Shortcode::prefix( $tag );
		$tag = isset( $this->shortcodes[ $tag ] ) ? $tag : 
			( isset( $this->shortcodes[ $prefixed ] ) ? $prefixed : '' );
		
		if( isset( $this->shortcodes[ $tag ] ) ) {
			$shortcode = $this->shortcodes[ $tag ];

			$content = $shortcode->content;
			$result = array_merge( $shortcode->atts, compact( 'content' ) );
		}

		return $result;
	}

	/**
	 * Method to retrieve all registered shortcodes deserializers
	 *
	 * @return array The shortcode deserializers
	 */
	public function get_shortcode_deserializers() {
		$deserializers = array();
		foreach( $this->shortcodes as $tag => $shortcode ) {
			$deserializers[ $tag ] = $shortcode->deserializers;
		}

		return array_filter( $deserializers );
	}

	/**
	 * Method to retrieve all registered shortcodes serializers
	 *
	 * @return array The shortcode serializers
	 */
	public function get_shortcode_serializers() {
		$serializers = array();
		foreach( $this->shortcodes as $tag => $shortcode ) {
			$serializers[ $tag ] = $shortcode->serializers;
		}

		return array_filter( $serializers );
	}

	/**
	 * Method to filter the shortcodes where unautop needs to be applied.
	 *
	 * @param array $tags The array of shortcode tags
	 *
	 * @return array The shortcode tags that needs unautop
	 */
	public function filter_unautop_tags( $tags ) {
		return array_keys( $this->get_shortcodes( array( 'inline' => false ) ) );
	}

	/**
	 * Handles shortcode editor request via AJAX. 
	 * Returns a JSON string containing the HTML contents of the editor.
	 */
	public function get_shortcode_editor() {

		if( isset( $_POST['shortcode'] ) ) {
			if( !current_user_can( 'edit_posts' ) ) {
				wp_die( __( 'You\'re not allowed to make this request.', 'youxi' ) );
			}

			$shortcode = $_POST['shortcode'];
			$output = array(
				'title' => '', 
				'html' => '', 
				'message' => ''
			);
			
			$shortcode = $this->get_shortcode( $shortcode );

			if( is_a( $shortcode, 'Youxi_Shortcode' ) && ! $shortcode->internal ) {
				if( ! class_exists( 'Youxi_Shortcode_Editor' ) ) {
					require( plugin_dir_path( __FILE__ ) . 'class-editor.php' );
				}

				$data = isset( $_POST['shortcodeData'] )? stripslashes_deep( $_POST['shortcodeData'] ) : array();
				$editor = new Youxi_Shortcode_Editor( $shortcode, $data );

				$output['title'] = sprintf( '%s %s', $data ? __( 'Update', 'youxi' ) : __( 'Insert', 'youxi' ), $shortcode->label );
				$output['html'] = $editor->get_html();

				wp_send_json_success( $output );

			} else {
				if( is_a( $shortcode, 'Youxi_Shortcode' ) ) {
					$output['message'] = __( 'You can\'t access the edit form of an internal shortcode.', 'youxi' );
				} else {
					$output['message'] = __( "The shortcode '$shortcode' is invalid.", 'youxi' );
				}

				wp_send_json_error( $output );
			}
		}

		if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			wp_die();
		} else {
			die;
		}
	}

	/**
	 * Handles shortcode parsing request via AJAX. 
	 * Returns a JSON string containing the parsed shortcodes.
	 */
	public function shortcode_to_json() {

		if( isset( $_POST['content'] ) ) {
			wp_send_json_success( array( 'parsed' => Youxi_Shortcode::to_array( stripslashes_deep( $_POST['content'] ) ) ) );
		}

		if( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			wp_die();
		} else {
			die;
		}
	}

	/**
	 * Register but do not enqueue the default needed scripts for shortcodes.
	 * Other plugins/themes can pass configuration variables through the youxi_shortcode_js_vars filter
	 */
	public function admin_enqueue_scripts( $hook ) {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		/* Register serialize JSON script as it's needed by the shortcode helper */
		wp_register_script(
			'serialize-json', 
			YOUXI_SHORTCODE_URL . "admin/assets/plugins/serializejson/jquery.serializejson{$suffix}.js", 
			array( 'jquery' ), 
			'1.2.3', 
			true
		);

		wp_register_script(
			'youxi-shortcode-editor', 
			YOUXI_SHORTCODE_URL . "admin/assets/js/youxi.shortcode.editor{$suffix}.js", 
			array( 'media-views', 'serialize-json' ), 
			YOUXI_SHORTCODE_VERSION, 
			true
		);

		/* Register shortcode admin helper */
		wp_register_script(
			'youxi-shortcode', 
			YOUXI_SHORTCODE_URL . "admin/assets/js/youxi.shortcode{$suffix}.js", 
			array( 'youxi-shortcode-editor', 'youxi-form-manager', 'underscore' ), 
			YOUXI_SHORTCODE_VERSION, 
			true
		);

		/* Register shortcode admin styles */
		wp_register_style(
			'youxi-shortcode', 
			YOUXI_SHORTCODE_URL . "admin/assets/css/youxi.shortcode{$suffix}.css", 
			array( 'youxi-form', 'media-views' ), 
			YOUXI_SHORTCODE_VERSION, 
			'screen'
		);

		/* Attach setting variables to the script */
		wp_localize_script(
			'youxi-shortcode', 
			'youxiShortcodeSettings', 
			apply_filters( 'youxi_shortcode_js_vars', array(
				'args' => $this->get_shortcode_args(), 
				'prefix' => Youxi_Shortcode::prefix()
			))
		);

		/* Enqueue all admin assets and print media templates if allowed */
		if( apply_filters( 'youxi_shortcode_admin_enqueue_scripts', false ) ) {
			
			foreach( $this->shortcodes as $shortcode ) {
				$shortcode->admin_enqueue_scripts( $hook );
			}
			add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );

			do_action( 'youxi_shortcode_admin_enqueue', $hook );
		}
	}

	/**
	 * Prints the shortcode serializers and deserializers in admin footer
	 */
	public function admin_print_footer_scripts() {
		
		if( wp_script_is( 'youxi-shortcode', 'done' ) ):

			$shortcode = Youxi_JS::encode( array(
				'serializers'   => $this->get_shortcode_serializers(), 
				'deserializers' => $this->get_shortcode_deserializers()
			));

$js = ';(function( $ ) {
	$(function() {
		$.Youxi = $.Youxi || {};
		$.extend( true, $.Youxi, { Shortcode: ' . $shortcode . ' });
	});
})( jQuery );';

?><script type="text/javascript">
/* <![CDATA[ */
<?php echo ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? $js : Youxi_JS::minify( $js ) ); ?>
/* ]]> */
</script>
		<?php endif;
	}

	/**
	 * Registers and optionally enqueue Bootstrap CSS and JS
	 * If you do not need the default assets, use the youxi_shortcode_enqueue_assets filter
	 */
	public function enqueue( $hook ) {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		/* Whether to enqueue the default assets */
		if( apply_filters( 'youxi_shortcode_enqueue_assets', true ) ) {

			/* Register default Bootstrap script */
			wp_register_script(
				'youxi-shortcode-bootstrap', 
				YOUXI_SHORTCODE_URL . "frontend/bootstrap/js/bootstrap{$suffix}.js", 
				array( 'jquery' ), 
				'3.2', 
				true
			);

			/* Register default Bootstrap style */
			wp_register_style(
				'youxi-shortcode-bootstrap', 
				YOUXI_SHORTCODE_URL . "frontend/bootstrap/css/bootstrap{$suffix}.css", 
				array(), 
				'3.2', 
				'screen'
			);

			/* Enqueue default Bootstrap styles if allowed */
			wp_enqueue_script( 'youxi-shortcode-bootstrap' );
			wp_enqueue_style( 'youxi-shortcode-bootstrap' );
		}

		global $post;
		if( is_a( $post, 'WP_Post' ) && $post->ID == get_queried_object_id() ) {

			/* Filter post content so it can be modified by external scripts */
			$post_content = apply_filters( 'youxi_shortcode_pre_enqueue_post_content', $post->post_content, $post );

			/* Get shortcode tags */
			$tagregexp = implode( '|', array_keys( $this->shortcodes ) );

			/* Match all shortcode tags inside the current post content */
			preg_match_all( "/\[($tagregexp)(?![\w-])/i", $post_content, $matches );

			/* If we got matches */
			if( isset( $matches[1] ) ) {

				/* Extract the matched shortcodes */
				foreach( array_unique( $matches[1] ) as $tag ) {
					$this->shortcodes[ $tag ]->enqueue( $hook );
				}
			}
		}
	}

	/**
	 * Prints media templates for shortcode editor views
	 */
	public function print_media_templates() {
		echo "\n\t".'<script type="text/html" id="tmpl-youxi-modal"><div class="youxi-modal"></div><div class="youxi-modal-backdrop"></div></script>'."\n";

		echo "\n\t".'<script type="text/html" id="tmpl-youxi-shortcode-editor">';
			echo '<div class="youxi-modal-content-wrap">';
				echo '<div class="youxi-modal-content">';
					echo '<div class="youxi-modal-header"></div>';
					echo '<div class="youxi-modal-body"></div>';
					echo '<div class="youxi-modal-footer"></div>';
				echo '</div>';
			echo '</div>';
		echo '</script>'."\n\n";
	}

	/**
	 * Helper function to create a table of the shortcodes
	 */
	public function dump_table() {
		
		foreach( $this->categories as $category ) {

			if( ! empty( $category->shortcodes ) ): ?>

<h4><?php echo $category->label ?></h4>

<table class="table table-striped table-bordered">
	<thead>
		<tr>
			<th>Tag</th>
			<th>Description</th>
		</tr>
	</thead>
	<tbody><?php foreach( $category->shortcodes as $shortcode ): if( ! $shortcode->internal ): ?>

		<tr>
			<td>[<?php echo $shortcode->tag ?>]</td>
			<td>
				<?php if( $shortcode->atts ):

				?><strong>Attributes: </strong><br>
				<ul><?php foreach( $shortcode->atts as $attribute => $val ): ?>
					
					<li>
						<strong><?php echo $attribute ?></strong><?php if( isset( $val['description'] ) ):
						?>:<br>
						<p><?php echo $val['description'] ?></p><?php endif; ?>

					</li><?php endforeach; ?>

				</ul><?php else: echo '<strong>No Attributes</strong>'; endif; ?>

			</td>
		</tr><?php endif; endforeach; ?>

	</tbody>
</table>
			<?php endif;
		}
	}

	/**
	 * Helper function to compare two objects by priority.
	 *
	 * @param object $a Object A.
	 * @param object $b Object B.
	 * @return int
	 */
	protected final function _cmp_priority( $a, $b ) {
		$ap = $a->priority;
		$bp = $b->priority;

		if ( $ap == $bp )
			return 0;
		return ( $ap > $bp ) ? 1 : -1;
	}
}