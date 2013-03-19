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

/* Be carefull ! Tweet IDs are too long for a call with .data('id') ! We need use .attr('data-id')


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
		loadMoreItem = $('<li>', {'class': 'moreItem'}).append($('<li>', {'class': 'response-loader hidden'}), elgg.echo('deck_river:more'));

	if (TheColumnHeader.data('direct')) { // this is a direct link. Feed is loaded by user's browser.
		$.ajax({
			url: TheColumnHeader.data('direct'),
			dataType: 'jsonP',
			success: function(response) {
				TheColumnRiver.html(elgg.deck_river.displayRiver(response, TheColumnHeader)).scrollTo(0);
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
		elgg.post('ajax/view/deck_river/ajax_json/' + TheColumnHeader.data('river_type'), {
			dataType: 'json',
			data: {
				tab: $('#deck-river-lists').data('tab'), // used only with 'column_river' call
				column: TheColumn.attr('id'), // used only with 'column_river' call
				guid: TheEntity ? TheEntity : null,
			},
			success: function(response) {
				response.TheColumn = TheColumn.removeClass('loadingRefresh');
				if (elgg.trigger_hook('deck-river', 'load:column:'+response.column_type, response, true)) {
					TheColumnRiver.html(elgg.deck_river.displayRiver(response, TheColumnHeader)).scrollTo(0);
					if ( TheColumn.find('.elgg-list-item').length >= 30 ) TheColumnRiver.append(loadMoreItem);
					/*} else if ( TheColumn.find('.elgg-list-item').length == 0 ) {
						var user = elgg.get_logged_in_user_entity(),
							c_type = response.column_type;

						if (c_type == 'mine' && Math.round($.now()/1000) - elgg.get_logged_in_user_entity().time_created  < (60*60*24*7)) c_type = 'now';
						TheColumnRiver.html($('<table>', {height: '100%', width: '100%'}).append(
							$('<tr>').append(
								$('<td>', {'class': 'helper'}).html(elgg.echo('deck_river:helper:'+c_type, [user.location])))
						));
					}*/
				}
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
		displayItems = function(response) {
			TheColumn.removeClass('loadingRefresh').find('.elgg-list-item').removeClass('newRiverItem');
			response.TheColumn = TheColumn;
			if (elgg.trigger_hook('deck-river', 'refresh:column:'+response.column_type, response, true)) {
				var responseHTML = elgg.deck_river.displayRiver(response, TheColumnHeader);

				TheColumn.find('.elgg-river > table').remove();
				if (!elgg.isUndefined(responseHTML)) {
					responseHTML.filter('.elgg-list-item').addClass('newRiverItem');
					TheColumn.find('.elgg-river').prepend(responseHTML).find('.newRiverItem').effect("highlight", 2000);
					elgg.deck_river.displayCount(response, TheColumn);
				}
			}
		}

	if (TheColumnHeader.data('direct')) { // this is a direct link. Feed is loaded by user's browser.
		var url = elgg.parse_url(TheColumnHeader.data('direct')),
			refreshURL = TheColumnHeader.data('refresh_url');
		$.ajax({
			url: refreshURL ? url.scheme+'://'+url.host+url.path + refreshURL : TheColumnHeader.data('direct'),
			dataType: 'jsonP',
			success: function(response) {
				displayItems(response);
				TheColumnHeader.data('refresh_url', response.refresh_url);
			}
		});
	} else {
		elgg.post('ajax/view/deck_river/ajax_json/column_river', {
			dataType: 'json',
			data: {
				tab: $('#deck-river-lists').data('tab'),
				column: TheColumn.attr('id'),
				time_method: 'lower',
				time_posted: TheColumn.find('.elgg-list-item').first().attr('data-timeid') || 0
			},
			success: function(response) {displayItems(response)},
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
		LastItem = TheColumn.find('.elgg-list-item').removeClass('newRiverItem').last(),
		displayItems = function(response) {
			var TheColumnRiver = TheColumn.removeClass('loadingMore').find('.elgg-river'),
				responseHTML = elgg.deck_river.displayRiver(response, TheColumnHeader);

			TheColumnHeader.find('.count').addClass('hidden');
			TheColumnRiver.append(responseHTML.effect("highlight", 2000))
				.find('.moreItem').appendTo(TheColumnRiver);
			TheColumnRiver.scrollTo(TheColumnRiver.scrollTop()+LastItem.next().position().top+'px', 1500, {easing:'easeOutQuart'});
		};

	if (TheColumnHeader.data('direct')) { // this is a direct link. Feed is loaded by user's browser.
		var url = elgg.parse_url(TheColumnHeader.data('direct'));
		$.ajax({
			url: url.scheme+'://'+url.host+url.path + TheColumnHeader.data('next_page'),
			dataType: 'jsonP',
			success: function(response) {
				if (undefined === response.next_page) response.next_page = TheColumnHeader.data('next_page').match('^.*=')[0] + response[response.length-1].id_str.addToLargeInt(-1);
				TheColumnHeader.data('next_page', response.next_page);
				displayItems(response);
			},
			error: function() {
				TheColumn.removeClass('loadingMore');
			}
		});
	} else {
		elgg.post('ajax/view/deck_river/ajax_json/' + TheColumnHeader.data('river_type'), {
			dataType: 'json',
			data: {
				tab: $('#deck-river-lists').data('tab'),
				column: TheColumn.attr('id'),
				time_method: 'upper',
				time_posted: LastItem.data('timeid'),
				guid: TheEntity ? TheEntity : null,
			},
			success: function(response) {displayItems(response)},
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
	var athreadResponses = athread.parent('.elgg-river-responses'),
		TheColumnHeader = athread.closest('.column-river').find('.column-header'),
		displayItems = function(response) {
			var riverID = athread.closest('.elgg-list-item').attr('class').match(/\d+/)[0],
				itemsRiver = $('.item-elgg-' + riverID),
				newItems = elgg.deck_river.displayRiver(response, TheColumnHeader, true);

			$.each(itemsRiver, function() {
				console.log($(this));
				var idToggle = $(this).find('.response-loader').addClass('hidden')
					.closest('.column-river').attr('id') + '-' + riverID;

				$(this).find('.elgg-river-responses')
					.append($('<div>', {id: idToggle, 'class': 'thread mts float hidden'}).html(newItems.clone()))
				.find('a.thread').attr({
					rel: 'toggle',
					href: '#' + idToggle
				});
			});
			athread.click(); // toggle after append
		};

	// if already exist, skip
	if (athreadResponses.find('div.thread').length) return;

	athreadResponses.find('.response-loader').removeClass('hidden');

	if (TheColumnHeader.data('network') == 'twitter') {
		$.get('http://api.twitter.com/1/related_results/show/'+ athread.attr('data-thread') +'.json?include_entities=1',
				function(response) {
					console.log(response);displayItems(response);
				},'json'
			).error(function(XHR, textStatus, errorThrown) {
				console.log(XHR);
				console.log("ERREUR: " + textStatus);
				console.log("ERREUR: " + errorThrown);
			});
		//http://api.twitter.com/1/related_results/show/254208368070258688.json?include_entities=1
		/*$.ajax({
			//url: 'https://api.twitter.com/1.1/statuses/show.json?id='+ athread.data('thread'),
			url: 'http://api.twitter.com/1/related_results/show/'+ athread.attr('data-thread') +'.json?include_entities=1',
			dataType: 'jsonP',
			success: function(response) {console.log(response);displayItems(response)},
			error: function(XHR, textStatus, errorThrown){
				athread.parent('.elgg-river-responses').find('.response-loader').addClass('hidden');
				console.log(XHR);
				console.log("ERREUR: " + textStatus);
				console.log("ERREUR: " + errorThrown);
			}
		});*/
	} else {
		elgg.post('ajax/view/deck_river/ajax_json/load_discussion', {
			dataType: "json",
			data: {
				discussion: athread.data('thread'),
			},
			success: function(response) {displayItems(response)},
			error: function() {
				athread.parent('.elgg-river-responses').find('.response-loader').addClass('hidden');
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



/*
 * Load Twitter timeline for an user
 */
elgg.deck_river.displayCount = function(response, TheColumn) {
	var TheColumnHeader = TheColumn.find('.column-header'),
		TheColumnRiver = TheColumn.find('.elgg-river'),
		networkReturn = (TheColumnHeader.data('network') == 'twitter') ? 'results' : 'activity'
		responseLength = response[networkReturn].length,
		countSpan = TheColumnHeader.find('.count').addClass('hidden');

	if (responseLength > 0) {
		countSpan.removeClass('hidden').text(responseLength);
		if (TheColumnRiver.scrollTop() > 50) {
			$('<li>', {'class': 'top-message'}).html(elgg.echo('deck_river:column:gotop', [responseLength])).click(function() {
				TheColumnRiver.scrollTo(0, 500, {easing:'easeOutQuart'});
			}).appendTo(TheColumn.find('.message-box')).effect('slide',{direction: 'up'}, 300);
			TheColumnRiver.unbind('scroll').bind('scroll', function() {
				if($(this).scrollTop() == 0) {
					TheColumn.find('.top-message').toggle('slide', {direction: 'up'}, 300, function() {$(this).remove()});
				}
			});
		}
		// column is hidden by #deck-river-lists scroll ?
		if (TheColumn.position().left < -TheColumn.width()+80) { // hidden at left
			var c = $('.deck-river-scroll-arrow.left div');
			(c.html() == '') ? c.html(responseLength) : c.html(parseInt(c.html()) + responseLength);
		} else if (TheColumn.position().left + TheColumn.width()-15 > $('#deck-river-lists').width()) { //hidden at right
			var c = $('.deck-river-scroll-arrow.right div');
			(c.html() == '') ? c.html(responseLength) : c.html(parseInt(c.html()) + responseLength);
		}
	}
};



/**
 * Displays messages in column
 *
 * @param {String} msgs The message we want to display
 * @param {dom} TheColumn The column we want to display
 * @param {Number} delay The amount of time to display the message in milliseconds. Defaults to 6 seconds.
 * @param {String} type The type of message (typically 'error' or 'message')
 * @private
 */
elgg.deck_river.column_messages = function(msgs, TheColumnHeader, delay, type) {
	if (elgg.isUndefined(msgs)) return;

	var classes = ['column-message'],
		messages_html = [],
		appendMessage = function(msg) {
			messages_html.push('<li class="' + classes.join(' ') + '"><p>' + msg + '</p></li>');
		};

	//validate delay.  Must be a positive integer.
	delay = parseInt(delay || 6000, 10);
	if (isNaN(delay) || delay <= 0) {
		delay = 6000;
	}

	//Handle non-arrays
	if (!elgg.isArray(msgs)) msgs = [msgs];

	if (type === 'error') {
		classes.push('elgg-state-error');
	} else {
		classes.push('elgg-state-success');
	}

	msgs.forEach(appendMessage);
	TheColumnHeader.parent().find('.column-messages').append($(messages_html.join('')))
		.effect('slide',{direction: 'up'}, 300).delay(delay).toggle('slide',{direction: 'up'}, 300, function() {$(this).html('')});
};

/**
 * Wrapper function for column_messages. Specifies "messages" as the type of message
 * @param {String} msgs  The message to display
 * @param {dom} TheColumn The column we want to display
 * @param {Number} delay How long to display the message (milliseconds)
 */
elgg.deck_river.column_message = function(msgs, TheColumn, delay) {
	elgg.deck_river.column_messages(msgs, TheColumn, delay, "message");
};

/**
 * Wrapper function for column_messages.  Specifies "errors" as the type of message
 * @param {String} errors The error message to display
 * @param {dom} TheColumn The column we want to display
 * @param {Number} delay  How long to dispaly the error message (milliseconds)
 */
elgg.deck_river.column_error = function(errors, TheColumn, delay) {
	elgg.deck_river.column_messages(errors, TheColumn, delay, "error");
};



String.prototype.addToLargeInt = function (value) {
	return this.substr(0, this.length-3)+(parseInt(this.substr(-3)) + value);
};
