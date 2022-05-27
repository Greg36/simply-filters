export { uniqid, addLoader, removeLoader, addFormNotice, invalidInputNotice, debounce };

import { __ } from '@wordpress/i18n';

function uniqid( prefix, moreEntropy ) {
	//  discuss at: https://locutus.io/php/uniqid/
	// original by: Kevin van Zonneveld (https://kvz.io)
	//  revised by: Kankrelune (https://www.webfaktory.info/)
	//      note 1: Uses an internal counter (in locutus global) to avoid collision
	//   example 1: var $id = uniqid()
	//   example 1: var $result = $id.length === 13
	//   returns 1: true
	//   example 2: var $id = uniqid('foo')
	//   example 2: var $result = $id.length === (13 + 'foo'.length)
	//   returns 2: true
	//   example 3: var $id = uniqid('bar', true)
	//   example 3: var $result = $id.length === (23 + 'bar'.length)
	//   returns 3: true
	if ( typeof prefix === 'undefined' ) {
		prefix = ''
	}
	let retId
	const _formatSeed = function ( seed, reqWidth ) {
		seed = parseInt( seed, 10 ).toString( 16 ) // to hex str
		if ( reqWidth < seed.length ) {
			// so long we split
			return seed.slice( seed.length - reqWidth )
		}
		if ( reqWidth > seed.length ) {
			// so short we pad
			return Array( 1 + (reqWidth - seed.length) ).join( '0' ) + seed
		}
		return seed
	}
	const $global = (typeof window !== 'undefined' ? window : global)
	$global.$locutus = $global.$locutus || {}
	const $locutus = $global.$locutus
	$locutus.php = $locutus.php || {}
	if ( !$locutus.php.uniqidSeed ) {
		// init seed with big random int
		$locutus.php.uniqidSeed = Math.floor( Math.random() * 0x75bcd15 )
	}
	$locutus.php.uniqidSeed++
	// start with prefix, add current milliseconds hex string
	retId = prefix
	retId += _formatSeed( parseInt( new Date().getTime() / 1000, 10 ), 8 )
	// add seed hex string
	retId += _formatSeed( $locutus.php.uniqidSeed, 5 )
	if ( moreEntropy ) {
		// for more entropy we add a float lower to 10
		retId += (Math.random() * 10).toFixed( 8 ).toString()
	}
	return retId
}

function addLoader( node ) {
	let src = '';
	if( window.hasOwnProperty( 'sf_admin' ) ) src = sf_admin.loader_s
	if( window.hasOwnProperty( 'sf_filters' ) )	src = sf_filters.loader_src;

	let loader = '<div id="sf-ajax-loader"><img src="' + src + '" aria-hidden="true" alt=""></div>';
	node.insertAdjacentHTML( 'beforeend', loader );
	setTimeout( function () {
		let loaderNode = document.getElementById( 'sf-ajax-loader' );
		loaderNode.classList.add( 'fade-in' );
	}, 0 );
}

function removeLoader() {
	document.getElementById( 'sf-ajax-loader' ).remove();
}

function addFormNotice( message, type = 'info' ) {
	const container = document.getElementById( 'post' );

	// Remove any previous notices
	removeFormNotices();

	// Create the notice
	let notice = document.createElement( 'div' );
	notice.classList.add( 'sf-notice', `sf-notice__${type}` );
	notice.innerHTML = `<p class="sf-notice__message">${message}</p><a class="sf-notice__close" href="#"><span class="screen-reader-text">${__( 'Close notice', 'simply-filters' )}</span></a>`;

	// Insert notice at the top of form
	notice = container.insertAdjacentElement( 'afterbegin', notice );

	// Add close handler
	notice.querySelector( 'a' ).addEventListener( 'click', ( e ) => {
		e.preventDefault();
		jQuery( notice ).slideUp( 200 );
		setTimeout( () => {
			notice.remove();
		}, 200 );
	} );
}

function removeFormNotices() {
	document.querySelectorAll( '#post > .sf-notice' ).forEach( ( notice ) => {
		notice.remove();
	} );
}

function invalidInputNotice( message, input ) {

	// If there is notice already, remove it
	if( input.previousElementSibling !== null && input.previousElementSibling.classList.contains( 'sf-notice' ) ) {
		input.previousElementSibling.remove();
	}

	// Create the notice
	let notice = document.createElement( 'div' );
	notice.classList.add( 'sf-notice', 'sf-notice__input' );
	notice.innerHTML = `<p class="sf-notice__message">${message}</p>`;

	// Insert notice at the top of form
	notice = input.insertAdjacentElement( 'beforebegin', notice );

	// Remove on input change
	input.addEventListener( 'focus', () => {
		jQuery( notice ).slideUp( 200 );
		setTimeout( () => {
			notice.remove();
		}, 200 );
	} );
}

function debounce( callback, wait ) {
	let timeoutId = null;
	return ( ...args ) => {
		window.clearTimeout( timeoutId );
		timeoutId = window.setTimeout( () => {
			callback.apply( null, args );
		}, wait );
	};
}