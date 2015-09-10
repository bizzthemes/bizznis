/**
 * This script adds responsive menus
 *
 * @since 1.3.0
 *
 * need the following css to make it work:
 *
 *  @media only screen and (max-width: 768px) {
 *  
 *  	.mobile-menu-icon {
 *  		cursor: pointer;
 *  		display: block;
 *  	}
 *  
 *  	.mobile-menu-icon::before {
 *  	 content: "\00b7 \00b7 \00b7";
 *  		display: block;
 *  		font-size: 70px;
 *  		line-height: 0;
 *  		margin: 0 auto;
 *  		padding: 25px 0 30px;
 *  		text-align: center;
 *  	}
 *  	
 *  	.nav-responsive .menu-bizznis,
 *  	.nav-responsive .menu-bizznis-extra {
 *  		display: none;
 *  	}
 *  
 *  	.nav-mobile .menu-bizznis,
 *  	.nav-mobile .menu-item,
 *  	.nav-mobile .menu-bizznis-extra {
 *  		clear: both;
 *  		display: block;
 *  	}
 *  
 *  	.nav-responsive li a {
 *  		display: block;
 *  	}
 *  
 *  	.nav-responsive .menu-item:hover {
 *  		position: static;
 *  	}
 *  
 *  	.nav-responsive .menu-item:hover > .sub-menu {
 *  		left: 0;
 *  		margin-left: 0;
 *  	}
 *  
 *  	.nav-responsive .menu-item-has-children {
 *  		cursor: pointer;	
 *  	}
 *  	
 *  	.nav-responsive .menu-item-has-children > a:after {
 *  		content: '';
 *  	}
 *  
 *  	.nav-responsive .menu-bizznis > .menu-item-has-children:before {
 *  		content: "\25bc";
 *  		float: right;
 *  		font-size: 15px;
 *  		line-height: 1;
 *  		padding: 24px 18px;
 *  	}
 *  
 *  	.nav-responsive .menu-open.menu-item-has-children:before {
 *  		content: "\25b2";
 *  	}
 *  
 *  	.nav-responsive .menu-bizznis  > .menu-item > .sub-menu {
 *  		display: none;
 *  	}
 *  
 *  	.nav-responsive .menu-item:hover > .sub-menu > .menu-item:hover > .sub-menu,
 *  	.nav-responsive .sub-menu {
 *  		left: auto;
 *  		opacity: 1;
 *  		position: relative;
 *  		-moz-transition:    opacity .4s ease-in-out;
 *  		-ms-transition:     opacity .4s ease-in-out;
 *  		-o-transition:      opacity .4s ease-in-out;
 *  		-webkit-transition: opacity .4s ease-in-out;
 *  		transition:         opacity .4s ease-in-out;
 *  		width: 100%;
 *  		z-index: 99;
 *  	}
 *  
 *  	.nav-responsive .sub-menu li a,
 *  	.nav-responsive .sub-menu li a:hover,
 *  	.nav-responsive .sub-menu .sub-menu {
 *  		margin: 0;
 *  		position: relative;
 *  		width: 100%;
 *  	}
 *  	
 *  }
 *
*/

( function( $ ) {
	
	/**
	 * Selectors.
	 *
	 * @since 1.3.0
	 */
	var nav_selector = $( 'nav[class*=nav-' + bizzmenuL10n['selector'] + ']' );
	var menu_selector = $( 'nav[class*=nav-' + bizzmenuL10n['selector'] + '] .menu-bizznis' );
	
	/**
	 * Add mobile menu icons.
	 *
	 * @since 1.3.0
	 */
	nav_selector.addClass( 'nav-responsive' );
	menu_selector.before( '<div class="mobile-menu-icon"></div>' );
	
	/**
	 * Events on mobile menu icon click.
	 *
	 * @since 1.3.0
	 */
	$( '.mobile-menu-icon' ).on( 'click', function() {
		nav_selector.toggleClass( 'nav-mobile' );
		$( 'body' ).toggleClass( 'nav-mobile-open' );
		return false;
	});
	
	/**
	 * Toggle submenus	.
	 *
	 * @since 1.3.0
	 */
	$( '.nav-responsive' ).find( '.menu-bizznis > .menu-item' ).live( 'click', function( event ) {
		if ( event.target !== this ) {
			return;
		}
		$( this ).find( '.sub-menu:first' ).slideToggle( function() {
			$( this ).parent().toggleClass( 'menu-open' );
		});
	});
	
} )( jQuery );
