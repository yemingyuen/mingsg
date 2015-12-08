/**
 * Youxi Shortcode JS
 *
 * This script contains the core utilities for editing shortcodes
 *
 * @package   Youxi Shortcode
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013, Mairel Theafila
 */

 "use strict";

 jQuery.Youxi = jQuery.Youxi || {};

;(function( $, window, document, undefined ) {

	$.extend( true, $.Youxi, {

		Shortcode: {

			getSetting: _.memoize( function( tag, key ) {
				var args       = youxiShortcodeSettings.args || {};
				var categories = args.categories || [];
				var orphans    = args.orphans || {};
				var setting;

				_.each( categories, function( category ) {
					if( _.isUndefined( setting ) 
						&& _.has( category, 'shortcodes' ) 
						&& _.has( category.shortcodes, tag ) 
						&& _.has( category.shortcodes[ tag ], key ) ) {

					 	setting = category.shortcodes[ tag ][ key ];
					}
				});

				if( _.isUndefined( setting ) && _.has( orphans, tag ) && _.has( orphans[ tag ], key ) ) {
					setting = orphans[ tag ][ key ];
				}

				return setting;

			}, function() {
				return '$.Youxi.Shortcode.getSetting(' + _.toArray( arguments ).join( ', ' ) + ')';
			}), 

			prefix: _.memoize( function( tag ) {
				
				var prefix = youxiShortcodeSettings.prefix;

				if( prefix && 'string' == $.type( prefix ) ) {
					if( tag && 'string' == $.type( tag ) ) {
						if( ! this.getSetting( tag, 'third_party' ) ) {
							return prefix + this.unprefix( tag );
						}
						return tag;
					}
					return prefix;
				}

				return 'string' == $.type( tag ) ? tag : '';
			}), 

			unprefix: _.memoize( function( tag ) {
				return tag.replace( new RegExp( "^" + this.prefix() ), '' );
			}), 

			construct: function( tag, data ) {

				tag = this.prefix( tag );

				var key, atts = [], content, result = '', 
					defaults = this.getSetting( tag, 'defaults' ) || {}, 
					delim = this.getSetting( tag, 'inline' ) ? '' : '\n\n', 
					nl = this.getSetting( tag, 'insert_nl' ) ? '\n\n' : '';

				if( _.isArray( data ) ) {

					result = _.map( data, function( data ) {
						return this.construct( tag, data );
					}, this ).join( delim );

				} else if( $.isPlainObject( data ) ) {

					/* Clone data to prevent changing the original data */
					data = $.extend( true, {}, data );

					if( _.has( data, 'content' ) ) {
						content = data.content;
						delete data.content;
					}

					atts = $.map( data, _.bind( function( value, key ) {
						value = this.serialize( tag, key, value );
						if( key && value ) {
							if( ! _.has( defaults, key ) || _.isNull( defaults[ key ] ) || value != defaults[ key ] ) {
								return key + '="' + value + '"';
							}
						}
					}, this ) );

					if( ! _.isUndefined( content ) ) {

						if( ! _.isString( content ) ) {
							content = this.serialize( tag, 'content', content );
						}
						content = ( '' === content ? content : [ nl, $.trim( content ), nl ].join( '' ) );
					}

					/* [tag atts (...)] */
					result = '[' + Array.prototype.concat( [ tag ], atts ).join( ' ' ) + ']';

					if( _.isString( content ) ) {
						/* [tag atts (...)]content */
						result += content;

						/* [tag atts (...)]content[/tag] */
						result += '[/' + tag + ']';
					}
				}

				return result;
			}, 

			serialize: function( tag, key, data ) {
				tag = this.prefix( tag );
				if( $.isPlainObject( this.serializers ) && _.has( this.serializers, tag ) ) {
					tag = this.serializers[ tag ];
					if( _.has( tag, key ) && _.isFunction( tag[ key ] ) ) {
						data = tag[ key ].call( this, data );
					}
				}

				return data;
			}, 

			deserialize: function( tag, key, data ) {
				tag = this.prefix( tag );
				if( $.isPlainObject( this.deserializers ) && _.has( this.deserializers, tag ) ) {
					tag = this.deserializers[ tag ];
					if( _.has( tag, key ) && _.isFunction( tag[ key ] ) ) {
						data = tag[ key ].call( this, data );
					}
				}

				return data;
			}, 

			deserializeArray: function( data ) {
				return _.map( _.isArray( data ) ? data : [], function( shortcode ) {
					if( $.isPlainObject( shortcode ) && _.has( shortcode, 'tag' ) ) {
						var k, v = _.extend( {}, shortcode.atts || {}, { 'content': shortcode.content || '' } );
						for( k in v ) {
							v[ k ] = this.deserialize( shortcode.tag, k, v[ k ] );
						}
						return v;
					}
				}, this );
			}
		}
	});

}) ( jQuery, window, document );