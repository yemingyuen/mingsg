/**
 * Youxi Colorpicker Form Field JS
 *
 * This script contains the initialization code for the colorpicker form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	// @todo: extend wpcolorpicker to include destroy method

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'colorpicker', function( context ) {

			if( $.fn.wpColorPicker ) {
				$( '.youxi-wp-color-picker', context ).wpColorPicker();
			}

		}, function( context ) {
			if( $.fn.wpColorPicker ) {
				$( ':wp-wpColorPicker', context ).each(function() {
					$( this ).wpColorPicker( 'destroy' );
				});
			}
		});
	}

})( jQuery, window, document );