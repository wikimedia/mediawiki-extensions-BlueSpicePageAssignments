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

			const assignmentsStore = new OOJSPlus.ui.data.store.RemoteStore( {
				action: 'bs-pageassignment-store',
				pageSize: 25
			} );
			assignmentsStore.filter( new OOJSPlus.ui.data.filter.String( {
				value: this.pageName,
				operator: 'eq',
				type: 'string'
			} ), 'page_title' );

			const rawData = await assignmentsStore.doLoadData();
			const pageData = Object.values( rawData ); // eslint-disable-line es/no-object-values
			const assignmentsData = pageData.length > 0 ? pageData[ 0 ].assignments : [];

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
				data: assignmentsData
			} );

			this.$element.append( this.assignmentGrid.$element );
		}
	};

	registryPageInformation.register( 'assignment_infos', bs.pageassignments.info.AssignmentsInformationPage ); // eslint-disable-line no-undef

} )( mediaWiki, blueSpice );
