( ( mw, bs ) => {
	bs.util.registerNamespace( 'bs.pageassignments.info' );

	bs.pageassignments.info.AssignmentsInformationPage = function AssignmentsInformationPage( name, config ) {
		this.assignmentGrid = null;
		bs.pageassignments.info.AssignmentsInformationPage.super.call( this, name, config );
	};

	OO.inheritClass( bs.pageassignments.info.AssignmentsInformationPage, StandardDialogs.ui.BasePage ); // eslint-disable-line no-undef

	bs.pageassignments.info.AssignmentsInformationPage.prototype.setupOutlineItem = function () {
		bs.pageassignments.info.AssignmentsInformationPage.super.prototype.setupOutlineItem.apply( this, arguments );

		if ( this.outlineItem ) {
			this.outlineItem.setLabel( mw.message( 'bs-pageassignment-info-dialog' ).plain() );
		}
	};

	bs.pageassignments.info.AssignmentsInformationPage.prototype.setup = function () {
		return;
	};

	bs.pageassignments.info.AssignmentsInformationPage.prototype.onInfoPanelSelect = async function () {
		if ( !this.assignmentGrid ) {
			await mw.loader.using( [ 'ext.oOJSPlus.data', 'oojs-ui.styles.icons-user' ] );
			const data = await bs.api.tasks.exec( 'pageassignment', 'getForPage', { pageId: mw.config.get( 'wgArticleId' ) } );

			this.assignmentGrid = new OOJSPlus.ui.data.GridWidget( {
				style: 'differentiate-rows',
				columns: {
					pa_assignee_key: { // eslint-disable-line camelcase
						headerText: mw.message( 'bs-pageassignments-column-assignedto' ).text(),
						type: 'user',
						showImage: true
					},
					pa_assignee_type: { // eslint-disable-line camelcase
						headerText: mw.message( 'bs-pageassignments-column-assignee-type' ).text(),
						type: 'text'

					}
				},
				data: data.payload
			} );

			this.$element.append( this.assignmentGrid.$element );
		}
	};

	registryPageInformation.register( 'assignment_infos', bs.pageassignments.info.AssignmentsInformationPage ); // eslint-disable-line no-undef

} )( mediaWiki, blueSpice );
