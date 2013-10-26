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
		$.fn.liveDraggable = function (options) {
			$(this).live('mouseover', function() {
				if (!$(this).hasClass('ui-draggable')) {
					options = $.extend({
						revert: true,
						revertDuration: 0,
						appendTo: "body",
						containment: "window",
						helper: "clone",
						zIndex: 9999,
						cursor: "crosshair"
					}, options);
					$(this).draggable(options);
				}
			});
			return this;
		};
	}($));

	// tabs popups
	$('.deck-popup .elgg-tabs a').live('click', function() {
		var popup = $(this).closest('.deck-popup'),
			tab = $(this).attr('href');

		if (popup.find($(tab)).hasClass('hidden')) {
			popup.find('.elgg-tabs li').removeClass('elgg-state-selected');
			$(this).parent('li').addClass('elgg-state-selected');
			popup.find('.elgg-body > li').addClass('hidden').filter(tab).removeClass('hidden');
		}
		if ($(tab).find('.elgg-ajax-loader').length) {
			elgg.deck_river.LoadRiver($(tab), $(tab).children('.column-header').data());
		}
		return false;
	});

	// user info popup
	$('.elgg-user-info-popup').live('click', function() {
		elgg.deck_river.userPopup($(this).attr('title'));
		return false;
	}).liveDraggable();

	// group info popup
	$('.group-info-popup').live('click', function() {
		elgg.deck_river.groupPopup($(this).attr('title'));
		return false;
	}).liveDraggable();

	// hashtag info popup
	$('.hashtag-info-popup').live('click', function() {
		var hashtag = $(this).attr('title'),
			network = $(this).data('network') || 'elgg';

		elgg.deck_river.createPopup('hashtag-info-popup', elgg.echo('deck_river:hashtag-info-header', [hashtag]));
		$('#hashtag-info-popup > .elgg-body').html(Mustache.render($('#hashtag-popup-template').html(), {hashtag: hashtag.replace('#', '')}));
		$('#hashtag-info-popup .elgg-tabs .'+network).click();
		return false;
	}).liveDraggable();

	// Twitter user info popup
	$('.twitter-user-info-popup').live('click', function() {
		elgg.deck_river.createPopup('user-info-popup', elgg.echo('deck_river:user-info-header', [$(this).attr('title')]));

		var body = $('#user-info-popup > .elgg-body'),
			userInfo = elgg.deck_river.findUser($(this).attr('title'), 'twitter'),
			templateRender = function(response) {
				response.profile_image_url = response.profile_image_url.replace(/_normal/, '');
				response.description = response.description.ParseEverythings('twitter');
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

	// video popup
	$('.video-popup').live('click', function() {
		var source = $(this).data('source');

		elgg.deck_river.createPopup('video-popup', $(this).find('h4').html());
		var vp = $('#video-popup'),
			resizeVP = function() {
				vp.find('.elgg-body').height(vp.height() - vp.find('.elgg-head').height()+7);
			};
		vp.resizable({
			handles: 'se',
			helper: 'resizable-helper',
			stop: function( event, ui ) {
				vp.width(ui.size.width);
				resizeVP();
			}
		}).find('.elgg-body').html(
			$('<div>', {'class': 'overlay hidden'}).after(
				$('<iframe>', {src: source, width: '100%', height: '100%'}))
		);
		resizeVP();
		return false;
	});

	// drag and drop linkbox
	$('.linkbox-droppable').liveDraggable();

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
		$('.elgg-page-body').after(
			Mustache.render($('#popup-template').html(), {popupID: popupID, popupTitle: popupTitle})
		);
		var popup = $('#'+popupID).draggable({
			handle: '.elgg-head',
			stack: '.elgg-module-popup'
		});
		popup.find('.elgg-icon-push-pin').click(function() {
			$(this).closest('.deck-popup').toggleClass('pinned');
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



/**
 * Show popup to choose facebook groups of a facebook_account object
 * @param  {[type]} account The facebook_account guid
 * @return void
 */
elgg.deck_river.getFBGroups = function(account) {
	elgg.post('ajax/view/deck_river/ajax_json/facebook_API', {
		data: {
			facebook_account: account,
			method: 'groups'
		},
		dataType: 'json',
		success: function(json) {
			if (json.result) {
				var groups = json.result.data;

				groups.sort(function(a, b) {
					return ((a.bookmark_order < b.bookmark_order) ? -1 : ((a.bookmark_order > b. bookmark_order) ? 1 : 0));
				});
				elgg.deck_river.createPopup('facebook-groups-popup', elgg.echo('deck_river:facebook:groups'));
				var $fgp = $('#facebook-groups-popup');

				$fgp.find('.elgg-body')
					.html($('<h3>', {'class': 'pbs'}).html(elgg.echo('deck_river:facebook:groups:choose')))
					.append($('<ul>').css({'overflow-y': 'scroll', height: '552px'}));
				$.each(groups, function(i,e) {
					$fgp.find('.elgg-body ul').append(
						$('<li>', {'class': 'pas link', id: e.id}).html(e.name).click(function() {
							elgg.action('deck_river/add_facebook_group', {
								data: {
									facebook_account: account,
									group_id : e.id
								},
								success: function(json) {
									elgg.deck_river.network_authorize(json.output);
									$fgp.find('#'+e.id).css('background-color', '#FF7777').fadeOut();
								}
							});
						})
					);
				});
			}
		},
		error: function() {
			return false;
		}
	});
};