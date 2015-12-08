
(function( wp, $ ) {
	var api = wp.customize, 
		prefix = ( _heliumCustomizeControls ? _heliumCustomizeControls.prefix : '' ), 

		wrapId = function( id ) {
			return prefix + '[' + id + ']';
		};

	$( function() {

		$.each([
			{
				setting: 'blog_related_posts', 
				controls: [ 'blog_related_posts_count', 'blog_related_posts_behavior' ], 
				callback: function( to ) { return !! to; }
			}, 
			{
				setting: 'blog_summary', 
				controls: [ 'blog_excerpt_length' ], 
				callback: function( to ) { return 'the_excerpt' == to; }
			}, 
			{
				setting: 'portfolio_show_related_items', 
				controls: [ 'portfolio_related_items_count', 'portfolio_related_items_behavior' ], 
				callback: function( to ) { return !! to; }
			}, 
			{
				setting: 'portfolio_grid_pagination', 
				controls: [ 'portfolio_grid_ajax_button_text', 'portfolio_grid_ajax_button_complete_text' ], 
				callback: function( to ) { return 'ajax' == to; }
			}, 
			{
				setting: 'portfolio_grid_pagination', 
				controls: [ 'portfolio_grid_posts_per_page' ], 
				callback: function( to ) { return 'show_all' != to; }
			}, 
			{
				setting: 'edd_show_related_items', 
				controls: [ 'edd_related_items_count', 'edd_related_items_behavior' ], 
				callback: function( to ) { return !! to; }
			}, 
			{
				setting: 'edd_grid_pagination', 
				controls: [ 'edd_grid_ajax_button_text', 'edd_grid_ajax_button_complete_text' ], 
				callback: function( to ) { return 'ajax' == to; }
			}, 
			{
				setting: 'edd_grid_pagination', 
				controls: [ 'edd_grid_posts_per_page' ], 
				callback: function( to ) { return 'show_all' != to; }
			}
		], function( i, o ) {
			api( wrapId( o.setting ), function( setting ) {
				$.each( o.controls, function( j, controlId ) {
					api.control( wrapId( controlId ), function( control ) {
						var visibility = function( to ) {
							control.container.toggle( o.callback( to ) );
						};

						visibility( setting.get() );
						setting.bind( visibility );
					});
				});
			});
		});
	});

})( wp, jQuery );
