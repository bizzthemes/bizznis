/**
 * This file controls the behaviours within the Bizznis theme.
 *
 * Note that while this version of the file include 'use strict'; at the function level,
 * the Closure Compiler version strips that away. This is fine, as the compiler may
 * well be doing things that are not use strict compatible.
 *
 * @author BizzThemes
 */

/**
 * Holds Bizznis values in an object to avoid polluting global namespace.
 *
 * @since 1.0.0
 */
window['bizznis'] = {

	settingsChanged: false,

	/**
	 * Inserts a category checklist toggle button and binds the behaviour.
	 *
	 * @since 1.0.0
	 */
	category_checklist_toggle_init: function () {
		'use strict';

		// Insert toggle button into DOM wherever there is a category checklist
		jQuery('<p><span id="bizznis-category-checklist-toggle" class="button">' + bizznisL10n['categoryChecklistToggle'] + '</span></p>').insertBefore('ul.categorychecklist');

		// Bind the behaviour to click
		jQuery(document).on('click.bizznis.bizznis_category_checklist_toggle', '#bizznis-category-checklist-toggle', bizznis.category_checklist_toggle);
	},

	/**
	 * Provides the behaviour for the category checklist toggle button.
	 *
	 * On the first click, it checks all checkboxes, and on subsequent clicks it
	 * toggles the checked status of the checkboxes.
	 *
	 * @since 1.0.0
	 */
	category_checklist_toggle: function (event) {
		'use strict';

		// Cache the selectors
		var $this = jQuery(event.target),
			checkboxes = $this.parent().next().find(':checkbox');

		// If the button has already been clicked once, clear the checkboxes and remove the flag
		if ($this.data('clicked')) {
			checkboxes.removeAttr('checked');
			$this.data('clicked', false);
		} else { // Mark the checkboxes and add a flag
			checkboxes.attr('checked', 'checked');
			$this.data('clicked', true);
		}
	},

	/**
	 * Grabs the array of toggle settings and loops through them to hook in
	 * the behaviour.
	 *
	 * The bizznis_toggles array is filterable in load-scripts.php before being
	 * passed over to JS via wp_localize_script().
	 *
	 * @since 1.0.0
	 */
	toggle_settings_init: function () {
		'use strict';

		jQuery.each(bizznis_toggles, function (k, v) {

			// Prepare data
			var data = {selector: v[0], show_selector: v[1], check_value: v[2]};

			// Setup toggle binding
			jQuery('div.bizznis-admin').on('change.bizznis.bizznis_toggle', v[0], data, bizznis.toggle_settings);

			// Trigger the check when page loads too.
			// Can't use triggerHandler here, as that doesn't bubble the event up to div.bizznis-admin.
			// We namespace it, so that it doesn't conflict with any other change event attached that
			// we don't want triggered on document ready.
			jQuery(v[0]).trigger('change.bizznis_toggle', data);
		});

	},

	/**
	 * Provides the behaviour for the change event for certain settings.
	 *
	 * Three bits of event data is passed - the jQuery selector which has the
	 * behaviour attached, the jQuery selector which to toggle, and the value to
	 * check against.
	 *
	 * The check_value can be a single string or an array (for checking against
	 * multiple values in a dropdown) or a null value (when checking if a checkbox
	 * has been marked).
	 *
	 * @since 1.0.0
	 */
	toggle_settings: function (event) {
		'use strict';

		// Cache selectors
		var $selector = jQuery(event.data.selector),
		    $show_selector = jQuery(event.data.show_selector),
		    check_value = event.data.check_value;

		// Compare if a check_value is an array, and one of them matches the value of the selected option
		// OR the check_value is _unchecked, but the checkbox is not marked
		// OR the check_value is _checked, but the checkbox is marked
		// OR it's a string, and that matches the value of the selected option.
		if (
			(jQuery.isArray(check_value) && jQuery.inArray($selector.val(), check_value) > -1) ||
				('_unchecked' === check_value && $selector.is(':not(:checked)')) ||
				('_checked' === check_value && $selector.is(':checked')) ||
				('_unchecked' !== check_value && '_checked' !== check_value && $selector.val() === check_value)
		) {
			jQuery($show_selector).slideDown('fast');
		} else {
			jQuery($show_selector).slideUp('fast');
		}

	},

	/**
	 * When a input or textarea field field is updated, update the character counter.
	 *
	 * For now, we can assume that the counter has the same ID as the field, with a _chars
	 * suffix. In the future, when the counter is added to the DOM with JS, we can add
	 * a data('counter', 'counter_id_here' ) property to the field element at the same time.
	 *
	 * @since 1.0.0
	 */
	update_character_count: function (event) {
		'use strict';
		//
		jQuery('#' + event.target.id + '_chars').html(jQuery(event.target).val().length.toString());
	},

	/**
	 * Provides the behaviour for the layout selector.
	 *
	 * When a layout is selected, the all layout labels get the selected class
	 * removed, and then it is added to the label that was selected.
	 *
	 * @since 1.0.0
	 */
	layout_highlighter: function (event) {
		'use strict';

		// Cache class name
		var selected_class = 'selected';

	    // Remove class from all labels
	    jQuery('input[name="' + jQuery(event.target).attr('name') + '"]').parent('label').removeClass(selected_class);

	    // Add class to selected layout
	    jQuery(event.target).parent('label').addClass(selected_class);

	},

	/**
	 * Helper function for confirming a user action.
	 *
	 * @since 1.0.0
	 */
	confirm: function (text) {
		'use strict';

		return confirm(text);

	},

	/**
	 * Have all form fields in Bizznis admin set a dirty flag when changed.
	 *
	 * @since 1.0.0
	 */
	attachUnsavedChangesListener: function () {
		'use strict';

		jQuery('div.bizznis-admin :input').change(function(){
			bizznis.registerChange();
		});
		window.onbeforeunload = function(){
			if ( bizznis.settingsChanged )
				return bizznisL10n['saveAlert'];
		};
		jQuery('div.bizznis-admin input[type="submit"]').click(function(){
			window.onbeforeunload = null;
		});
	},

	/**
	 * Set a flag, to indicate form fields have changed.
	 *
	 * @since 1.0.0
	 */
	registerChange: function () {
		'use strict';

		bizznis.settingsChanged = true;
	},

	/**
	 * Ask user to confirm that a new version of Bizznis should now be installed.
	 *
	 * @since 1.0.0
	 */
	confirmUpgrade: function () {
		'use strict';

		return confirm(bizznisL10n['confirmUpgrade']);
	},

	/**
	 * Ask user to confirm that settings should now be reset.
	 *
	 * @since 1.0.0
	 */
	confirmReset: function () {
		'use strict';

		return confirm(bizznisL10n['confirmReset']);
	},

	/**
	 * Initialises all aspects of the scripts.
	 *
	 * Generally ordered with stuff that inserts new elements into the DOM first,
	 * then stuff that triggers an event on existing DOM elements when ready,
	 * followed by stuff that triggers an event only on user interaction. This
	 * keeps any screen jumping from occuring later on.
	 *
	 * @since 1.0.0
	 */
	ready: function () {
		'use strict';

		// Move all messages below our floated buttons
		jQuery('h2').nextAll('div.updated, div.error').insertAfter('p.top-buttons');

		// Initialise category checklist toggle button
		bizznis.category_checklist_toggle_init();

		// Initialise settings that can toggle the display of other settings
		bizznis.toggle_settings_init();

		// Initialise form field changing flag.
		bizznis.attachUnsavedChangesListener();

		// Bind character counters
		jQuery('#bizznis_title, #bizznis_description').on('keyup.bizznis.bizznis_character_count', bizznis.update_character_count);

		// Bind layout highlighter behaviour
		jQuery('.bizznis-layout-selector').on('change.bizznis.bizznis_layout_selector', 'input[type="radio"]', bizznis.layout_highlighter);

		// Bind upgrade confirmation
		jQuery('.bizznis-js-confirm-upgrade').on('click.bizznis.bizznis_confirm_upgrade', bizznis.confirmUpgrade);

		// Bind reset confirmation
		jQuery('.bizznis-js-confirm-reset').on('click.bizznis.bizznis_confirm_reset', bizznis.confirmReset);

	}

};

jQuery(bizznis.ready);

/**
 * Helper function for confirming a user action.
 *
 * @since 1.0.0
 */
function bizznis_confirm(text) {
	'use strict';
	return bizznis.confirm(text);
}
