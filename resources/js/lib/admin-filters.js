/**
 * Filters edit screen
 *
 * @package   SimplyFilters
 */
// @todo: I could rework it to be a "admin filter" singular - and have row as this.row and so on
import ColorControl from './admin-color';
import { addLoader, removeLoader, uniqid } from "./helpers";

export default class AdminFilters {

	init() {
		const colorControl = new ColorControl( '.sf-color__field' );
		colorControl.init();

		this.initRows();
	}

	initRows() {
		const rows = document.querySelectorAll( '.sf-filter' );
		rows.forEach( ( current ) => {
			this.setupRowEvents( current )
		} );

		jQuery( '.sf-filters__list' ).sortable( {
			handle: '.sf-row__order',
			stop: () => {
				this.updateOrderNumbers()
			}
		} );
	}

	/**
	 * Setup all events related to the filter row functionality
	 *
	 * @param row
	 */
	setupRowEvents( row ) {
		row.querySelector( 'a.edit-filter' ).addEventListener( 'click', ( e ) => {
			e.preventDefault();
			this.toggleOptions( row );
		} );

		row.querySelector( 'a.duplicate-filter' ).addEventListener( 'click', ( e ) => {
			e.preventDefault();
			this.duplicateFilter( row );
		} );

		row.querySelector( 'a.remove-filter' ).addEventListener( 'click', ( e ) => {
			e.preventDefault();
			e.stopPropagation();
			this.removeFilter( row );
		} );

		row.querySelector( 'a.sf-close' ).addEventListener( 'click', ( e ) => {
			e.preventDefault();
			this.toggleOptions( row );
		} );

		row.querySelector( '#' + row.dataset.filter_id + '-label' ).addEventListener( 'focusout', ( e ) => {
			row.querySelector( '.sf-row__label' ).innerText = e.target.value.trim();
		} );

		const source_select = row.querySelector( '#' + row.dataset.filter_id + '-sources' );
		if ( source_select ) {
			source_select.addEventListener( 'change', ( e ) => {
				this.changeSource( row, e.target.value );
			} );
			this.changeSource( row, source_select.value );
		}

		// Color
		if ( row.dataset.filter_type === 'Color' ) {
			var options = {
				sources: row.querySelector( '#' + row.dataset.filter_id + '-sources' ),
				attributes: row.querySelector( '#' + row.dataset.filter_id + '-attributes' ),
				product_cat: row.querySelector( '#' + row.dataset.filter_id + '-product_cat' ),
				product_tag: 'product_tag'
			};

			// If either source directly or one of the options was changed
			// check selected source and option and pass it to get color
			Object.keys( options ).forEach( key => {

					// Skip options with no source select
					if( typeof options[key] === 'string' ) return '';

					options[key].addEventListener( 'change', () => {
						let source = options.sources.value,
							selected = options[ source ].value;
						this.getColorOptions( row, source, selected );
					} );
			} );

		}

		// @todo: make it cleaner
	}

	getColorOptions( row, key, term_id ) {

		addLoader( row.querySelector( '.sf-color' ).closest( '.sf-option' ) );

		fetch( sf_admin.ajax_url, {
			method: 'POST',
			body: new URLSearchParams( {
				action: 'sf/get_color_options',
				nonceAjax: sf_admin.ajax_nonce,
				key: key,
				term: term_id,
				id: row.dataset.filter_id
			} ),
		} ).then( ( response ) => {
			return response.text();
		} ).then( ( text ) => {
			this.loadColorOptions( row, text );
		} );
	}

	loadColorOptions( row, text ) {
		// Replace existing color optiosn with new
		let frag = document.createRange().createContextualFragment( text );
		row.querySelector( '.sf-color' ).closest( 'td' ).innerHTML = frag.querySelector( '.sf-color' ).outerHTML;

		// Reinitialize color picker
		const colorControl = new ColorControl( row.querySelectorAll( '.sf-color__field' ) );
		colorControl.init();
		removeLoader();
	}

	/**
	 * Toggle visibility of fitter's options
	 *
	 * @param row
	 * @param speed
	 */
	toggleOptions( row, speed = 300 ) {
		const options = row.querySelector( '.sf-filter__options' );
		jQuery( options ).slideToggle( speed );
		row.classList.toggle( 'open' );
	}

	changeSource( row, selected ) {
		const attributes = row.querySelector( '#' + row.dataset.filter_id + '-attributes' ).closest( '.sf-option' );
		const categories = row.querySelector( '#' + row.dataset.filter_id + '-product_cat' ).closest( '.sf-option' );

		if ( selected === 'attributes' ) {
			attributes.style.display = 'table-row';
			categories.style.display = 'none';
		} else if ( selected === 'product_cat' ) {
			attributes.style.display = 'none';
			categories.style.display = 'table-row';
		} else {
			attributes.style.display = 'none';
			categories.style.display = 'none';
		}
	}

	/**
	 * Duplicate filter and update all of its keys with new unique one
	 *
	 * @param row
	 */
	duplicateFilter( row ) {

		// Get new unique key
		const new_key = uniqid( sf_admin.prefix );

		// Clone the node and update key
		let new_row = row.cloneNode( true );
		new_row.dataset.filter_id = new_key;
		new_row = this.replaceRowKey( new_row, row.dataset.filter_id, new_key ); // @todo update replace to new name and id fields

		// Update field label
		const label = new_row.querySelector( '.sf-row__label' ),
			new_label = label.innerText.trim() + ' ' + sf_admin.locale.copy;
		label.innerText = new_label;
		new_row.querySelector( '#' + new_key + '-label' ).value = new_label;

		// Insert new row and setup events
		row.insertAdjacentElement( 'afterend', new_row );
		this.setupRowEvents( new_row );

		// Manage open settings
		if ( row.classList.contains( 'open' ) ) {
			this.toggleOptions( row );

			// Instantly close duplicated row
			new_row.querySelector( '.sf-filter__options' ).style.display = 'none';
		}

		// Open new row
		this.toggleOptions( new_row );

		// Update filter numbers
		this.updateOrderNumbers();
	}

	/**
	 * Update numbers at the start of the row to be in order
	 */
	updateOrderNumbers() {
		const numbers = document.querySelectorAll( '.sf-row__order' );
		numbers.forEach( ( current, index ) => {
			current.innerText = index + 1;
		} );
	}

	/**
	 * Replace all keys in a row to new key
	 *
	 * @param row
	 * @param search
	 * @param replace
	 * @returns {*}
	 */
	replaceRowKey( row, search, replace ) {
		const elements = row.querySelectorAll( 'label, input, select' );

		elements.forEach( ( ele ) => {
			if ( ele.hasAttribute( 'id' ) ) {
				ele.id = ele.id.replace( search, replace );
			}
			if ( ele.hasAttribute( 'name' ) ) {
				ele.setAttribute( 'name', ele.getAttribute( 'name' ).replace( search, replace ) );
			}
			if ( ele.hasAttribute( 'for' ) ) {
				ele.setAttribute( 'for', ele.getAttribute( 'for' ).replace( search, replace ) );
			}
		} );

		return row;
	}

	/**
	 * Remove filter with a confirmation tooltip
	 *
	 * @param row
	 */
	removeFilter( row ) {

		// Create tooltip with message
		const message = `${sf_admin.locale.sure}<a href="#" data-event="remove">${sf_admin.locale.delete}</a><a href="#" data-event="cancel">${sf_admin.locale.cancel}</a>`,
			tooltip = document.createElement( 'div' );
		tooltip.classList.add( 'sf-remove-tooltip' );
		tooltip.innerHTML = message;

		// Append tooltip to remove button
		row.querySelector( '.remove-filter' ).insertAdjacentElement( 'afterend', tooltip );

		const removeBtn = row.querySelector( 'a[data-event="remove"]' ),
			cancelBtn = row.querySelector( 'a[data-event="cancel"]' );
		removeBtn.focus();

		// Do remove
		removeBtn.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			e.stopPropagation();

			row.querySelector( '.sf-remove-tooltip' ).remove();

			jQuery( row ).slideToggle( 300 );
			setTimeout( () => {
				row.remove();
			}, 300 );
		} );

		// Cancel remove
		cancelBtn.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			this.removeNodes( '.sf-remove-tooltip' );
		} );

		// Remove tooltip when clicked outside of the box
		removeBtn.addEventListener( 'focusout', () => {
			setTimeout( () => {
				this.removeNodes( '.sf-remove-tooltip' );
			} );
		} );
	}

	/**
	 * Remove all nodes of specified selector
	 *
	 * @param selector
	 */
	removeNodes( selector ) {
		const tooltips = document.querySelectorAll( selector );
		tooltips.forEach( ( current ) => {
			current.remove();
		} );
	}

}

