/**
 * Youxi jQuery-UI Slider Form Field JS
 *
 * This script contains the initialization code for the jQuery-UI Slider form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'uislider', function( context ) {

			if( $.fn.slider ) {
				$( '.youxi-ui-slider', context ).each(function() {
					var sliderInput = $( this ).find( '.youxi-slider-input' ), 
						slider = $( this ).find( '.youxi-slider-widget' );

					if( slider.length ) {
						slider.slider( $.extend( true, {}, slider.data(), {
							value: sliderInput.val(), 
							change: function( event, ui ) {
								sliderInput.val( ui.value );
							}, 
							slide: function( event, ui ) {
								sliderInput.val( ui.value );
							}
						}));
					}
				});
			}

		}, function( context ) {

			if( $.fn.slider ) {
				$( '.youxi-ui-slider :ui-slider', context ).each(function() {
					$( this ).slider( 'destroy' );
				});
			}
		});
	}

})( jQuery, window, document );