/**
 * Rules Chosen Controller
 * 
 * Author: Kevin Carwile
 */

;( function($, _, undefined){
	"use strict";

	ips.controller.register( 'rules.admin.ui.chosen', 
	{
		initialize: function()
		{
			if ( typeof $.fn.chosen != 'undefined' )
			{
				var scope = this.scope;
				scope.find( 'select' ).chosen(
				{
					disable_search_threshold: 50,
					search_contains: true,
					include_group_label_in_selected: false
				});
				
				scope.on( 'click', '.group-result', function()
				{
					var current = $(this).next();
					while( current.hasClass( 'group-option' ) )
					{
						if ( ! current.hasClass( 'result--selected' ) )
						{
							current.toggle();
						}
						current = current.next();
					}
				});
				
				/**
				 * Chosen interferes with the IPS process of hiding toggle fields
				 * for the select input, so we wait momentarily to fire the event
				 * again and trigger any toggles.
				 */
				setTimeout( function() 
				{
					var select = scope.find( 'select' );
					if ( select.attr( 'id' ).match(/source$/) )
					{
						setTimeout( function() {
							select.change();
						}, 200 );
					}
					select.change();
				}, 
				200 );
							
			}
		}
		
	});
}(jQuery, _));