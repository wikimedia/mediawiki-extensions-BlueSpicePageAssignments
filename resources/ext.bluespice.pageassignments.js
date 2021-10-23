(function( mw, $, d, undefined ){
	blueSpice.util.registerNamespace( 'bs.pageassignments.ui' );

	$(d).on( 'click', '#ca-pageassignments a, a#ca-pageassignments', function( e ) {
		e.preventDefault();

		var dialog = new OOJSPlus.ui.dialog.BookletDialog( {
			id: 'bs-pageassignments-set',
			pages: function() {
				var dfd = $.Deferred();
				mw.loader.using( "ext.bluespice.pageassignments.dialog.pages", function() {
					var api = new mw.Api();
					api.postWithToken( 'csrf', {
						'action': 'bs-pageassignment-tasks',
						'formatversion': 2,
						'task': 'getForPage',
						'taskData': JSON.stringify( {
							pageId: mw.config.get( 'wgArticleId' )
						} )
					} ).done( function( response, xhr ){
						if ( response.success ) {
							dfd.resolve( [
								new bs.pageassignments.ui.AssignmentsPage( {
									data: {
										page: mw.config.get( 'wgArticleId' ),
										assignments: response.payload
									}
								} )
							] );
						} else {
							dfd.reject();
						}
					} ).fail( function( e ) {
						dfd.reject();
					} );

				}, function( e ) {
					dfd.reject( e );
				} );
				return dfd.promise();
			}
		} );

		dialog.show();
	} );
})( mediaWiki, jQuery, document );
