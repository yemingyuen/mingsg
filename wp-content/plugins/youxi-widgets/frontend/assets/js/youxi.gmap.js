/**
 * Youxi Google Maps Initialization Plugin
 *
 * This script contains the initialization code for Google Maps
 *
 * @package   Youxi Widgets
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	/* Make sure youxi google maps doesn't exists */
	if( ! $.fn.youxiGoogleMaps ) {

		/* Create the plugin */
		$.fn.youxiGoogleMaps = function( options ) {

			/* Make sure gmap3 is present */
			if( ! $.fn.gmap3 || ! this.length ) {
				return this;
			}

			if( 'string' === typeof options && 'destroy' === options ) {
				return $( this ).gmap3( options ).remove();
			}

			/*!
			 * JavaScript - loadGoogleMaps( version, apiKey, language )
			 *
			 * - Load Google Maps API using jQuery Deferred. 
			 *   Useful if you want to only load the Google Maps API on-demand.
			 * - Requires jQuery 1.5
			 * 
			 * Copyright (c) 2011 Glenn Baker
			 * Dual licensed under the MIT and GPL licenses.
			 */
			var now = $.now(), promise;

			(function( version, apiKey, language, sensor ) {

				if( promise ) {
					return promise;
				}

				//Create a Deferred Object
				var deferred = $.Deferred(),

					//Declare a resolve function, pass google.maps for the done functions
					resolve = function() {
						deferred.resolve( window.google && window.google.maps ? window.google.maps : false );
					}, 

					//global callback name
					callbackName = "loadGoogleMaps_" + (now++), 

					// Default Parameters
					params = $.extend(
						{ "sensor": sensor || "false" }, 
						apiKey ? { "key": apiKey } : {}, 
						language ? { "language": language } : {}
					);

				// If google.maps exists, then Google Maps API was probably loaded with the <script> tag
				if( window.google && window.google.maps ) {

					resolve();

				//If the google.load method exists, lets load the Google Maps API in Async.
				} else if( window.google && window.google.load ) {

					window.google.load( "maps", version || 3, { "other_params": $.param( params ), "callback": resolve } );

				//Last, try pure jQuery Ajax technique to load the Google Maps API in Async.
				} else {

					//Ajax URL params
					params = $.extend( params, {
						'v': version || 3, 
						'callback': callbackName
					});

					//Declare the global callback
					window[ callbackName ] = function () {
						resolve();

						//Delete callback
						setTimeout(function() {
							try {
								delete window[ callbackName ];
							} catch (e) {}
						}, 20);
					};

					//Can't use the jXHR promise because 'script' doesn't support 'callback=?'
					$.ajax({
						dataType: 'script', 
						data: params, 
						url: '//maps.googleapis.com/maps/api/js'
					});
				}

				promise = deferred.promise();

				return promise;

			})().done( $.proxy( function() {
				
				this.each(function() {
					var gmapOpts = {}, 
						gmapData = $( this ).data(), 
						key, 
						markers = [];

					for( key in gmapData ) {
						switch( key ) {
							case 'center':
								gmapOpts[ key ] = gmapData[ key ].split( ',' );
								break;
							case 'mapTypeId':
								gmapOpts[ key ] = window.google.maps.MapTypeId[ gmapData[ key ] ];
								break;
							case 'markers':
								markers = $.map( gmapData[ key ] || [], function( obj ) {
									var opts = {};
									if( obj.title ) {
										opts.title = obj.title;
									}
									if( obj.icon ) {
										opts.icon = obj.icon;
									}
									return {
										latLng: [ obj.lat, obj.lng ], 
										options: opts
									}
								});
								break;
							case 'monochrome':
								if( gmapData[ key ] ) {
									gmapOpts['styles'] = [ { stylers: [ { saturation: -100 } ] } ];
								}
							default:
								gmapOpts[ key ] = gmapData[ key ];
								break;
						}
					}

					$( this ).gmap3( $.extend( true, options, {
						map: {
							options: gmapOpts
						}, 
						marker: {
							values: markers
						}
					}));
				});
			}, this ));

			return this;
		}

		/* Initialize the maps on window.load event */
		$( window ).load(function() {
			$( '[data-widget="gmap"]' ).youxiGoogleMaps();
		});
	}

}) ( jQuery, window, document );