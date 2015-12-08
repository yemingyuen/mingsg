<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Hi there!  I\'m just a plugin, not much I can do when called directly.' );
}
/**
 * Youxi Tabular Form Class
 *
 * This class renders a basic tabular input field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
if( ! class_exists( 'Youxi_Tabular_Form_Field' ) ) {

	class Youxi_Tabular_Form_Field extends Youxi_Form_Field {

		private static $media_template_printed = false;

		/**
		 * Constructor
		 */
		public function __construct( $scope, $options, $allowed_hooks = array() ) {

			// Merge default options
			$this->default_options = array_merge( $this->default_options, array(

				/* 
					Specify an array of column headings if you need a fixed number of columns.
					eg: array( 'column one', 'column two' )
				*/
				'columns' => false, 

				/*
					The options below is ignored for fixed columns tabular input.
				*/
				'min_columns' => 2, 
				'max_columns' => 10, 

				/*
					Specify the number of rows if you need a fixed number of rows.
				*/
				'rows' => false, 

				/*
					The options below is ignored for fixed rows tabular input.
				*/
				'min_rows' => 2, 
				'max_rows' => 0, 

				'mode' => 'textarea', 
				'textarea_rows' => 4
			));

			parent::__construct( $scope, $options, $allowed_hooks );
		}

		/**
		 * Apply form item attributes filtering
		 * 
		 * @param array The current attributes of the field
		 *
		 * @return array The filtered attributes of the field
		 */
		public function filter_field_attr( $attr ) {
			if( isset( $attr['class'] ) ) {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-tabular-input', $attr['class'] );
			} else {
				$attr['class'] = Youxi_Form::normalize_class( 'youxi-tabular-input' );
			}

			return parent::filter_field_attr( $attr );
		}

		/**
		 * Enqueue Required Assets
		 */
		public function enqueue( $hook ) {

			if( parent::enqueue( $hook ) ) {

				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

				wp_enqueue_script(
					'youxi-tabular-input', 
					self::field_assets_url( "js/youxi.form.tabular{$suffix}.js" ), 
					array( 'jquery-ui-widget', 'media-models', 'youxi-form-manager' ), 
					YOUXI_CORE_VERSION, 
					true
				);
				wp_enqueue_style(
					'youxi-tabular-input', 
					self::field_assets_url( "css/youxi.form.tabular{$suffix}.css" ), 
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

	<script type="text/html" id="tmpl-youxi-tabular-header">
		<th class="youxi-tabular-header">
			<input name="{{ data.fieldName }}[headers][]" type="text">
		</th>
	</script>

	<script type="text/html" id="tmpl-youxi-tabular-row">
		<tr class="youxi-tabular-row">
			{{{ data.tabularRowControls }}}
			{{{ data.tabularRowCells }}}
		</tr>
	</script>

	<script type="text/html" id="tmpl-youxi-tabular-cell">
		<td class="youxi-tabular-cell">
			<# if( data.inputMode == 'textarea' ) { #>
			<textarea class="youxi-tabular-field" rows="{{ data.textareaRows }}" name="{{ data.fieldName }}[cells][{{ data.rowIndex }}][]"></textarea>
			<# } else { #>
			<input class="youxi-tabular-field" type="text" name="{{ data.fieldName }}[cells][{{ data.rowIndex }}][]">
			<# } #>
		</td>
	</script>

	<script type="text/html" id="tmpl-youxi-tabular-row-controls">
		<td class="youxi-tabular-controls youxi-tabular-row-controls">
			<button type="button" data-action="add-row-before"><i class="dashicons dashicons-arrow-up-alt2"></i></button>
			<button type="button" data-action="delete-row"><i class="dashicons dashicons-trash"></i></button>
			<button type="button" data-action="add-row-after"><i class="dashicons dashicons-arrow-down-alt2"></i></button>
		</td>
	</script>

	<script type="text/html" id="tmpl-youxi-tabular-col-controls">
		<th class="youxi-tabular-controls youxi-tabular-col-controls">
			<button type="button" data-action="add-col-before"><i class="dashicons dashicons-arrow-left-alt2"></i></button>
			<button type="button" data-action="delete-col"><i class="dashicons dashicons-trash"></i></button>
			<button type="button" data-action="add-col-after"><i class="dashicons dashicons-arrow-right-alt2"></i></button>
		</th>
	</script>
			<?php
			}
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

			$columns = $this->get_option( 'columns' );
			$rows    = $this->get_option( 'rows' );

			$value = wp_parse_args( $value, array( 'headers' => array(), 'cells' => array() ) );
			extract( $value );

			// Determine if the rows/columns is fixed
			$fixed_cols = is_array( $columns ) && ! empty( $columns );
			$fixed_rows = (bool) $rows;

			// Get the number of initial rows and columns
			$num_cols = $fixed_cols ? count( $columns ) : max( count( (array) $headers ), $this->get_option( 'min_columns' ) );
			$num_rows = $fixed_rows ? $rows : max( count( (array) $cells ), $this->get_option( 'min_rows' ) );

			// Define the data options
			$attributes = array_merge( $attributes, array(
				'data-fixed-rows'      => esc_attr( json_encode( $fixed_rows ) ), 
				'data-fixed-cols'      => esc_attr( json_encode( $fixed_cols ) ), 
				'data-field-name'      => esc_attr( $this->get_the_name() ), 
				'data-textarea-rows'   => esc_attr( $this->get_option( 'textarea_rows' ) ), 
				'data-input-mode'      => esc_attr( $this->get_option( 'mode' ) ), 
				'data-min-columns'     => esc_attr( $this->get_option( 'min_columns' ) ), 
				'data-max-columns'     => esc_attr( $this->get_option( 'max_columns' ) ), 
				'data-min-rows'        => esc_attr( $this->get_option( 'min_rows' ) ), 
				'data-max-rows'        => esc_attr( $this->get_option( 'max_rows' ) ), 
				'data-confirm-del-row' => esc_attr__( 'Are you sure you want to delete this row?', 'youxi' ), 
				'data-confirm-del-col' => esc_attr__( 'Are you sure you want to delete this column?', 'youxi' )
			));

			$output = '<div' . Youxi_Form::render_attr( $attributes ) . '>';

			$output .= '<table>';

			$output .= '<thead>';
			$output .= '<tr>';

			if( ! $fixed_rows ) {
				$output .= '<th' . ( $fixed_cols ? '' : ' rowspan="2"' ) . '></th>';
			}

			if( $fixed_cols ) {

				// If fixed columns, render the labels directly and add a hidden input
				foreach( $columns as $column ) {
					$output .= '<th class="youxi-tabular-header">' . $column . '<input name="' . $this->get_the_name() . '[headers][]" type="hidden" value="' . $column . '"></th>';
				}

			} else {

				// Render the column toolbars
				for( $i = 0; $i < $num_cols; $i++ ) {
					$output .= '<th class="youxi-tabular-controls youxi-tabular-col-controls">';
					$output .= '<button type="button" data-action="add-col-before"><i class="dashicons dashicons-arrow-left-alt2"></i></button>';
					$output .= '<button type="button" data-action="delete-col"><i class="dashicons dashicons-trash"></i></button>';
					$output .= '<button type="button" data-action="add-col-after"><i class="dashicons dashicons-arrow-right-alt2"></i></button>';
					$output .= '</th>';
				}
				$output .= '</tr><tr>';

				// Render the column inputs
				for( $i = 0; $i < $num_cols; $i++ ) {
					$val = isset( $headers[ $i ] ) ? strval( $headers[ $i ] ) : '';
					$output .= "<th class=\"youxi-tabular-header\"><input name=\"" . $this->get_the_name() . "[headers][]\" type=\"text\" value=\"{$val}\"></th>";
				}
			}

			$output .= '</tr>';
			$output .= '</thead>';

			if( $num_rows ) {

				$output .= '<tbody>';

				for( $i = 0; $i < $num_rows; $i++ ) {

					$row_values = isset( $cells[ $i ] ) ? (array) $cells[ $i ] : array();
					$output .= '<tr class="youxi-tabular-row">';

					if( ! $fixed_rows ) {
						$output .= '<td class="youxi-tabular-controls youxi-tabular-row-controls">';
						$output .= '<button type="button" data-action="add-row-before"><i class="dashicons dashicons-arrow-up-alt2"></i></button>';
						$output .= '<button type="button" data-action="delete-row"><i class="dashicons dashicons-trash"></i></button>';
						$output .= '<button type="button" data-action="add-row-after"><i class="dashicons dashicons-arrow-down-alt2"></i></button>';
						$output .= '</td>';
					}

					for( $j = 0; $j < $num_cols; $j++ ) {

						$col_value = isset( $row_values[ $j ] ) ? strval( $row_values[ $j ] ) : '';
						$output .= '<td class="youxi-tabular-cell">';

						switch( $this->get_option( 'mode' ) ) {
							case 'textarea':
								$output .= '<textarea class="youxi-tabular-field" rows="' . $this->get_option( 'textarea_rows' ) . '" name="' . $this->get_the_name() . "[cells][$i][]\">" . esc_textarea( $col_value ) . '</textarea>';
								break;
							case 'text':
								$output .= '<input class="youxi-tabular-field" type="text" name="' . $this->get_the_name() . "[cells][$i][]\" value=\"" . esc_attr( $col_value ) . "\">";
							default:
								break;
						}

						$output .= '</td>';

					}

					$output .= '</tr>';
				}

				$output .= '</tbody>';
			}

			return $output . '</table></div>';
		}
	}
}