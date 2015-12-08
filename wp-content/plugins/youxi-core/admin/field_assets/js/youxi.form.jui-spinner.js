/**
 * Youxi jQuery-UI Spinner Form Field JS
 *
 * This script contains the initialization code for the jQuery-UI Spinner form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'uispinner', function( context ) {

			if( $.fn.spinner ) {
				$( '.youxi-ui-spinner .youxi-spinner-input', context ).each(function() {
					$( this ).spinner( $( this ).data() );
				});
			}
		}, function( context ) {

			if( $.fn.spinner ) {
				$( '.youxi-ui-spinner :ui-spinner', context ).each(function() {
					$( this ).spinner( 'destroy' );
				});
			}
		});
	}

})( jQuery, window, document );