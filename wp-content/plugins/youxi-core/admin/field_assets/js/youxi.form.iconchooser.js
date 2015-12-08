/**
 * Youxi Icon Chooser Form Field JS
 *
 * This script contains the initialization code for the icon chooser form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	function formatIconItem( state ) {
		if( ! state.id ) {
			return state.text; // optgroup
		}

		return '<span style="margin-right: 8px;"><i class="' + state.id + '"></i></span>' + state.text;
	}

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'icon_chooser', function( context ) {

			if( $.fn.select2 ) {
				$( '.youxi-icon-chooser-select', context ).select2({
					formatResult: formatIconItem, 
					formatSelection: formatIconItem, 
					escapeMarkup: function( m ) {
						return m;
					}
				});
			}

		}, function( context ) {

			if( $.fn.select2 ) {
				$( '.youxi-icon-chooser-select', context ).select2( 'destroy' );
			}
			
		});
	}

})( jQuery, window, document );