/**
 * Youxi Tabular Form Field JS
 *
 * This script contains the initialization code for the tabular form field.
 *
 * @package   Youxi Core
 * @author    Mairel Theafila <maimairel@gmail.com>
 * @copyright Copyright (c) 2013-2015, Mairel Theafila
 */
;(function( $, window, document, undefined ) {

	"use strict";

	if( ! wp.media || ! $.widget )
		return;

	$.widget( 'youxi.tabularinput', {

		options: {
			confirmDelRow: '', 
			confirmDelCol: '', 
			fixedRows: false, 
			fixedCols: false, 
			inputMode: 'text', 
			textareaRows: 4, 
			fieldName: null, 
			minColumns: 2, 
			maxColumns: 10, 
			minRows: 2, 
			maxRows: 0
		}, 

		templates: {
			row: wp.media.template( 'youxi-tabular-row' ), 
			cell: wp.media.template( 'youxi-tabular-cell' ), 
			header: wp.media.template( 'youxi-tabular-header' ), 
			rowControls: wp.media.template( 'youxi-tabular-row-controls' ), 
			colControls: wp.media.template( 'youxi-tabular-col-controls' )
		}, 

		_init: function() {

			// Ensure min rows/cols is greater than zero
			this.options.minRows = Math.max( this.options.minRows, 1 );
			this.options.minColumns = Math.max( this.options.minColumns, 1 );

			this._refreshButtons();
		}, 

		_create: function() {

			// Listen to control buttons click event
			this._on({
				'click .youxi-tabular-controls button[data-action]': function( e ) {

					var action, button = $( e.target ).closest( '[data-action]' );

					if( button && button.length ) {

						action = button.data( 'action' );

						switch( action ) {
							case 'add-col-after':
							case 'add-col-before':
								if( ! this.options.fixedColumns && ( ! this.options.maxColumns || this._getColumnCount() + 1 <= this.options.maxColumns ) ) {
									this.addColumn( button, action.split( 'l-' )[1] );
									this._refreshButtons();
								}
								break;
							case 'delete-col':
								if( ! this.options.fixedColumns && this._getColumnCount() - 1 >= this.options.minColumns && confirm( this.options.confirmDelCol ) ) {
									this.deleteColumn( button );
									this._refreshButtons();
								}
								break;
							case 'add-row-after':
							case 'add-row-before':
								if( ! this.options.fixedRows && ( ! this.options.maxRows || this._getRowCount() + 1 <= this.options.maxRows ) ) {
									this.addRow( button, action.split( 'w-' )[1] );
									this._reIndex();
									this._refreshButtons();
								}
								break;
							case 'delete-row':
								if( ! this.options.fixedRows && this._getRowCount() - 1 >= this.options.minRows && confirm( this.options.confirmDelRow ) ) {
									this.deleteRow( button );
									this._reIndex();
									this._refreshButtons();
								}
								break;
						}
					}

					e.stopPropagation();
					e.preventDefault();
				}
			});
		}, 

		addColumn: function( column, method ) {
			var t = this;
			var colControls = column.closest( 'th' );
			var colIndex    = colControls.index( '.youxi-tabular-controls' );
			var colHeader   = colControls.parent().next( 'tr' ).children( '.youxi-tabular-header' ).eq( colIndex );

			colControls[ method ]( this.templates.colControls() );
			colHeader[ method ]( this.templates.header({ fieldName: this.options.fieldName }) );

			$( 'table', this.element )
				.find( 'tbody > .youxi-tabular-row' ).each( function( index ) {
					var col = $( this ).children( '.youxi-tabular-cell' ).eq( colIndex );
					col[ method ]( t._getColHtml( index ) );
				});
		}, 

		deleteColumn: function( column ) {
			var colControls = column.closest( 'th' );
			var colIndex    = colControls.index( '.youxi-tabular-controls' );
			var colHeader   = colControls.parent().next( 'tr' ).children( '.youxi-tabular-header' ).eq( colIndex );

			colControls.remove();
			colHeader.remove();

			$( 'table', this.element )
				.find( 'tbody > .youxi-tabular-row' ).each( function() {
					$( this ).children( '.youxi-tabular-cell' ).eq( colIndex ).remove();
				});
		}, 

		addRow: function( row, method ) {
			row.closest( '.youxi-tabular-row' )[ method ]( this._getRowHtml() );
		}, 

		deleteRow: function( row ) {
			row.closest( '.youxi-tabular-row' ).remove();
		}, 

		_reIndex: function() {
			var t = this;
			$( 'table > tbody', this.element ).children( '.youxi-tabular-row' ).each( function( index ) {
				$( this ).find( '.youxi-tabular-field' ).attr( 'name', t.options.fieldName + '[cells][' + index + '][]' );
			});
		}, 

		_refreshButtons: function() {
			var addColPossible = ! this.options.maxColumns || this._getColumnCount() + 1 <= this.options.maxColumns;
			var addRowPossible = ! this.options.maxRows || this._getRowCount() + 1 <= this.options.maxRows;

			var deleteColPossible = this._getColumnCount() - 1 >= this.options.minColumns;
			var deleteRowPossible = this._getRowCount() - 1 >= this.options.minRows;

			$( '[data-action^="add-col-"]', this.element ).attr( 'disabled', ! addColPossible );
			$( '[data-action^="add-row-"]', this.element ).attr( 'disabled', ! addRowPossible );
			$( '[data-action="delete-col"]', this.element ).attr( 'disabled', ! deleteColPossible );
			$( '[data-action="delete-row"]', this.element ).attr( 'disabled', ! deleteRowPossible );
		}, 

		_getRowHtml: function() {
			var rowControls = this.options.fixedRows ? '' : this.templates.rowControls();
			var rowCount    = this._getRowCount();

			var cells = $.map( new Array( this._getColumnCount() ), $.proxy( function() {
				return this._getColHtml( rowCount );
			}, this ));

			return this.templates.row({
				tabularRowControls: rowControls, 
				tabularRowCells: cells.join( '' )
			});
		}, 

		_getColHtml: function( rowIndex ) {
			return this.templates.cell({
				rowIndex: rowIndex, 
				inputMode: this.options.inputMode, 
				textareaRows: this.options.textareaRows, 
				fieldName: this.options.fieldName
			});
		}, 

		_getColumnCount: function() {
			return $( 'table > thead', this.element ).find( '.youxi-tabular-header' ).length;
		}, 

		_getRowCount: function() {
			return $( 'table > tbody', this.element ).children( '.youxi-tabular-row' ).length;
		}
	});

	if( $.Youxi.Form.Manager ) {

		$.Youxi.Form.Manager.addCallbacks( 'tabular', function( context ) {
			if( $.fn.tabularinput ) {
				$( '.youxi-tabular-input', context ).each(function() {
					if( ! $( this ).is( ':youxi-tabularinput' ) ) {
						$( this ).tabularinput( $( this ).data() );
					}
				});
			}
		}, function( context ) {
			if( $.fn.tabularinput ) {
				$( ':youxi-tabularinput', context ).each( function() {
					$( this ).tabularinput( 'destroy' );
				});
			}
		});
	}
	
})( jQuery, window, document );