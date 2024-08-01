Ext.define( 'BS.PageAssignments.panel.Overview', {
	extend: 'Ext.grid.Panel',
	requires: [ 'BS.store.BSApi' ],
	plugins: 'gridfilters',

	initComponent: function() {
		var storeFields = [
			'page_id',
			'page_prefixedtext',
			'page_link',
			'assigned_by',
			'assignment'
		];

		var cols = [
			{
				text: mw.message('bs-pageassignments-column-title').plain(),
				dataIndex: 'page_prefixedtext',
				sortable: true,
				filter: {
					type: 'string'
				},
				renderer: function( value, metaData, record, rowIndex, colIndex, store, view ) {
					return record.get('page_link');
				}
			},
			{
				text: mw.message('bs-pageassignments-column-assignedto').plain(),
				dataIndex: 'assigned_by',
				sortable: true,
				hidden: true,
				filter: {
					type: 'list'
				},
				renderer: function( value, metaData, record, rowIndex, colIndex, store, view ) {
					var html = '';
					for( var i = 0; i < record.get( 'assignment' ).length; i++ ) {
						var item = record.get( 'assignment' )[i];
						html += "<span class=\'bs-icon-" + item.pa_assignee_type + " bs-typeicon\'></span>";
						html += item.anchor;
						if( i !== record.get( 'assignment' ).length -1 ) {
							html += ',<br />';
						}
					};

					return html;
				}
			}
		];

		$(document).trigger('BSPageAssignmentsOverviewPanelInit', [ this, cols, storeFields, this._actions ]);

		this.columns = {
			items: cols,
			defaults: {
				flex: 1
			}
		};

		this.store = new BS.store.BSApi({
			apiAction: 'bs-mypageassignment-store',
			fields: storeFields
		});

		this.bbar = new Ext.PagingToolbar({
			store : this.store,
			displayInfo : true
		});

		this.callParent( arguments );

		this.on( 'afterrender', function() {
			if ( this.headerCt.tabGuardBeforeEl ) {
				this.headerCt.tabGuardBeforeEl.set( { tabIndex: -1 } );
			}

			if ( this.headerCt.tabGuardAfterEl ) {
				this.headerCt.tabGuardAfterEl.set( { tabIndex: -1 } );
			}

			if ( this.el ) {
				this.el.set( { tabIndex: -1 } );
			}
		}.bind( this ) );
	}
});