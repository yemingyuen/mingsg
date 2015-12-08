/**
 * Youxi Builder Views JS
 *
 * This script contains the page builder Backbone Views
 *
 * @package   Youxi Builder
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */

"use strict";

jQuery.Youxi = jQuery.Youxi || {};

;(function( $, window, document, undefined ) {
	var builder = $.Youxi.Builder, 
		l10n = builder.l10n, 
		_win = $( window );

	/**
	 * builder.view.Wrapper
	 */
	builder.view.Wrapper = wp.media.View.extend({

		className: 'youxi-builder-wrapper', 

		template: wp.template( 'youxi-builder-wrapper' ), 

		initialize: function() {
			_.defaults( this.options, {
				editorId: 'content'
			});

			this.createMenu();
			this.createRoot();
			this.bindHandlers();
		}, 

		bindHandlers: function() {
			this.listenTo( this, 'show', this.importFromEditor );
			this.listenTo( this.root, 'insert:tinymce', this.exportToEditor );

			this.listenTo( this, 'show', this.disableEditorExpand );
			this.listenTo( this, 'hide', this.restoreEditorExpand );
		}, 

		disableEditorExpand: function() {
			this.editorExpandEnabled = $( '#editor-expand-toggle' ).prop( 'checked' );
			$( '#editor-expand-toggle' )
				.prop( 'checked', false )
				.prop( 'disabled', true )
				.triggerHandler( 'change' );
			$( document.getElementById( this.options.editorId ) ).hide();
		}, 

		restoreEditorExpand: function() {
			$( '#editor-expand-toggle' )
				.prop( 'checked', this.editorExpandEnabled )
				.prop( 'disabled', false )
				.triggerHandler( 'change' );
		}, 

		createMenu: function() {
			this.menu = new builder.view.Menu({
				controller: this
			});
			this.views.add( '.youxi-builder-header', this.menu );
		}, 

		createToolbar: function() {
			this.toolbar = new builder.view.Toolbar({
				controller: this
			});
			this.views.add( '.youxi-builder-header', this.toolbar );
		}, 

		createRoot: function() {
			this.root = new builder.view.Root({
				parent: this
			});
			this.views.add( '.youxi-builder-content-wrap', this.root );
		}, 

		exportToEditor: function( content ) {

			if( ! _.isUndefined( tinymce ) ) {

				var editor = tinymce.get( this.options.editorId ) || document.getElementById( this.options.editorId );

				if( editor instanceof tinymce.Editor ) {

					if( editor.isHidden() ) {

						// If editor is hidden, we set the raw contents without wpautop since the saving will not trigger an event that'll remove the HTML
						editor.setContent( content, { format: 'raw' } );

						// If editor is hidden, we should save with no events since WP always takes the value from the real textarea upon save
						editor.save( { format: 'raw', no_events: true } );

					} else {

						// wpautop the content if the editor is visible because we need to preserve line breaks
						if( editor.getParam( 'wpautop', true ) && typeof switchEditors == 'object' )
							content = switchEditors.wpautop( content );

						// Set content with HTML format so TinyMCE can parse the HTML
						editor.setContent( content );

						// Save with events, so WP can remove the wpautop results
						editor.save();
					}

				} else if( _.isElement( editor ) && /TEXTAREA/i.test( editor.nodeName ) ) {

					editor.value = content;

				}
			}
		}, 

		importFromEditor: function() {

			if( typeof tinymce != "undefined" ) {

				var editor = tinymce.get( this.options.editorId ),	
					content = '';

				if( editor && editor instanceof tinymce.Editor ) {
					content = editor.save();
				} else if( $( 'textarea#' + this.options.editorId ).length ) {
					content = $( 'textarea#' + this.options.editorId ).val();
				}

				switch( builder.settings.parseMethod ) {
					case 'ajax':
						wp.ajax.post( 'parse_shortcode_to_json', {
							content: content
						}).done( _.bind( function( data ) {
							this.root.clear();
							this.root.populate( data.parsed );
						}, this ));
						break;
					case 'js':
					default:
						var json = builder.Shortcode.toJSON( content );
						this.root.clear();
						this.root.populate( json );
						break;
				}
			}
		}, 

		attach: function() {
			if( this.views.attached )
				return this;

			if ( ! this.views.rendered )
				this.render();

			this.$el.appendTo( '#wp-' + this.options.editorId + '-editor-container' );
			
			this.views.attached = true;
			this.views.ready();

			return this.trigger( 'attach' );
		}, 

		show: function() {
			var $el = this.$el;

			if( $el.is( ':visible' ) ) {
				return this;
			}

			if( ! this.views.attached ) {
				this.attach();
			}

			$el.show();
			return this.trigger( 'show' );
		}, 

		hide: function() {
			if ( ! this.views.attached || ! this.$el.is(':visible') )
				return this;

			this.$el.hide();
			return this.trigger( 'hide' );
		}, 

		isHidden: function() {
			return ! this.$el.is( ':visible' );
		}, 

		dispose: function() {
			this.stopListening();
			this.restoreEditorExpand();
			wp.media.View.prototype.dispose.apply( this, arguments );
		}
	});


	/**
	 * builder.view.Menu
	 */
	builder.view.Menu = wp.media.View.extend({
		className: 'youxi-builder-menu', 

		template: wp.template( 'youxi-builder-menu' ), 

		events: {
			'click .youxi-builder-menu-tabs a': 'tabclick'
		}, 

		initialize: function() {
			var tabs = _.map( builder.model.ShortcodeButton.getTabs() || [], function( tab ) {
				return new builder.view.MenuTab( _.extend( tab, {
					controller: this
				}));
			}, this );

			var items = _.map( builder.model.ShortcodeButton.getItems() || [], function( item ) {
				return new builder.view.MenuTabItem( _.extend( item, {
					controller: this
				}));
			}, this );

			this.views.add( '.youxi-builder-menu-tabs', tabs );
			this.views.add( '.youxi-builder-menu-items', items );
		}, 

		ready: function() {
			var firstTab = this.$( '.youxi-builder-menu-tab:first-child a[href^="#"]' );
			if( firstTab.length ) {
				this.changetab( firstTab );
			}
		}, 

		tabclick: function(e) {
			var tab = $( e.target ).closest( '.youxi-builder-menu-tab a[href^="#"]' );
			if( tab.length ) {
				this.changetab( tab );
			}
			e.preventDefault();
		}, 

		changetab: function( tab ) {
			tab.closest( '.youxi-builder-menu-tab' )
				.addClass( 'active' ).siblings().removeClass( 'active' );

			this.$( '.youxi-builder-menu-items' )
				.find( tab.attr( 'href' ) ).show().siblings().hide();

			this.trigger( 'changetab' );
		}
	});


	/**
	 * builder.view.MenuTab
	 */
	builder.view.MenuTab = wp.media.View.extend({

		tagName: 'li', 

		className: 'youxi-builder-menu-tab', 

		template: wp.template( 'youxi-builder-menu-tab' ), 

		initialize: function() {
			_.defaults( this.options, {
				href:  '#', 
				label: ''
			});
		}
	});


	/**
	* builder.view.MenuTabItem
	*/
	builder.view.MenuTabItem = wp.media.View.extend({

		className: 'youxi-builder-menu-tab-content', 

		template: wp.template( 'youxi-builder-menu-tab-content' ), 

		initialize: function() {
			this.views.set( '.youxi-builder-component-list', this.collection.map( function( model ) {
				return new builder.view.ShortcodeButton({
					model:      model, 
					controller: this.controller
				})
			}, this));
		}, 

		ready: function() {

			if( ! $.fn.draggable )
				return;

			_.each( this.views.get( '.youxi-builder-component-list' ), function( view ) {
				view.$el.draggable({
					scope: 'youxi-builder', 
					helper: 'clone', 
					zIndex: 99999, 
					stop: function() {
						/* Remove the dropped flag */
						$( this ).removeData( 'isDropped' );
					}
				});
			});
		}
	});


	/**
	* builder.view.ShortcodeButton
	*/
	builder.view.ShortcodeButton = wp.media.View.extend({

		tagName: 'li', 

		className: 'youxi-builder-component', 

		template: wp.template( 'youxi-builder-shortcode-button' ), 

		initialize: function() {
			_.defaults( this.options, {
				icon: this.model.get( 'icon' ), 
				label: this.model.get( 'label' )
			});
		}, 

		attributes: function() {
			return this.model.get( 'dataset' );
		}
	});

	/**
	 * builder.view.Panel
	 */
	builder.view.Panel = wp.media.View.extend({

		template: wp.template( 'youxi-builder-panel' ), 

		onMessages: {
			'copy:view': function( m ) {
				if( _.has( m.data, 'object' ) && ( m.source instanceof builder.view.Panel ) ) {
					this.populate( m.data.object, {
						at: m.source.$el.index() + 1, 
						compile: true
					});
				}
			}
		}, 

		passMessages: {
			'compile:shortcode': '.', 
			'sort:subview:start': '.', 
			'sort:subview:stop': '.'
		}, 

		initialize: function() {
			this.createController();
			this.createContents();
			this.constructUI();
			this.bindHandlers();

			Backbone.Courier.add( this );

			_.defaults( this.options, {
				type: this.controller.get( 'type' )
			});

			this.$el.data( 'backbone.view', this );
			this.trigger( 'initialized', this );
		}, 

		ready: function() {

			if( this.childContainer ) {

				// Make the view's workarea droppable
				if( $.fn.droppable ) {
					this.childContainer.$el.droppable( this.controller.droppableOptions() );
				}

				// Make the view's workarea sortable
				if( $.fn.sortable ) {
					this.childContainer.$el.sortable( this.controller.sortableOptions() );
				}

				// Disable selection on the view's workarea
				if( $.fn.disableSelection ) {
					this.childContainer.$el.disableSelection();
				}
			}
		}, 

		bindHandlers: $.noop, 

		createController: $.noop, 

		createContents: function() {
			this.childContainer = new wp.media.View({
				className:  'youxi-builder-panel-cw youxi-builder-' + this.controller.get( 'type' ) + '-cw', 
				controller: this
			});
			this.views.add( '.youxi-builder-panel-contents', this.childContainer );
		}, 

		getControlButtons: function() {
			var buttons = [];

			if( ! $.Youxi.Shortcode.getSetting( this.options.tag, 'instant' ) ) {
				buttons.unshift( new builder.view.ControlButton({
					controller: this, 
					action: 'edit', 
					icon: builder.settings.uiIcons.edit.icon, 
					title: builder.settings.uiIcons.edit.title
				}));
			}

			if( ! ( this instanceof builder.view.Column ) ) {
				buttons.push( new builder.view.ControlButton({
					controller: this, 
					action: 'copy', 
					icon: builder.settings.uiIcons.copy.icon, 
					title: builder.settings.uiIcons.copy.title
				}));
			}

			buttons.push( new builder.view.ControlButton({
				controller: this, 
				message: l10n.confirmRemoveContainer, 
				action: 'remove', 
				icon: builder.settings.uiIcons.remove.icon, 
				title: builder.settings.uiIcons.remove.title
			}));

			return buttons;
		}, 

		getControlViews: function() {
			var controls, buttons;

			buttons = this.getControlButtons();

			if( buttons.length ) {
				controls = new builder.view.Controls({
					controller: this
				});
				controls.add( buttons );
				return controls;
			}
		}, 

		getTitleView: function() {
			return new builder.view.PanelTitle({
				className: 'youxi-builder-panel-title youxi-builder-' + this.controller.get( 'type' ) + '-title', 
				title:     $.Youxi.Shortcode.getSetting( this.options.tag, 'label' )
			});
		}, 

		getUIElements: function() {
			var uiElements = [];

			this.title = this.getTitleView();
			this.controls = this.getControlViews();

			if( this.title instanceof builder.view.PanelTitle ) {
				uiElements.push( this.title );
			}

			if( this.controls instanceof builder.view.Controls ) {
				uiElements.push( this.controls );
			}

			return uiElements;
		}, 

		constructUI: function() {
			var uiElements = this.getUIElements();
			if( _.isArray( uiElements ) ) {
				this.views.add( '.youxi-builder-' + this.controller.get( 'type' ) + '-header', uiElements );
			}
		}, 

		handleDropped: function( options ) {
			if( this.childContainer ) {

				var y1 = this.childContainer.$el.offset().top, 
					y2 = options.coords.y;
				var x1 = this.childContainer.$el.offset().left, 
					x2 = options.coords.x;

				this.maybeAddView( options, {}, { x: x2 - x1, y: y2 - y1 } );
			}
		}, 

		getDropIndex: function( coords ) {

			if( $.isPlainObject( coords ) && _.has( coords, 'y' ) ) {

				var viewObjects = this.childContainer.views.all();
				var v1, v2, v1c, v2c, i, j;

				for( i = 0, j = viewObjects.length; i < j; i++ ) {
					v1 = viewObjects[ i ].$el;
					v2 = i + 1 < j ? viewObjects[ i + 1 ].$el : null;

					v1c = v1.position().top + ( v1.outerHeight() / 2 );
					v2c = ( v2 && v2.position().top + ( v2.outerHeight() / 2 ) );

					if( 0 == i && v1c > coords.y ) {
						// Dropped at the left
						return 0;
					}

					if( v2 && v1c < coords.y && v2c > coords.y ) {
						// Dropped between blocks
						return v2.index();
					}
				}
			}
		}, 

		maybeAddView: function( view, options, coords ) {
			view = view instanceof builder.view.Panel ? view : this.controller.toView( view );

			if( this.validateView( view ) ) {
				return this.addView( view, options, coords );
			} else if( view && view.remove ) {
				view.remove();
			}
		}, 

		validateView: function( view ) {
			return view instanceof builder.view.Panel;
		}, 

		addView: function( view, options, coords ) {
			if( this.childContainer ) {

				/* Make sure the view is not present in the workArea */
				if( ! _.contains( this.childContainer.views.all(), view ) ) {

					view.on( 'remove', this.confirmRemoveView, this );

					var dropIndex = this.getDropIndex( coords );

					if( ! _.isUndefined( dropIndex )  ) {
						_.extend( options, {
							at: dropIndex
						});
					}

					this.childContainer.views.add( view, options );
					this.trigger( 'add:view', view, options );

					return view;
				}
			}
		}, 

		removeView: function( view, options ) {
			if( this.childContainer ) {

				/* Make sure that the view is present in the workarea */
				if( _.contains( this.childContainer.views.all(), view ) ) {

					view.off( null, null, this );

					this.childContainer.views.unset( view, options );

					this.trigger( 'remove:view', view, options );
				}
			}
		}, 

		remove: function( options ) {
			if( options && options.silent ) {
				return wp.media.View.prototype.remove.apply( this, arguments );
			}

			this.$el.animate({ opacity: 'hide', height: 'hide' }, _.bind( function() {
				wp.media.View.prototype.remove.apply( this, arguments );
			}, this ));

			return this;
		}, 

		confirmRemoveView: function( e ) {
			if( _.isElement( e.currentTarget ) && e.view instanceof builder.view.Panel ) {

				if( confirm( $( e.currentTarget ).data( 'message' ) ) ) {
					this.removeView( e.view );
				}
			}
		}, 

		getChildViews: function( sort ) {
			if( this.childContainer ) {
				var childViews = this.childContainer.views.all();
				return sort ? _.sortBy( childViews, function( view ) { return view.$el.index() } ) : childViews;
			}
		}, 

		_getParentView: function() {
			return this.parent;
		}, 

		clear: function() {
			if( this.childContainer ) {
				_.invoke( this.childContainer.views.all(), 'remove', { silent: true } );
			}
		}, 

		populate: function( data, options ) {
			if( this.controller ) {
				this.controller.populate( data, options );
			}
		}, 

		dispose: function() {

			if( this.childContainer ) {

				if( $.fn.droppable && this.childContainer.$el.is( ':ui-droppable' ) ) {
					this.childContainer.$el.droppable( 'destroy' );
				}
				if( $.fn.sortable && this.childContainer.$el.is( ':ui-sortable' ) ) {
					this.childContainer.$el.sortable( 'destroy' );
				}
				if( $.fn.enableSelection ) {
					this.childContainer.$el.enableSelection();
				}
			}

			if( this.controller ) {

				if( this.controller.dispose ) {
					this.controller.dispose();
				}

				if( this.controller.off ) {
					this.controller.off( null, null, this );
				}
			}

			if( this.parent ) {
				delete this.parent;
			}

			wp.media.View.prototype.dispose.apply( this, arguments );
		}
	});

	/**
	 * builder.view.RowContainer
	 */
	builder.view.RowContainer = builder.view.Panel.extend({

		getRowSubviews: function() {
			return _.flatten( $.map( this.getChildViews() || [], function( view ) {
				if( view instanceof builder.view.RowContainer ) {
					return view.getRowSubviews();
				} else if( view instanceof builder.view.Row ) {
					return view;
				}
			}));
		}
	});

	/**
	 * builder.view.Root
	 */
	builder.view.Root = builder.view.RowContainer.extend({

		className: 'youxi-builder-panel youxi-builder-root', 

		template: null, 

		onMessages: _.extend({}, builder.view.RowContainer.prototype.onMessages, {
			'sort:subview:start': 'sortSubviewstart', 
			'sort:subview:stop': 'sortSubviewStop', 
			'compile:shortcode': function() {
				this.trigger( 'insert:tinymce', this.controller.toShortcode() );
			}
		}), 

		createController: function() {
			this.controller = new builder.controller.Base({
				view: this, 
				tag: 'root', 
				type: 'root'
			});
		}, 

		createContents: function() {
			this.childContainer = this;
		}, 

		getUIElements: $.noop, 

		_getParentView: $.noop, 

		getContainerSubviews: function() {
			return _.filter( this.getChildViews() || [], function( view ) {
				return view instanceof builder.view.Container;
			});
		}, 

		sortSubviewstart: function( m ) {

			var sourceView = m.data.originalSource, 
				target, checkCallback;

			if( sourceView instanceof builder.view.Row ) {
				target = 'Row';
				checkCallback = _.has( m.data, 'columnSize' ) ? function( view ) {
					return view.controller.get( 'slots' ) < m.data.columnSize;
				} : function() { return false; }
			} else if( sourceView instanceof builder.view.Container ) {
				target = 'Container';
				checkCallback = (function( view ) {
					var method = builder.controller.Column.isColumnContainer, 
						isColumnContainer = method( sourceView.controller.get( 'tag' ) );
					return function( view ) {
						return isColumnContainer != method( view.controller.get( 'tag' ) );
					}
				})();
			} else {
				return;
			}

			if( _.isFunction( this['get' + target + 'Subviews'] ) && _.isFunction( checkCallback ) ) {
				this['lastDisabled' + target] = $([]);
				_.each( _.without( this['get' + target + 'Subviews'](), sourceView ), function( view ) {
					if( view instanceof builder.view[target] && checkCallback( view ) ) {
						this['lastDisabled' + target] = this['lastDisabled' + target].add( view.childContainer.el );
					}
				}, this );
				this['lastDisabled' + target].sortable( 'option', 'disabled', true );
			}

		}, 

		sortSubviewStop: function( m ) {
			var sourceView = m.data.originalSource, target;
			if( sourceView instanceof builder.view.Row ) {
				target = 'lastDisabledRow';
			} else if( sourceView instanceof builder.view.Container ) {
				target = 'lastDisabledContainer';
			}

			if( target && _.has( this, target ) && this[target] instanceof jQuery ) {
				this[target].sortable( 'option', 'disabled', false );
				delete this[target];
			}
		}
	});

	/**
	 * builder.view.Container
	 */
	builder.view.Container = builder.view.RowContainer.extend({

		className: 'youxi-builder-panel youxi-builder-container', 

		createController: function() {
			this.controller = new builder.controller.Base({
				view: this, 
				tag:  this.options.tag, 
				type: 'container'
			});
		}
	});

	/**
	 * builder.view.Row
	 */
	builder.view.Row = builder.view.Panel.extend({

		className: 'youxi-builder-panel youxi-builder-row',  

		onMessages: _.extend( {}, builder.view.Panel.prototype.onMessages, {
			'request:slots!': function() {
				return this.controller.get( 'slots' );
			}
		}), 

		createController: function() {
			this.controller = new builder.controller.Row({
				view: this, 
				tag:  this.options.tag, 
				type: 'row'
			});
		}, 

		getDropIndex: function( coords ) {

			if( $.isPlainObject( coords ) && _.has( coords, 'x' ) ) {

				var viewObjects = this.childContainer.views.all();
				var v1, v2, v1c, v2c, i, j;

				for( i = 0, j = viewObjects.length; i < j; i++ ) {
					v1 = viewObjects[ i ].$el;
					v2 = i + 1 < j ? viewObjects[ i + 1 ].$el : null;

					v1c = v1.position().left + ( v1.width() / 2 );
					v2c = ( v2 && v2.position().left + ( v2.width() / 2 ) );

					if( 0 == i ) {
						v1c += parseInt( v1.css( 'paddingLeft' ) );
						if( v1c > coords.x ) {
							// Dropped at the left
							return 0;
						}
					}

					if( v2 && v1c < coords.x && v2c > coords.x ) {
						// Dropped between blocks
						return v2.index();
					}
				}
			}
		}, 

		validateView: function( view ) {
			if( ! builder.view.Panel.prototype.validateView.apply( this, arguments ) ) {
				return false;
			}
			if( ! view.controller || ! view.controller.get( 'size' ) ) {
				return false;
			}
			if( view.controller.get( 'size' ) > this.controller.get( 'slots' ) ) {
				return false;
			}

			return true;
		}
	});


	/**
	 * builder.view.Column
	 */
	builder.view.Column = builder.view.Panel.extend({

		className: 'youxi-builder-panel youxi-builder-column',  

		initialize: function() {
			var parent = this.options.parent, colObject;

			if( builder.settings.simpleColumns ) {
				colObject = builder.controller.Column.getColumnByTag( this.options.tag );
			} else if( parent instanceof builder.view.Panel && parent.controller instanceof Backbone.Model ) {
				colObject = builder.controller.Column.getBestFit( parent.controller.get( 'slots' ) );
			}

			_.defaults( this.options, _.pick( colObject || {}, 'size' ) );

			builder.view.Panel.prototype.initialize.apply( this, arguments );
		}, 

		createController: function() {
			this.controller = new builder.controller.Column(
				_.extend( {}, _.pick( this.options, 'tag', 'size' ), { view: this, type: 'column' } )
			);
		}, 

		getControlButtons: function() {
			var buttons = builder.view.Panel.prototype.getControlButtons.apply( this, arguments );

			Array.prototype.unshift.apply( buttons, 
				[new builder.view.ControlButton({
					controller: this, 
					action: 'decrease', 
					icon: builder.settings.uiIcons.resizeLeft.icon, 
					title: builder.settings.uiIcons.resizeLeft.title
				}), new builder.view.ControlButton({
					controller: this, 
					action: 'increase', 
					icon: builder.settings.uiIcons.resizeRight.icon, 
					title: builder.settings.uiIcons.resizeRight.title
				})]
			);

			return buttons;
		}, 

		getUIElements: function() {
			var uiElements = builder.view.Panel.prototype.getUIElements.apply( this, arguments );
			if( _.isArray( uiElements ) ) {
				return uiElements.reverse();
			}
		}, 

		refresh: function( size ) {
			this.title.set( builder.controller.Column.getTitle( size ) );
			this.$el.css( 'width', ( 100 * size / builder.settings.rowSize ) + '%' );
		}
	});


	/**
	 * builder.view.Widget
	 */
	builder.view.Widget = builder.view.Panel.extend({

		className: 'youxi-builder-panel youxi-builder-widget',  

		createController: function() {
			this.controller = new builder.controller.Base({
				view: this, 
				tag:  this.options.tag, 
				type: 'widget'
			});
		}, 

		createContents: $.noop
	});


	/**
	 * builder.view.PanelTitle
	 */
	builder.view.PanelTitle = wp.media.View.extend({
		className: 'youxi-builder-panel-title', 

		initialize: function() {
			_.defaults( this.options, {
				title: ''
			});

			this.model = new Backbone.Model({
				title: this.options.title
			});

			this.model.on( 'change:title', this.refresh, this );
			this.refresh();
		}, 

		set: function( title ) {
			this.model.set( 'title', title );
		}, 

		refresh: function() {
			this.$el.text( this.model.get( 'title' ) );
		}
	});


	/**
	 * builder.view.Controls
	 */
	builder.view.Controls = wp.media.View.extend({

		template: wp.template( 'youxi-builder-controls' ), 

		className: 'youxi-builder-controls', 

		add: function( controls ) {
			this.views.add( 'ul', controls );
			return this;
		}
	});


	/**
	 * builder.view.ControlButton
	 */
	builder.view.ControlButton = wp.media.View.extend({
		tagName: 'li', 

		template: wp.template( 'youxi-builder-panel-control-button' ), 

		events: {
			'click a': '_click'
		}, 

		initialize: function() {
			_.defaults( this.options, {
				action: '', 
				title: '', 
				message: '', 
				icon:   ''
			});
		}, 

		_click: function( event ) {
			event.preventDefault();
			event.view = this.controller;

			this.controller.trigger( this.options.action, event );
		}
	});

}) ( jQuery, window, document );