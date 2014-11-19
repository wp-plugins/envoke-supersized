/*! Envoke Supersized - v2.2.0
 * http://envokedesign.com
 * Copyright (c) 2014; * Licensed GPLv2+ */
/**
 * Envoke Supersized - Per Post Override Scripts
 *
 * Copyright (c) 2013 Chris Marslender
 * Licensed under the GPLv2+ license.
 */
 
( function( window, undefined ) {
	'use strict';

	jQuery(document).ready(function($) {
		var _custom_media = true,
			_orig_send_attachment = wp.media.editor.send.attachment,
			$meta_box_container = $('.enss-per-post-internal-input'),
			$internal_radio = $('#enss-image-input-type-internal'),
			$external_radio = $('#enss-image-input-type-external');

		$meta_box_container.on( 'click', '[data-enss-action="choose-image"]', function(e) {
			var send_attachment_bkp = wp.media.editor.send.attachment,
				$button = $(this),
				$id = $button.attr('data-enss-for'),
				$preview = $($button.attr('data-enss-preview'));
			_custom_media = true;
			wp.media.editor.send.attachment = function(props, attachment){
				if ( _custom_media ) {
					$($id).val(attachment.id);
					$preview.html('<img src="'+attachment.sizes.thumbnail.url+'" />');
				} else {
					return _orig_send_attachment.apply( this, [props, attachment] );
				}
			};

			wp.media.editor.open($button);
		});

		$meta_box_container.on( 'click', '[data-enss-action="clear-image"]', function(e) {
			var $button = $(this),
				$id = $($button.attr('data-enss-for')),
				$preview = $($button.attr('data-enss-preview'));

			$id.val('');
			$preview.remove();
		});

		$internal_radio.on('change', function() {
			var $me = $(this);
			if ( $me.is(':checked') ) {
				show_internal();
			}
		});

		$external_radio.on('change', function() {
			var $me = $(this);
			if ( $me.is(':checked') ) {
				show_external();
			}
		});

		$('.add_media').on('click', function() {
			_custom_media = false;
		});

		function show_internal() {
			$('.enss-per-post-internal-input').removeClass('hidden');
			$('.enss-per-post-external-input').addClass('hidden');
		}

		function show_external() {
			$('.enss-per-post-external-input').removeClass('hidden');
			$('.enss-per-post-internal-input').addClass('hidden');
		}
	});

} )( this );