
;(function( $, window, document, undefined ) {

	"use strict";

	$(function() {

		$( document ).on( 'click', '.youxi-likes-button', function(e) {

			if( ! $( this ).is( '.youxi-likes-liked' ) ) {

				var postId = $( this ).data( 'likes-post-id' ), 
					ajaxAction = $( this ).data( 'likes-ajax-action' ), 
					ajaxUrl = $( this ).data( 'likes-ajax-url' ), 
					ajaxNonce = $( this ).data( 'likes-ajax-nonce' );

				$.ajax({
					type: 'post', 
					url: ajaxUrl, 
					data: {
						action: ajaxAction, 
						_ajax_nonce: ajaxNonce, 
						post_id: postId
					}, 
					context: this, 
					dataType: 'json'
				}).done(function( response ) {
					if( response.success ) {
						$( this ).addClass( 'youxi-likes-liked' )
							.find( '.youxi-likes-count' )
								.text( response.data.count );
					}
				});
			}

			e.preventDefault();
		})
	});
	
})( jQuery, window, document );
