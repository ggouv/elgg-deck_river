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
		popupElem.find('.elgg-tabs > li > a').click(function() {console.log($(this));
			var tab = $(this).attr('href');
			if (popupElem.find(tab).hasClass('hidden')) {
				popupElem.find('.elgg-tabs > li').removeClass('elgg-state-selected');
				$(this).parent('li').addClass('elgg-state-selected');
				popupElem.find('.elgg-body > li').addClass('hidden').filter(tab).removeClass('hidden');
			}
			if ($(tab).find('.elgg-ajax-loader').length) {
				elgg.deck_river[$(tab).attr('data-load-type')](tab.match(/[0-9]+/)[0], $(tab));
			}
		});
	};

	// user info popup
	$('.user-info-popup').die().live('click', function() {
		elgg.deck_river.createPopup('user-info-popup', elgg.echo('deck_river:user-info-header', [$(this).attr('title')]));

		elgg.post('ajax/view/deck_river/ajax/user_info', {
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
		elgg.deck_river.createPopup('group-info-popup', elgg.echo('deck_river:group-info-header'));

		elgg.post('ajax/view/deck_river/ajax/group_info', {
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
				$('#hashtag-info-popup').find('.elgg-ajax-loader').wrap($('<ul>', {'class': 'elgg-river elgg-list'}));
			}
		);

		elgg.deck_river.LoadEntity($(this).attr('title'), $('#hashtag-info-popup'));
	});

	// Twitter user info popup
	$('.twitter-user-info-popup').die().live('click', function() {
		elgg.deck_river.createPopup('user-info-popup', elgg.echo('deck_river:user-info-header', [$(this).attr('title')]));

		$.ajax({
			url: 'https://api.twitter.com/1/users/show.json?include_entities=true&screen_name='+ $(this).attr('title'),
			dataType: "jsonP",
			success: function(response) {
				console.log(response);
				var tabs = ['profile', 'activity', 'mentions', 'favoris'];
				var output = $('<ul>', {'class': 'elgg-tabs elgg-htabs'}).html(function() {
								var tabsHtml = '';
								$.each(tabs, function(i, e) {
									tabsHtml += '<li class="' + (i==0 ? 'elgg-state-selected' : '') + '"><a href="#'+response.id+'-info-'+e+'">' + elgg.echo(e) + '</a></li>';
								});
								return tabsHtml;
							}
							).after($('<ul>', {'class': 'elgg-body'}).html(function() {
								var lisHtml = '';
								$.each(tabs, function(i, e) {
									lisHtml += '<li id="' + response.id+'-info-'+e + (i==0 ? '"' : '" class="hidden" data-load-type="LoadTwitter_'+e + '"><ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul') + '></li>';
								});
								return lisHtml;
							}));
				output.filter('.elgg-body').find('li:first-child').html(
					$('<div>', {'class': 'elgg-avatar elgg-avatar-large float'}).html(
						$('<a>', {title: response.screen_name, rel: 'nofollow', href: 'http://twitter.com/'+response.screen_name}).append(
						$('<img>', {title: response.screen_name, alt: response.screen_name, src: response.profile_image_url.replace(/_normal/, ''), width: '200px', height: '200px'})
					)).after(
					$('<div>', {'class': 'elgg-body plm'}).html(
						$('<h1>', {'class': 'mbm'}).html(response.name).after(
						$('<h2>', {'class': 'mbs', style: 'font-weight:normal;'}).html('@'+response.screen_name).after(
							$('<div>').html(response.description)
					)))).after(
					$('<div>', {id: 'profile-details', 'class': 'elgg-body pll'}).html(
						$('<ul>', {'class': 'user-stats mbm'}).append(
							$('<li>').append($('<div>', {'class': 'stats'}).html(response.followers_count), elgg.echo('friends:followers')),
							$('<li>').append($('<div>', {'class': 'stats'}).html(response.friends_count), elgg.echo('friends:following')),
							$('<li>').append($('<div>', {'class': 'stats'}).html(response.listed_count), elgg.echo('list')),
							$('<li>').append($('<div>', {'class': 'stats'}).html(response.statuses_count), elgg.echo('item:object:thewire'))
					)).append(
						$('<div>', {'class': 'even'}).html('<b>' + elgg.echo('Twitter') + ' :</b> <a target="_blank" href="http://twitter.com/'+response.screen_name + '">http://twitter.com/'+response.screen_name + '</a>'),
						$('<div>', {'class': 'even'}).html('<b>' + elgg.echo('site') + ' :</b> <a target="_blank" href="'+ response.url + '">' + response.url + '</a>'),
						$('<div>', {'class': 'even'}).html('<b>' + elgg.echo('profile:time_created') + ' :</b> ' + response.created_at)
					))
				);
				fillPopup($('#user-info-popup'), output);
			},
			error: function() {
				$('#user-info-popup > .elgg-body').html(elgg.echo('deck_river:ajax:erreur'));
			}
		});
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
		//var method = 'append';
		//$('.elgg-page-body')[method]( @todo option to always pin popup ?
		$('.elgg-page-body').append(
			$('<div>', {id: popupID, 'class': 'elgg-module-popup deck-popup'})
			.draggable({
				handle: '.elgg-head',
				stack: '.elgg-module-popup',
			})
			.append(
				$('<div>', {'class': 'elgg-head'}).append(
					$('<h3>').html(popupTitle).after(
					$('<a>', {href: '#', 'class': 'pin'}).append(
						$('<span>', {'class': 'elgg-icon elgg-icon-push-pin tooltip s', title: elgg.echo('deck-river:popups:pin')})
					).click(function() {
						var popupP = $(this).parents('.deck-popup');
						if (popupP.hasClass('pinned')) {
							$('.elgg-page-body').append(popupP.removeClass('pinned'));
						} else {
							$('.elgg-page-body').after(popupP.addClass('pinned'));
						}
					})).after(
					$('<a>', {href: '#'}).append(
						$('<span>', {'class': 'elgg-icon elgg-icon-delete-alt tooltip s', title: elgg.echo('deck-river:popups:close')})
					).click(function() {
						$('#'+popupID).remove();
						$('.tipsy').remove();
					})
				)).after(
					$('<div>', {'class': 'elgg-body'}).append(
						$('<div>', {'class': 'elgg-ajax-loader'})
		))));
	} else {
		$('#'+popupID+' > .elgg-head h3').html(popupTitle);
		$('#'+popupID+' > .elgg-body').html($('<div>', {'class': 'elgg-ajax-loader'}));
	}

	if (callback) callback();
};
