/* global jQuery, bpmAdminObject*/

/**
 * @param bpmAdminObject.ajaxUrl
 * @param bpmAdminObject.searchProductAction
 * @param bpmAdminObject.searchProductNonce
 * @param bpmAdminObject.deleteProductAction
 * @param bpmAdminObject.deleteProductNonce
 */
jQuery( document ).ready( function( $ ) {
	const searchEL = $( '#product_bundle_data_tab' );

	let flag = true;

	if ( searchEL.length ) {
		let string = '';
		let timeout = null;

		searchEL.on( 'input', function( e ) {
			if ( /[a-zA-Z0-9А-Яа-я!@#$%^&*()_+{}\[\]:;<>,.?~\\/-\s]/.test( e.target.value ) ) {
				string = e.target.value;

				if ( string.length > 3 ) {
					if ( timeout !== null ) {
						clearTimeout( timeout );
					}

					timeout = setTimeout( function() {
						getSearchProduct( string );
					}, 500 );
				}
			}
		} );
	}

	/**
	 * Get Search Product.
	 *
	 * @param {string} string Search string.
	 *
	 * @return {void}
	 */
	function getSearchProduct( string ) {
		const data = {
			action: bpmAdminObject.searchProductAction,
			nonce: bpmAdminObject.searchProductNonce,
			searchString: string,
			productID: $( '#post_ID' ).val()
		};

		const preloader = $( '#product_bundle_data_tab .preload' );

		$.ajax( {
			type: 'POST',
			url: bpmAdminObject.ajaxUrl,
			data: data,
			beforeSend: function( e ) {
				preloader.show();
			},
			success: function( res ) {
				if ( res.success ) {
					flag = true;
					outputProductList( res.data.productsList );
				} else {
					noResult( res.data.message );
				}
				preloader.hide();
				$( '.result-search' ).show();
			},
			error: function( xhr ) {
				console.log( 'error...', xhr );
				//error logging
			}
		} );
	}

	/**
	 * Output no result or errot.
	 *
	 * @param {string} message
	 */
	function noResult( message ) {
		const resultSearchEl = $( '.result-search' );
		let html = '<ul>';
		html += '<li>' + message + '</li>';
		html += '</ul>';

		resultSearchEl.html( html );
	}

	/**
	 * Output Product List.
	 *
	 * @param {object[]} productList Json response product list.
	 *
	 * @return {void}
	 */
	function outputProductList( productList ) {
		const resultSearchEl = $( '.result-search' );
		let html = '<ul>';

		productList.map( function( el ) {
			html += '<li><a href="#" class="add_product" data-product_id="' + el.id + '"><span class="title">' + el.title + '</span><div class="product-price">' + el.price + '</div></a></li>';
		} );

		html += '</ul>';

		resultSearchEl.html( html );
		setClickEvent();
		setOnFocus();

		$( document ).mouseup( function( e ) {
			let folder = $( '#product_bundle_data_tab .result-search, #product_bundle_data_tab input' );
			if ( ! folder.is( e.target ) && folder.has( e.target ).length === 0 ) {
				$( '#product_bundle_data_tab .result-search' ).hide();
			}
		} );
	}

	/**
	 * Set Click Event and add selected product.
	 */
	function setClickEvent() {

		const resultsEl = $( '.add_product' );
		const selectedProductEL = $( '.selected-products' );

		if ( resultsEl.length ) {
			resultsEl.click( function( e ) {
				e.preventDefault();

				let clonedElement = $( this ).clone();
				$( this ).parent().remove();
				clonedElement.addClass( 'selected-product' );
				clonedElement.append( '<span class="remove-button dashicons dashicons-trash"></span>' );

				let wrappedElement = $( '<div>' ).append( clonedElement );
				$( this ).replaceWith( wrappedElement );

				selectedProductEL.append( wrappedElement );
				let productID = $( this ).data( 'product_id' );
				let hiddenFields = $( '[name=product_bundle_ids]' );

				if ( hiddenFields.val().length ) {
					hiddenFields.val( [ ...hiddenFields.val().split( ',' ), productID ] );
				} else {
					hiddenFields.val( productID );
				}

				setEventClickDeleteBtn();
			} );
		}
	}

	/**
	 * Set On Focus from input search.
	 *
	 * @return {void}
	 */
	function setOnFocus() {
		const inputEl = $( '#product_bundle_data_tab input' );

		if ( inputEl.length ) {
			inputEl.on( 'focus', function() {
				if ( $( '.result-search ul' ).children().length > 0 ) {
					$( this ).parents( '.options_group' ).find( '.result-search' ).show();
				}
			} );
		}
	}

	/**
	 * Set event click delete btn.
	 */
	function setEventClickDeleteBtn() {
		const deleteBtn = $( '.remove-button.dashicons.dashicons-trash' );

		if ( deleteBtn.length ) {
			deleteBtn.off( 'click' ).on( 'click', function( e ) {
				e.preventDefault();

				const el = $( this ).parent();
				const value = $( this ).parent().data( 'product_id' );

				const hiddenInputEl = $( '[name=product_bundle_ids]' );
				let hiddenInputValues = hiddenInputEl.val().split( ',' );
				let indexToRemove = hiddenInputValues.indexOf( value.toString() );

				if ( indexToRemove > -1 ) {
					hiddenInputValues.splice( indexToRemove, 1 ).join( ',' );
				}
				hiddenInputEl.val( hiddenInputValues );

				deleteAjax( hiddenInputValues );

				el.parent().remove();
			} );
		}
	}

	setEventClickDeleteBtn();

	/**
	 * Ajax delete ids.
	 *
	 * @param ids
	 * @param productID
	 */
	function deleteAjax( ids ) {
		const data = {
			action: bpmAdminObject.deleteProductAction,
			nonce: bpmAdminObject.deleteProductNonce,
			ids: ids.join( ',' ),
			productID: $( '#post_ID' ).val()
		};

		$.ajax( {
			type: 'POST',
			url: bpmAdminObject.ajaxUrl,
			data: data,
			success: function( res ) {
				if ( ! res.success ) {
					alert( res.data.message );
				}
			},
			error: function( xhr, ajaxOptions, thrownError ) {
				console.log( 'error...', xhr );
				//error logging
			}
		} );
	}
} );
