/*global $:false, jQuery:false, document:false, wp:false location:false */
/**
 * Envoke Supersized - Settings Page Scripts
 *
 * Copyright (c) 2013 Chris Marslender
 * Licensed under the GPLv2+ license.
 */

( function( window, undefined ) {
	'use strict';

	jQuery(document).ready(function($) {
		var $tabs_container = $('#enss-tab-wrapper');

		$tabs_container.on('click', 'a', function() {
			var $clickedTab = $(this);

			set_active_tab( $clickedTab.attr('href') );
		});

		function set_active_tab( hash ) {
			var $newTab = $tabs_container.find('[href="'+hash+'"]');
			//Set the active tab
			$tabs_container.find('.nav-tab-active').removeClass('nav-tab-active');
			$newTab.addClass('nav-tab-active');

			//Set the active content area
			$('.enss-panel.active').removeClass('active').addClass('hidden');
			$(hash).addClass('active').removeClass('hidden');
		}

		if ( location.hash ) {
			set_active_tab( location.hash );
		}
	});

} )( this );