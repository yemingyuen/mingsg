
;(function( $, window, document ) {

	"use strict";

	var _doc  = $( document ), 
		_win  = $( window ), 
		_body = $( document.body );

	var Helium = window.Helium = window.Helium || {};

	if( ! Helium.ajax || ! Helium.ajax.enabled || ! window.History ) {
		return;
	}

	$.extend( Helium, {

		teardown: function( context ) {

			context = $( context || document );

			/* ==========================================================================
				Royal Slider
			============================================================================= */

			if( $.fn.royalSlider ) {

				context.find( '.royalSlider' ).each(function() {
					var api = $( this ).data( 'royalSlider' );
					api && api.destroy();
				});

			}
			
			/* ==========================================================================
				Justified Grids
			============================================================================= */
			
			if( $.fn.justifiedGrids ) {
				context.find( '.justified-grids' ).justifiedGrids( 'destroy' );
			}
			
			/* ==========================================================================
				Isotope Galleries
			============================================================================= */

			if( $.fn.isotope ) {
				context.find( '.gallery' ).isotope( 'destroy' );
			}

			/* ==========================================================================
				Grid List
			============================================================================= */

			context.find( '.grid-list' ).each(function() {

				var api = $.data( this, 'helium.gridlist._inst' );
				if( api instanceof Helium.GridList ) {
					api.destroy();
				}

			});

			/* ==========================================================================
				MFP (clear cached instances)
			============================================================================= */

			if( $.magnificPopup ) {
				$.magnificPopup.instance.close();
				$.magnificPopup.instance.items = null;
				$.magnificPopup.instance.ev = null;
				$.magnificPopup.instance.st = null;
				$.magnificPopup.instance.contentContainer = null;
			}

			/* ==========================================================================
				Google Maps
			============================================================================= */

			if( $.fn.youxiGoogleMaps ) {
				context.find( '.google-maps' ).youxiGoogleMaps( 'destroy' );
			}

			/* ==========================================================================
				Contact Form 7
			============================================================================= */

			if( $.fn.ajaxFormUnbind ) {
				context.find( 'div.wpcf7 > form' )
					.ajaxFormUnbind().unbind( '.form-plugin' );
			}

			/* ==========================================================================
				MEJS
			============================================================================= */

			if( $.fn.mediaelementplayer ) {
				context.find( '.wp-audio-shortcode, .wp-video-shortcode' )
					.mediaelementplayer( false );
			}
		}, 

		Ajax: {

			init: function() {

				var rootUrl = History.getRootUrl(), 

					isLoading = false, 

					splitText = null, 

					ajaxPreloader = document.createElement( 'span' ), 

					contentAreaWrap = $( '.content-area-wrap' ), 

					appendLoadingIndicator = function() {

						// Make sure the ajax
						if( isLoading && ! ajaxPreloader.parentNode ) {

							// Replace content title with loading text
							contentAreaWrap.find( '.content-header .content-title' )
								.html( ajaxPreloader )
								.transition({
									opacity: 1, 
									duration: 350, 
									ease: 'easeOutQuad', 
									complete: playLoadingIndicator
								});
						}
					}, 

					playLoadingIndicator = function( backwards ) {

						if( isLoading ) {

							if( ! splitText ) {
								splitText = new SplitText( ajaxPreloader, { type: 'words,chars' } );
							}

							var chars = splitText.chars, 
								lastIndex = chars.length - 1, 
								target = ( backwards ? chars : Array.prototype.slice.call( chars ).reverse() );

							$.each( target, function( index ) {

								$( this ).transition({
									opacity: backwards ? 1 : 0, 
									x: backwards ? 0 : 10, 
									delay: ( index * 100 ), 
									duration: 400, 
									complete: ( index == lastIndex ? function() {
										playLoadingIndicator( ! backwards );
									} : $.noop )
								});

							});
						}
					}, 

					hideOldContent = function() {

						// Get current elements
						contentAreaWrap.find( '.content-area .content-wrap' )
							.add( contentAreaWrap.find( '.content-area .content-header-affix' ).children() )
							.transition({
								opacity: 0, 
								duration: 350, 
								ease: 'easeOutQuad', 
								complete: appendLoadingIndicator
							});
					}, 

					replaceOldContent = function( newContent ) {

						// Remove preloader from the DOM
						if( ajaxPreloader.parentNode ) {
							ajaxPreloader.parentNode.removeChild( ajaxPreloader );
						}

						// Reset ajax preloader
						if( splitText instanceof SplitText ) {
							$( splitText.chars ).css({ opacity: '', transition: '', transform: '' });
						}

						// Setup new content
						Helium.teardown( contentAreaWrap );
						Helium.setup( contentAreaWrap.html( newContent ) );

						// Force repaint
						contentAreaWrap[0].offsetHeight;

						// Animate
						contentAreaWrap.find( '.content-area' )
							.removeClass( 'beforeload' );

						// Stop loading
						isLoading = false;
					};

				// Assign ajax preloader text
				ajaxPreloader.innerHTML = Helium.ajax.loadingText || '';

				// Initialize Ajax Navigator
				Helium.AjaxNavigator.init({

					linkSelector: [
						'a.ajaxify', 
						'.header .brand a', 
						'.main-nav .menu a', 
						'.portfolio a.portfolio-info-link', 
						'.pages-nav a', 
						'.post-tags a', 
						'.content-nav a', 
						'.post-title a', 
						'.post-meta a', 
						'.post-media a', 
						'.post-body a.more-link', 
						'.related-entry-media a:not(.mfp-image)', 
						'.related-entry-title a', 
						'.related-entry-meta a', 
						'.edd-download-title a', 
						'a.edd-download-view-details', 
						'.grid-list a.grid-list-image-link.grid-list-page', 
						'.featured-portfolio-slider .entry-link', 
						'.search-entry-post-type a', 
						'.search-entry-title a'
					].join( ', ' ), 

					excludeUrls: Helium.ajax.excludeUrls, 

					bodyClassReplacer: function( className ) {

						if( _body.hasClass( 'nav-open' ) ) {
							className = [ className, 'nav-open' ].join( ' ' );
						}

						_body[0].className = className;
					}, 

					stateChange: function( url, callback ) {

						isLoading = true;

						if( _win.scrollTop() == 0 || ! Helium.ajax.scrollTop ) {
							hideOldContent();
						} else {
							var promise = $( 'html,body' ).finish()
								.animate( { scrollTop: 0 }, 500 ).promise();

							promise.done(function() {
								hideOldContent();
								callback();
							});
							
							return false;
						}
					}, 

					done: function( dom, url ) {

						var menu, 
							responseObj = $( dom ), 
							responseContentArea = responseObj.find( '.content-area' ), 
							responseMenuItems   = responseObj.find( '.main-nav .menu .menu-item' )

						// setTimeout(function() {

							// Match menu item classes with the response
							responseMenuItems.each(function() {
								if( this.id && ( menu = document.getElementById( this.id ) ) ) {
									if( $( menu ).hasClass( 'sub-menu-open' ) ) {
										$( this ).addClass( 'sub-menu-open' );
									}
									menu.className = this.className;
								}
							});

							// Hide the new content first
							responseContentArea.addClass( 'beforeload' );

							// Replace the old content with the new one
							replaceOldContent( responseContentArea[0].outerHTML );

							// Inform Google Analytics of the change
							if ( typeof window._gaq !== 'undefined' ) {
								window._gaq.push([ '_trackPageview', url.replace( rootUrl, '' ) ]);
							}

						// }, Math.random() * 2000 + 1200 );
					}
				});
			}
		}, 

		AjaxNavigator: {

			defaults: {
				stateChange: $.noop, 
				done: $.noop, 
				fail: $.noop, 
				always: $.noop, 

				titleReplacer: null, 
				bodyClassReplacer: null, 

				stateChangeContext: null, 
				doneContext: null, 
				failContext: null, 
				alwaysContext: null, 

				titleReplacerContext: null, 
				bodyClassReplacerContext: null, 

				ajaxParams: {}, 
				linkSelector: 'a:not(.no-ajaxify)', 
				excludeUrls: []
			}, 

			_hash: (function() {
				return 'tag-' + Math.random().toString( 36 ).substr( 2, 9 );
			}), 

			init: function( options ) {

				if( window.History && window.History.enabled ) {

					this.options = $.extend( true, {}, this.defaults, options );

					_doc.on( 'click.ajaxnavigator', this.options.linkSelector, $.proxy( this.onClick, this ) );

					_win.on( 'statechange.ajaxnavigator', $.proxy( this.onStateChange, this ) );
				}
			}, 

			onClick: function( event ) {
				
				var link = event.currentTarget, 
					rootUrl = History.getRootUrl();

				if( 
					// Check for excluded Urls
					( $.map( this.options.excludeUrls || [], function( url ) { if( link.href.substring( 0, url.length ) === url ) return url; }).length ) || 

					// Only handle clicks from internal links
					( link.href.substring( 0, rootUrl.length ) !== rootUrl && link.href.indexOf( ':' ) > -1 ) || 

					// Middle click, cmd click, and ctrl click should open
					// links in a new tab as normal.
					( event.which > 1 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey ) || 

					// Ignore cross origin links
					( location.protocol !== link.protocol || location.hostname !== link.hostname ) || 

					// Ignore anchors on the same page
					( link.hash && link.href.replace( link.hash, '' ) === location.href.replace( location.hash, '' ) ) || 

					// Ignore target="_blank";
					( link.target === '_blank' ) ||

					// Ignore empty anchor "foo.html#"
					( link.href === location.href + '#' ) || 

					// Ignore event with default prevented
					event.isDefaultPrevented()

				) {
					return;
				}

				History.pushState( null, null, link.href );

				event.preventDefault();
			}, 

			onStateChange: function( event ) {

				var state = History.getState(), 
					doRequestCallback = $.proxy( function() {
						this._doRequest.apply( this, [ state.url, this.options.ajaxParams ] );
					}, this );

				if( $.isFunction( this.options.stateChange ) ) {

					// Return false to do the request manually using the supplied callback as argument
					if( false === this.options.stateChange.apply( this.options.stateChangeContext || this, [ state.url, doRequestCallback ] ) ) {
						return;
					}
				}

				doRequestCallback();
			}, 

			_documentHtml: function( html, hash ) {

				// Prepare
				var result = String( html )
					.replace( /<(html|head|title|body)([\s\>])/gi, '<div data-' + hash + '="$1"$2' )
					.replace( /<\/(html|head|title|body)\>/gi, '</div>' )
				;

				// Return
				return $.trim( result );
			}, 

			_doRequest: function( url, params ) {

				$.ajax( url, $.extend( params, {
					context: this, 
					dataType: 'html', 
					type: 'GET'
				})).done( function( response ) {

					// Parse the returned HTML
					var _documentClean = $( document.createElement( 'div' ) ).append( $.parseHTML( response, true ) ), 
						_title         = _documentClean.find( 'title' ), 
						_body          = _documentClean.find( 'body' ), 
						_scripts       = _documentClean.find( 'script[src]' );

					// Since jQuery.parseHTML may remove <title>, <head>, or <body> we need to parse the manually
					var _hash     = this._hash(), 
						_document = $( this._documentHtml( response, _hash ) );

					// If <title> was stripped out by jQuery.parseHTML
					if( ! _title.length ) {
						_title = _document.find( '[data-' + _hash + '="title"]' );
					}

					// If <body> was stripped out by jQuery.parseHTML
					if( ! _body.length ) {
						_body = _document.find( '[data-' + _hash + '="body"]' );
					}

					// Replace document title
					if( _title.length ) {

						if( $.isFunction( this.options.titleReplacer ) ) {
							this.options.titleReplacer.apply( this.options.titleReplacerContext, [ _title.text() ] );
						} else {
							document.title = _title.text();
						}
					}

					// Replace body classes
					if( _body.length ) {

						if( $.isFunction( this.options.bodyClassReplacer ) ) {
							this.options.bodyClassReplacer.apply( this.options.bodyClassReplacerContext, [ _body[0].className ] );
						} else {
							document.body.className = _body[0].className;
						}
					}

					// Pass the jQuery parsed body contents to external callback for further processing
					if( $.isFunction( this.options.done ) ) {
						this.options.done.apply( this.options.doneContext || this, 
							[ $( '<div></div>' ).append( _documentClean ).html(), url ] );
					}
				})
				.fail( $.proxy( this.options.fail, this.options.failContext ) )
				.always( $.proxy( this.options.always, this.options.alwaysContext ) );

			}
		}
	});

	$(function() {
		Helium.Ajax.init();
	});

	/* EOF */

}) ( jQuery, window, document );