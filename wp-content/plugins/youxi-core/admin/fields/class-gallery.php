<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Gallery Class
 *
 * This class creates a gallery form field using WordPress 3.5 media uploader.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Gallery_Form_Field' ) ) {

	class Youxi_Gallery_Form_Field extends Youxi_Form_Field {

		private static $media_template_printed = false;

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				// wp_enqueue_media();
				wp_enqueue_script(
					'youxi-gallery', 
					self::field_assets_url( "js/youxi.form.gallery{$suffix}.js" ), 
					array( 'youxi-form-manager', 'media-editor', 'jquery-ui-widget' ), 
					YOUXI_CORE_VERSION, 
					true
				);
				wp_enqueue_style(
					'youxi-gallery', 
					self::field_assets_url( "css/youxi.form.gallery{$suffix}.css" ), 
					array( 'youxi-form' ), 
					YOUXI_CORE_VERSION
				);

				if( ! self::$media_template_printed ) {
					add_action( 'print_media_templates', array( get_class(), 'print_media_templates' ) );
				}
			}
		}

		/**
		 * Print the media template once
		 *
		 * @return string The media template
		 */
		public static function print_media_templates() {
			if( ! self::$media_template_printed ) {
				self::$media_template_printed = true;
?>
<script type="text/html" id="tmpl-youxi-gallery-preview">
	<div class="youxi-gallery-preview-item">
		<img src="{{ data.url }}" alt="">
		<input type="hidden" name="{{ data.fieldName }}[]" class="youxi-gallery-value" value="{{ data.id }}">
	</div>
</script>
<?php
			}
		}

		/**
		 * Helper method to filter an array of attributes before using it on a field
		 * 
		 * @param mixed The attributes to filter
		 *
		 * @return mixed The filtered attributes
		 */
		public function filter_field_attr( $attr ) {
			return array_merge( $attr, array(
				'class'           => esc_attr( 'youxi-gallery-form' ), 
				'data-field-name' => $this->get_the_name()
			));
		}

		/**
		 * Get the field's HTML markup
		 *
		 * @param mixed The field's current value (if it exists)
		 * @param array The HTML attributes to be added on the field
		 *
		 * @return string The field's HTML markup
		 */
		public function get_the_field( $value, $attributes = array() ) {

			$o = '<div id="' . $this->get_the_ID() . '"' . Youxi_Form::render_attr( $attributes ) . '>';

				$o .= '<div class="youxi-gallery-previews">';

					foreach( (array) $value as $val ): 

						$url = wp_get_attachment_thumb_url( $val );

						if( ! isset( $url ) || empty( $url ) )
							continue;

					$o .= '<div class="youxi-gallery-preview-item">';
						$o .= '<div class="youxi-gallery-preview-img-wrap">';
							$o .= '<div class="youxi-gallery-preview-img-wrap-inner">';
								$o .= '<img src="' . esc_url( $url ) . '" alt="">';
							$o .= '</div>';
						$o .= '</div>';
						$o .= '<input type="hidden" name="' . $this->get_the_name() . '[]" class="youxi-gallery-value" value="' . esc_attr( $val ) . '">';
					$o .= '</div>';

					endforeach;

				$o .= '</div>';

			$o .= '</div>';

			return $o;
		}
	}
}
