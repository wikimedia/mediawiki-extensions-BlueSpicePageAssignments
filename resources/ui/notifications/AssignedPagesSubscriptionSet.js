bs.util.registerNamespace( 'bs.pageassignments.ui.notifications' );

bs.pageassignments.ui.notifications.AssignedPagesSubscriptionSet = function( cfg ) {
	// Parent constructor
	bs.pageassignments.ui.notifications.AssignedPagesSubscriptionSet.parent.apply( this, arguments );
};

OO.inheritClass( bs.pageassignments.ui.notifications.AssignedPagesSubscriptionSet, ext.notifications.ui.SubscriptionSet );


bs.pageassignments.ui.notifications.AssignedPagesSubscriptionSet.prototype.getLabel = function() {
	return mw.message( 'bs-pageassignments-notification-ui-subscriptionset-assigned-pages' ).text();
};

bs.pageassignments.ui.notifications.AssignedPagesSubscriptionSet.prototype.getKey = function() {
	return 'pa-assigned-pages';
};

bs.pageassignments.ui.notifications.AssignedPagesSubscriptionSet.prototype.getEditor = function( dialog ) {
	return null;
};

bs.pageassignments.ui.notifications.AssignedPagesSubscriptionSet.prototype.getHeaderKeyValue = function() {
	return '';
};

ext.notifications.subscriptionSetRegistry.register( 'pa-assigned-pages', bs.pageassignments.ui.notifications.AssignedPagesSubscriptionSet );
