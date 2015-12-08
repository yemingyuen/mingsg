
;(function( $, window, document ) {

	"use strict";

	var _doc  = $( document ), 
		_win  = $( window ), 
		_body = $( document.body );

	var Helium = window.Helium = window.Helium || {};

	$.extend( Helium, window._helium || {}, {

		isHandheld: !! ( bowser.mobile || bowser.tablet ), 

		mql: window.matchMedia && window.matchMedia( '(min-width: 992px)' ), 

		init: function() {
			
			/* ==========================================================================
				Add Mobile Device class
			============================================================================= */

			if( ! Helium.isHandheld ) {
				$( 'html' ).addClass( 'desktop' );
			}

			/* ==========================================================================
				Setup Listeners
			============================================================================= */

			Helium.setupListeners();

			/* ==========================================================================
				Wait for Document.Ready
			============================================================================= */

			$( Helium.ready );

		}, 

		ready: function() {

			/* ==========================================================================
				Apply any patches/fixes
			============================================================================= */

			Helium.applyPatches();

			/* ==========================================================================
				Contextual Setups
			============================================================================= */

			Helium.setup( document, true );

			/* ==========================================================================
				Fire initial window resize callback
			============================================================================= */

			Helium.onResize();
		}, 

		onResize: function() {

			/* ==========================================================================
				Fullscreen Content Area
			============================================================================= */

			Helium.adjustFullscreenContent();

			/* ==========================================================================
				Restore Menu
			============================================================================= */

			$( '.main-nav' ).find( 'ul' ).css( 'display', '' )
				.end().find( '.sub-menu-open' ).removeClass( 'sub-menu-open' );
		}, 

		onMqlChange: function( mql ) {

			// Unbind event handlers
			_doc.off( '.hoverIntent .helium.nav' );

			if( mql.matches ) {

				/* ==========================================================================
					Desktop Hover Intent
				============================================================================= */

				if( $.fn.hoverIntent ) {

					_doc.hoverIntent({
						over: function() {
							$( this ).children( 'ul.sub-menu' ).finish().slideDown()
								.closest( '.menu-item' ).addClass( 'sub-menu-open' );
						}, 
						out: $.noop, 
						selector: '.main-nav .menu-item-has-children'
					});
				}

				/* ==========================================================================
					Close Submenus
				============================================================================= */

				_doc.on( 'click.helium.nav', '.menu-item.sub-menu-open .subnav-close', function(e) {

					$( this ).closest( '.menu-item' ).removeClass( 'sub-menu-open' );
					$( this ).siblings( 'ul.sub-menu' )
						.finish().slideUp(function() {
							$( this ).find( 'ul.sub-menu' ).hide();
							$( this ).find( '.sub-menu-open' )
								.addBack().removeClass( 'sub-menu-open' );
						});

				});

			} else {

				/* ==========================================================================
					Mobile Navigation Toggle
				============================================================================= */

				_doc.on( 'click.helium.nav', '.header-toggle', function(e) {

					_body.toggleClass( 'header-open' );
					e.preventDefault();

				}).on( 'click.helium.nav', '.main-nav .menu-item a', function() {
					_body.removeClass( 'header-open' );
				});

			}
		}, 

		setupListeners: function() {

			/* ==========================================================================
				Window.Resize
			============================================================================= */

			_win.on( 'resize.helium orientationchange.helium', Helium.onResize );

			/* ==========================================================================
				Easy Digital Downloads Cart Events
			============================================================================= */

			_body.on( 'edd_cart_item_added', function(e, response) {
				var qty = $( '<div></div>' ).append( response.cart_item ).find( '.edd-cart-item' ).data( 'cart-quantity' );
				$( '.header-links .edd-shopping-cart .header-links-tooltip' ).text( qty );
			});

			/* ==========================================================================
				Back to Top
			============================================================================= */

			_doc.on( 'click.helium.btt', '.back-to-top > .btn', function( e ) {
				$( 'html,body' ).finish().animate({ scrollTop: 0 }, 500 );
				e.preventDefault();
			});

			/* ==========================================================================
				Search Form Modal
			============================================================================= */

			(function() {

				var openSearchForm = function() {
					if( $( '.search-wrap .search-form' ).length ) {
						$( '.search-wrap .search-form' )[0].reset();
					}
					_body.addClass( 'search-open' );
					_doc.on( 'keyup.helium.search', function( e ) {
						if( e.keyCode == 27 ) {
							closeSearchForm();
						}
					});
				}, 
				closeSearchForm = function() {
					_body.removeClass( 'search-open' );
					_doc.off( 'keyup.helium.search' );
				};

				_doc.on( 'click.helium.search', '.header-links .ajax-search-link a', function(e) {
					openSearchForm();
					e.preventDefault();
				});

				if( Helium.ajax && Helium.ajax.enabled ) {

					_doc.on( 'submit', '.search-open .search-wrap .search-form', function(e) {
						var url = Helium.homeUrl + '?s=' + $( '.form-control', this ).val();
						closeSearchForm();
						History && History.pushState( null, null, url );
						e.preventDefault();
					});
				}

			})();

			/* ==========================================================================
				MediaQueryList Listener
			============================================================================= */

			if( Helium.mql ) {
				Helium.mql.addListener( Helium.onMqlChange );
				Helium.onMqlChange( Helium.mql );
			}
		}, 

		adjustFullscreenContent: function( context ) {

			context = $( context || document );

			context.find( '.content-area.fullscreen .content-wrap' ).each(function() {
				$( this ).css({
					height: _win.height() - ( Helium.mql.matches ? 0 : $( this ).offset().top  )
				});
			});
		}, 

		setup: function( context, isInit ) {

			context = $( context || document );

			/* ==========================================================================
				Fullscreen Content Area
			============================================================================= */

			Helium.adjustFullscreenContent( context );

			/* ==========================================================================
				Add Bootstrap classes to WordPress elements
			============================================================================= */

			context.find( '.form-submit #submit' ).addClass( 'btn btn-primary' );

			/* ==========================================================================
				Royal Slider
			============================================================================= */

			if( $.fn.royalSlider ) {

				context.find( '.royalSlider' ).each(function() {

					$( this ).royalSlider( $.extend( true, {}, $( this ).data( 'rs-settings' ), {
						slidesSpacing: 0, 
						imageScalePadding: 0, 
						keyboardNavEnabled: true, 
						addActiveClass: true
					}));

				});

			}
			
			/* ==========================================================================
				Isotope Galleries
			============================================================================= */

			if( $.fn.isotope ) {

				context.find( '.gallery' ).each(function() {

					var gallery = $( this );
					gallery.imagesLoaded(function() {
						gallery.isotope();
					});

				});

			}

			/* ==========================================================================
				Justified Grids
			============================================================================= */
			
			if( $.fn.justifiedGrids ) {
				context.find( '.justified-grids' ).justifiedGrids();
			}

			/* ==========================================================================
				GridLists
			============================================================================= */

			if( $.fn.heliumGridList ) {

				context.find( '.grid-list' ).heliumGridList({

					afterAppend: function( instance, items ) {
						if( $( this ).is( '.edd-download-grid' ) ) {
							$( '.edd-no-js', items ).hide();
							$( 'a.edd-add-to-cart', items ).addClass( 'edd-has-js' );
						}
					}

				});

			}

			/* ==========================================================================
				MFP Galleries
			============================================================================= */

			if( $.fn.magnificPopup ) {

				$.each({
					'.gallery': '.gallery-item a', 
					'.grid-list-wrap': '.grid:visible .grid-list-image-link.grid-list-mfp', 
					'.related-items': '.related-item-media a.mfp-image', 
					'a.mfp-image': false
				}, function( selector, delegate ) {
					context.find( selector ).each(function() {
						$( this ).magnificPopup({
							delegate: delegate, 
							type: 'image', 
							gallery: delegate ? {
								enabled: true, 
								navigateByImgClick: true
							} : false
						});
					});
				});
				
			}

			/* ==========================================================================
				Team Popup
			============================================================================= */
			
			if( $.fn.magnificPopup ) {

				// var source = [
				// 	'{{#photo}}', 
				// 	'<figure class="team-photo">', 
				// 		'<img src="{{photo}}" alt="{{name}}">', 
				// 	'</figure>', 
				// 	'{{/photo}}', 
				// 	'<div class="team-info">', 
				// 		'<div class="team-header">', 
				// 			'<h3 class="team-name">{{name}}</h3>', 
				// 			'{{#role}}<p class="team-role">{{role}}</p>{{/role}}', 
				// 		'</div>', 
				// 		'{{#content}}', 
				// 		'<div class="team-description">', 
				// 			'{{content}}', 
				// 		'</div>', 
				// 		'{{/content}}', 
				// 		'{{#has_social}}', 
				// 		'<div class="team-social">', 
				// 			'<ul class="inline-list">', 
				// 				'{{#social_profiles}}', 
				// 				'<li><a href="{{url}}"><i class="{{icon}}"></i></a></li>', 
				// 				'{{/social_profiles}}', 
				// 			'</ul>', 
				// 		'</div>', 
				// 		'{{/has_social}}', 
				// 	'</div>'
				// ].join('');

				// console.log( Hogan.compile(source, {asString: true } ) );

				Helium.TeamTemplate = Helium.TeamTemplate || new Hogan.Template(function(c,p,i){var _=this;_.b(i=i||"");if(_.s(_.f("photo",c,p,1),c,p,0,10,82,"{{ }}")){_.rs(c,p,function(c,p,_){_.b("<figure class=\"team-photo\"><img src=\"");_.b(_.v(_.f("photo",c,p,0)));_.b("\" alt=\"");_.b(_.v(_.f("name",c,p,0)));_.b("\"></figure>");});c.pop();}_.b("<div class=\"team-info\"><div class=\"team-header\"><h3 class=\"team-name\">");_.b(_.v(_.f("name",c,p,0)));_.b("</h3>");if(_.s(_.f("role",c,p,1),c,p,0,184,217,"{{ }}")){_.rs(c,p,function(c,p,_){_.b("<p class=\"team-role\">");_.b(_.v(_.f("role",c,p,0)));_.b("</p>");});c.pop();}_.b("</div>");if(_.s(_.f("content",c,p,1),c,p,0,244,291,"{{ }}")){_.rs(c,p,function(c,p,_){_.b("<div class=\"team-description\">");_.b(_.v(_.f("content",c,p,0)));_.b("</div>");});c.pop();}if(_.s(_.f("has_social",c,p,1),c,p,0,318,453,"{{ }}")){_.rs(c,p,function(c,p,_){_.b("<div class=\"team-social\"><ul class=\"inline-list\">");if(_.s(_.f("social_profiles",c,p,1),c,p,0,367,422,"{{ }}")){_.rs(c,p,function(c,p,_){_.b("<li><a href=\"");_.b(_.v(_.f("url",c,p,0)));_.b("\"><i class=\"");_.b(_.v(_.f("icon",c,p,0)));_.b("\"></i></a></li>");});c.pop();}_.b("</ul></div>");});c.pop();}_.b("</div>");return _.fl();;});

				context.find( '.team .team-photo a' ).magnificPopup({
					gallery: {
						enabled: true
					}, 
					inline: {
						markup: '<div class="team-popup"></div>'
					}, 
					callbacks: {
						elementParse: function( item ) {
							if( item.el ) {
								var data = $( item.el ).closest( '.team' ).data( 'team-data' );
								if( data ) {
									item = $.extend( true, item, { data: data });

									delete item.src;
									delete item.el;
								}
							}
							return item;
						}, 
						markupParse: function( template, values, item ) {
							template.empty().html( Helium.TeamTemplate.render( values ) );
						}
					}
				});
			}

			/* ==========================================================================
				FitVids
			============================================================================= */

			if( $.fn.fitVids ) {
				context.find( '.featured-content, .post-media' ).fitVids();
			}

			/* ==========================================================================
				Google Maps
			============================================================================= */

			if( $.fn.youxiGoogleMaps ) {
				context.find( '.google-maps' ).youxiGoogleMaps();
			}

			/* ==========================================================================
				AddThis
			============================================================================= */

			if( typeof addthis !== 'undefined' ) {
				addthis.toolbox( '.addthis_toolbox' );
			}

			if( ! isInit ) {

				/* ==========================================================================
					Easy Digital Downloads
				============================================================================= */

				if( Helium.EDD ) {
					
					if( ! Helium.EDD.ajaxDisabled ) {
						context.find( '.edd-no-js' ).hide();
						context.find( 'a.edd-add-to-cart' ).addClass( 'edd-has-js' );
					}

					if( window.edd_scripts ) {
						var isCheckout = ( Helium.EDD.checkoutPage == window.location.href );
						window.edd_scripts.redirect_to_checkout = Helium.EDD.straightToCheckout || isCheckout ? '1' : '0';
					}
				}

				/* ==========================================================================
					Contact Form 7
				============================================================================= */

				if( $.fn.wpcf7InitForm ) {
					context.find( 'div.wpcf7 > form' ).wpcf7InitForm();
				}

				/* ==========================================================================
					MEJS
				============================================================================= */

				if( $.fn.mediaelementplayer ) {

					(function() {

						var settings = {};

						if ( typeof _wpmejsSettings !== 'undefined' ) {
							settings = _wpmejsSettings;
						}

						settings.success = function (mejs) {
							var autoplay, loop;

							if ( 'flash' === mejs.pluginType ) {
								autoplay = mejs.attributes.autoplay && 'false' !== mejs.attributes.autoplay;
								loop = mejs.attributes.loop && 'false' !== mejs.attributes.loop;

								autoplay && mejs.addEventListener( 'canplay', function () {
									mejs.play();
								}, false );

								loop && mejs.addEventListener( 'ended', function () {
									mejs.play();
								}, false );
							}
						};

						context.find( '.wp-audio-shortcode, .wp-video-shortcode' ).mediaelementplayer( settings );
					})();
				}
			}
		}, 

		applyPatches: function() {

			/* ==========================================================================
				Media Element JS Patch for Fluid Video Players
			============================================================================= */

			if( $.fn.mediaelementplayer ) {

				!function( oldFn ) {

					$.fn.mediaelementplayer = function( options ) {
						if( false !== options ) {

							this.filter( '.wp-video-shortcode' )
								.css({ width: '100%', height: '100%' });

							options = $.extend( options, {
								audioHeight: 36
							});
						}

						return oldFn.apply( this, [ options ] );
					};

				} ( $.fn.mediaelementplayer );
			}
		}
	});

	Helium.init();

	/* EOF */

}) ( jQuery, window, document );