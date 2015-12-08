/**
 * Youxi Code Editor Form Field JS
 *
 * This script contains the initialization code for the code editor form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	if( $.Youxi.Form.Manager && typeof CodeMirror !== 'undefined' ) {

		$.Youxi.Form.Manager.addCallbacks( 'code-editor', function( context ) {

			$( context ).find( '.youxi-code-editor-textarea' ).each(function() {

				$.data( this, 'youxi-cm', CodeMirror.fromTextArea( this, {
					mode: $( this ).data( 'editor-mode' ), 
					lineNumbers: true
				}));
			});

		}, function( context ) {
			
			$( context ).find( '.youxi-code-editor-textarea' ).each(function() {

				var api = $.data( this, 'youxi-cm' );
				if( api ) {
					if( ( api instanceof CodeMirror ) && $.isFunction( api.toTextArea ) ) {
						api.toTextArea();
					}
					$.removeData( this, 'youxi-cm' );
				}
			});
		});
	}

})( jQuery, window, document );