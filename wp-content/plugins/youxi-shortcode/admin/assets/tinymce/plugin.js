/**
 * Youxi Shortcode TinyMCE Plugin
 *
 * This script contains the TinyMCE plugin for inserting shortcodes
 *
 * @package   Youxi Shortcode
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013, Mairel Theafila
 */

;(function( $, window, document, undefined ) {

	"use strict";

	if( typeof tinymce == 'undefined' || ! $.Youxi || ! $.Youxi.Shortcode )
		return;

	tinymce.PluginManager.add( 'youxishortcode', function( editor ) {

		function generateShortcodeMenu() {

			var args = youxiShortcodeSettings.args,	
				categories = args.categories || [], 
				orphans = args.orphans || {}, 
				hasCategories = false, 
				menu = [];

			if( 'tiny' !== youxiShortcodeSettings.tinyMceMode ) {

				$.each( categories, function( id, category ) {

					var items = $.map( category.shortcodes || {}, function( args, tag ) {

						if( ( 'content' !== editor.id && ! args.inline ) || args.internal )
							return;

						return {
							'text': args.label, 
							'onclick': function() {
								editor.execCommand( getShortcodeCommandName( args ), false, {
									'shortcode': tag
								});
							}
						};
					});

					if( items.length ) {
						menu.push({
							'text': category.args.label || id, 
							'menu': items
						});
					}
				});
			}

			$.each( orphans, function( tag, args ) {

				if( args.internal )
					return;

				menu.push({
					'text': args.label, 
					'onclick': function() {
						editor.execCommand( getShortcodeCommandName( args ), false, {
							'shortcode': tag
						});
					}
				});
			});

			return menu;
		}

		function getShortcodeCommandName( args ) {
			return args.instant? 'youxiInsertShortcode' : 'youxiOpenShortcodeEditor';
		}

		editor.addCommand( 'youxiOpenShortcodeEditor', function( a, params ) {

			$.Youxi.Shortcode.Editor( params.shortcode, [], function( data ) {

				editor.execCommand( 'youxiInsertShortcode', false, {
					shortcode: params.shortcode, 
					data: data[ params.shortcode ] || {}
				});

			});
		});

		editor.addCommand( 'youxiInsertShortcode', function( a, params ) {

			var data = params.data || {};
			if( _.isEmpty( data ) && $.Youxi.Shortcode.getSetting( params.shortcode, 'instant' ) ) {
				data = { content: youxiShortcodeSettings.defaultContent };
			}

			var content = $.Youxi.Shortcode.construct( params.shortcode, data );

			if( ! $.Youxi.Shortcode.getSetting( params.shortcode, 'inline' ) 
				&& editor.getParam( 'wpautop', true ) && typeof switchEditors == 'object' ) {

				content = switchEditors.wpautop( content );
			}

			editor.execCommand( 'mceInsertContent', false, content );
		});

		editor.addButton( 'youxi_shortcode_menu', {
			'title': ( youxiShortcodeSettings.tinyMCEL10N && youxiShortcodeSettings.tinyMCEL10N.shortcodeButtonTitle ) || '', 
			'type': 'menubutton', 
			'icon': 'youxi-shortcode', 
			'menu': generateShortcodeMenu()
		});

	});

})( jQuery, window, document );
