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
				this.grid.addItems( [
					{
						type: data.pa_assignee_type,
						key: data.pa_assignee_key
					}
				] );
				this.assignmentPicker.setValue( '' );
				this.updateDialogSize();
			}
		}
	} );

	this.grid = new OOJSPlus.ui.data.GridWidget( {
		deletable: true,
		pageSize: 9999,
		noHeader: true,
		allowDuplicates: false,
		deleteNoConfirm: true,
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
			}
		}
	} );
	this.grid.connect( this, {
		rowDeleteComplete: function() {
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
		this.grid.addItems( rows );
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
	var data = this.grid.getData(),
		combined = [];

	for ( var i = 0; i < data.length; i++ ) {
		combined.push( data[i].type + '/' + data[i].key );
	}

	return {
		pageId: this.page,
		pageAssignments: combined
	};
};

