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

	// livedraggable
	(function ($) {
		$.fn.liveDraggable = function () {
			$(this).live('mouseover', function() {
				if (!$(this).hasClass('ui-draggable')) {
					$(this).draggable({
						revert: true,
						revertDuration: 0,
						appendTo: "body",
						containment: "window",
						helper: "clone",
						zIndex: 9999,
						cursor: "crosshair"
					});
				}
			});
			return this;
		};
	}(jQuery));

	// tabs popups
	$('.deck-popup .elgg-tabs a').die().live('click', function() {
		var popup = $(this).closest('.deck-popup'),
			tab = $(this).attr('href');

		if (popup.find(tab).hasClass('hidden')) {
			popup.find('.elgg-tabs li').removeClass('elgg-state-selected');
			$(this).parent('li').addClass('elgg-state-selected');
			popup.find('.elgg-body > li').addClass('hidden').filter(tab).removeClass('hidden');
		}
		if ($(tab).find('.elgg-ajax-loader').length) {
			elgg.deck_river.LoadRiver($(tab), $(tab).children('.column-header').data('entity'));
		}
		return false;
	});

	// user info popup
	$('.user-info-popup').die().live('click', function() {
		elgg.deck_river.userPopup($(this).attr('title'));
		return false;
	}).liveDraggable();

	// group info popup
	$('.group-info-popup').die().live('click', function() {
		elgg.deck_river.groupPopup($(this).attr('title'));
		return false;
	}).liveDraggable();

	// hashtag info popup
	$('.hashtag-info-popup').die().live('click', function() {
		var hashtag = $(this).attr('title'),
			network = $(this).data('network') || 'elgg';

		elgg.deck_river.createPopup('hashtag-info-popup', elgg.echo('deck_river:hashtag-info-header', [hashtag]));
		$('#hashtag-info-popup > .elgg-body').html(Mustache.render($('#hashtag-popup-template').html(), {hashtag: hashtag.replace('#', '')}));
		$('#hashtag-info-popup .elgg-tabs .'+network).click();
		return false;
	}).liveDraggable();

	// Twitter user info popup
	$('.twitter-user-info-popup').die().live('click', function() {
		elgg.deck_river.createPopup('user-info-popup', elgg.echo('deck_river:user-info-header', [$(this).attr('title')]));

		var body = $('#user-info-popup > .elgg-body'),
			userInfo = elgg.deck_river.findUser($(this).attr('title'), 'twitter'),
			templateRender = function(response) {
				response.profile_image_url = response.profile_image_url.replace(/_normal/, '');
				response.description = response.description.TwitterParseURL().TwitterParseUsername().TwitterParseHashtag();
				body.html(Mustache.render($('#twitter-user-profile-template').html(), response));
			};

		if (elgg.isUndefined(userInfo) || elgg.isUndefined(userInfo.id)) { // Twitter feed from search api doesn't contains user info, only screen_name and image_profile
			elgg.post('ajax/view/deck_river/ajax_json/twitter_OAuth', {
				dataType: 'json',
				data: {
					params: {method: 'get_usersShow', include_entities: true, screen_name: $(this).attr('title')}
				},
				success: function(response) {
					elgg.deck_river.storeEntity(response, 'twitter');
					templateRender(response);
				},
				error: function() {
					body.html(elgg.echo('deck_river:ajax:erreur'));
				}
			});
		} else {
			templateRender(userInfo);
		}
	}).liveDraggable();

}
elgg.register_hook_handler('init', 'system', elgg.deck_river.popups);


/**
 * show user popup
 * @param  {[string]} user username
 */
elgg.deck_river.userPopup = function(user) {
	elgg.deck_river.createPopup('user-info-popup', elgg.echo('deck_river:user-info-header', [user]));

	var body = $('#user-info-popup > .elgg-body');
	elgg.post('ajax/view/deck_river/ajax_view/user_info', {
		dataType: "html",
		data: {
			user: user
		},
		success: function(response) {
			body.html(response);
			if ($.isFunction(elgg.markdown_wiki.view.init)) elgg.markdown_wiki.view.init();
		},
		error: function() {
			body.html(elgg.echo('deck_river:ajax:erreur'));
		}
	});
};



/**
 * show group popup
 * @param  {[string]} group name
 */
elgg.deck_river.groupPopup = function(group) {
	elgg.deck_river.createPopup('group-info-popup', elgg.echo('deck_river:group-info-header', [group]));

	var body = $('#group-info-popup > .elgg-body');
	elgg.post('ajax/view/deck_river/ajax_view/group_info', {
		dataType: "html",
		data: {
			group: group
		},
		success: function(response) {
			body.html(response);
		},
		error: function() {
			body.html(elgg.echo('deck_river:ajax:erreur'));
		}
	});
};



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
			Mustache.render($('#popup-template').html(), {popupID: popupID, popupTitle: popupTitle})
		);
		var popup = $('#'+popupID).draggable({
			handle: '.elgg-head',
			stack: '.elgg-module-popup'
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


