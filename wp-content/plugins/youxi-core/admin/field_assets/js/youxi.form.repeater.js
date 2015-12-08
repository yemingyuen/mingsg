/**
 * Youxi Repeater Form Field JS
 *
 * This script contains the repeater form field widget.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	// Make sure wp.media and jquery-ui widget factory is defined
	if( ! wp.media || ! wp.media.template || ! $.widget )
		return;

	$.widget( 'youxi.fieldrepeater', {
		options: {
			max: 0, 
			min: 1, 
			depth: 0, 
			templateId: '', 
			templateVars: {}, 
			confirmRemove: 'Are you sure you want to delete this row?'
		}, 

		_init: function() {

			var t = this;

			// Keep the depth safe as it should not change on runtime
			this._depth = this.options.depth;
			delete this.options.depth;

			// Refresh controls
			this._refresh();

			// initialize the current index
			this.options.templateVars[ 'index_' + this._depth ] = this._getRows().length;

			// initialize preview and nested repeaters
			this._getRows().each(function( index ) {

				// render preview
				t._renderPreview.apply( t, [ t._getInputs( this ).first() ] );

				// initialize immediate nested repeaters
				$( this ).find( '.youxi-repeater' ).not( ':youxi-fieldrepeater' ).filter(function() {
					return t.ownsElement( this );
				}).each(function() {
					// Copy and modify the current template vars based on row index
					var tVars = $.extend( true, {}, t.options.templateVars );
					tVars[ 'index_' + t._depth ] = index;

					// Initialize the repeater
					$( this ).fieldrepeater( $.extend( true, $( this ).data(), { templateVars: tVars } ) );
				});
			});
		}, 

		_create: function() {

			this.templates = {};

			// Create the template
			this.templates['row'] = wp.media.template( this.options.templateId );

			// Create the preview template if specified
			if( !! $( '#tmpl-' + this.options.templateId + '-preview' ).length ) {
				this.templates['preview'] = wp.media.template( this.options.templateId + '-preview' );
			}

			// Listen to control buttons click event
			this._on({
				'click > fieldset button[data-action]': function( e ) {
					var button = $( e.target ).closest( 'button' );
					var action = button.data( 'action' );

					// Make sure the element is not a nested repeater element
					if( this.ownsElement( button ) ) {

						if( $.isFunction( this[action] ) ) {
							this[action].apply( this, [ button[0] ] );
						}

					}

					e.stopPropagation();
				}, 
				'change :input:not(:disabled):not(:button)': function( e ) {
					var input = $( e.target );

					if( this.ownsElement( input ) ) {
						this._renderPreview.apply( this, [ input ] );
					}
				}
			});
		}, 

		_renderPreview: function( el ) {
			// Make sure we have a preview template
			if( ! this.templates.preview ) {
				return;
			}

			// Make sure the element is not a nested repeater element
			if( ! this.ownsElement( el ) ) {
				return;
			}

			var name, formData = {};

			var formRow    = el.closest( '.youxi-repeater-row' );
			var scope      = el.closest( '[data-field-scope]' ).data( 'field-scope' );
			var formInputs = this._getInputs( formRow );

			$.map( formInputs.serializeArray(), function( form ) {
				name = form.name;
				name = name.replace( /(\]\[|\[|\]|\[\])/g, '_' );
				name = name.replace( scope, '' );
				name = name.replace( /^_|_$/g, '' );

				if( form.value ) {
					formData[ name ] = form.value;
				}
			});

			formRow.find( ' > .youxi-repeater-row-header .youxi-repeater-row-title' )
				.html( _.isEmpty( formData ) ? '' : this.templates.preview( formData ) );
		}, 

		_refresh: function() {
			var t = this, 
				states = {
					'add': 0 !== this.options.max && this._getRows().length >= this.options.max, 
					'remove': this._getRows().length <= this.options.min
				};

			this.element.find( 'button[data-action]' ).each(function() {
				if( t.ownsElement( this ) && states.hasOwnProperty( $( this ).data( 'action' ) ) ) {
					$( this ).attr( 'disabled', states[ $( this ).data( 'action' ) ] );
				}
			});
		}, 

		_getInputs: function( el ) {
			var t = this;
			return $( el ).find( ':input:not(:disabled):not(:button)' ).filter(function() {
				return t.ownsElement( this );
			});
		}, 

		_getRows: function() {
			return $( ' > fieldset > .youxi-repeater-fields', this.element ).children( '.youxi-repeater-row' );
		}, 

		ownsElement: function( obj ) {
			// Check if obj is a DOM element that is not a nested repeater's element
			return _.isElement( ( obj = $( obj ) )[0] ) && obj.parents( ':youxi-fieldrepeater' )[0] == this.element[0];
		}, 

		edit: function( obj ) {
			var t = this;
			var row = $( obj ).closest( '.youxi-repeater-row' );
			var siblings = row.siblings( '.youxi-repeater-row' );

			siblings.removeClass( 'editing' )
				.find( 'button[data-action="edit"]' )
					.filter(function() { return t.ownsElement( this ); })
						.removeClass( 'active' );

			row.toggleClass( 'editing' )
				.find( 'button[data-action="edit"]' )
					.filter(function() { return t.ownsElement( this ); })
						.toggleClass( 'active' );
		}, 

		remove: function( obj ) {
			if( this._getRows().length > this.options.min ) {
				if( confirm( this.options.confirmRemove ) ) {
					var row = $( obj ).closest( '.youxi-repeater-row' );
					if( row && row.length ) {
						$.Youxi.Form.Manager.destroy( row );
					}
					row.remove();

					this._refresh();
				}
			}
		}, 

		add: function() {
			var t = this;

			if( this.options.max == 0 || this._getRows().length < this.options.max ) {

				var row = $( this.templates.row( this.options.templateVars ) );

				$( ' > fieldset > .youxi-repeater-fields', this.element ).first().append( row );

				$.Youxi.Form.Manager.initialize( row );

				row.find( ':youxi-fieldrepeater' ).each(function() {
					var vars = $( this ).fieldrepeater( 'option', 'templateVars' );
					vars = $.extend( true, {}, vars, t.options.templateVars );

					$( this ).fieldrepeater( 'option', 'templateVars', vars );
				});

				this._renderPreview( this._getInputs( row ).first() );

				this.options.templateVars[ 'index_' + this._depth ]++;

				this._refresh();

				this.edit( row );
			}
		}
	});

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'repeater', function( context ) {
			if( $.fn.fieldrepeater ) {
				$( '.youxi-repeater', context ).each(function() {
					if( ! $( this ).is( ':youxi-fieldrepeater' ) ) {
						$( this ).fieldrepeater( $( this ).data() );
					}
				});
			}
		}, function( context ) {
			if( $.fn.fieldrepeater ) {
				$( ':youxi-fieldrepeater', context ).each( function() {
					$( this ).fieldrepeater( 'destroy' );
				});
			}
		});
	}
	
})( jQuery, window, document );