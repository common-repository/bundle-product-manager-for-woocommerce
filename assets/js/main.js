/* global jQuery */

let urlParams = new URLSearchParams( window.location.search );
if ( urlParams.has( 'add-to-cart' ) ) {
	removeQueryParams();
}

function removeQueryParams() {
	let urlWithoutParams = window.location.protocol + '//' + window.location.host + window.location.pathname;
	window.history.replaceState( { path: urlWithoutParams }, '', urlWithoutParams );
}
