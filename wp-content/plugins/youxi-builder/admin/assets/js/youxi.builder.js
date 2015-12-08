/**
 * Youxi Builder JS
 *
 * This script contains the page builder base code
 *
 * @package   Youxi Builder
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */

;(function( $, window, document, undefined ) {

	"use strict";

	$.Youxi.Builder.Manager = {

		instances: {}, 

		add: function( editorId ) {

			var instance = this.instances[ editorId ];
			if( ! instance ) {
				instance = $.Youxi.Builder({ editorId: editorId });
			}
			return ( this.instances[ editorId ] = instance );
		}, 

		get: function( editorId ) {
			return this.instances[ editorId ];
		}, 

		init: function() {

			/* Don't initialize multiple times, and bail if tinymce is undefined */
			if( this.initialized || ! window.switchEditors || typeof tinymce == 'undefined' )
				return;

			this.initialized = true;

			/* Move the builder toggle button */
			$( '.wp-switch-editor.switch-youxi-builder' ).each(function() {
				var wpEditorTabs = $( this ).closest( '.wp-editor-tools' ).find( '.wp-editor-tabs' );
				$( this ).removeAttr( 'style' ).appendTo( wpEditorTabs );
			});
		}, 

		show: function( id ) {

			var builderInstance = this.get( id );
			if( ! this.initialized || ( builderInstance && ! builderInstance.isHidden() ) ) {
				return;
			}

			/* Change to TinyMCE mode first, it's easier */
			window.switchEditors.go( id, 'tmce' );

			/* Hide TinyMCE */
			var editor = tinymce.get( id );
			if( editor && ! editor.isHidden() ) {
				editor.hide();
			}

			/* Show the page builder */			
			if( ! builderInstance ) {
				builderInstance = this.add( id );
			}
			builderInstance.show();

			$( '#wp-' + id + '-wrap' )
				.removeClass( 'tmce-active html-active' )
				.addClass( 'ypbl-active' );
		}, 

		hide: function( id, mode ) {

			var builderInstance = this.get( id );
			if( ! this.initialized || ! builderInstance || builderInstance.isHidden() )
				return;

			builderInstance.hide();
			$( '#wp-' + id + '-wrap' ).removeClass( 'ypbl-active' );

			/* Work around when we need to go to html mode */
			window.switchEditors.go( id, 'html' == mode ? 'tmce' : 'html' );
		}
	};

	/* Init builder on document ready */
	$( function() {
		$.Youxi.Builder.Manager.init();
	});

	/* Hack the switchEditors event handler */
	$( document ).on( 'click', function( event ) {
		
		var id, mode, 
			target = $( event.target );

		if( target.is( '.wp-switch-editor' ) ) {

			id = target.data( 'wp-editor-id' );

			if( target.is( '.switch-youxi-builder' ) ) {

				if( $.Youxi.Builder.Manager.initialized ) {

					$.Youxi.Builder.Manager.show( id );
					event.stopImmediatePropagation();
				}
			} else {

				mode = target.hasClass( 'switch-tmce' ) ? 'tmce' : 'html';
				$.Youxi.Builder.Manager.hide( id, mode );
			}
		}
	});

}) ( jQuery, window, document );