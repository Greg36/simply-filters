/**
 * Filters edit screen
 *
 * @package   SimplyFilters
 */

import ColorControl from './admin-color';
import { addLoader, removeLoader, uniqid } from "./helpers";
import { updateOrderNumbers } from "./admin-filters-group";

export default class AdminFilter {

	constructor( filter ) {
		this.filter = filter;
		this.type = filter.dataset.filter_type;
		this.id = filter.dataset.filter_id;
		this.raw_id = this.id.replace( sf_admin.prefix + '-', '' );
		this.nodes = {};

		// Initialize color setting options
		if ( this.type === 'Color' ) {
			this.initColorPicker();
			this.setupColorControl();
		}

		// Setup source options
		if ( this.getInput( 'sources' ) ) {
			this.setupSources();
		}

		// Setup events
		this.setupEvents();
	}

	/**
	 * Get filter's input field by its label with query cache
	 */
	getInput( label ) {
		if ( !this.nodes.hasOwnProperty( label ) ) {
			this.nodes[label] = this.filter.querySelector( '#' + this.id + '-' + label );
		}
		return this.nodes[label];
	}

	/**
	 * Setup all events related to the filter row functionality
	 */
	setupEvents() {

		// Toggle filter settings visibility
		this.filter.querySelectorAll( 'a.edit-filter,a.sf-close ' ).forEach( link => {
			link.addEventListener( 'click', ( e ) => {
				e.preventDefault();
				this.toggleOptions();
			} );
		} );

		// Duplicate filter
		this.filter.querySelector( 'a.duplicate-filter' ).addEventListener( 'click', ( e ) => {
			e.preventDefault();
			this.duplicate();
		} );

		// Remove filter
		this.filter.querySelector( 'a.remove-filter' ).addEventListener( 'click', ( e ) => {
			e.preventDefault();
			e.stopPropagation();
			this.remove();
		} );

		// Check if any of the fields has been changed
		this.filter.querySelectorAll( `[name^="${sf_admin.prefix}"]` ).forEach( input => {
			input.addEventListener( 'change', () => {
				this.save();
			} );
		} );

		// Update menu_order when filter position have changed
		this.filter.addEventListener( 'orderChanged', ( e ) => {
			const menu_order = this.getInput( 'menu_order' );

			// If order value changed, dispatch change event to save file in POST
			if( parseInt( menu_order.value ) !== parseInt( e.detail )  ) {
				menu_order.dispatchEvent( new Event('change') );
			}
			menu_order.value = e.detail;
		} );

		// Update label when label's input is changed
		this.getInput( 'label' ).addEventListener( 'focusout', ( e ) => {
			this.filter.querySelector( '.sf-row__label' ).innerText = e.target.value.trim();
		} );
	}

	/**
	 * Initialize color picker
	 */
	initColorPicker() {
		const colorControl = new ColorControl( this.filter.querySelectorAll( '.sf-color__field' ) );
		colorControl.init();
	}

	/**
	 * If either source or one of the options was changed check
	 * selected source and option and pass it to get color
	 */
	setupColorControl() {

		const options = [
			'sources',
			'attributes',
			'product_cat',
			'product_tag'
		]
		options.forEach( key => {
			const input = this.getInput( key );

			// Skip options with no source select
			if ( input === null ) return '';

			input.addEventListener( 'change', () => {
				let source = this.getInput( 'sources' ).value;
				addLoader( this.filter.querySelector( '.sf-color' ).closest( '.sf-option' ) );
				this.getColorOptions( source, this.getInput( source ).value );
			} );
		} );
	}

	/**
	 * Make AJAX request to get new color settings
	 */
	getColorOptions( taxonomy, term_id ) {
		fetch( sf_admin.ajax_url, {
			method: 'POST',
			body: new URLSearchParams( {
				action: 'sf/get_color_options',
				nonceAjax: sf_admin.ajax_nonce,
				taxonomy: taxonomy,
				term_id: term_id,
				filter_id: this.id
			} ),
		} ).then( ( response ) => {
			return response.text();
		} ).then( ( text ) => {
			this.updateColorOptions( text );
		} );
	}

	/**
	 * Update color options with data from AJAX request
	 */
	updateColorOptions( text ) {

		// Replace existing color option with new
		let frag = document.createRange().createContextualFragment( text );
		this.filter.querySelector( '.sf-color' ).closest( 'td' ).innerHTML = frag.querySelector( '.sf-color' ).outerHTML;

		// Reinitialize color picker
		this.initColorPicker();
		removeLoader();
	}

	/**
	 * Toggle visibility of fitter's options
	 *
	 */
	toggleOptions( speed = 300 ) {
		const options = this.filter.querySelector( '.sf-filter__options' );
		jQuery( options ).slideToggle( speed );
		this.filter.classList.toggle( 'open' );
	}

	/**
	 * Change displayed source option
	 */
	setupSources() {
		const sources = this.getInput( 'sources' );

		this.changeSourceDisplay( sources.value );

		sources.addEventListener( 'change', ( e ) => {
			this.changeSourceDisplay( e.target.value );
		} );
	}

	/**
	 * Display only selected source
	 */
	changeSourceDisplay( selected ) {
		const attributes = this.getInput( 'attributes' ).closest( '.sf-option' );
		const categories = this.getInput( 'product_cat' ).closest( '.sf-option' );

		attributes.style.display = selected === 'attributes' ? 'table-row' : 'none';
		categories.style.display = selected === 'product_cat' ? 'table-row' : 'none';
	}

	/**
	 * Duplicate filter and update all of its keys with new unique one
	 */
	duplicate() {
		// Generate new unique ID
		const unique_id = uniqid()

		// Clone the node and update key and ID
		let new_filter = this.filter.cloneNode( true );
		new_filter = this.replaceFilterID( new_filter, this.raw_id, unique_id );

		// Insert new row and update ID
		this.filter.insertAdjacentElement( 'afterend', new_filter );
		new_filter.dataset.filter_id = sf_admin.prefix + '-' + unique_id;

		// Setup new filter
		this.setupDuplicatedFilter( new_filter );

		// Close current filter if it is open
		if ( this.filter.classList.contains( 'open' ) ) {
			this.toggleOptions();
			this.filter.querySelector( '.sf-filter__options' ).style.display = 'none';
		}

		// Update filter numbers
		updateOrderNumbers();
	}

	/**
	 * Instantiate new filter and set its options
	 */
	setupDuplicatedFilter( filter ) {
		const new_filter = new AdminFilter( filter );

		// Update field label
		const label = filter.querySelector( '.sf-row__label' );
		label.innerText = label.innerText.trim() + ' ' + sf_admin.locale.copy;
		new_filter.getInput( 'label' ).value = label.innerText;

		// Open the filter
		new_filter.toggleOptions();
	}

	/**
	 * Replace all IDs in a row to new ID
	 */
	replaceFilterID( filter, old_id, new_id ) {
		const elements = filter.querySelectorAll( 'label, input, select' );

		elements.forEach( ( ele ) => {
			if ( ele.hasAttribute( 'id' ) ) {
				ele.id = ele.id.replace( old_id, new_id );
			}
			if ( ele.hasAttribute( 'name' ) ) {
				ele.setAttribute( 'name', ele.getAttribute( 'name' ).replace( old_id, new_id ) );
			}
			if ( ele.hasAttribute( 'for' ) ) {
				ele.setAttribute( 'for', ele.getAttribute( 'for' ).replace( old_id, new_id ) );
			}
		} );

		return filter;
	}

	/**
	 * Remove filter with a confirmation tooltip
	 */
	remove() {
		// Create confirmation tooltip
		const buttons = this.placeRemoveTooltip();

		// Remove tooltip when clicked outside the box
		buttons.remove.addEventListener( 'focusout', () => {
			setTimeout( () => {
				this.removeTooltips();
			} );
		} );

		// Cancel remove
		buttons.cancel.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			this.removeTooltips();
		} );

		// Remove filter
		buttons.remove.addEventListener( 'click', ( e ) => {
			e.preventDefault();
			e.stopPropagation();

			this.removeTooltips();
			this.saveRemovedID();

			jQuery( this.filter ).slideToggle( 300 );
			setTimeout( () => {
				this.filter.remove();
				updateOrderNumbers();
			}, 300 );
		} );
	}

	/**
	 * Add removed filter ID to hidden input to pass it via POST
	 */
	saveRemovedID() {
		let input = document.getElementById( 'sf-removed-fields' );
		input.value = input.value + '|' + this.raw_id;
	}

	/**
	 * Display confirmation tooltip for filter removal
	 */
	placeRemoveTooltip() {
		const tooltip = document.createElement( 'div' );
		tooltip.classList.add( 'sf-remove-tooltip' );
		tooltip.innerHTML = `${sf_admin.locale.sure}<a href="#" data-event="remove">${sf_admin.locale.delete}</a><a href="#" data-event="cancel">${sf_admin.locale.cancel}</a>`;

		// Append tooltip to remove button
		this.filter.querySelector( '.remove-filter' ).insertAdjacentElement( 'afterend', tooltip );

		const buttons = {
			remove: this.filter.querySelector( 'a[data-event="remove"]' ),
			cancel: this.filter.querySelector( 'a[data-event="cancel"]' )
		}
		buttons.remove.focus();
		return buttons;
	}

	/**
	 * Delete any removal tooltip
	 */
	removeTooltips() {
		const tooltips = document.querySelectorAll( '.sf-remove-tooltip' );
		tooltips.forEach( ( current ) => {
			current.remove();
		} );
	}

	/**
	 * Mark field to be saved on form submit
	 */
	save() {
		this.filter.dataset.save = true;
	}

	submit() {
	}
}

