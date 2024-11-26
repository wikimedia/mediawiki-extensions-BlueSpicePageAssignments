ext.bluespice = ext.bluespice || {};
ext.bluespice.pageassignments = ext.bluespice.pageassignments || {};
ext.bluespice.pageassignments.ui = ext.bluespice.pageassignments.ui || {};
ext.bluespice.pageassignments.ui.panel = ext.bluespice.pageassignments.ui.panel || {};

ext.bluespice.pageassignments.ui.panel.Manager = function ( cfg ) {
	ext.bluespice.pageassignments.ui.panel.Manager.super.apply( this, cfg );
	this.$element = $( '<div>' );

	this.store = new OOJSPlus.ui.data.store.RemoteStore( {
		action: 'bs-pageassignment-store',
		pageSize: 25
	} );

	this.setup();
};

OO.inheritClass( ext.bluespice.pageassignments.ui.panel.Manager, OO.ui.PanelLayout );

ext.bluespice.pageassignments.ui.panel.Manager.prototype.setup = function () {
	this.gridCfg = this.setupGridConfig();
	this.grid = new OOJSPlus.ui.data.GridWidget( this.gridCfg );
	this.grid.connect( this, {
		action: 'doActionOnRow'
	} );

	this.$element.append( this.grid.$element );
};

ext.bluespice.pageassignments.ui.panel.Manager.prototype.setupGridConfig = function () {
	const gridCfg = {
		exportable: true,
		style: 'differentiate-rows',
		columns: {
			page_prefixedtext: { // eslint-disable-line camelcase
				headerText: mw.message( 'bs-pageassignments-column-title' ).plain(),
				type: 'text',
				sortable: true,
				filter: { type: 'text' },
				valueParser: ( value ) => {
					return new OO.ui.HtmlSnippet( mw.html.element(
						'a',
						{
							href: mw.util.getUrl( value )
						},
						value
					) );
				}
			},
			assignments: {
				headerText: mw.message( 'bs-pageassignments-column-assignments' ).plain(),
				type: 'text',
				sortable: true,
				filter: { type: 'text' },
				valueParser: ( value ) => {
					if ( !value.length ) {
						return mw.message( 'bs-pageassignments-no-assignments' ).plain();
					}

					const $htmlElement = $( '<tr>' );
					value.forEach( ( element ) => {
						const widget = new OOJSPlus.ui.widget.UserWidget( {
							user_name: element.pa_assignee_key, // eslint-disable-line camelcase
							showImage: element.pa_assignee_type === 'user',
							showLink: element.pa_assignee_type === 'user',
							showRawUsername: false
						} );
						$htmlElement.append( $( '<th>' ).append( widget.$element ) );
					} );
					return $htmlElement;
				}
			}
		},
		actions: {
			edit: {
				headerText: mw.message( 'bs-pageassignments-header-action-edit' ).text(),
				title: mw.message( 'bs-pageassignments-title-edit' ).text(),
				type: 'action',
				actionId: 'edit',
				icon: 'settings',
				invisibleHeader: true,
				width: 30
			},
			delete: {
				headerText: mw.message( 'bs-pageassignments-header-action-delete' ).text(),
				title: mw.message( 'bs-pageassignments-title-delete' ).text(),
				type: 'action',
				actionId: 'delete',
				icon: 'trash',
				invisibleHeader: true,
				width: 30
			},
			log: {
				headerText: mw.message( 'bs-pageassignments-action-log' ).text(),
				title: mw.message( 'bs-pageassignments-action-log' ).text(),
				type: 'action',
				actionId: 'log',
				icon: 'article',
				invisibleHeader: true,
				width: 30
			}
		},
		store: this.store,
		provideExportData: () => {
			const deferred = $.Deferred();
			const isReadConfirmationNS = ( ns ) => {
				const namespaces = mw.config.get( 'bsgReadConfirmationActivatedNamespaces', [] );
				return ( namespaces.some( ( namespace ) => namespace == ns ) ); // eslint-disable-line eqeqeq
			};

			( async () => {
				try {
					this.store.setPageSize( 99999 );
					const response = await this.store.reload();

					const $table = $( '<table>' );
					let $row = $( '<tr>' );

					$row.append( $( '<td>' ).text( mw.message( 'bs-pageassignments-column-title' ).text() ) );
					$row.append( $( '<td>' ).text( mw.message( 'bs-pageassignments-column-assignments' ).text() ) );
					$row.append( $( '<td>' ).text( mw.message( 'bs-readconfirmation-column-read' ).text() ) ); // BlueSpiceReadConfirmation

					$table.append( $row );

					for ( const id in response ) {
						if ( response.hasOwnProperty( id ) ) { // eslint-disable-line no-prototype-builtins
							const record = response[ id ];
							$row = $( '<tr>' );

							$row.append( $( '<td>' ).text( record.page_prefixedtext ) );
							const assignees = record.assignments.map( ( assignment ) => assignment.pa_assignee_key ).join( ' - ' ); // CSV comma delimiter
							if ( !assignees ) {
								$row.append( $( '<td>' ).text( mw.message( 'bs-pageassignments-no-assignments' ).plain() ) );
							} else {
								$row.append( $( '<td>' ).text( assignees ) );
							}

							// BlueSpiceReadConfirmation
							if ( isReadConfirmationNS( record.page_namespace ) ) {
								$row.append( $( '<td>' ).text( record.all_assignees_have_read ) );
							} else {
								$row.append( $( '<td>' ).text( mw.message( 'bs-readconfirmation-disabled-ns-short' ).text() ) );
							}

							$table.append( $row );
						}
					}

					deferred.resolve( `<table>${$table.html()}</table>` );
				} catch ( error ) {
					deferred.reject( 'Failed to load data' );
				}
			} )();

			return deferred.promise();
		}
	};

	mw.hook( 'BSPageAssignmentsManagerPanelInit' ).fire( gridCfg );

	// Move action columns to the end
	gridCfg.columns = Object.assign( gridCfg.columns, gridCfg.actions ); // eslint-disable-line compat/compat

	return gridCfg;
};

ext.bluespice.pageassignments.ui.panel.Manager.prototype.doActionOnRow = function ( action, row ) {
	switch ( action ) {
		case 'edit': {
			const assignmentsPage = new bs.pageassignments.ui.AssignmentsPage( {
				data: {
					page: row.page_id,
					assignments: row.assignments
				}
			} );
			this.showPageassignmentsDialog( assignmentsPage );
			break;
		}

		case 'delete': {
			bs.util.confirm(
				'bs-pa-remove',
				{
					textMsg: 'bs-pageassignments-action-delete-confirm'
				},
				{
					ok: () => { this.onRemovePageassignmentsOk( row.page_id ); }
				}
			);
			break;
		}

		case 'log': {
			window.location.href = mw.util.getUrl(
				'Special:Log', {
					page: row.page_prefixedtext,
					type: 'bs-pageassignments'
				}
			);
			break;
		}

		// Dynamic action handler
		// BlueSpiceReadConfirmation
		default: {
			if ( this.gridCfg.actions[ action ] ) {
				this.gridCfg.actions[ action ].doActionOnRow( row );
			}
			break;
		}
	}
};

ext.bluespice.pageassignments.ui.panel.Manager.prototype.showPageassignmentsDialog = async function ( assignmentsPage ) {
	const dialog = new OOJSPlus.ui.dialog.BookletDialog( {
		id: 'bs-pageassignments-set',
		title: mw.message( 'bs-pageassignments-dlg-title' ).plain(),
		pages: [ assignmentsPage ]
	} );

	const result = await dialog.show().closed;
	if ( result.success ) {
		this.store.reload();
	}
};

ext.bluespice.pageassignments.ui.panel.Manager.prototype.onRemovePageassignmentsOk = async function ( pageId ) {
	const result = await bs.api.tasks.execSilent( 'pageassignment', 'edit', {
		pageId: pageId,
		pageAssignments: []
	} );

	if ( result.success ) {
		this.store.reload();
	}
};
