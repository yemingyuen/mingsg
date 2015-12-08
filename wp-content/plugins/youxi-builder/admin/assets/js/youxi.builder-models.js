/**
 * Youxi Builder Models JS
 *
 * This script contains the page builder Backbone Models
 *
 * @package   Youxi Builder
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */

"use strict";

jQuery.Youxi = jQuery.Youxi || {};

;(function( $, window, document, undefined ) {
	var builder, 
		shortcodes, l10n, 
		BaseController, 
		RootController, 
		ContainerController, 
		RowController, 
		ColumnController, 
		WidgetController;

	builder = $.Youxi.Builder = function( options ) {
		return new builder.view.Wrapper( options );
	}

	_.extend( builder, { model: {}, view: {}, controller: {} } );

	// Link any localized strings.
	l10n = builder.l10n = _.isUndefined( _youxiBuilderL10n ) ? {} : _youxiBuilderL10n;

	// Link any settings.
	builder.settings = l10n.settings || {};
	delete l10n.settings;

	// Copy the shortcode settings
	shortcodes = builder.settings.shortcodes = $.extend( true, {}, youxiShortcodeSettings.args || {} );

	// List the shortcode tags
	shortcodes.tags = _.flatten( _.map( shortcodes.categories || [], function( category ) {
		return _.keys( category.shortcodes );
	})).concat( _.keys( shortcodes.orphans ) );

	/**
	 * Extend WordPress Shortcode
	 */
	builder.Shortcode = {

		regex: wp.shortcode.regexp( shortcodes.tags.join( '|' ) ), 

		/* Use a custom attrs parser method 
		  (toJSON is twice faster compared to using wp.shortcode.attrs) */
		attrs: _.memoize( function( text ) {
			var attrs   = {},
				pattern, match;

			pattern = /(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/g;

			// Map zero-width spaces to actual spaces.
			text = text.replace( /[\u00a0\u200b]/g, ' ' );

			// Match and normalize attributes.
			while ( (match = pattern.exec( text )) ) {
				if ( match[1] ) {
					attrs[ match[1].toLowerCase() ] = match[2];
				} else if ( match[3] ) {
					attrs[ match[3].toLowerCase() ] = match[4];
				} else if ( match[5] ) {
					attrs[ match[5].toLowerCase() ] = match[6];
				} else if ( '' !== match[7] ) {
					attrs[ match[7] ] = true;
				} else if ( '' !== match[8] ) {
					attrs[ match[8] ] = true;
				}
			}

			return attrs;
		}), 

		toJSON: function( text ) {
			var match, result = [];
			var regexp = this.regex;
			
			regexp.lastIndex = 0;

			while( match = regexp.exec( text ) ) {

				// If we matched an escaped shortcode
				if( match[1] === '[' && match[7] === ']' )
					continue;

				/* Parse content and attributes */
				var attrs   = this.attrs( match[3] );				
				var content = match[5];

				/* Backup the last matching index */
				var reLastIndex = regexp.lastIndex;

				/* Parse the content */
				if( content ) {
					if( $.Youxi.Shortcode.getSetting( match[2], 'escape' ) ) {
						content = jQuery.trim( content );
					} else {
						content = this.toJSON( content );
					}
				}

				/* Save the shortcode objects recursively */
				result.push({
					tag:     match[2], 
					atts:    attrs, 
					content: content
				});

				/* Restore the last matching index */
				regexp.lastIndex = reLastIndex;
			}

			return result.length ? result : jQuery.trim( text );
		}
	};

	builder.Helpers = {

		getValidationStates: _.memoize(function( parentShortcode, shortcode ) {

			if( _.has( builder.settings.rules, parentShortcode ) ) {

				/* Check whether the shortcodes is allowed to be nested on the current view */
				var rule   = builder.settings.rules[ parentShortcode ];
				var states = { 'accept': true, 'reject': false };

				_.each( _.keys( states ), function( key ) {

					if( _.has( rule, key ) ) {
						if( _.isArray( rule[ key ] ) ) {
							states[ key ] = _.indexOf( rule[ key ], shortcode ) > -1;
						} else {
							states[ key ] = _.isString( rule[ key ] ) && '*' == rule[ key ];
						}
					}
				});

				return states;
			}
		}, function() {
			return 'builder.Helpers.getValidationStates(' + _.toArray( arguments ).join( ',' ) + ')';
		}), 

		getWrapperView: function( parent ) {

			switch( parent.get( 'type' ) ) {

				case 'root':
					if( ! builder.settings.enableContainer ) {
						return new builder.view.Row({
							parent: parent.view, 
							tag: builder.settings.rowShortcode
						});
					} else {
						return new builder.view.Container({
							parent: parent.view, 
							tag: builder.settings.columnContainerShortcode
						});
					}
				case 'container':
					return new builder.view.Row({
						parent: parent.view, 
						tag: builder.settings.rowShortcode
					});
				case 'row':
					var bestFit = ColumnController.getBestFit( parent.get( 'slots' ) );
					if( ! _.isUndefined( bestFit ) && _.has( bestFit, 'tag' ) ) {
						return new builder.view.Column({
							parent: parent.view, 
							tag: bestFit.tag
						});
					}
			}
		}, 

		getShortcodeClass: function( shortcode ) {

			if( builder.settings.enableContainer && _.indexOf( builder.settings.containerShortcodes, shortcode ) > -1 ) {
				return builder.view.Container;
			}

			if( shortcode == builder.settings.rowShortcode ) {
				return builder.view.Row;
			}

			if( ColumnController.getColumnByTag( shortcode ) ) {
				return builder.view.Column;
			}

			return builder.view.Widget;
		}
	};

	/**
	 * builder.model.ShortcodeButton
	 */
	builder.model.ShortcodeButton = Backbone.Model.extend({
		defaults: {
			'tag':     '', 
			'label':   '', 
			'atts':    '', 
			'dataset': {}
		},

		initialize: function() {
			var dataSet = {
				'data-tag': this.get( 'tag' )
			};
			if( ! _.isEmpty( this.get( 'atts' ) ) ) {
				_.extend( dataSet, { 'data-atts': JSON.stringify( this.get( 'atts' ) ) } );
			}

			this.set( 'dataset', dataSet );
		}
	}, {

		getTabs: function() {
			return _.map( shortcodes.categories || [], function( category ) {
				var label = category.args.label;

				return {
					href: label.toLowerCase().replace( /[^a-z0-9]/gi, '-' ), 
					label: label
				};
			});
		}, 

		getItems: function() {
			return _.map( shortcodes.categories || [], function( category ) {
				var label = category.args.label, 
					models = $.map( category.shortcodes, function( args, tag ) {
						if( ! args.internal && ( builder.settings.enableContainer || _.indexOf( builder.settings.containerShortcodes, tag ) < 0 ) ) {
							return new builder.model.ShortcodeButton( _.extend( {}, args, { tag: tag } ) );
						}
					});

				return {
					id: label.toLowerCase().replace( /[^a-z0-9]/gi, '-' ), 
					collection: new Backbone.Collection( models )
				};
			});
		}
	});

	/**
	 * builder.controller.Base
	 */
	BaseController = builder.controller.Base = Backbone.Model.extend({
		defaults: {
			tag:     '', 
			atts:    {}, 
			content: '', 
			type:    ''
		}, 

		constructor: function( options ) {
			if( options && options.view ) {
				this.view = options.view;
				delete options.view;
			}

			Backbone.Model.apply( this, arguments );
		}, 

		initialize: function() {
			this.view.on( 'initialized', this.viewInitialized, this );

			this.view.on( 'copy', this.copy, this );
			this.view.on( 'edit', this.edit, this );

			this.view.on( 'add:view', this.addViewHandler, this );
			this.view.on( 'remove:view', this.removeViewHandler, this );
		}, 

		/* Controller sortable/droppable options */
		sortableOptions: function() {
			var t = this;
			return {
				tolerance: 'pointer', 

				items: '> .youxi-builder-panel', 

				handle: '.youxi-builder-panel-header', 

				cancel: 'a.button', 

				connectWith: '.youxi-builder-' + this.get( 'type' ) + '-cw', 

				start: function( event, ui ) {
					ui.placeholder.css({
						'width': ui.item[0].style.width
					}).append( '<div class="youxi-builder-panel-inner"></div>' );

					// Prevent wrong helper width due to absolute position
					ui.helper.css({
						'width': ui.placeholder.outerWidth()
					});

					// Spawn message informing the sort start
					t.view.spawn( 'sort:subview:start', t.getMessageHash( this, ui.item ) );
					if( t.view.childContainer ) {
						t.view.childContainer.$el.sortable( 'refresh' );
					}
				}, 

				remove: function( event, ui ) {
					var view = ui.item.data( 'backbone.view' );

					if( view instanceof builder.view.Panel ) {
						t.view.removeView( view, { silent: true } );
					}
				}, 

				update: function( event, ui ) {
					if( null == ui.sender ) {
						t.compile();
					}
				}, 

				receive: function( event, ui ) {
					var view = ui.item.data( 'backbone.view' );

					if( view instanceof builder.view.Panel ) {
						t.view.maybeAddView( view, { at: ui.item.index(), silent: true } );
					}
				}, 

				stop: function( event, ui ) {
					// Spawn message informing the sort stop
					t.view.spawn( 'sort:subview:stop', t.getMessageHash( this, ui.item ) );
				}
			};
		}, 

		droppableOptions: function() {
			var t = this;
			return {
				scope: 'youxi-builder', 

				greedy: ( 'root' != this.get( 'type' ) ), 

				hoverClass: 'ui-state-hover', 

				tolerance: 'pointer', 

				accept: function( el ) {
					return el.is( '.youxi-builder-component' ) 
						&& ! el.data( 'isDropped' ) // Make sure the draggable isn't dropped yet
						&& t.validateDrop( el.data( 'tag' ) );
				}, 

				drop: function( e, ui ) {
					var data = ui.draggable.data();
					var options = _.extend({
							'coords': {
								'x': e.pageX, 
								'y': e.pageY
							}
						}, 
						_.pick( data, 'tag' ), 
						data.atts || {}
					);

					if( t.validateDrop( options.tag ) ) {
						t.view.handleDropped.apply( t.view, [ options ] );
						/* Flag the draggable to prevent it being dropped multiple times by unpredictable causes */
						ui.draggable.data( 'isDropped', true );
					}
				}
			};
		}, 

		getShortcodeObject: function() {
			return $.extend( true, {}, this.get( 'atts' ) || {}, { 'content': this.get( 'content' ) } )
		}, 

		toShortcode: function() {

			var childViews, content;

			if( childViews = this.view.getChildViews( true ) ) {
				content = _.map( childViews, function( view ) {
					return view.controller.toShortcode();
				}).join( '\n\n' );

				this.set( { 'content': content }, { silent: true } );
			}

			if( 'root' == this.get( 'type' ) ) {
				return this.get( 'content' );
			}
			
			return $.Youxi.Shortcode.construct( this.get( 'tag' ), this.getShortcodeObject() );
		}, 

		/* Controller action handlers */
		edit: function() {

			var tag = this.get( 'tag' );

			/* Open up the shortcode editor */
			$.Youxi.Shortcode.Editor( tag, this.getShortcodeObject(), _.bind( function( result ) {
				if( _.isObject( result ) && _.has( result, tag ) ) {
					var atts    = _.omit( result[ tag ], 'content' );
					var content = _.has ( result[ tag ], 'content' ) ? result[ tag ].content : '';

					this.set({ 'atts': atts, 'content': content });
				}
			}, this ));
		}, 

		copy: function() {
			this.view.spawn( 'copy:view', {
				object: builder.Shortcode.toJSON( this.toShortcode() )
			});
		}, 

		/* Controller event handlers */
		viewInitialized: $.noop, 

		addViewHandler: function( view, options ) {
			if( view.controller ) {
				view.controller.on( 'change:tag', this.compile, this );
				view.controller.on( 'change:atts', this.compile, this );
				view.controller.on( 'change:content', this.compile, this );
			}

			view.parent = this.view;

			if( ! options || ! options.populate ) {
				this.compile();
			}
		}, 

		removeViewHandler: function( view, options ) {
			if( view.controller ) {
				view.controller.off( null, null, this );
			}

			if( view.parent ) {
				delete view.parent;
			}

			if( ! options || ! options.populate ) {
				this.compile();
			}
		}, 

		/* Controller internal methods */
		toView: function( options ) {

			var states = this.getValidationStates( options.tag );

			/* Continue if not rejected */
			if( $.isPlainObject( states ) && ! states.reject ) {

				/* If not accepted nor rejected, we need to create the views recursively */
				if( ! states.accept ) {

					var view = builder.Helpers.getWrapperView( this );

					if( view instanceof builder.view.Panel ) {
						view.maybeAddView( options );
						return view;
					}

				} else {

					/* Just create the view if accepted */
					var viewClass = builder.Helpers.getShortcodeClass( options.tag );

					return new viewClass( _.extend( options, {
						parent: this.view
					}));
				}
			}
		}, 

		compile: function() {
			if( 'root' == this.get( 'type' ) ) {
				this.view.trigger( 'insert:tinymce', this.toShortcode() );
			} else {
				this.view.spawn( 'compile:shortcode' );
			}
		}, 

		populate: function( content, options ) {

			if( _.isString( content ) ) {

				this.set( 'content', content );

			} else {

				_.each( _.isArray( content ) ? content : [ content ], function( shortcode ) {

					/* Make sure the shortcode object is valid */
					if( $.isPlainObject( shortcode ) && _.has( shortcode, 'tag' ) ) {

						/* Check whether the shortcode is allowed to be nested in the current view */
						var states = this.getValidationStates( shortcode.tag );

						if( $.isPlainObject( states ) && states.accept ) {

							/* Determine shortcode class */
							var viewClass = builder.Helpers.getShortcodeClass( shortcode.tag );

							/* Create the view */
							var view = new viewClass({
								parent: this.view, 
								tag: shortcode.tag
							});

							/* Deserialize shortcode atts and content */
							var data = $.extend( true, {}, shortcode.atts || {}, { 'content': shortcode.content } );
							for( var key in data ) {
								data[ key ] = $.Youxi.Shortcode.deserialize( shortcode.tag, key, data[ key ] );
							}

							/* Populate view with content */
							view.populate( shortcode.content );

							/* Assign shortcode to view controller */
							if( view.controller ) {
								view.controller.set({
									'atts': _.omit( data, 'content' ), 
									'content': data.content
								});
							}

							/* Append view */
							this.view.maybeAddView( view, _.extend( options || {}, { populate: true } ) );
						}
					}
				}, this );
			}

			if( options && options.compile ) {
				this.compile();
			}
		}, 

		getMessageHash: function( container, item ) {
			return {
				originalSource: this.view
			};
		}, 

		getValidationStates: function( tag ) {
			return builder.Helpers.getValidationStates( this.get( 'tag' ), tag );
		}, 

		validateDrop: function( tag ) {
			var states = this.getValidationStates( tag );
			return $.isPlainObject( states ) && ! states.reject;
		}, 

		dispose: function() {
			if( this.view && this.view.off ) {
				this.view.off( null, null, this );
			}

			_.each( this.view.getChildViews() || [], function( view ) {
				if( view.controller && view.controller.off ) {
					view.controller.off( null, null, this );
				}
			}, this );
		}
	});

	/**
	 * builder.controller.Row
	 */
	RowController = builder.controller.Row = BaseController.extend({
		defaults: function() {
			return _.extend( {}, BaseController.prototype.defaults, {
				slots:  builder.settings.rowSize
			});
		}, 

		validateDrop: function( tag ) {
			var base = BaseController.prototype.validateDrop.apply( this, arguments ), 
				neededSize;

			if( ! builder.settings.simpleColumns ) {
				neededSize = ColumnController.getMinSize();
			} else {
				if( neededSize = ColumnController.getColumnByTag( tag ) ) {
					neededSize = neededSize.size;
				}
			}

			return base && $.isNumeric( neededSize ) && this.get( 'slots' ) >= neededSize;
		}, 

		validate: function( attributes, options ) {
			if( attributes.slots < 1 || attributes.slots > builder.settings.rowSize ) {
				return l10n.invalidRowSlotsSize
			}
		}, 

		updateSlots: function( model, size ) {
			var prevSize = model.previous( 'size' ), 
				slots = this.get( 'slots' );

			if( $.isNumeric( prevSize ) ) {
				this.set( 'slots', slots + prevSize - size );
			}
		}, 

		addViewHandler: function( view ) {
			if( view instanceof builder.view.Panel ) {
				this.set( 'slots', this.get( 'slots' ) - view.controller.get( 'size' ) );
				view.controller.on( 'change:size', this.updateSlots, this );
			}
			BaseController.prototype.addViewHandler.apply( this, arguments );
		}, 

		removeViewHandler: function( view ) {
			if( view instanceof builder.view.Panel ) {
				this.set( 'slots', this.get( 'slots' ) + view.controller.get( 'size' ) );
			}
			BaseController.prototype.removeViewHandler.apply( this, arguments );
		}, 

		getMessageHash: function( container, item ) {
			var view = item.data( 'backbone.view' ), 
				hash = BaseController.prototype.getMessageHash.apply( this, arguments );

			if( item.is( '.youxi-builder-column' ) && view instanceof builder.view.Panel ) {
				_.extend( hash, { columnSize: view.controller.get( 'size' ) });
			}

			return hash;
		}
	});

	/**
	 * builder.controller.Column
	 */
	ColumnController = builder.controller.Column = BaseController.extend({

		defaults: function() {
			return _.extend( {}, BaseController.prototype.defaults, {
				size:  builder.settings.rowSize
			});
		}, 

		initialize: function() {
			BaseController.prototype.initialize.apply( this, arguments );

			this.view.on( 'increase decrease', this.resize, this );

			if( ! builder.settings.simpleColumns ) {
				this.on( 'change:atts', this.synchronize, this );
			}
			this.on( 'change:size', this.synchronize, this );
		}, 

		viewInitialized: function() {
			this.trigger( 'change:size', this, this.get( 'size' ) );
		}, 

		validate: function( attributes, options ) {
			if( attributes.size < 1 || attributes.size > builder.settings.rowSize ) {
				return l10n.invalidColumnSize;
			}

			if( this.view.spawn( 'request:slots!' ) < ( attributes.size - this.get( 'size' ) ) ) {
				return l10n.notEnoughColumnSlots;
			}
		}, 

		copy: $.noop, 

		resize: function( e ) {
			if( _.isElement( e.currentTarget ) ) {
				var ns, act = $( e.currentTarget ).data( 'action' ), 
					actMap = { 'increase': 'getNextSize', 'decrease': 'getPrevSize' };

				if( _.has( actMap, act ) ) {
					ns = ColumnController[ actMap[ act ] ]( this.get( 'size' ) );
					if( ! _.isUndefined( ns ) ) {
						this.set( _.pick( ns, 'tag', 'size' ), { validate: true });
					}
				}
			}
		}, 

		synchronize: function( model, sizeObject ) {
			if( ! builder.settings.simpleColumns ) {
				if( $.isPlainObject( sizeObject ) && _.has( sizeObject, 'size' ) ) {
					this.set({ 'size': parseInt( sizeObject.size ) });
				} else if( $.isNumeric( sizeObject ) ) {
					this.set({ 'atts': _.extend( {}, this.get( 'atts' ), { 'size': parseInt( sizeObject ) } ) });
				}
			}
			this.view.refresh( this.get( 'size' ) );
		}

	}, {
		isColumnContainer: function( tag ) {
			return tag === builder.settings.columnContainerShortcode;
		}, 

		getColumnByTag: _.memoize( function( tag ) {
			return _.find( builder.settings.columnShortcodes || [], function( current ) {
				return current.tag == tag;
			});
		}), 

		getBestFit: _.memoize( function( size ) {
			return _.find( builder.settings.columnShortcodes || [], function( current ) {
				return current.size == size;
			}) || ColumnController.getPrevSize( size );
		}), 

		getNextSize: _.memoize( function( size ) {
			return _.find( builder.settings.columnShortcodes || [], function( current ) {
				return current.size > size;
			});
		}), 

		getPrevSize: _.memoize( function( size ) {
			return _.find( _.clone( builder.settings.columnShortcodes || [] ).reverse(), function( current ) {
				return current.size < size;
			});
		}), 

		getMinSize: _.memoize( function() {
			return Math.min.apply( Math, _.map( builder.settings.columnShortcodes || [], function( o ) { return o.size }));
		}), 

		getTitle: _.memoize( function( size ) {
			var gcdFn = function( a, b ) {
				return b ? gcdFn( b, a % b ) : Math.abs( a );
			};

			var reduce = _.memoize(function( a, b ) {
				var gcd = gcdFn( a, b );
				return [ a / gcd, b / gcd ];
			});

			return function() {
				return l10n.columnTitlePrefix + reduce( size, builder.settings.rowSize ).join( '/' );
			};
		})
	});

}) ( jQuery, window, document );