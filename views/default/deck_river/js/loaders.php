/**
 *	Elgg-deck_river plugin
 *	@package elgg-deck_river
 *	@author Emmanuel Salomon @ManUtopiK
 *	@license GNU Affero General Public License, version 3 or late
 *	@link https://github.com/ManUtopiK/elgg-deck_river
 *
 *	Elgg-deck_river loaders js
 *
 */

/**
 * Load a column
 *
 * Makes Ajax call to persist column and inserts the column html
 *
 * @param {TheColumn} the column
 * @param {ajaxCall} the name of the view to call
 * @param {TheEntity} optional : GUID of an TheEntity
 * @return void
 */
elgg.deck_river.LoadRiver = function(TheColumn, TheEntity) {
	var TheColumnHeader = TheColumn.addClass('loadingRefresh').find('.column-header'),
		TheColumnRiver = TheColumn.find('.elgg-river'),
		networkDisplay = TheColumnHeader.data('network')+ 'DisplayItems',
		loadMoreItem = $('<li>', {'class': 'moreItem'}).append($('<li>', {'class': 'response-loader hidden'}), elgg.echo('deck_river:more'));

	if (TheColumnHeader.data('direct')) { // this is a direct link. Feed is loaded by user's browser.
		$.ajax({
			url: TheColumnHeader.data('direct'),
			dataType: 'jsonP',
			success: function(response) {
				TheColumnRiver.html(elgg.deck_river[networkDisplay](response));
				if (response.refresh_url !== undefined) {
					TheColumnHeader.data('refresh_url', response.refresh_url);
				} else {
					TheColumnHeader.data('refresh_url', TheColumnHeader.data('direct'));
				}
				if (response.next_page !== undefined) {
					TheColumnRiver.append(loadMoreItem);
					TheColumnHeader.data('next_page', response.next_page);
				}
				TheColumn.removeClass('loadingRefresh');
			},
			error: function(xmlhttp, status, error) {
				//TheColumn.find('.elgg-river').html();
				elgg.register_error(elgg.echo('deck_river:twitter:access:error', [status, error]));
				TheColumn.removeClass('loadingRefresh');
			}
		});
	} else {
		elgg.post('ajax/view/deck_river/ajax_json/' + TheColumnHeader.data('view_type'), {
			dataType: 'json',
			data: {
				tab: $('#deck-river-lists').data('tab'), // used only with 'column_river' call
				column: TheColumn.attr('id'), // used only with 'column_river' call
				guid: TheEntity ? TheEntity : null,
			},
			success: function(response) {
				response.TheColumn = TheColumn;
				$output = elgg.trigger_hook('deck-river', 'column:'+response.column_type, response, 'nohook');
				if ($output == 'nohook') {
					TheColumnRiver.html(elgg.deck_river[networkDisplay](response));
					if ( TheColumn.find('.elgg-list-item').length >= 20 ) {
						TheColumnRiver.append(loadMoreItem);
					} else if ( TheColumn.find('.elgg-list-item').length == 0 ) {
						var user = elgg.get_logged_in_user_entity(),
							c_type = response.column_type;

						if (c_type == 'mine' && Math.round($.now()/1000) - elgg.get_logged_in_user_entity().time_created  < (60*60*24*7)) c_type = 'now';
						TheColumnRiver.html($('<table>', {height: '100%', width: '100%'}).append(
							$('<tr>').append(
								$('<td>', {'class': 'helper'}).html(elgg.echo('deck_river:helper:'+c_type, [user.location])))
						));
					}
				}
				TheColumn.removeClass('loadingRefresh');
			}
		});
	}
};



/**
 * Refresh a column
 *
 * Makes Ajax call to persist column and inserts items at the beginig column html
 *
 * @param {TheColumn} the column
 * @return void
 */
elgg.deck_river.RefreshColumn = function(TheColumn) {
	var TheColumnHeader = TheColumn.addClass('loadingRefresh').find('.column-header'),
		TheColumnRiver = TheColumn.find('.elgg-river'),
		networkDisplay = TheColumnHeader.data('network')+ 'DisplayItems';

	if (TheColumnHeader.data('direct')) { // this is a direct link. Feed is loaded by user's browser.
		var url = elgg.parse_url(TheColumnHeader.data('direct'));
		$.ajax({
			url: url.scheme+'://'+url.host+url.path + TheColumnHeader.data('refresh_url'),
			dataType: 'jsonP',
			success: function(response) {
				var responseHTML = elgg.deck_river[networkDisplay](response);
				TheColumn.removeClass('loadingRefresh').find('.elgg-list-item').removeClass('newRiverItem');
				TheColumnHeader.data('refresh_url', response.refresh_url);
				responseHTML.filter('.elgg-list-item').addClass('newRiverItem');
				TheColumn.find('.elgg-river').prepend(responseHTML).find('.newRiverItem').fadeIn('slow');
			}
		});
	} else {
		elgg.post('ajax/view/deck_river/ajax_json/column_river', {
			dataType: 'json',
			data: {
				tab: $('#deck-river-lists').data('tab'),
				column: TheColumn.attr('id'),
				time_method: 'lower',
				time_posted: TheColumn.find('.elgg-river .elgg-list-item:first .elgg-friendlytime span').first().text() || 0
			},
			success: function(response) {
				TheColumn.removeClass('loadingRefresh').find('.elgg-list-item').removeClass('newRiverItem');
				response.TheColumn = TheColumn;
				$output = elgg.trigger_hook('deck-river', 'column:'+response.column_type, response, 'nohook');
				if ($output == 'nohook') {
					var res = elgg.deck_river[networkDisplay](response);
					res.filter('.elgg-list-item').addClass('newRiverItem');
					if (res.length) TheColumn.find('.elgg-river > table').remove();
					TheColumn.find('.elgg-river').prepend(res).find('.newRiverItem').fadeIn('slow');
				}
			}
		});
	}
};



/**
 * Load more item in a column
 *
 * Makes Ajax call to persist column and inserts items at the end of the column html
 *
 * @param {TheColumn} the column
 * @return void
 */
elgg.deck_river.LoadMore = function(TheColumn, TheEntity) {
	var TheColumnHeader = TheColumn.addClass('loadingMore').find('.column-header'),
		LastItem = TheColumn.find('.elgg-river .elgg-list-item:last'),
		networkDisplay = TheColumnHeader.data('network')+ 'DisplayItems',
		displayItems = function(response) {
			TheColumn.removeClass('loadingMore').find('.elgg-river').append(elgg.deck_river[networkDisplay](response))
				.find('.moreItem').appendTo(TheColumn.find('.elgg-river'));
			//	var pos = LastItem.next().position();
			//TheColumn.find('.elgg-river').scrollTo('+='+pos.top+'px', 2500, {easing:'easeOutQuart'});
		};

	if (TheColumnHeader.data('direct')) { // this is a direct link. Feed is loaded by user's browser.
		var url = elgg.parse_url(TheColumnHeader.data('direct'));
		$.ajax({
			url: url.scheme+'://'+url.host+url.path + TheColumnHeader.data('next_page'),
			dataType: 'jsonP',
			success: function(response) {
				if (undefined === response.next_page) response.next_page = TheColumnHeader.data('next_page').match('^.*=')[0] + response[response.length-1].id_str.addToLargeInt(-1);
				console.log(response.next_page, 'nextpage');
				TheColumnHeader.data('next_page', response.next_page);
				displayItems(response);
			},
			error: function() {
				TheColumn.removeClass('loadingMore');
			}
		});
	} else {
		elgg.post('ajax/view/deck_river/ajax_json/' + TheColumnHeader.data('view_type'), {
			dataType: 'json',
			data: {
				tab: $('#deck-river-lists').data('tab'),
				column: TheColumn.attr('id'),
				time_method: 'upper',
				time_posted: LastItem.find('.elgg-friendlytime span').text(),
				guid: TheEntity ? TheEntity : null,
			},
			success: function(response) {
				displayItems(response);
			},
			error: function() {
				TheColumn.removeClass('loadingMore');
			}
		});
	}
};



/**
 * Load a discussion
 *
 * Makes Ajax call to load discussion if doesn't exist and inserts items after the river item
 *
 * @param {athread} the wire thread
 * @return void
 */
elgg.deck_river.LoadDiscussion = function(athread) {
	// if already exist, skip
	if (athread.parent('.elgg-river-responses').find('div.thread').length) return;

	athread.parent('.elgg-river-responses').find('.response-loader').removeClass('hidden');

	if (athread.find('.column-header').data('network')) {
		//http://api.twitter.com/1/related_results/show/254208368070258688.json?include_entities=1
		$.ajax({
			url: 'https://api.twitter.com/1.1/statuses/show.json?id='+ athread.data('thread'),
			//url: 'http://api.twitter.com/1/related_results/show/'+ athread.data('thread') +'.json?include_entities=1',
			dataType: 'json',
			success: function(response) {
				athread.parent('.elgg-river-responses').find('.response-loader').addClass('hidden');
				var idToggle = athread.parents('.column-river').attr('id') + '-' + athread.parents('.elgg-list-item').attr('class').match(/item-twitter-\d+/);

				athread.parent('.elgg-river-responses').append($('<div>', {id: idToggle, 'class': 'thread mts float'}).html(elgg.deck_river.TwitterDisplayItems(response, true)));
				athread.attr({
					rel: 'toggle',
					href: '#' + idToggle
				}).html(elgg.echo('deck_river:toggle_discussion'));
			},
			error: function(XHR, textStatus, errorThrown){
				athread.parent('.elgg-river-responses').find('.response-loader').addClass('hidden');
				console.log(XHR);
				console.log("ERREUR: " + textStatus);
				console.log("ERREUR: " + errorThrown);
			}
		});
	} else {
		elgg.post('ajax/view/deck_river/ajax_json/load_discussion', {
			dataType: "json",
			data: {
				discussion: athread.data('thread'),
			},
			success: function(response) {
				athread.parent('.elgg-river-responses').find('.response-loader').addClass('hidden');
				var idToggle = athread.parents('.column-river').attr('id') + '-' + athread.parents('.elgg-list-item').attr('class').match(/item-river-\d+/);

				athread.parent('.elgg-river-responses').append($('<div>', {id: idToggle, 'class': 'thread mts float'}).html(elgg.deck_river.elggDisplayItems(response, true)));
				athread.attr({
					rel: 'toggle',
					href: '#' + idToggle
				}).html(elgg.echo('deck_river:toggle_discussion'));
			},
			error: function() {
				athread.parent('.elgg-river-responses').find('.response-loader').addClass('hidden');
				//$('#user-info-popup > .elgg-body').html(elgg.echo('deck_river:ajax:erreur'));
			}
		});
	}
};



/*
 * Load Twitter timeline for an user
 */
elgg.deck_river.LoadTwitter_activity = function(twitterID, OutputElem) {
	var OutputElemHeader = OutputElem.find('.column-header'),
		url = elgg.parse_url(OutputElemHeader.data('direct'));
	$.ajax({
		url: url.scheme+'://'+url.host+url.path+'?count=50&include_rts=1&user_id='+twitterID,
		dataType: 'jsonP',
		success: function(response) {
			OutputElem.find('.elgg-river').html(elgg.deck_river.twitterDisplayItems(response))
				.append($('<li>', {'class': 'moreItem'}).append($('<li>', {'class': 'response-loader hidden'}), elgg.echo('deck_river:more')));
			OutputElemHeader.data('next_page',
				'?count=50&include_rts=1&user_id='+twitterID+'&max_id='+ response[response.length-1].id_str.addToLargeInt(-1));
		},
		error: function(xmlhttp, status, error) {
			//OutputElem.find('.elgg-river').html();
			elgg.register_error(elgg.echo('deck_river:twitter:access:error', [status, error]));
		}
	});
};



String.prototype.addToLargeInt = function (value) {
	return this.substr(0, this.length-3)+(parseInt(this.substr(-3)) + value);
};
