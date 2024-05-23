(function( mw, $, d, undefined ){

	function getPageId( pageName ) {
		var dfd = $.Deferred();
		var api = new mw.Api();
		api.get( {
			action: 'query',
			titles: pageName,
			format: 'json',
			prop: 'info'
		} ).fail( function () {
			dfd.reject( e );
		} )
		.done( function ( resp ) {
			var pages  = resp.query.pages;
			var pageId = 0;
			for ( var page in pages ) {
				pageId = pages[page].pageid;
			}
			dfd.resolve( pageId );
		} );

		return dfd.promise();
	}

	function _showDialog( page ) {
		var dfd = $.Deferred();

		getPageId( page ).done( function ( pageID ) {
			var api = new mw.Api();
			api.postWithToken( 'csrf', {
				'action': 'bs-pageassignment-tasks',
				'formatversion': 2,
				'task': 'getForPage',
				'taskData': JSON.stringify( {
					pageId: pageID
				} )
			} ).done( function( response, xhr ){
				if ( response.success ) {
					dfd.resolve( [
						new bs.pageassignments.ui.AssignmentsPage( {
							data: {
								page: pageID,
								assignments: response.payload
							}
						} )
					] );
				} else {
					dfd.reject();
				}
			} ).fail( function( e ) {
				dfd.reject( e );
			} );
		} );

		var dialog = new OOJSPlus.ui.dialog.BookletDialog( {
			id: 'bs-pageassignments-set',
			pages: function() {
				return dfd.promise();
			}
		} );

		dialog.show();
	}

	$( d ).on( 'click', '.pageassignments-book-overview', function( e ) {
		e.preventDefault();
		var target = e.target;
		if ( target.nodeName != 'A' ) {
			target = $( target).parent();
		}

		var page = $( target ).data( 'prefixed_db_key' );
		mw.loader.using( 'ext.bluespice.pageassignments.dialog.pages' ).done( _showDialog( page ) );
	} );
})( mediaWiki, jQuery, document );