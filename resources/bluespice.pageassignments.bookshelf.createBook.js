mw.hook( 'bs.bookshelf.newbook.actionprocess' ).add( function( process, dialog ) {
	return process.next( function () {
		const mwApi = new mw.Api();
		mwApi.get( {
			action: 'query',
			titles: dialog.bookTitle,
			format: 'json',
			prop: 'info'
		} ).fail( function () {
			dialog.showErrors( new OO.ui.Error( arguments[ 0 ], { recoverable: false } ) );
		} )
		.done( function ( resp ) {
			var pages  = resp.query.pages;
			var pageId = 0;
			for ( var page in pages ) {
				pageId = pages[page].pageid;
			}
			blueSpice.api.tasks.exec(
				'pageassignment',
				'edit',
				{
					pageId: pageId,
					pageAssignments: [ 'user/' + mw.user.getName() ]
				}, {
					success: function() {},
					failure: function( response ) {
						dialog.showErrors( new OO.ui.Error( response ) );
					}
				}
			);
		} );
	} );
} );
