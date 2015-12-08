
;(function( $, window, document, undefined ) {

	var isImporting = false, 
		tasksCount = (function() {
			var n = 0;
			for( var o in _demoImporterSettings.importTasks ) n++;
			return n;
		})(), 
		demo_buttons = $( '.demo-content .demo-actions > button' );

	function toggleButtons( demo, toggle ) {
		demo.toggleClass( 'is-importing', toggle );
		demo_buttons.prop( 'disabled', toggle );
	}

	function showCompletionMessage( demo, feedback, failures ) {

		if( failures > 0 ) {
			feedback.html( _demoImporterSettings.failureMessage.replace( '{count}', failures.toString() ) );
		} else {
			feedback.html( _demoImporterSettings.successMessage );
		}
		_demoImporterSettings.hasPreviousImport = ! failures;

		setTimeout(function() {
			isImporting = false;
			toggleButtons( demo, isImporting );
			feedback.empty();
		}, parseInt( _demoImporterSettings.importFinishTimeout ) );
	}

	$( window ).on( 'beforeunload.demo-importer', function() {
		if( isImporting ) {
			return _demoImporterSettings.beforeUnloadMessage;
		}
	});

	$( document ).on( 'click.demo-importer', '.demo-content .demo-actions > button', function( e ) {

		e.preventDefault();

		if( isImporting || ( _demoImporterSettings.hasPreviousImport && 
				! confirm( _demoImporterSettings.hasPreviousImportMessage ) ) ) {
			return;
		}

		var tasksCompleted = 0, 
			tasksFailed = 0, 
			$this = $( this ), 
			demo = $this.closest( '.demo-content' ), 
			feedback = demo.find( '.more-details' ), 
			demo_id = demo.data( 'demo-id' ), 
			nonce = demo.data( 'wp-nonce' );

		if( demo_id ) {

			$.each( _demoImporterSettings.importTasks, function( task_id, task_args ) {

				$.ajaxQueue({
					type: 'POST', 
					dataType: 'json', 
					url: _demoImporterSettings.ajaxUrl, 
					data: {
						action: _demoImporterSettings.ajaxAction, 
						_ajax_nonce: nonce, 
						task: $.extend( true, {}, task_args, {
							task_id: task_id, 
							demo_id: demo_id
						})
					}, 
					beforeSend: function() {
						toggleButtons( demo, isImporting = true );
						feedback.html( task_args.status );
					}
				}).done( function( response ) {
					if( _demoImporterSettings.importDebug ) {
						if( ! response.success ) {
							console.log( response.data.error );
						} else {
							console.log( response.data.result );
						}
					}
				}).fail(function() {
					++tasksFailed;
				}).always(function() {
					if( ( ++tasksCompleted ) == tasksCount ) {
						showCompletionMessage( demo, feedback, tasksFailed );
					}
				});

			});

		}
	});

})( jQuery, window, document );
