/**
 * Youxi Uploader Form Field JS
 *
 * This script contains the initialization code for the image uploader form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	if( 'undefined' == typeof wp || ! wp.media || ! $.widget || ! $.ui.sortable )
		return;

	var media = wp.media, 
		YouxiMediaFrame = media.view.MediaFrame.Select.extend({

			createStates: function() {
				media.view.MediaFrame.Select.prototype.createStates.apply( this, arguments );

				if( this.options.embed ) {
					this.states.add( new media.controller.Embed({ metadata: {} }) );
				}
			}, 

			bindHandlers: function() {
				media.view.MediaFrame.Select.prototype.bindHandlers.apply( this, arguments );

				this.on( 'toolbar:create:main-embed', this.mainEmbedToolbar, this );
				this.on( 'content:render:embed', this.embedContent, this );
			}, 

			createSelectToolbar: function( toolbar, options ) {
				options = _.extend( {}, options || this.options.button || {}, {
					controller: this
				});

				toolbar.view = new media.view.Toolbar.Select( options );
			}, 

			embedContent: function() {
				var view = new media.view.Embed({
					controller: this,
					model:      this.state()
				}).render();

				this.content.set( view );
				view.url.focus();
			}, 

			mainEmbedToolbar: function( toolbar ) {
				toolbar.view = new media.view.Toolbar.Embed({
					controller: this
				});
			}
		});

	$.widget( 'youxi.mediauploader', {
		options: {
			fieldName: '', 
			multiple: false, 
			returnType: 'id', 
			returnUrlSize: 'full', 
			title: '', 
			buttonText: ''
		}, 

		_mediaFrame: null, 

		_mediaTemplate: media.template( 'youxi-media-field' ), 

		_init: function() {
			this._refreshButton();
		}, 

		_create: function() {

			// Create the media frame
			this._createMediaFrame();

			// Bind button click event to open the media frame
			this._on({
				'click .youxi-media-button': function( e ) {
					this.open();
					e.stopPropagation();
				}, 
				'click .youxi-media-preview-remove': function( e ) {
					this.removeItem( e.target );
					e.stopPropagation();
				}
			});

			// Initialize sortable if this uploader is multiple
			if( this.options.multiple ) {
				$( '.youxi-media-previews', this.element ).sortable({
					items: '> .youxi-media-preview-item', 
					tolerance: 'pointer'
				});
			}
		}, 

		_createMediaFrame: function() {

			// Make sure the media frame doesn't exist
			if( this._mediaFrame )
				return;

			var frameClass = media.view.MediaFrame.Select;

			if( 'url' == this.options.returnType ) {
				frameClass = YouxiMediaFrame;
			}

			// Create the media frame object
			this._mediaFrame = new frameClass({
				title: this.options.title, 
				library: {
					type: 'image'
				}, 
				button: {
					text: this.options.buttonText
				}, 
				multiple: this.options.multiple
			});

			// Bind media frame select event
			this._mediaFrame.on( 'select', this._handleSelection, this );

			// Bind embed select event
			this._mediaFrame.state( 'embed' ).on( 'select', this._handleEmbed, this );
		}, 

		_handleSelection: function() {
			var selection = this._mediaFrame.state().get( 'selection' );

			if( ! selection ) {
				return;
			}
			
			selection.map( function( attachment ) {
				var data = {
					id: attachment.id, 
					url: this._getAttachmentSize( attachment, 'thumbnail' ).url, 
					fieldName: this.options.fieldName, 
					fieldNamePostfix: this.options.multiple ? '[]' : ''
				};

				if( 'url' == this.options.returnType ) {
					data.id = this._getAttachmentSize( attachment, this.options.returnUrlSize ).url;
				}

				$( '.youxi-media-previews', this.element )
					.append( this._mediaTemplate( data ) );
			}, this );

			this._refreshButton();
		}, 

		_handleEmbed: function() {
			var embed = this._mediaFrame.state().props.toJSON(), 
				data = {
					id: embed.url || '', 
					url: embed.url || '', 
					fieldName: this.options.fieldName, 
					fieldNamePostfix: this.options.multiple ? '[]' : ''
				};

			$( '.youxi-media-previews', this.element )
				.append( this._mediaTemplate( data ) );

			this._refreshButton();
		}, 

		_getAttachmentSize: function( attachment, size ) {
			var sizes = attachment.attributes.sizes || {};

			if( _.has( sizes, size ) ) {
				return _.pick( sizes[ size ], 'url' );
			}
			return { url: attachment.url };
		}, 

		_destroy: function() {
			this._mediaFrame && this._mediaFrame.dispose();
			this._super();
		}, 

		_refreshButton: function() {
			var hasItems = $( '.youxi-media-preview-item', this.element ).length >= 1;
			if( ! this.options.multiple ) {
				$( '.youxi-media-button', this.element ).toggle( ! hasItems );
			}
			$( '.youxi-media-no-item', this.element ).prop( 'disabled', hasItems );
		}, 

		removeItem: function( target ) {
			$( target ).parents( '.youxi-media-preview-item' ).remove();
			this._refreshButton();
		}, 

		open: function() {
			if( ! this._mediaFrame ) {
				this._createMediaFrame();
			}

			this._mediaFrame.open();
		}
	});

	$.widget( 'youxi.fileuploader', {
		options: {
			fieldName: '', 
			title: '', 
			buttonText: '', 
			alwaysReturnUrl: false, 
			enableEmbed: true, 
			libraryType: ''
		}, 

		_mediaFrame: null, 

		_init: function() {
			this._refreshButtons();
		}, 

		_create: function() {

			// Make sure the media frame doesn't exist
			if( ! this._mediaFrame ) {

				// Create the media frame object
				this._mediaFrame = new YouxiMediaFrame( _.extend({
					title: this.options.title, 
					button: {
						text: this.options.buttonText
					}, 
					embed: this.options.enableEmbed, 
					multiple: false
				}, this.options.libraryType ? {
					library: {
						type: this.options.libraryType
					}
				} : {} ));

				// Bind media frame select event
				this._mediaFrame.on( 'select', this._handleSelection, this );

				// Bind embed select event
				this._mediaFrame.state( 'embed' ).on( 'select', this._handleEmbed, this );
			}

			// Bind button click event to open the media frame
			this._on({
				'click .youxi-uploader-button': function( e ) {
					this.open();
					e.stopPropagation();
				}, 
				'click .youxi-uploader-feedback': function( e ) {
					this.open();
					e.stopPropagation();
				}, 
				'click .youxi-uploader-remove': function( e ) {
					this.removeItem();
					e.stopPropagation();
				}
			});
		}, 

		_handleSelection: function() {
			var selection = this._mediaFrame.state().get( 'selection' );

			if( ! selection ) {
				return;
			}
			
			selection.map( function( attachment ) {
				if( ! attachment || ! attachment.attributes ) {
					return;
				}
				this._feedback( attachment.attributes.url, 
					attachment.attributes[ this.options.alwaysReturnUrl ? 'url' : 'id' ] );
			}, this );

			this._refreshButtons();
		}, 

		_handleEmbed: function() {
			var embed = this._mediaFrame.state().props.toJSON();
			this._feedback( embed.url || '', embed.url || '' );
			this._refreshButtons();
		}, 

		_refreshButtons: function() {
			var fieldValue = this.element.find( '.youxi-uploader-value' ).val();
			this.element.find( '.youxi-uploader-button' ).toggle(  ! fieldValue );
			this.element.find( '.youxi-uploader-remove' ).toggle( !! fieldValue );
		}, 

		_destroy: function() {
			this._mediaFrame && this._mediaFrame.dispose();
			this._super();
		}, 

		_feedback: function( url, value ) {

			this.element.find( '.youxi-uploader-feedback' )
				.val( url );

			this.element.find( '.youxi-uploader-value' )
				.val( value );
		}, 

		removeItem: function() {
			this.element.find( '.youxi-uploader-feedback, .youxi-uploader-value' ).val( '' );
			this._refreshButtons();
		}, 

		open: function() {
			if( ! this._mediaFrame ) {
				this._createMediaFrame();
			}

			this._mediaFrame.open();
		}
	});

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'media_uploader', function( context ) {

			if( $.fn.mediauploader ) {
				$( '.youxi-media-uploader', context ).each( function() {
					$( this ).mediauploader( $( this ).data() );
				});
			}

		}, function( context ) {
			if( $.fn.mediauploader ) {
				$( '.youxi-media-uploader:youxi-mediauploader', context ).each( function() {
					$( this ).mediauploader( 'destroy' );
				});
			}
		});

		$.Youxi.Form.Manager.addCallbacks( 'file_uploader', function( context ) {

			if( $.fn.fileuploader ) {
				$( '.youxi-file-uploader', context ).each( function() {
					$( this ).fileuploader( $( this ).data() );
				});
			}

		}, function( context ) {
			if( $.fn.fileuploader ) {
				$( '.youxi-file-uploader:youxi-fileuploader', context ).each( function() {
					$( this ).fileuploader( 'destroy' );
				});
			}
		});
	}
	
})( jQuery, window, document );