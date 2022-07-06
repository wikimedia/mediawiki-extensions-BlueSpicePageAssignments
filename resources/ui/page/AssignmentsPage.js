bs.util.registerNamespace( 'bs.pageassignments.ui' );

bs.pageassignments.ui.AssignmentsPage = function( cfg ) {
	cfg = cfg || {};

	this.page = cfg.data.page || 0;
	bs.pageassignments.ui.AssignmentsPage.parent.call( this, 'page-assignments', cfg );
};

OO.inheritClass( bs.pageassignments.ui.AssignmentsPage, OOJSPlus.ui.booklet.DialogBookletPage );

bs.pageassignments.ui.AssignmentsPage.prototype.getItems = function() {
	this.assignmentPicker = new OOJSPlus.ui.widget.StoreDataInputWidget( {
		id: 'assignment-picker',
		placeholder: mw.message( "bs-pageassignments-dialog-input-placeholder" ).text(),
		queryAction: 'bs-pageassignable-store',
		groupBy: 'pa_assignee_type',
		labelField: 'text',
		additionalQueryParams: {
			context: JSON.stringify( {
				wgArticleId: this.page
			} )
		},
		groupLabelCallback: function( label, data ) {
			return mw.message( 'bs-pageassignments-assignee-type-' + label ).text();
		}
	} );
	this.assignmentPicker.connect( this, {
		change: function() {
			var data = this.assignmentPicker.getSelectedItemData();
			if ( typeof data === 'object' && data !== null ) {
				this.appendToStoreData( {
					type: data.pa_assignee_type,
					key: data.pa_assignee_key
				} );
				this.assignmentPicker.setValue( '' );
				this.updateDialogSize();
			}
		}
	} );

	this.store = new OOJSPlus.ui.data.store.Store( {
		pageSize: 99999
	} );
	this.grid = new OOJSPlus.ui.data.GridWidget( {
		noHeader: true,
		hidePagination: true,
		columns: {
			type: {
				type: "icon",
				valueParser: function( value ) {
					return value === 'user' ? 'userAvatar' : 'userGroup';
				},
				width: 50
			},
			key: {
				type: "text"
			},
			delete: {
				type: 'action',
				actionId: 'delete',
				icon: 'clear',
				width: 30
			}
		},
		store: this.store
	} );
	this.grid.connect( this, {
		action: function( action, row ) {
			this.removeFromStoreData( row );
			this.updateDialogSize();
		}
	} );

	return [
		this.grid,
		this.assignmentPicker
	];
};

bs.pageassignments.ui.AssignmentsPage.prototype.setData = function( data ) {
	if ( data.hasOwnProperty( 'page' ) ) {
		this.page = data.page;
	}
	if ( data.hasOwnProperty( 'assignments' ) ) {
		var rows = [];
		for ( var i = 0; i < data.assignments.length; i++ ) {
			rows.push( {
				type: data.assignments[i].pa_assignee_type,
				key: data.assignments[i].pa_assignee_key
			} );
		}
		this.grid.store.setData( rows );
		this.updateDialogSize();
	}
};

bs.pageassignments.ui.AssignmentsPage.prototype.getTitle = function() {
	return mw.message( 'bs-pageassignments-dlg-title' ).plain();
};

bs.pageassignments.ui.AssignmentsPage.prototype.getSize = function() {
	return 'medium';
};

bs.pageassignments.ui.AssignmentsPage.prototype.getActionKeys = function() {
	return [ 'cancel', 'done' ];
};

bs.pageassignments.ui.AssignmentsPage.prototype.getAbilities = function() {
	return { done: true, cancel: true };
};

bs.pageassignments.ui.AssignmentsPage.prototype.appendToStoreData = function( assignment ) {
	var original = this.store.originalData,
		duplicate = false;
	for( var i = 0; i < original.length; i++ ) {
		if ( assignment.key === original[i].key && assignment.type === original[i].type ) {
			duplicate = true;
			break;
		}
	}
	if ( !duplicate ) {
		original.push( assignment );
		this.store.setData( original );
	}
};

bs.pageassignments.ui.AssignmentsPage.prototype.removeFromStoreData = function( assignment ) {
	var original = this.store.originalData,
		newData = [];
	for( var i = 0; i < original.length; i++ ) {
		if ( assignment.key === original[i].key && assignment.type === original[i].type ) {
			continue;
		}
		newData.push( original[i] );
	}
	this.store.setData( newData );
};

bs.pageassignments.ui.AssignmentsPage.prototype.onAction = function( action ) {
	var dfd = $.Deferred();

	if ( action === 'done' ) {
		blueSpice.api.tasks.exec(
			'pageassignment',
			'edit',
			this.getApiData(), {
				success: function() {
					dfd.resolve( { action: 'close', data: { success: true } } );
				}.bind( this ),
				failure: function( response ) {
					dfd.reject( response.message );
				}
			}
		);
	} else {
		return bs.pageassignments.ui.AssignmentsPage.parent.prototype.onAction.call( this, action );
	}

	return dfd.promise();
};

bs.pageassignments.ui.AssignmentsPage.prototype.getApiData = function() {
	var data = this.store.originalData,
		combined = [];

	for ( var i = 0; i < data.length; i++ ) {
		combined.push( data[i].type + '/' + data[i].key );
	}

	return {
		pageId: this.page,
		pageAssignments: combined
	};
};

