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
	
	var setToTop = function(e) {
		var index_highest = 0;
		$('.elgg-module-popup.ui-draggable:visible').each(function(){
			index_highest = Math.max(index_highest, parseInt($(this).css("z-index"), 10));
		});
		e.css({'z-index': index_highest+1, position: 'absolute'});
	};
	
	if (!$('#'+popupID).length) {
		//var method = 'append';
		//$('.elgg-page-body')[method](
		$('.elgg-page-body').append(
			$('<div>', {id: popupID, 'class': 'elgg-module-popup deck-popup'})
			.draggable({
				handle: '.elgg-head',
				//containment: 'document',
				start: function(event, ui) {
					setToTop($(this));
				}
			})
			.click(function(){
				setToTop($(this));
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
	
	setToTop($('#'+popupID));
	if (callback) callback();
};
