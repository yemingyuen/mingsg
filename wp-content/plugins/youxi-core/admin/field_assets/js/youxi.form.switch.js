/**
 * Youxi Switch Form Field JS
 *
 * This script contains the initialization code for the switch form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'switch', function( context ) {

			if( typeof Switchery !== 'undefined' ) {
				$( '.js-switch', context ).each(function() {
					if( this.type == 'checkbox' ) {
						var disabledState = this.disabled;
						this.disabled = false;
						new Switchery( this );
						this.disabled = disabledState;
					}
				});
			}

		});
	}

})( jQuery, window, document );