/**
 *	Elgg-deck_river plugin
 *	@package elgg-deck_river
 *	@author Emmanuel Salomon @ManUtopiK
 *	@license GNU Affero General Public License, version 3 or late
 *	@link https://github.com/ManUtopiK/elgg-deck_river
 *
 *	Elgg-deck_river popups js
 *
 */

/**
 * Elgg-deck_river popups
 *
 * @return void
 */
elgg.deck_river.popups = function() {

	// user info popup
	$('.user-info-popup').die().live('click', function() {
		if (!$('#user-info-popup').length) {
			//var method = 'append';
			//$('.elgg-page-body')[method](
			$('.elgg-page-body').append(
				$('<div>', {id: 'user-info-popup', class: 'elgg-module-popup'}).draggable({ handle: ".elgg-head" }).append(
					$('<div>', {class: 'elgg-head'}).append(
						$('<h3>').html(elgg.echo('deck_river:user-info-header')).after(
						$('<a>').append(
							$('<span>', {class: 'elgg-icon elgg-icon-delete-alt'})
						).click(function() {
							$('#user-info-popup').remove();
						})
					)).after(
						$('<div>', {class: 'elgg-body'}).append(
							$('<div>', {class: 'elgg-ajax-loader'})
			))));
		} else {
			$('#user-info-popup > .elgg-body').html($('<div>', {class: 'elgg-ajax-loader'}));
		}
		elgg.post('ajax/view/deck_river/ajax/user_info', {
			dataType: "html",
			data: {
				user: $(this).attr('title'),
			},
			success: function(response) {
				$('#user-info-popup > .elgg-body').html(response);
				$('#user-info-popup .elgg-tabs > li > a').click(function() {
					var tab = $(this).attr('href');
					if ($('#user-info-popup ' + tab).hasClass('hidden')) {
						$('#user-info-popup .elgg-tabs > li').removeClass('elgg-state-selected');
						$(this).parent('li').addClass('elgg-state-selected');
						$('#user-info-popup .elgg-body > li').addClass('hidden');
						$('#user-info-popup ' + tab).removeClass('hidden');
					}
					if (tab == '#user-info-activity' && $('#user-info-activity .elgg-ajax-loader').length) {
						elgg.deck_river.LoadEntity($('#user-info-activity > .elgg-river').attr('data-user'), $('#user-info-popup #user-info-activity'));
					}
				});
			},
			error: function() {
				$('#user-info-popup > .elgg-body').html(elgg.echo('deck_river:ajax:erreur'));
			}
		});
	});
	
	// group info popup
	$('.group-info-popup').die().live('click', function() {
		if (!$('#group-info-popup').length) {
			$('.elgg-page-body').append(
				$('<div>', {id: 'group-info-popup', class: 'elgg-module-popup'}).draggable({ handle: ".elgg-head" }).append(
					$('<div>', {class: 'elgg-head'}).append(
						$('<h3>').html(elgg.echo('deck_river:group-info-header')).after(
						$('<a>').append(
							$('<span>', {class: 'elgg-icon elgg-icon-delete-alt'})
						).click(function() {
							$('#group-info-popup').remove();
						})
					)).after(
						$('<div>', {class: 'elgg-body'}).append(
							$('<div>', {class: 'elgg-ajax-loader'})
			))));
		} else {
			$('#group-info-popup > .elgg-body').html($('<div>', {class: 'elgg-ajax-loader'}));
		}
		elgg.post('ajax/view/deck_river/ajax/group_info', {
			dataType: "html",
			data: {
				group: $(this).attr('title'),
			},
			success: function(response) {
				$('#group-info-popup > .elgg-body').html(response);
				$('#group-info-popup .elgg-tabs > li > a').click(function() {
					var tab = $(this).attr('href');
					if ($('#group-info-popup ' + tab).hasClass('hidden')) {
						$('#group-info-popup .elgg-tabs > li').removeClass('elgg-state-selected');
						$(this).parent('li').addClass('elgg-state-selected');
						$('#group-info-popup .elgg-body > li').addClass('hidden');
						$('#group-info-popup ' + tab).removeClass('hidden');
					}
					if (tab == '#group-info-activity' && $('#group-info-activity .elgg-ajax-loader').length) {
						elgg.deck_river.LoadEntity($('#group-info-activity > .elgg-river').attr('data-group'), $('#group-info-popup #group-info-activity'));
					}
				});
			},
			error: function() {
				$('#group-info-popup > .elgg-body').html(elgg.echo('deck_river:ajax:erreur'));
			}
		});
	});
	
	// hashtag info popup
	$('.hashtag-info-popup').die().live('click', function() {
		if (!$('#hashtag-info-popup').length) {
			$('.elgg-page-body').append(
				$('<div>', {id: 'hashtag-info-popup', class: 'elgg-module-popup'}).draggable({ handle: ".elgg-head" }).append(
					$('<div>', {class: 'elgg-head'}).append(
						$('<h3>').html(elgg.echo('deck_river:hashtag-info-header', [$(this).attr('title')])).after(
						$('<a>').append(
							$('<span>', {class: 'elgg-icon elgg-icon-delete-alt'})
						).click(function() {
							$('#hashtag-info-popup').remove();
						})
					)).after(
						$('<div>', {class: 'elgg-body'}).append(
							$('<ul>', {class: 'elgg-river elgg-list'}).append(
								$('<div>', {class: 'elgg-ajax-loader'})
			)))));
		} else {
			$('#hashtag-info-popup > .elgg-river').html($('<div>', {class: 'elgg-ajax-loader'}));
		}
		elgg.deck_river.LoadEntity($(this).attr('title'), $('#hashtag-info-popup'));
	});

}
elgg.register_hook_handler('init', 'system', elgg.deck_river.popups);