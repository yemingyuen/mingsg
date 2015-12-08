/**
 * Youxi Form Manager JS
 *
 * This script handles every form's initialization.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	$.Youxi = $.Youxi || {};

	var inputsToCheck = 'select, input[type="radio"]:checked, input[type="checkbox"]';

	$.extend( true, $.Youxi, {
		Form: {
			Manager: {
				_callbacks: {
					init: {}, 
					destroy: {}
				}, 

				addCallbacks: function( name, init, destroy ) {
					if( $.isFunction( init ) ) {
						this._callbacks.init[ name ] = init;
					}

					if( $.isFunction( destroy ) ) {
						this._callbacks.destroy[ name ] = destroy;
					}
				},

				removeCallbacks: function( name ) {
					delete this._callbacks.init[ name ];
					delete this._callbacks.destroy[ name ];
				}, 

				initialize: function( context, name ) {
					if( typeof name !== 'undefined' ) {
						if( this._callbacks.init.hasOwnProperty( name ) ) {
							this._callbacks.init.name.apply( this, [ context ] );
						}
					} else {
						$.each( this._callbacks.init, function( name, callback ) {
							callback.apply( this, [ context ] );
						});
					}
				}, 

				destroy: function( context, name ) {
					if( typeof name !== 'undefined' ) {
						if( this._callbacks.destroy.hasOwnProperty( name ) ) {
							this._callbacks.destroy.name.apply( this, [ context ] );
						}
					} else {
						$.each( this._callbacks.destroy, function( name, callback ) {
							callback.apply( this, [ context ] );
						});
					}
				}
			},  

			parseCriteria: _.memoize( function( criteria ) {

				var match;
				var regex = /(.+?):(is|isnot|not|contains|less_than|less_than_or_equal_to|greater_than|greater_than_or_equal_to)\((.*?)\),?/g;
				var criterias = [];

				while( match = regex.exec( criteria ) ) {
					criterias.push({
						'check': match[1], 
						'rule':  match[2], 
						'value': match[3]
					});
				}

				return criterias;
			}), 

			criteriaCheck: function( context ) {

				/* Notice: doesn't work for checking checkbox lists */

				$( '[data-field-scope][data-criteria]', context ).each(function() {

					// Don't check on repeater templates
					if( $( this ).closest( '.youxi-repeater-template' ).length ) {
						return;
					}

					var passed;
					var currentScope = $( this ).data( 'field-scope' );

					var criterias = $( this ).data( 'criteria-cache' );
					var operator  = ( $( this ).data( 'criteria-operator' ) || 'and' ).toLowerCase();

					if( ! criterias ) {
						criterias = $.Youxi.Form.parseCriteria( $( this ).data( 'criteria' ) );
						$( this ).data( 'criteria-cache', criterias );
					}

					$.each( criterias, function( index, criteria ) {

						var target   = $( '#' + currentScope + '_' + criteria.check + '_group', context );
						var targetEl = target.find( inputsToCheck ).first();

						if( targetEl.is( ':checkbox' ) && ! targetEl.is( ':checked' ) ) {
							targetEl = targetEl.siblings( 'input[type="hidden"][name="' + targetEl.attr( 'name' ) + '"]' ).first();
						}

						if( ! targetEl.length ) {
							return;
						}

						var v1 = targetEl.val().toString();
						var v2 = criteria.value.toString();
						var result;

						switch ( criteria.rule ) {
							case 'is':
								result = ( v1 == v2 );
								break;
							case 'isnot':
							case 'not':
								result = ( v1 != v2 );
								break;
							case 'less_than':
								result = ( parseInt( v1 ) < parseInt( v2 ) );
								break;
							case 'less_than_or_equal_to':
								result = ( parseInt( v1 ) <= parseInt( v2 ) );
								break;
							case 'greater_than':
								result = ( parseInt( v1 ) > parseInt( v2 ) );
								break;
							case 'greater_than_or_equal_to':
								result = ( parseInt( v1 ) >= parseInt( v2 ) );
								break;
							case 'contains':
								result = ( v2.indexOf(v1) !== -1 ? true : false );
								break;
						}

						if( _.isUndefined( passed ) ) {
							passed = result;
						}

						switch( operator ) {
							case 'or':
								passed = ( passed || result );
								break;
							case 'and':
							default:
								passed = ( passed && result );
								break;
						}
					});

					$( this )
						.toggle( passed )
						.find( ':input' )
							.not( '.skip-criteria-check' )
							.attr( 'disabled', ! passed );
				});
			}
		}
	});

	// Bind the conditional checks callback on the document
	$( document ).on( 'change.conditional-toggle', '[data-field-scope]', function( e ) {
		if( $( e.target ).is( inputsToCheck ) && $.contains( this, e.target ) ) {
			$.Youxi.Form.criteriaCheck( $( this ).closest( '[data-field-scope]' ).parent() );
		}
	});

	// Fires the initial conditional checks on the specified context
	$.Youxi.Form.Manager.addCallbacks( 'conditional-toggle', function( context ) {
		$.Youxi.Form.criteriaCheck( context );
	});

	// Form tabs
	$.Youxi.Form.Tabs = function( element ) {
		this.element = $( element );
		return this.init();
	}

	$.Youxi.Form.Tabs.prototype = {
		init: function() {
			this.element.on( 'click.youxi-form-tabs', '.youxi-form-tabs-nav a[href^="#"]', this.change );
			this.change.apply( this.element.find( '.youxi-form-tabs-nav li:first-child a[href^="#"]' ) );
		}, 
		change: function(e) {
			$( this ).closest( '.youxi-form-tabs-nav ul li' )
				.addClass( 'active' ).siblings().removeClass( 'active' );

			$( this ).closest( '.youxi-form-tabs' )
				.find( '.youxi-form-tabs-content ' + $( this ).attr( 'href' ) ).show().siblings().hide();

			e && e.preventDefault();
		}
	};

	// Initialize form tabs inside forms
	$.Youxi.Form.Manager.addCallbacks( 'form-tabs', function( context ) {
		$( '.youxi-form-tabs', context ).each(function() {
			new $.Youxi.Form.Tabs( this );
		});
	}, function( context ) {
		$( '.youxi-form-tabs', context ).off( '.youxi-form-tabs' );
	});

	// Fires the initial callback when the DOM is ready
	$( document ).ready(function() {
		$.Youxi.Form.Manager.initialize( this );
	});
	
})( jQuery, window, document );