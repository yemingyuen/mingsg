
;(function( $, window, document, undefined ) {

	"use strict";

	$.Youxi = $.Youxi || {};

	var setupMethods = {

		'flickr': function( context, config ) {

			if( $.fn.jflickrfeed ) {

				context.find( '.flickr-feed' ).each(function() {

					var flickrId = $( this ).data( 'flickr-id' );
					var limit    = $( this ).data( 'limit' ) || 9;
					
					$( '<ul class="feed-list"/>' )
						.jflickrfeed( $.extend( true, config || {}, { qstrings: { id: flickrId }, limit: limit }))
						.prependTo( this );

				});

			}

		}, 

		'twitter': function( context, config ) {

			if( $.fn.miniTweets ) {

				context.find( '.twitter-feed' ).each(function() {
					
					$( '<ul class="feed-list"/>' )
						.miniTweets( $.extend( true, config || {}, $( this ).data() ) )
						.prependTo( this );

				});

			}

		}, 

		'google-maps': function( context, config ) {

			if( $.fn.youxiGoogleMaps ) {

				context.find( '.google-maps' ).each(function() {
					$( this ).youxiGoogleMaps( $.extend( true, config || {}, $( this ).data() ) );
				});

			}

		}, 

		'instagram': function( context, config ) {

			context.find( '.instagram-feed' ).each(function() {

				var $widget = $( this ), 
					options = $.extend({
						username: '', 
						count: 8, 
						imageSize: 'thumbnail'
					}, $widget.data() );

				$.ajax({
					type: 'post', 
					url: _youxiWidgets.ajaxUrl, 
					dataType: 'json', 
					data: {
						action: config.ajaxAction, 
						instagram: options
					}
				}).done(function( response ) {

					if( response.success && $.isArray( response.data ) ) {

						$( '<ul class="feed-list"/>' )

							.html( $.map( response.data, function( data ) {

								if( data.images && data.images.hasOwnProperty( options.imageSize ) ) {

									var href  = data.link || '#';
									var title = data.caption && data.caption.text || '';
									var image = data.images.hasOwnProperty( options.imageSize ) ? data.images[ options.imageSize ] : data.images.thumbnail;

									if( image.url && image.width && image.height ) {

										/* Make sure image urls are in the same URL scheme */
										var src = image.url.replace( /^https?:\/\//, '//' );
										var w   = image.width;
										var h   = image.height;

										return '<li><a href="' + href + '" title="' + title + '" target="_blank"><img width="' + w + '" height="' + h + '" src="' + src + '" alt="' + title + '"></a></li>';
									}

								}

							})).prependTo( $widget );

					} else {
						$widget.html( '<div class="alert alert-danger">' + ( response.data && response.data.error_message || '' ) + '</div>' );
					}
					
				});

			});

		},

		'rotatingquotes': function( context, config ) {

			var animateQuote = function( quote ) {
				var nextQuote = quote.fadeOut( 500 ).next().fadeIn( 500 );
				this.append( quote )
					.animate({ 'height': nextQuote.outerHeight( true ) }, 500 );

				return nextQuote;
			}

			context.find( '.rotating-quotes' ).each(function() {

				var $widget = $( this ), 
					duration = $widget.data( 'duration' ) || 6000, 
					$quotes = $widget.children( 'blockquote' ), 
					$firstQuote = $quotes.first();

				// Start by only showing the first quote
				$firstQuote.nextAll().hide();

				// Schedule slideshow
				setTimeout(function() {

					$widget.css({
						'position': 'relative', 
						'height': $widget.outerHeight()
					});
					$quotes.css( 'position', 'absolute' );
					$firstQuote = animateQuote.call( $widget, $firstQuote );
					
					// Start the real slideshow and store setInterval ID
					$widget.data( 'rotatingquotes', setInterval(function() {
						$firstQuote = animateQuote.call( $widget, $firstQuote );
					}, duration ));

				}, duration );

			});

		}

	};

	var teardownMethods = {

		'google-maps': function( context, config ) {

			if( $.fn.youxiGoogleMaps ) {

				context.find( '.google-maps' ).youxiGoogleMaps( 'destroy' );

			}

		},

		'rotatingquotes': function( context, config ) {

			context.find( '.rotating-quotes' ).each(function() {

				var intervalId = $( this ).data( 'rotatingquotes' );
				if( intervalId ) clearInterval( intervalId );

			});

		}

	};

	$.extend( $.Youxi, {

		Widgets: {

			setup: function( context ) {

				context = $( context || document );

				$.each( setupMethods, function( id_base, fn ) {

					if( _youxiWidgets.hasOwnProperty( id_base ) ) {
						$.isFunction( fn ) && fn( context, _youxiWidgets[ id_base ] );
					}

				});

			}, 

			teardown: function( context ) {

				context = $( context || document );

				$.each( teardownMethods, function( id_base, fn ) {

					if( _youxiWidgets.hasOwnProperty( id_base ) ) {
						$.isFunction( fn ) && fn( context, _youxiWidgets[ id_base ] );
					}

				});

			}

		}

	});

	$(function() {
		$.Youxi.Widgets.setup();
	});

}) ( jQuery, window, document );