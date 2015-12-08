/**
 * Youxi Shortcode Editor Plugin
 *
 * This script contains the modal shortcode editor script
 *
 * @package   Youxi Shortcode
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @copyright Copyright (c) 2013, Mairel Theafila
 */

"use strict";

jQuery.Youxi = jQuery.Youxi || {};

;(function( $, window, document, undefined ) {

	if( ! wp || ! wp.media )
		return;

	$.extend( true, $.Youxi, {
		
		Shortcode: {

			Editor: function( tag, data, callback ) {

				var editor = $.Youxi.Shortcode.EditorManager.getEditor(), 
					once = _.once(function() {
						editor.off( 'submit', once );
						callback.apply( this, arguments );
					});

				editor.on( 'submit', once );

				editor.open();
				editor.load( tag, data );
			}, 

			EditorModal: wp.media.view.Modal.extend({

				className: 'youxi-modal-wrap', 
				
				template: wp.template( 'youxi-modal' ), 

				events: function() {
					return _.extend( wp.media.view.Modal.prototype.events, {
						'click [data-dismiss="modal"]': 'escapeHandler'
					});
				}, 

				content: function( content ) {
					if( content instanceof Backbone.View ) {
						content.on( 'loading', this.contentLoading, this );
						content.on( 'loaded', this.contentLoaded, this );
					}
					this.views.set( '.youxi-modal', content );
					return this;
				}, 

				propagate: function( id ) {
					var result = wp.media.view.Modal.prototype.propagate.apply( this, arguments );

					var e = $.Event( id + '.youxi.modal' );
					this.$el.trigger( e );

					return result;
				}, 

				contentLoading: function() {
					this.$el.addClass( 'youxi-modal-loading' );
				}, 

				contentLoaded: function() {
					this.$el.removeClass( 'youxi-modal-loading' );
				}
			}), 

			EditorForm: wp.media.View.extend({
				tagName: 'form', 

				className: 'youxi-modal-dialog', 

				template: wp.template( 'youxi-shortcode-editor' ), 

				events: {
					'submit': 'submit'
				}, 

				initialize: function() {
					wp.media.View.prototype.initialize.apply( this, arguments );

					// Initialize modal container view.
					this.modal = new $.Youxi.Shortcode.EditorModal({
						controller: this, 
						title:      this.options.title
					});
					this.modal.content( this );

					// Construct the form model
					this.model = new Backbone.Model({
						title: '', 
						form: '', 
						open: false
					});

					this.closeButton = new Backbone.View({
						tagName: 'button', 
						className: 'close', 
						attributes: {
							'type': 'button', 
							'data-dismiss': 'modal', 
							'aria-hidden': 'true'
						}
					});
					this.closeButton.$el.html( '&times;' );

					// Construct the title
					this.title = new Backbone.View({
						tagName: 'h2', 
						className: 'youxi-modal-title'
					});

					// Construct the form
					this.form = new Backbone.View({
						className: 'youxi-shortcode-editor-form'
					});

					// Construct the editor buttons
					this.buttons = [
						new wp.media.view.Button({
							text: 'Save', 
							style: 'primary', 
							size: 'large', 
							tagName: 'button', 
							attributes: {
								type: 'submit'
							}
						}), 
						new wp.media.view.Button({
							text: 'Reset Fields', 
							size: 'large', 
							tagName: 'button', 
							attributes: {
								type: 'reset'
							}
						})
					];

					this.setupViews();
					this.bindHandlers();
				}, 

				setupViews: function() {
					this.views.add( '.youxi-modal-header', [ this.closeButton, this.title ] );
					this.views.add( '.youxi-modal-body', this.form );
					this.views.add( '.youxi-modal-footer', this.buttons );
				}, 

				bindHandlers: function() {
					this.model.on( 'change:form', this.afterLoad, this );
					this.on( 'open', this.afterOpen, this );
					this.on( 'close', this.afterClose, this );
				}, 

				load: function( tag, data, options ) {
					var t = this;
					
					t.trigger( 'loading' );
					wp.ajax.post( 'get_shortcode_editor', {
						shortcode: tag, 
						shortcodeData: data
					}).done( function( data ) {
						t.trigger( 'loaded' );
						t.model.set({
							title: data.title, 
							form:  data.html
						});
					});
				}, 

				unload: function( options ) {
					if( $.Youxi.Form && $.Youxi.Form.Manager ) {
						$.Youxi.Form.Manager.destroy( this.form.$el );
					}

					this.title.$el.empty();
					this.form.$el.empty();

					if( options && options.close ) {
						this.model.set( 'title', '', { silent: true } );
						this.model.set( 'form',  '', { silent: true } );
					}
				}, 

				submit: function( e ) {

					e.preventDefault();
					
					if( $.fn.serializeJSON ) {
						
						/* Handle tinymce textareas */
						if( typeof tinymce !== 'undefined' ) {
							_.each( this.form.$( 'textarea' ), function( textarea ) {
								var id = textarea.id, 
									ed = tinymce.get( id );

								if( ed && ed instanceof tinymce.Editor ) {
									ed.save();
								}
							}, this );
						}

						this.trigger( 'submit', this.$el.serializeJSON() );
						this.close();
					}
				}, 

				afterLoad: function( model ) {
					this.title.$el.html( model.get( 'title' ) );
					this.form.$el.html( model.get( 'form' ) );

					if( $.Youxi.Form && $.Youxi.Form.Manager ) {
						$.Youxi.Form.Manager.initialize( this.form.$el );
					}
				}, 

				afterOpen: function() {
					if( this.model ) {
						this.model.set( 'open', true );
					}
				}, 

				afterClose: function() {
					this.unload({ close: true });
					this.off( 'submit' );

					if( this.model ) {
						this.model.set( 'open', false );
					}
				}
			})
		}
	});

	$.Youxi.Shortcode.EditorManager = {
		editorPool: [], 

		init: function() {
			var t = this;
			$( document ).on( 'close.youxi.modal', '.youxi-modal-wrap', function() {
				if( ! t.getActiveEditors().length ) {
					$( 'body' ).removeClass( 'youxi-modal-open' );
				}
			}).on( 'open.youxi.modal', '.youxi-modal-wrap', function() {
				$( 'body' ).addClass( 'youxi-modal-open' );
			});
		}, 

		getInactiveEditors: function() {
			return $.map( this.editorPool, function( editor ) {
				if( ! editor.model.get( 'open' ) )
					return editor;
			});
		}, 

		getActiveEditors: function() {
			return $.map( this.editorPool, function( editor ) {
				if( editor.model.get( 'open' ) )
					return editor;
			});
		}, 

		getEditor: function() {
			var editor = this.getInactiveEditors().shift();

			if( ! editor ) {
				editor = new $.Youxi.Shortcode.EditorForm();
				this.editorPool.push( editor );
			}

			return editor;
		}
	};

	$( document ).ready(function() {
		$.Youxi.Shortcode.EditorManager.init();
	});

	// Map some of the modal's methods to the frame.
	_.each(['open','close','attach','detach','escape'], function( method ) {
		$.Youxi.Shortcode.EditorForm.prototype[ method ] = function( view ) {
			if ( this.modal )
				this.modal[ method ].apply( this.modal, arguments );
			return this;
		};
	});

}) ( window.jQuery, window, document );