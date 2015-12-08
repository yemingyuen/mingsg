
;(function( $, window, document, undefined ) {

	"use strict";

	$( document ).ready(function() {

		/* Get current page template */
		var currentPageTemplate = $( '#page_template' ).val()
			.replace( /\.php$/, '' ).replace( /\W/g, '_' );

		/* Get all metaboxes with the page_template() class */
		var metaboxes = $( '.postbox.youxi-metabox[class*="youxi_page_template_"]' );

		/* Get togglers from 'Screen Options' panel */
		metaboxes.each(function() {
			if( this.id ) {
				var toggler = $( '.hide-postbox-tog#' + this.id + '-hide' );
				var isCurrentPageTemplate = $( this ).is( '.youxi_page_template_' + currentPageTemplate );
				toggler.prop( 'checked', isCurrentPageTemplate ).triggerHandler( 'click' );
				toggler.parent( 'label' ).remove();
			}
		});

		/* Bind change event to page template */
		$( document ).on( 'change', '#page_template', function() {
			var selected = $( this ).val()
				.replace( /\.php$/, '' ).replace( /\W/g, '_' );
			var m = metaboxes.hide()
				.filter( '.youxi_page_template_' + selected );
				m.show();
		});
	});
	
})( jQuery, window, document );
