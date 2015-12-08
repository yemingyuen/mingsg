/**
 * Youxi Multiselect Form Field JS
 *
 * This script contains the initialization code for the multiselect form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'multiselect', function( context ) {

			if( $.fn.bsmSelect ) {
				$( '.youxi-multiselect', context ).bsmSelect({
					plugins: [
						$.bsmSelect.plugins.sortable({
							axis : 'y'
						})
					], 
					removeLabel: '<i class="dashicons dashicons-trash"></i>', 
					containerClass: 'bsmContainer', 
					selectClass: 'bsmSelect youxi-form-large',
					optionDisabledClass: 'bsmOptionDisabled', 
					listClass: 'bsmList', 
					listItemClass: 'bsmListItem', 
					listItemLabelClass: 'bsmListItemLabel', 
					removeClass: 'button button-primary bsmListItemRemove', 
					highlightClass: 'bsmHighlight'
				});
			}

		}, function( context ) {
			
		});
	}

})( jQuery, window, document );