/**
 * Youxi Post Format Metabox Manager
 *
 * This script manages the post format metaboxes
 *
 * @package   Youxi Post Format
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @version   1.0
 * @copyright Copyright (c) 2013, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	$( document ).ready(function() {

		/* Get togglers from 'Screen Options' panel */
		var togglers = $( $.map( YouxiPostFormatsConfig.metaboxes, function( id, name ) {
			return $( '.hide-postbox-tog' ).filter( '#' + id + '-hide' )[0];
		}));

		/* Enforce saving of hidden state by unchecking togglers */
		togglers.prop( 'checked', false ).triggerHandler( 'click' );

		/* Remove the togglers */
		togglers.parent( 'label' ).remove();

		/* Get current post format */
		var currentPostFormat = $( '#post-formats-select input[name="post_format"]:checked' ).val();

		/* Get metaboxes */
		var metaboxes = $( $.map( YouxiPostFormatsConfig.metaboxes, function( id, name ) {
			var metabox;
			if( metabox = document.getElementById( id ) ) {
				/* Toggle metabox based on current post format */
				return $( metabox ).toggle( currentPostFormat == name )[0];
			}
		}));

		/* Post format select change event */
		$( document ).on( 'change', '#post-formats-select input[name="post_format"]', function() {
			metaboxes.hide()
				.filter( '#' + YouxiPostFormatsConfig.metaboxes[ $( this ).val() ] )
				.show();
		});

	});
	
})( jQuery, window, document );