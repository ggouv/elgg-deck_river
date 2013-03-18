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

	// function for user and group popups
	var fillPopup = function(popupElem, response) {
		popupElem.children('.elgg-body').html(response);
		popupElem.find('.elgg-tabs > li > a').click(function() {
			var tab = $(this).attr('href');
			if (popupElem.find(tab).hasClass('hidden')) {
				popupElem.find('.elgg-tabs > li').removeClass('elgg-state-selected');
				$(this).parent('li').addClass('elgg-state-selected');
				popupElem.find('.elgg-body > li').addClass('hidden').filter(tab).removeClass('hidden');
			}
			if ($(tab).find('.elgg-ajax-loader').length) {
				elgg.deck_river.LoadRiver($(tab), tab.match(/[0-9]+/)[0]);
			}
			return false;
		});
	};

	// user info popup
	$('.user-info-popup').die().live('click', function() {
		elgg.deck_river.createPopup('user-info-popup', elgg.echo('deck_river:user-info-header', [$(this).attr('title')]));

		elgg.post('ajax/view/deck_river/ajax_view/user_info', {
			dataType: "html",
			data: {
				user: $(this).attr('title'),
			},
			success: function(response) {
				fillPopup($('#user-info-popup'), response);
			},
			error: function() {
				$('#user-info-popup > .elgg-body').html(elgg.echo('deck_river:ajax:erreur'));
			}
		});
	});

	// group info popup
	$('.group-info-popup').die().live('click', function() {
		elgg.deck_river.createPopup('group-info-popup', elgg.echo('deck_river:group-info-header', [$(this).attr('title')]));

		elgg.post('ajax/view/deck_river/ajax_view/group_info', {
			dataType: "html",
			data: {
				group: $(this).attr('title'),
			},
			success: function(response) {
				fillPopup($('#group-info-popup'), response);
			},
			error: function() {
				$('#group-info-popup > .elgg-body').html(elgg.echo('deck_river:ajax:erreur'));
			}
		});
	});

	// hashtag info popup
	$('.hashtag-info-popup').die().live('click', function() {
		elgg.deck_river.createPopup(
			'hashtag-info-popup',
			elgg.echo('deck_river:hashtag-info-header', [$(this).attr('title')]),
			function() {
				$('#hashtag-info-popup').find('.elgg-ajax-loader').wrap(
					$('<ul>', {'class': 'elgg-river elgg-list'})
				).before(
					$('<ul>', {'class': 'column-header hidden', 'data-network': 'elgg', 'data-river_type': 'entity_river'})
				);
			}
		);
		elgg.deck_river.LoadRiver($('#hashtag-info-popup'), $(this).attr('title'));
	});

	// Twitter user info popup
	$('.twitter-user-info-popup').die().live('click', function() {
		elgg.deck_river.createPopup('user-info-popup', elgg.echo('deck_river:user-info-header', [$(this).attr('title')]));

		var userInfo = elgg.deck_river.findUser($(this).attr('title'), 'twitter'),
			templateRender = function(response) {
				console.log(response);
				response.profile_image_url = response.profile_image_url.replace(/_normal/, '');
				fillPopup($('#user-info-popup'), Mustache.render($('#templates .twitter-user-profile').html(), response));
			};

		if (elgg.isUndefined(userInfo) || elgg.isUndefined(userInfo.id)) { // Twitter feed from search api doesn't contains user info, only screen_name and image_profile
			$.get('https://api.twitter.com/1/users/show.json?include_entities=true&screen_name='+ $(this).attr('title'),
				function(response) {
					elgg.deck_river.storeEntity(response, 'twitter');
					templateRender(response);
				},'jsonP'
			).error(function() {
				$('#user-info-popup > .elgg-body').html(elgg.echo('deck_river:ajax:erreur'));
			});
		} else {
			templateRender(userInfo);
		}
	});

}
elgg.register_hook_handler('init', 'system', elgg.deck_river.popups);



/**
 * Elgg-deck_river plugin
 *
 * Create a new popup
 * @return void
 */
elgg.deck_river.createPopup = function(popupID, popupTitle, callback) {
	if (!popupID) return false;
	var popupTitle = popupTitle || '';

	if (!$('#'+popupID).length) {
		$('.elgg-page-body').append(
			Mustache.render($('#templates .popup-template').html(), {popupID: popupID, popupTitle: popupTitle})
		);
		var popup = $('#'+popupID).draggable({
			handle: '.elgg-head',
			stack: '.elgg-module-popup',
		});
		popup.find('.elgg-icon-push-pin').click(function() {
			var popupP = $(this).closest('.deck-popup');
			if (popupP.hasClass('pinned')) {
				$('.elgg-page-body').append(popupP.removeClass('pinned'));
			} else {
				$('.elgg-page-body').after(popupP.addClass('pinned'));
			}
			return false;
		});
		popup.find('.elgg-icon-delete-alt').click(function() {
			popup.remove();
			$('.tipsy').remove();
			return false;
		});
	} else {
		$('#'+popupID+' > .elgg-head h3').html(popupTitle);
		$('#'+popupID+' > .elgg-body').html($('<div>', {'class': 'elgg-ajax-loader'}));
	}

	if (callback) callback();
};


