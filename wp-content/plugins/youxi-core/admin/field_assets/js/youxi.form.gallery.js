/**
 * Youxi Gallery Form Field JS
 *
 * This script contains the initialization code for the gallery form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	if( 'undefined' == typeof wp || ! wp.media || ! wp.media.gallery || ! $.widget )
		return;

	var media = wp.media, 
		gallery = wp.media.gallery;

	$.widget( 'youxi.galleryform', {

		options: {
			fieldName: ''
		}, 

		_mediaTemplate: media.template( 'youxi-gallery-preview' ), 

		_create: function() {

			/* Bind event handlers */
			this._on({
				'click': function( e ) {
					this.open();
					e.preventDefault();
				}
			});

			/* Extract attachment ids upon creation */
			this.ids = $( '.youxi-gallery-preview-item', this.element ).map(function() {
				return $( this ).find( 'input.youxi-gallery-value' ).val();
			}).get();

			if( ! this.ids.length )  {

				/* Prefetch attachment to prevent uploader to get stuck */
				gallery.attachments( wp.shortcode.next( 'gallery', '[gallery]' ).shortcode ).more();
			}
		}, 

		_handleUpdate: function( attachments ) {

			/* Generate gallery preview from selected attachments */
			var galleryItems = attachments.map(function( attachment ) {
				return this._mediaTemplate({
					id: attachment.id, 
					url: this._getAttachmentSize( attachment, 'thumbnail' ).url, 
					fieldName: this.options.fieldName
				});
			}, this ).join( '' );

			/* Extract attachment ids */
			this.ids = attachments.pluck( 'id' );

			/* Append all generated gallery previews */
			$( '.youxi-gallery-previews', this.element )
				.html( galleryItems );
		}, 

		_getAttachmentSize: function( attachment, size ) {
			var sizes = attachment.attributes.sizes || {};

			if( _.has( sizes, size ) ) {
				return _.pick( sizes[ size ], 'url' );
			}
			return { url: attachment.url };
		}, 

		open: function() {

			var frame = gallery.edit( '[gallery' + ( this.ids ? ' ids="' + this.ids.join( ',' ) + '"' : '' ) + ']' );

			frame.state( 'gallery-edit' ).on( 'update', this._handleUpdate, this );
			frame.on( 'close', function() { frame.detach(); });
		}
	});

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'gallery_form', function( context ) {

			if( $.fn.galleryform ) {
				$( '.youxi-gallery-form', context ).each( function() {
					$( this ).galleryform( $( this ).data() );
				});
			}

		}, function( context ) {
			if( $.fn.galleryform ) {
				$( '.youxi-gallery-form:youxi-galleryform', context ).each( function() {
					$( this ).galleryform( 'destroy' );
				});
			}
		});
	}
	
})( jQuery, window, document );