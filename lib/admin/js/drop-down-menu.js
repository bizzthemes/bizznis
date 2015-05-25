/**
 * This script adds keyboard accessibility to a drop down menu.
 *
 * @since 1.2.0
 *
 * need the following css to make it work:
 *
 *  .menu .menu-item:focus {
 * 	  position: static;
 *  }
 *
 *  .menu .menu-item > a:focus + ul.sub-menu,
 *  .menu .menu-item.bizznis-hover > ul.sub-menu {
 * 	  left: auto;
 * 	  opacity: 1;
 *  }
 *
*/

var bizznis_drop_down_menu = ( function( $ ) {
	'use strict';

	/**
	 * Add class to menu item on hover.
	 *
	 * @since 1.2.0
	 */
	var menuItemEnter = function() {
		$( this ).addClass( 'bizznis-hover' );
	},

	/**
	 * Remove a class when focus leaves menu item.
	 *
	 * @since 1.2.0
	 */
	menuItemLeave = function() {
		$( this ).removeClass( 'bizznis-hover' );
	},

	/**
	 * Toggle menu item class when a link fires a focus or blur event.
	 *
	 * @since 1.2.0
	 */
	menuItemToggleClass = function() {
		$( this ).parents( '.menu-item' ).toggleClass( 'bizznis-hover' );
	},

	/**
	 * Bind behaviour to events.
	 *
	 * @since 1.2.0
	 */
	ready = function() {
		$( '.menu li' )
			.on( 'mouseenter.bizznis-hover', menuItemEnter )
			.on( 'mouseleave.bizznis-hover', menuItemLeave )
			.find( 'a' )
			.on( 'focus.bizznis-hover blur.bizznis-hover', menuItemToggleClass );
	};

	// Only expose the ready function to the world
	return {
		ready: ready
	};

})( jQuery );

jQuery( bizznis_drop_down_menu.ready );
