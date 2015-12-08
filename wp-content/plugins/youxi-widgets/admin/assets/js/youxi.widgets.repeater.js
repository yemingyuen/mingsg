/**
 * Youxi Widgets Mini Repeater Script
 *
 * This script contains the script for a mini repeater widgets control
 *
 * @package   Youxi Widgets
 * @author    Mairel Theafila <maimairel@yahoo.com>
 * @version   1.0.1
 * @copyright Copyright (c) 2013, Mairel Theafila
 */

;(function( $, window, document, undefined ) {

	"use strict";

	$( document ).on( 'click', '.youxi-repeater[data-tmpl] .button-repeater-add', function( e ) {
		e.preventDefault();

		var control = $( this ).parents( '.youxi-repeater' ),	
			itemsWrap = $( '.youxi-repeater-items-wrap', control ), 
			currentIndex = control.data( 'repeater-current-index' ) || ( itemsWrap.children( '.youxi-repeater-item' ).length ), 
			options = {
				evaluate:    /<#([\s\S]+?)#>/g,
				interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
				escape:      /\{\{([^\}]+?)\}\}(?!\})/g,
				variable:    'data'
			}, 
			tmpl = _.template( $( '#tmpl-' + control.data( 'tmpl' ) ).html(), null, options );

		itemsWrap.append( tmpl( { index: currentIndex++ } ) );
		control.data( 'repeater-current-index', currentIndex );
		
	}).on( 'click', '.youxi-repeater .button-repeater-remove', function( e ) {
		e.preventDefault();

		$( this ).parents( 'table.youxi-repeater-item' ).remove();
	});
	
})( jQuery, window, document );