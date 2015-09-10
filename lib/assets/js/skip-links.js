/**
 * This file fixes the browser bug for skip-links: While the visual focus of the browser shifts to the element being linked to, the input focus stays where it was.
 * Affects Internet Explorer and Chrome
 * http://www.nczonline.net/blog/2013/01/15/fixing-skip-to-content-links/
*/

function ga_skiplinks() {
    'use strict';
    var element = document.getElementById( location.hash.substring( 1 ) );

    if ( element ) {
        if ( ! /^(?:a|select|input|button|textarea)$/i.test( element.tagName ) ) {
            element.tabIndex = -1;
        }
        element.focus();
    }
}

if ( window.addEventListener ) {
    window.addEventListener( 'hashchange', ga_skiplinks, false );
} else { // IE8 and earlier
    window.attachEvent( 'onhashchange', ga_skiplinks, false );
}
