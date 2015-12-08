/**
 * Youxi Togglable Fieldsets Form Field JS
 *
 * This script contains the togglable fieldsets form field widget.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	if( ! $.widget )
		return;

	$.widget( 'youxi.togglableFieldsets', {
		options: {}, 

		_create: function() {

			var t = this;

			// Find all switches, toggle the fieldsets and initialize Switchery
			this.element.find( '.youxi-togglable-fieldset-toggle input[type="checkbox"]' ).each(function() {

				t._toggleFieldset( this );

				if( this.type == 'checkbox' && Switchery ) {
					var disabledState = this.disabled;
					this.disabled = false;
					new Switchery( this );
					this.disabled = disabledState;
				}
			});

			// Listen to control buttons click event
			this._on({
				'change .youxi-togglable-fieldset-toggle input[type="checkbox"]': function( e ) {
					this._toggleFieldset( e.currentTarget );
				}
			});
		}, 

		_toggleFieldset: function( jsSwitch ) {
			jsSwitch = $( jsSwitch );
			if( jsSwitch.length && 'checkbox' == jsSwitch[0].type ) {
				jsSwitch.closest( '.youxi-togglable-fieldset' )
					.find( '.youxi-togglable-fieldset-content' )
					.toggle( jsSwitch.prop( 'checked' ) )
					.prop( 'disabled', ! jsSwitch.prop( 'checked' ) );
			}
		}
	});

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'togglable-fieldsets', function( context ) {
			if( $.fn.togglableFieldsets ) {
				$( '.youxi-togglable-fieldsets', context ).each(function() {
					if( ! $( this ).is( ':youxi-togglableFieldsets' ) ) {
						$( this ).togglableFieldsets( $( this ).data() );
					}
				});
			}
		}, function( context ) {
			if( $.fn.togglableFieldsets ) {
				$( ':youxi-togglableFieldsets', context ).each( function() {
					$( this ).togglableFieldsets( 'destroy' );
				});
			}
		});
	}
	
})( jQuery, window, document );