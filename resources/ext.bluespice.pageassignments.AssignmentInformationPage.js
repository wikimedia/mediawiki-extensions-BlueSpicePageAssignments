(function( mw, $, bs ) {
	bs.util.registerNamespace( 'bs.pageassignments.info' );

	bs.pageassignments.info.AssignmentsInformationPage = function AssignmentsInformationPage( name, config ) {
		this.assignmentGrid = null;
		bs.pageassignments.info.AssignmentsInformationPage.super.call( this, name, config );
	};

	OO.inheritClass( bs.pageassignments.info.AssignmentsInformationPage, StandardDialogs.ui.BasePage );

	bs.pageassignments.info.AssignmentsInformationPage.prototype.setupOutlineItem = function () {
		bs.pageassignments.info.AssignmentsInformationPage.super.prototype.setupOutlineItem.apply( this, arguments );

		if ( this.outlineItem ) {
			this.outlineItem.setLabel( mw.message( 'bs-pageassignment-info-dialog' ).plain() );
		}
	};

	bs.pageassignments.info.AssignmentsInformationPage.prototype.setup = function () {
		return;
	};

	bs.pageassignments.info.AssignmentsInformationPage.prototype.onInfoPanelSelect = function () {
		var me = this;
		if ( me.assignmentGrid === null ){
			mw.loader.using( 'ext.bluespice.extjs' ).done( function () {
				Ext.onReady( function( ) {
					me.assignmentGrid = Ext.create( 'BS.PageAssignments.flyout.grid.AssigneesPanel', {
						title: false,
						renderTo: me.$element[0],
						width: me.$element.width(),
						height: me.$element.height()
					});
				}, me );
			});
		}
	};

	bs.pageassignments.info.AssignmentsInformationPage.prototype.getData = function () {

		var dfd = new $.Deferred();
		mw.loader.using( 'ext.bluespice.extjs' ).done( function () {
			Ext.require( 'BS.PageAssignments.flyout.grid.AssigneesPanel', function() {
				dfd.resolve();
			});
		});
		return dfd.promise();
	};

	registryPageInformation.register( 'assignment_infos', bs.pageassignments.info.AssignmentsInformationPage );

})( mediaWiki, jQuery, blueSpice );
