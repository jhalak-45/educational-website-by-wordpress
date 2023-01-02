(function ($) {
	$(document).ready(function () {

		var menuImageUpdate = function( item_id, thumb_id, is_hover ) {
			$( '.wp-core-ui .media-modal-icon').trigger( 'click' );
			wp.media.post( 'set-menu-item-thumbnail', {
				json:         true,
				post_id:      item_id,
				thumbnail_id: thumb_id,
				is_hover:     is_hover ? 1 : 0,
				_wpnonce:     menuImage.settings.nonce
			}).done( function( html ) {
				$('.menu-item-images').html( html );

					$( '.menu-item-preview .dashicons' ).remove();
					var new_content_preview = $( '.menu-item-preview span' );

					$( '.menu-item-preview i, .menu-item-preview svg, .menu-item-preview img' ).remove();
					$( '.menu-item-preview' ).append( new_content_preview );
					$( '.menu-item-preview .title-text' ).prepend( $( '.menu-item-images .set-post-thumbnail img').clone() );
					$( '.menu-image-item-settings-content .menu-item-preview' ).append( '<div class="menu-image-toast">Image was saved successfully</div>' ).fadeIn();
					setTimeout(() => {
						$( '.menu-image-toast' ).fadeOut( '500' ).remove();
					}, 2000);
				});
			

		};

		$(document).on( 'click', '.button.set-post-thumbnail', function (e) {
			e.preventDefault();
			e.stopPropagation();

			var item_id = $( '.menu-image-item-settings-content' ).attr( 'data-menu-item-id' );
			var is_hover = $(this).hasClass('hover-image');
			var uploader = wp.media({
					title: menuImage.l10n.uploaderTitle, // todo: translate
					button: { text: menuImage.l10n.uploaderButtonText },
					multiple: false
				}).on('select', function () {
					var attachment = uploader.state().get('selection').first().toJSON();
					menuImageUpdate( item_id, attachment.id, is_hover );
				}).open();
		})

		$(document).on( 'click', '.menu-item-image-options .remove-post-thumbnail', function (e) {
			e.preventDefault();
			e.stopPropagation();

			var item_id = $( '.menu-image-item-settings-content' ).attr( 'data-menu-item-id' );

			if ( $( this).hasClass( 'hover-image' ) ) {
				menuImageUpdate( item_id, -1, 1 );
			} else {
				menuImageUpdate( item_id, -1, 0 );
			}

		})

		// Control the visibility of the fields related to the option selected.
		$( document ).on( 'change', 'input[name="menu_item_image_title_position"]', function(){

			var title_position = $( this ).val();

			if ( title_position == 'above'  || title_position == 'before' ) {
				$( '.menu-item-preview .title-text' ).append( jQuery( '.menu-item-preview .title-text i, .menu-item-preview .title-text img, .menu-item-preview .title-text svg, .menu-item-preview .title-text .dashicons' ).detach());
			} else {
				$( '.menu-item-preview .title-text' ).prepend( jQuery( '.menu-item-preview .title-text img, .menu-item-preview .title-text i, .menu-item-preview .title-text svg, .menu-item-preview .title-text .dashicons' ).detach());
			}

			$( '.menu-item-preview' ).removeClass().addClass( title_position + '-title' ).addClass( 'menu-item-preview' );

		});

		// Control the visibility of the fields related to the option selected.
		$( document ).on( 'change', 'input[name="menu_item_image_type"]', function(){
			$( '.menu-item-icon-type, .menu-item-image-type' ).hide();
			$( '.menu-item-' + $( this ).val() + '-type' ).show();

			if ( $( this ).val() == 'image' ) {
				$( '.menu-item-preview i, .menu-item-preview svg, .menu-item-preview img, .menu-item-preview .dashicons' ).remove();
				$( '.menu-item-preview .title-text' ).prepend( $( '.menu-item-images .set-post-thumbnail img').clone() );
			} else {
				$( '.menu-item-preview i, .menu-item-preview svg, .menu-item-preview img' ).remove();

					if ( $( '.menu-item-icon-selected' ).hasClass('fa-2x') ) {
						$( '.menu-item-preview span' ).first().prepend( $( '.menu-item-icon-selected' ).clone().removeClass('fa-2x').addClass('fa-1x') );
					} else {
						$( '.menu-item-preview span' ).first().prepend( $( '.menu-item-icon-selected' ).clone() );
					}

			}
		});

		$( document ).on( 'change', '#menu_item_image_notification', function(){
			switch( $(this).val() ) {
				case 'none':
						$( '.menu-item-preview .menu-image-badge' ).hide();
						$( '.menu-item-preview .menu-image-bubble' ).hide();
						$( '.menu-image-badge-type' ).hide();
						$( '.menu-image-bubble-type' ).hide();
						break;
				case 'bubble':
						$( '.menu-item-preview .menu-image-badge' ).hide();
						$( '.menu-item-preview .menu-image-bubble' ).toggle();
						$( '.menu-image-badge-type' ).hide();
						$( '.menu-image-bubble-type' ).show();
						break;
				case 'badge':
						$( '.menu-item-preview .menu-image-bubble' ).hide();
						$( '.menu-item-preview .menu-image-badge' ).toggle();
						$( '.menu-image-badge-type' ).show();
						$( '.menu-image-bubble-type' ).hide();
						break;
			}
		});
		
		// Control the visibility of the fields related to the button options.
		$( document ).on( 'click', 'input[name="menu_item_button"]', function(){
			$( '.menu-item-button-type' ).toggle();
			updateMenuItemPreview();
		});

		// Close the settings overlay.
		$( document ).on( 'click', '.menu-image-close-overlay' , function () {

			$( '.menu-image-item-settings-content' ).fadeOut();
			$( '.menu-image-item-settings-overlay' ).fadeOut();
			$( 'body').css( 'overflow-y', 'initial' );
			return false;

		});

	});

	$( '#menu-to-edit li.menu-item' ).each( function() {

		var menu_item = $(this);
		$( '.item-title', menu_item ).append( $( "<i class='menu-image-item-settings mob-icon-mobile-2'><span class='dashicons dashicons-admin-generic'></span><span>Menu Image</span></i>" ) );

	});

	// Update Preview - When changing Border Radius.
	$( document ).on( 'change', '#menu_item_image_border_radius, #menu_item_image_bt_padding_top, #menu_item_image_bt_padding_bottom, #menu_item_image_bt_padding_left, #menu_item_image_bt_padding_right', function (){
		updateMenuItemPreview();
	});

	// Update Preview - When changing Button Color.
	$( document ).on( 'change', 'input[name=menu_item_image_button_style]', function( e ) {
		var color = $( '.menu-image-button-color' ).val();

		if ( $( this ).val() == 'outline' ) {
			$( '.menu-item-preview .title-text' ).css( 'border', '1px solid ' + color );
		} else {
			$( '.menu-item-preview .title-text' ).css( 'border', '1px solid #F3F3F3' );
		}
	
	});

	$( document ).on( 'keyup', '#menu_item_badge_text', function(){
		$( '.menu-image-badge' ).text( $( this ).val() );
	});

	$( document ).on( 'click', '.menu-image-fontawesome-list svg, .menu-image-fontawesome-list i, .menu-image-dashicons-list .dashicons', function(){

		$( '.menu-item-icon-selected' ).removeClass( 'menu-item-icon-selected' );
		$( this ).addClass( 'menu-item-icon-selected' );
		$( '.menu-item-preview i, .menu-item-preview svg, .menu-item-preview img, .menu-item-preview .dashicons' ).remove();

		var new_content_preview = $( '.menu-item-preview span' );

		$( '.menu-item-preview' ).append( new_content_preview );


		if ( $( '.menu-item-preview' ).hasClass( 'below-title' ) ||  $( '.menu-item-preview' ).hasClass( 'after-title' ) ) {
			$( '.menu-item-preview span' ).first().prepend( $( '.menu-item-icon-selected' ).clone().removeClass('fa-2x').addClass('fa-1x') );
		}
		if ( $( '.menu-item-preview' ).hasClass( 'above-title' ) ||  $( '.menu-item-preview' ).hasClass( 'before-title' ) ||  $( '.menu-item-preview' ).hasClass( 'hide-title' ) ) {
			$( '.menu-item-preview span' ).first().append( $( '.menu-item-icon-selected' ).clone().removeClass('fa-2x').addClass('fa-1x') );
		}
	});

	$( document ).on( 'click', '#menu-image-modal-header h2', function(){
		$( '#menu-image-modal-header h2' ).removeClass('active' );
		$( '.menu-image-container' ).hide();
		$( '.' + $( this ).attr('data-target')).show();
		$( this ).addClass( 'active' );
	});

	$( document ).on( 'click', '.menu-image-icons-list-header li', function(){
		$( '.menu-image-icons-list-header li, .menu-image-fontawesome-list, .menu-image-dashicons-list' ).removeClass( 'active' );
		$( '.menu-image-' + $( this ).attr( 'data-tab-id' ) ).addClass( 'active' );
		$( this ).addClass( 'active' );
	});

	$( document ).on( 'click', '.menu-image-icons-fa-list-header li', function(){
		$( '.menu-image-fa-regular-list, .menu-image-fa-solid-list, .menu-image-fa-brands-list, .menu-image-icons-fa-list-header li' ).removeClass( 'active' );
		$( '.menu-image-' + $( this ).attr( 'data-tab-id' ) ).addClass( 'active' );

		$( this ).addClass( 'active' );
	});

	$( document ).on( 'click', '.menu-image-item-settings-content #submit' , function( e ) {

		e.preventDefault();
		$( this ).attr( 'disabled', true);
		var menu_item_id            = $( '.menu-image-item-settings-content' ).attr( 'data-menu-item-id' );
		var border_radius           = $( '#menu_item_image_border_radius' ).val();
		var button_color            = $( '.menu-image-button-color' ).val();
		var button_bg_color         = $( '.menu-image-button-bg-color' ).val();
		var button_padding_top      = $( '#menu_item_image_bt_padding_top' ).val();
		var button_padding_left     = $( '#menu_item_image_bt_padding_left' ).val();
		var button_padding_right    = $( '#menu_item_image_bt_padding_right' ).val();
		var button_padding_bottom   = $( '#menu_item_image_bt_padding_bottom' ).val();
		var button_style            = $( 'input[name="menu_item_image_button_style"]:checked' ).val();
		var select_icon             = '';
		var title_position          = $( 'input[name="menu_item_image_title_position"]:checked' ).val();
		var item_image_type         = $( 'input[name="menu_item_image_type"]:checked' ).val();
		var item_image_size         = $( '#menu_item_image_size' ).val();
		var item_image_notification = $( '#menu_item_image_notification' ).val();
		var menu_item_button        = $( 'input[name="menu_item_button"]' ).is(':checked');
		var bubble_color            = $( '.menu-image-bubble-color' ).val();
		var bubble_func             = $( '#menu_item_bubble_number_func' ).val();
		var badge_text              = $( '#menu_item_badge_text' ).val();
		var badge_color             = $( '.menu-image-badge-color' ).val();

		var menuImageData = {
			action      :                   'set-menu-item-settings',
			menu_item_id:                   menu_item_id,
			menu_item_image_title_position: title_position,
			menu_item_image_type:           item_image_type,
			menu_item_image_size:           item_image_size,
			menu_item_nonce:                 menuImage.settings.nonce,
		}

		if ( $( '.menu-item-icon-selected' ).length > 0 ) {
			if (  $( '.menu-item-icon-selected' ).hasClass( 'dashicons') ) {
				var cloneIcon = $( '.menu-item-icon-selected' ).clone();
				cloneIcon.removeClass('menu-item-icon-selected').removeClass( 'dashicons' );
				select_icon = cloneIcon.attr('class').trim()
			} else {
				select_icon = $( '.menu-item-icon-selected' ).attr( 'data-prefix' ) + ' fa-' + $( '.menu-item-icon-selected' ).attr( 'data-icon' );
			}
			menuImageData.menu_image_icon = select_icon;

		}

		// Collect the Notification data (badges, bubble)
		menuImageData.menu_item_image_notification = item_image_notification;

		if ( item_image_notification == 'bubble' ) {
			menuImageData.bubble_color = bubble_color;
			menuImageData.bubble_func  = bubble_func;
		}

		if ( item_image_notification == 'badge' ) {
			menuImageData.badge_text   = badge_text;
			menuImageData.badge_color  = badge_color;
		}

		// Collect button data.
		menuImageData.menu_item_image_button         = menu_item_button;
		menuImageData.menu_item_image_button         = menu_item_button;
		menuImageData.menu_image_button_bg_color     = button_bg_color;
		menuImageData.menu_item_button_border_radius = border_radius;
		menuImageData.menu_image_button_color        = button_color;
		menuImageData.menu_image_button_style        = button_style;
		menuImageData.menu_image_button_pd_top       = button_padding_top;
		menuImageData.menu_image_button_pd_left      = button_padding_left;
		menuImageData.menu_image_button_pd_right     = button_padding_right;
		menuImageData.menu_image_button_pd_bottom    = button_padding_bottom;

		// Send the data to be saved.
		$.ajax({
				type: 'POST',
				url: ajaxurl,
				data:  menuImageData,
					success: function( response ) {
						$( '.menu-image-item-settings-content .menu-item-preview' ).append( '<div class="menu-image-toast">Changes saved successfully</div>' ).fadeIn();
						setTimeout(() => {
							$( '.menu-image-toast' ).fadeOut( '500' ).remove();
						}, 2000);

						$( 'input[name="submit"] ').attr('disabled' , false );
					}
		});

		e.preventDefault();
	});
	
	$( document ).on( 'click', '.menu-image-button' , function( e ) {
		e.preventDefault();
		$( this ).parent().prev().find('.menu-image-item-settings').click();
		$( 'body' ).append( '<div class="menu-image-item-settings-overlay"></div>' );
	});

	$( document ).on( 'click', '.menu-image-item-settings' , function( e ) {

		e.preventDefault();

		var menu_item  = $( this ).parent().parent().parent().parent();
		var menu_title = $(this).parent().parent().find( '.menu-item-title' ).text();
		var menu_id    = $( '#menu' ).val();
		var id         = parseInt( menu_item.attr( 'id' ).match(/[0-9]+/)[0], 10);
		$( 'body' ).append( '<div class="menu-image-item-settings-overlay"></div>' );
		$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					action:       'get_menu_image_item_settings',
					menu_item_id: id,
					menu_id:      menu_id,
					menu_title:   menu_title
					},

				success: function( response ) {
					if ( $( '.menu-image-item-settings-content' ).length ) {
						$( '.menu-image-item-settings-content' ).replaceWith( response );
						$( '.menu-image-item-settings-overlay' ).fadeIn();
					} else {
						$( 'body' ).append( response );
					}

					$( 'body').css( 'overflow-y', 'hidden' );
					$( '#menu-image-modal-body' ).scrollTop( $( '.mobmenu-item-selected' ).offset() - 250 );
					$( '.menu-image-item-settings-content' ).attr( 'data-menu-id', menu_id );
					$( '.menu-image-item-settings-content' ).attr( 'data-menu-item-id' , id );

					if  (  $( 'input[name="menu_item_image_type"]:checked' ).val() == 'image' ) {
						// Load Menu Images on Preview.
						if ( $( '.menu-item-images .set-post-thumbnail img' ).length > 0 ) {
							$( '.menu-item-preview .title-text' ).prepend( $( '.menu-item-images .set-post-thumbnail img' ).clone() );
						}
						$( '.menu-item-image-type' ).show();
						$( '.menu-item-icon-type' ).hide();
					} else {
						$( '.menu-item-icon-type' ).show();
						$( '.menu-item-image-type' ).hide();
						$( '.menu-item-preview span' ).first().prepend( $( '.menu-item-icon-selected' ).clone().removeClass('fa-2x').addClass('fa-1x') );
					}

					// Load Badge color.
					$( '.menu-image-badge' ).css( 'background-color', $( '.menu-image-badge-color' ).val() );

					// Load Bubble color.
					$( '.menu-image-bubble' ).css( 'background-color', $( '.menu-image-bubble-color' ).val() );

					// Load menu item notification.
					$( '#menu_item_image_notification' ).trigger( 'change' );

					// Set the icons active tab.
					if (  $( '.menu-item-icon-selected' ).hasClass( 'fa-2x') ) {
						$( '.menu-image-icons-list-header li[data-tab-id="fontawesome-list"]' ).addClass( 'active' );
					}  else {
						$( '.menu-image-icons-list-header li[data-tab-id="dashicons-list"]' ).addClass( 'active' );
					}

					// Load the Menu item as button option.
					if ( $( 'input[name="menu_item_button"]' ).is( ':checked' ) ) {
						$( '.menu-item-button-type' ).toggle();
						updateMenuItemPreview();
					}

					$( '.menu-image-item-settings-content .menu-item-preview').hide();

					setTimeout(() => {
						// Load Title Position.
						$( 'input[name="menu_item_image_title_position"]:checked' ).trigger( 'change' );
						$( '.menu-image-item-settings-content .menu-item-preview').fadeIn();
					}, 200);

					$( '.menu-image-color-picker').wpColorPicker({
						/**
						 * @param {Event} event - standard jQuery event, produced by whichever
						 * control was changed.
						 * @param {Object} ui - standard jQuery UI object, with a color member
						 * containing a Color.js object.
						 */
						change: function (event, ui) {
							var element = event.target;
							var color = ui.color.toString();

							// Button Color.
							if ( $( element ).hasClass( 'menu-image-button-color' ) ) {
								
								if ( $( 'input[name=menu_item_image_button_style]:checked' ).val() == 'outline' ) {
									$( '.menu-item-preview .title-text' ).css( 'border', '1px solid ' + color );
								}
								$( '.menu-item-preview .title-text' ).css('color', color );
							}

							// Button Background Color.
							if ( $( element ).hasClass( 'menu-image-button-bg-color' ) ) {
								$( '.menu-item-preview .title-text' ).css( 'background-color', color );
							}

							// Bubble Color.
							if ( $( element ).hasClass( 'menu-image-bubble-color' ) ) {
								$( '.menu-image-bubble' ).css( 'background-color', color );
							}

							// Badge Color.
							if ( $( element ).hasClass( 'menu-image-badge-color' ) ) {
								$( '.menu-image-badge' ).css( 'background-color', color );
							}
						},
					});
				}

			});

			e.stopPropagation();
			return false;
});

function updateMenuItemPreview() {

	var color         = '#444';
	var bgcolor       = '#FFF';
	var border_radius = '10';
	var pad_top       = '0';
	var pad_bottom    = '0';
	var pad_left      = '0';
	var pad_right     = '0';
	
	// If button option is enabled.
	if ( $( 'input[name="menu_item_button"]' ).is(':checked') ) {

		border_radius = $('#menu_item_image_border_radius' ).val() + 'px';
		color         = $( '.menu-image-button-color' ).val();
		bgcolor       = $( '.menu-image-button-bg-color' ).val();
		pad_top       = $( '#menu_item_image_bt_padding_top' ).val();
		pad_bottom    = $( '#menu_item_image_bt_padding_bottom' ).val();
		pad_left      = $( '#menu_item_image_bt_padding_left' ).val();
		pad_right     = $( '#menu_item_image_bt_padding_right' ).val();

		// Check if the button style is Outline or Fill.
		if ( $( 'input[name=menu_item_image_button_style]:checked' ).val() == 'outline' ) {
			$( '.menu-item-preview .title-text' ).css( 'border', '1px solid ' + color );
		}

		// Update Preview - When changing Padding Fields.
		$( '.menu-item-preview .title-text' ).css( 'padding',   pad_top + 'px ' + pad_right + 'px ' + pad_bottom + 'px ' + pad_left + 'px ');
	} else {
		$( '.menu-item-preview .title-text' ).css( 'border', '1px solid #FFF' );
	}

	$( '.menu-item-preview .title-text' ).css( 'border-radius', border_radius );
	$( '.menu-item-preview .title-text' ).css( 'color', color );
	$( '.menu-item-preview .title-text' ).css( 'background-color', bgcolor );
}
$( document ).on( 'click', '.wp-menu-image-notice .notice-dismiss' , function( e ) {
        
	$.ajax({
			  type: 'POST',
			  url: ajaxurl,

			  data: {
				  action: 'dismiss_wp_menu_image_fa',
				  security: $( this ).parent().attr( 'data-ajax-nonce' )
				  }
		  });
	});
})(jQuery);
