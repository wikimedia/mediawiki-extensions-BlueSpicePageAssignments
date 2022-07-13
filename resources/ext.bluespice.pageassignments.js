(function( mw, $, d, undefined ){
	function _showDialog() {
		var dfd = $.Deferred();

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

		var dialog = new OOJSPlus.ui.dialog.BookletDialog( {
			id: 'bs-pageassignments-set',
			pages: function() {
				return dfd.promise();
			}
		} );

		dialog.show();
	}

	$(d).on( 'click', '#ca-pageassignments a, a#ca-pageassignments', function( e ) {
		e.preventDefault();
		mw.loader.using( 'ext.bluespice.pageassignments.dialog.pages' ).done( _showDialog );
	} );
})( mediaWiki, jQuery, document );
