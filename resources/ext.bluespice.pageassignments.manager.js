(function( mw, $, bs, d, undefined ){
	function _renderGrid() {
		Ext.onReady(function(){
			Ext.create( 'BS.PageAssignments.panel.Manager', {
				renderTo: 'bs-pageassignments-manager'
			});
		});
	}

	const deps = require( './config.json' ).pageAssignmentsOverviewDeps;
	if ( deps ) {
		mw.loader.using( deps, _renderGrid );
	}
	else {
		_renderGrid();
	}

})( mediaWiki, jQuery, blueSpice, document );