
;(function( $, window, document ) {

	"use strict";

	var _doc  = $( document ), 
		_win  = $( window ), 
		_body = $( document.body );

	// jQuery on an empty object, we are going to use this as our Queue
	var ajaxQueue = $({});

	$.ajaxQueue = function( ajaxOpts ) {
		var jqXHR,
		dfd = $.Deferred(),
		promise = dfd.promise();

		// run the actual query
		function doRequest( next ) {
			jqXHR = $.ajax( ajaxOpts );
			jqXHR.done( dfd.resolve )
				.fail( dfd.reject )
				.then( next, next );
		}

		// queue our ajax request
		ajaxQueue.queue( doRequest );

		// add the abort method
		promise.abort = function( statusText ) {

		// proxy abort to the jqXHR if it is active
		if ( jqXHR ) {
			return jqXHR.abort( statusText );
		}

		// if there wasn't already a jqXHR we need to remove from queue
		var queue = ajaxQueue.queue(),
		index = $.inArray( doRequest, queue );

		if ( index > -1 ) {
			queue.splice( index, 1 );
		}

		// and then reject the deferred
		dfd.rejectWith( ajaxOpts.context || ajaxOpts, [ promise, statusText, "" ] );
			return promise;
		};

		return promise;
	}

	window.Helium = window.Helium || {};

	var GridList = Helium.GridList = function( element, options ) {
		this.element = $( element );
		return this._init( options );
	}

	GridList.instances = 0;

	GridList.prototype = {

		_mql: window.matchMedia && window.matchMedia( '(min-width: 992px)' ), 

		_defaults: {
			itemSelector: '.grid', 
			itemsWrapperSelector: '.grid-list-wrap', 
			itemsFilterSelector: '.grid-list-filter', 
			paginationSelector: '.grid-list-nav', 

			layout: 'masonry', 

			pagination: false, 
			ajaxLoadingText: 'Loading', 
			ajaxButtonText: 'Load More', 
			ajaxButtonCompleteText: 'No More Items', 

			imageLoadedClass: 'loaded', 

			beforeAppend: $.noop, 
			afterAppend: $.noop
		}, 

		_init: function( options ) {

			this.options = $.extend( true, {}, this._defaults, options, this._extractOptions( this.element.data() ) );

			this.instanceId = this.element[0].id;

			this.eventNamespace = '.helium.gridlist._inst' + ( ++GridList.instances );

			this.itemsFilter  = this.element.find( this.options.itemsFilterSelector );
			this.itemsWrapper = this.element.find( this.options.itemsWrapperSelector );
			this.pagination   = this.element.find( this.options.paginationSelector );

			this._createLayoutEngine();
			this._createAjaxNav();
			this._toggleFilters();
			this._bindHandlers();
		}, 

		_afterFilter: function() {
			if( 'infinite' == this.options.pagination ) {
				Waypoint && Waypoint.refreshAll();
			}
		}, 

		_afterAppend: function( items ) {

			this._updateAjaxPager();
			this._bindImageLoad( items );
			this._toggleFilters();

			if( $.isFunction( this.options.afterAppend ) ) {
				this.options.afterAppend.apply( this.element, [ this, items ] );
			}
		}, 

		_beforeAppend: function( items ) {

			if( $.isFunction( this.options.beforeAppend ) ) {
				this.options.beforeAppend.apply( this.element, [ this, items ] );
			}
		}, 

		_bindHandlers: function() {

			var _this = this;

			this._bindImageLoad();

			this.itemsFilter.on( 'click' + this.eventNamespace, '.filter[data-filter]', function( e ) {

				if( ! $( this ).is( '.disabled' ) ) {

					_this.itemsFilter.find( '.filter.active' )
						.removeClass( 'active' );
					$( this ).addClass( 'active' );

					_this.filter( $( this ).data( 'filter' ) );

					if( _this._mql && ! _this._mql.matches ) {
						_this.itemsFilter.find( '.filter-items' ).slideUp();
					}
				}

				e.preventDefault();
			});

			if( this._mql ) {
				this._mqlListenerProxy = $.proxy( this._mqlListener, this );
				this._mql.addListener( this._mqlListenerProxy );
				this._mqlListener( this._mql );
			}

			if( this.ajaxPagerLink && this.ajaxPagerLink.length ) {

				if( 'infinite' == this.options.pagination ) {
					this._initializeInfinite();
					this.ajaxPagerLink.on( 'click' + this.eventNamespace, function(e) {
						e.preventDefault();
					});
				} else {
					this.ajaxPagerLink.on( 'click' + this.eventNamespace, function(e) {
						_this._query( this.href, 'ajax' );
						e.preventDefault();
					});
				}
			}
		}, 

		_bindImageLoad: function( items ) {

			var _this = this, 
				_opts = this.options;

			if( ! items || ! items.length ) {
				items = this.itemsWrapper.find( this.options.itemSelector );
			}
			items.find( 'img' ).each(function() {
				if( this.complete ) {
					$( this ).closest( _opts.itemSelector ).addClass( _opts.imageLoadedClass );
				} else {
					$( this ).one( 'load' + _this.eventNamespace, function() {
						$( this ).closest( _opts.itemSelector ).addClass( _opts.imageLoadedClass );
					});
				}
			});
		}, 

		_createLayoutEngine: function() {

			var _opts = this.options, 
				layoutEngine = _opts.layout.charAt(0).toUpperCase() + _opts.layout.slice(1), 
				layoutEngineOpts = {};

			if( LayoutEngine.hasOwnProperty( layoutEngine ) ) {

				layoutEngineOpts = $.extend( layoutEngineOpts, {
					selector: _opts.itemSelector, 
					afterFilter: this._afterFilter, 
					afterFilterScope: this
				});

				this.layoutEngine = new LayoutEngine[ layoutEngine ]( this, layoutEngineOpts );
			}

		}, 

		_createAjaxNav: function() {

			if( ! this.options.pagination || ! this.options.pagination.match( /^(ajax|infinite)$/ ) ) {
				return;
			}

			var selector = '.content-nav .page-numbers:not(.current):not(.next):not(.prev):not(.dots)', 
				navLinks = this.pagination.find( selector );

			this.ajaxLinks = navLinks.map(function() { return this.href; }).get();

			if( this.ajaxLinks.length ) {

				this.ajaxPagerLink = $( '<a/>' )
					.addClass( 'gridlist-' + this.options.pagination + '-link' )
					.text( this.options.ajaxButtonText )
					.attr( 'href', this.ajaxLinks.shift() );

				this.pagination.find( '.content-nav > ul' ).empty()
					.append( $( '<li class="content-nav-link"/>' ).append( this.ajaxPagerLink ) );
			}
		}, 

		_extractOptions: function( data ) {
			var options = {};
			$.each( data, function( key, value ) {
				if( /^gridlist(.+)/.test( key ) ) {
					key = key.match( /^gridlist(.+)/ )[1];
					key = key.charAt(0).toLowerCase() + key.substr( 1 );
					options[ key ] = value;
				}
			});

			return options;
		}, 

		_initializeInfinite: function() {

			var _this = this;

			if( Waypoint && this.ajaxPagerLink.length ) {

				this.activeWaypoint = new Waypoint({
					element: this.itemsWrapper[0], 
					offset: 'bottom-in-view', 
					handler: function( direction ) {
						if( 'down' === direction ) {
							_this._query( _this.ajaxPagerLink[0].href, 'infinite' );
						}
						this.destroy();
						_this.activeWaypoint = null;
					}
				});
			}
		}, 

		_mqlListener: function( mql ) {
			if( ! mql.matches ) {
				this.itemsFilter.on( 'click' + this.eventNamespace, '.filter-label', function( e ) {
					$( this ).next( '.filter-items' ).slideToggle();
					e.preventDefault();
				});
			} else {
				this.itemsFilter.off( 'click' + this.eventNamespace, '.filter-label' )
					.find( '.filter-items' ).css( 'display', '' );
			}
		}, 

		_toggleFilters: function() {
			var _this = this, items, filter;
			this.itemsFilter.find( '.filter[data-filter]' ).each(function() {
				filter = $( this ).data( 'filter' );
				items = _this.itemsWrapper.find( _this.options.itemSelector );
				$( this ).toggleClass( 'disabled', ! items.filter( filter ).length );
			});
		}, 

		_updateAjaxPager: function() {

			if( this.ajaxLinks && this.ajaxPagerLink ) {

				var next;
				if( next = this.ajaxLinks.shift() ) {
					this.ajaxPagerLink
						.attr( 'href', next )
						.text( this.options.ajaxButtonText )
						.closest( '.content-nav-link' ).removeClass( 'disabled' );

					if( 'infinite' === this.options.pagination ) {
						this._initializeInfinite();
					}
				} else {
					this.ajaxPagerLink.off( this.eventNamespace )
						.text( this.options.ajaxButtonCompleteText )
						.removeAttr( 'href' )
						.closest( '.content-nav-link' ).addClass( 'disabled' );
				}
			}
		}, 

		_query: function( url, mode ) {

			if( ! url ) {
				return;
			}

			$.ajaxQueue({
				type: 'GET', 
				dataType: 'html', 
				url: url, 
				context: this, 
				beforeSend: function() {
					$( this.ajaxPagerLink )
						.text( this.options.ajaxLoadingText )
						.closest( '.content-nav-link' ).addClass( 'disabled' );
				}
			}).done(function( response ) {
				var selector = [ this.options.itemsWrapperSelector, this.options.itemSelector ], items;
				if( this.instanceId ) {
					selector.unshift( '#' + this.instanceId );
				}
				items = $( response ).find( selector.join( ' ' ) );

				if( items.length ) {
					this.append( items );
				} else {
					$( this.ajaxPagerLink )
						.text( this.options.ajaxButtonText )
						.closest( '.content-nav-link' ).removeClass( 'disabled' );
				}
			});
		}, 

		append: function( items ) {

			if( this.layoutEngine ) {

				items = $( items ).filter( this.options.itemSelector );

				this._beforeAppend( items );

				this.itemsWrapper.append( items );
				this.layoutEngine.append( items );

				this._afterAppend( items );
			}
		}, 

		filter: function( filter ) {
			if( this.layoutEngine ) {
				this.layoutEngine.filter( filter );
			}
		}, 

		getItems: function() {
			return this.itemsWrapper.find( this.options.itemSelector );
		}, 

		destroy: function() {

			if( this.layoutEngine ) {
				this.layoutEngine.destroy();
				this.layoutEngine = null;
			}

			if( this.itemsFilter ) {
				this.itemsFilter.off( this.eventNamespace );
			}

			if( this.ajaxPagerLink ) {
				this.ajaxPagerLink.off( this.eventNamespace );
				this.ajaxPagerLink = null;
				this.ajaxLinks = null;
			}

			if( this.activeWaypoint ) {
				this.activeWaypoint.destroy();
				this.activeWaypoint = null;
			}

			this.itemsFilter = null;
			this.itemsWrapper = null;
			this.pagination = null;

			if( this._mql ) {
				if( $.isFunction( this._mqlListenerProxy ) ) {
					this._mql.removeListener( this._mqlListenerProxy );
				}
				this._mqlListenerProxy = null;
				this._mql = null;
			}

			$.removeData( this.element.get(0), 'helium.gridlist._inst' );

			this.element = null;
		}
	};

	$.fn.heliumGridList = function( options ) {
		return this.each(function() {
			if( ! $.data( this, 'helium.gridlist._inst' ) ) {
				$.data( this, 'helium.gridlist._inst', new GridList( this, options ) );
			}
		});
	}

	/* GridList LayoutEngines */

	var LayoutEngine = GridList.LayoutEngine = function( manager, options ) {
		this.manager = manager;
		return this._init( options );
	};

	// Inheritance method from Backbone.js
	LayoutEngine.extend = function( protoProps, staticProps ) {
		var parent = this;
		var child;

		// The constructor function for the new subclass is either defined by you
		// (the "constructor" property in your `extend` definition), or defaulted
		// by us to simply call the parent's constructor.
		if (protoProps && protoProps.hasOwnProperty( 'constructor' ) ) {
			child = protoProps.constructor;
		} else {
			child = function(){ return parent.apply(this, arguments); };
		}

		// Add static properties to the constructor function, if supplied.
		$.extend( child, parent, staticProps );

		// Set the prototype chain to inherit from `parent`, without calling
		// `parent`'s constructor function.
		var Surrogate = function(){ this.constructor = child; };
		Surrogate.prototype = parent.prototype;
		child.prototype = new Surrogate;

		// Add prototype properties (instance properties) to the subclass,
		// if supplied.
		if (protoProps) $.extend( child.prototype, protoProps );

		// Set a convenience property in case the parent's prototype is needed
		// later.
		child.__super__ = parent.prototype;

		return child;
	};

	LayoutEngine.prototype = {

		_defaults: {
			afterAppend: $.noop, 
			afterAppendScope: window, 
			afterFilter: $.noop, 
			afterFilterScope: window
		}, 

		_init: function( options ) {
			this.options = $.extend( this._defaults, options );
		}, 

		_afterFilter: function() {
			if( $.isFunction( this.options.afterFilter ) ) {
				this.options.afterFilter.apply( this.options.afterFilterScope, arguments );
			}
		}, 

		_afterAppend: function() {
			if( $.isFunction( this.options.afterAppend ) ) {
				this.options.afterAppend.apply( this.options.afterAppendScope, arguments );
			}
		}, 

		append: function( items ) {}, 

		filter: function( filter ) {}, 

		destroy: function() {
			this.manager = null;
		}
	};

	LayoutEngine.Classic = LayoutEngine.extend({

		append: function( items ) {

			if( items && items.length ) {

				var _this = this;

				items = _this._currentFilter ? 
					items.hide().filter( _this._currentFilter ).show() : items;

				if( _this._visibleItems ) {
					_this._visibleItems = _this._visibleItems.add( items );
				}

				if( GridList.useAnimation ) {

					items.css({ opacity: 0, y: -30 }).each(function( i ) {
						$( this ).transit({ opacity: 1, y: 0, delay: ( i * 100 ), queue: false }, 200, function() {
							if( items.last().is( this ) ) {
								_this._afterAppend();
							}
						});
					});

				} else {
					_this._afterAppend();
				}
			}
		}, 

		filter: function( filter ) {

			var _this = this
				, itemsWrapper = _this.manager.itemsWrapper
				, items = _this.manager.getItems()
				, hide = _this._visibleItems || items
				, show = items
				, i, len;
			
			if( 'string' == typeof filter && '*' != filter ) {
				_this._currentFilter = filter;
				_this._visibleItems = show = items.filter( filter );
			} else {
				_this._currentFilter = null;
				_this._visibleItems = null;
			}

			if( GridList.useAnimation ) {

				i = 0;
				len = hide.length;

				hide.transit({ y: -30, opacity: 0, queue: false }, 200, function() {

					if( ++i == len ) {

						hide.css( 'display', 'none' );
						show.css({ opacity: 0, y: -30, display: '' })

						show.each(function( j ) {

							$( this ).transit({ y: 0, opacity: 1, queue: false, delay: ( j * 100 ) }, 200, function() {
								if( show.last().is( this ) ) {
									_this._afterFilter();
								}
							});

						});
					}

				});

			} else {

				hide.css( 'display', 'none' );
				show.css( 'display', '' );

				_this._afterFilter();

			}
		}, 

		destroy: function() {
			this._currentFilter = null;
			this._visibleItems = null;
			LayoutEngine.prototype.destroy.apply( this, arguments );
		}
	});

	LayoutEngine.Masonry = LayoutEngine.extend({

		_init: function( options ) {
			LayoutEngine.prototype._init.apply( this, arguments );

			var itemsWrapper = this.manager.itemsWrapper, 
				beforeArrangeWidth;

			itemsWrapper.isotope( $.extend( true, {
				itemSelector: this.manager.options.itemSelector, 
				isInitLayout: false, 
				masonry: {
					columnWidth: '.grid-sizer'
				}
			}, this.options ) );

			beforeArrangeWidth = itemsWrapper.outerWidth();
			itemsWrapper.isotope( 'arrange' );

			if( beforeArrangeWidth != itemsWrapper.outerWidth() ) {
				itemsWrapper.isotope( 'arrange' );
			}
		}, 

		append: function( items ) {
			if( items && items.length ) {
				this.manager.itemsWrapper.isotope( 'once', 'layoutComplete', $.proxy( this._afterAppend, this ) );
				this.manager.itemsWrapper.isotope( 'appended', items );
			}
		}, 

		filter: function( filter ) {
			this.manager.itemsWrapper.isotope( 'once', 'layoutComplete', $.proxy( this._afterFilter, this ) );
			this.manager.itemsWrapper.isotope({ filter: filter });
		}, 

		destroy: function() {
			this.manager.itemsWrapper.isotope( 'destroy' );
			LayoutEngine.prototype.destroy.apply( this, arguments );
		}
	});

	LayoutEngine.Justified = LayoutEngine.extend({

		_currentFilter: null, 

		_init: function( options ) {
			LayoutEngine.prototype._init.apply( this, arguments );

			this.manager.itemsWrapper.justifiedGrids( $.extend( true, {
				selector: this.manager.options.itemSelector, 
				margin: 30, 
				ratio: 'img',
				assignHeight: false
			}, this.options ) );
		}, 

		append: function( items ) {

			if( items && items.length ) {

				var _this = this
					, itemsWrapper = _this.manager.itemsWrapper, itemsNeedLayout
					, lastRowItems = itemsWrapper.justifiedGrids( 'getLastRow' )
					, visibleItems = _this._currentFilter ? items.filter( _this._currentFilter ) : items
					, i, len;

				// Hide all items first
				items.hide();

				if( GridList.useAnimation && visibleItems.length && ( len = lastRowItems.length ) ) {

					i = 0;

					lastRowItems.transit({ opacity: 0, y: -30, queue: false }, 200, function() {

						if( ++i == len ) {

							itemsNeedLayout = itemsWrapper.justifiedGrids( 'append', items, true );
							itemsNeedLayout.css({ opacity: 0, y: -30 }).each(function( j ) {
								$( this ).transit({ opacity: 1, y: 0, delay: ( j * 100 ), queue: false }, 200, function() {
									if( itemsNeedLayout.last().is( this ) ) {
										_this._afterAppend();
									}
								});
							});
						}
					});

				} else {
					itemsWrapper.justifiedGrids( 'append', items, true );
					_this._afterAppend();
				}
			}
		}, 

		filter: function( filter ) {

			var _this = this
				, itemsWrapper = this.manager.itemsWrapper
				, hide, show
				, i, len;

			if( ! _this._currentFilter || _this._currentFilter != filter ) {

				_this._currentFilter = filter;				

				if( GridList.useAnimation ) {

					hide = itemsWrapper.justifiedGrids( 'getItems', true );
					show = itemsWrapper.justifiedGrids( 'getItems' ).filter( filter );

					i = 0;
					len = hide.length;

					hide.transit({ y: -30, opacity: 0, queue: false }, 200, function() {

						if( ++i == len ) {

							itemsWrapper.justifiedGrids( 'filter', filter );

							show.css({ opacity: 0, y: -30 }).each(function( j ) {

								$( this ).transit({ y: 0, opacity: 1, queue: false, delay: ( j * 100 ) }, 200, function() {
									if( show.last().is( this ) ) {
										_this._afterFilter();
									}
								});

							});

						}

					});
				} else {
					itemsWrapper.justifiedGrids( 'filter', filter );
					_this._afterFilter();
				}
			}
		}, 

		destroy: function() {
			this._currentFilter = null;
			this.manager.itemsWrapper.justifiedGrids( 'destroy' );
			LayoutEngine.prototype.destroy.apply( this, arguments );
		}
	});

	$(function() {
		GridList.useAnimation = !! ( $.fn.transit && $.support.transition );
	});

	/* EOF */

}) ( jQuery, window, document );
