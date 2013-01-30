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
 * @return void
 */
elgg.deck_river.LoadColumn = function(TheColumn) {
	TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + 'mod/elgg-deck_river/graphics/elgg_refresh.gif") no-repeat scroll -1px -1px transparent');
	if (TheColumn.find('h3').attr('data-direct') == 'true') {
		$.ajax({
			url: 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=ManUtopiK&count=20&include_rts=1&callback=?',
			dataType: 'json',
			success: function(response) {
				TheColumn.find('.elgg-river').html(elgg.deck_river.TwitterDisplayItems(response));
				TheColumn.find('.elgg-river').append($('<li>', {'class': 'moreItem'}).html(elgg.echo('deck_river:more')));
				// load more items
				TheColumn.find('.moreItem').click(function() {
					var TheColumn = $(this).closest('.column-river');
					var max_id = TheColumn.find('.elgg-river .elgg-list-item:last .elgg-friendlytime span').text() || 0;
					TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + 'mod/elgg-deck_river/graphics/elgg_refresh.gif") no-repeat scroll -1px -1px transparent');
					$.ajax({
						url: 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=ManUtopiK&count=21&include_rts=1&max_id='+max_id+'&callback=?',
						dataType: 'json',
						success: function(response) {
							TheColumn.find('.elgg-river').append(elgg.deck_river.TwitterDisplayItems(response.splice(1,20)))
								.find('.moreItem').appendTo(TheColumn.find('.elgg-river'));
							TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
						},
						error: function() {
							TheColumn.find('.elgg-river').html();
						}
					});
				});
			},
			error: function() {
				TheColumn.find('.elgg-river').html();
			}
		});
		TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
	} else {
		elgg.post('ajax/view/deck_river/ajax/column_river', {
			dataType: 'json',
			data: {
				tab: $('.deck-river-lists').attr('id'),
				column: TheColumn.attr('id'),
			},
			success: function(response) {
				response.TheColumn = TheColumn;
				$output = elgg.trigger_hook('deck-river', 'column:'+response.column_type, response, 'nohook');
				if ($output == 'nohook') {
					TheColumn.find('.elgg-river').html(elgg.deck_river.displayItems(response));
					if ( TheColumn.find('.elgg-list-item').length >= 20 ) {
						TheColumn.find('.elgg-river').append($('<li>', {'class': 'moreItem'}).html(elgg.echo('deck_river:more')));
						// load more items
						TheColumn.find('.moreItem').click(function() {
							var TheColumn = $(this).closest('.column-river');
							var LastItem = TheColumn.find('.elgg-river .elgg-list-item:last');
							TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + 'mod/elgg-deck_river/graphics/elgg_refresh.gif") no-repeat scroll -1px -1px transparent');
							elgg.post('ajax/view/deck_river/ajax/column_river', {
									dataType: 'json',
									data: {
										tab: $('.deck-river-lists').attr('id'),
										column: TheColumn.attr('id'),
										time_method: 'upper',
										time_posted: LastItem.find('.elgg-friendlytime span').text(),
									},
									success: function(response) {
										TheColumn.find('.elgg-river').append(elgg.deck_river.displayItems(response))
											.find('.moreItem').appendTo(TheColumn.find('.elgg-river'));
										//	var pos = LastItem.next().position();
										//TheColumn.find('.elgg-river').scrollTo('+='+pos.top+'px', 2500, {easing:'easeOutQuart'});
										TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
									}
							});
						});
					} else if ( TheColumn.find('.elgg-list-item').length == 0 ) {
						var user = elgg.get_logged_in_user_entity(),
						c_type = response.column_type;
						
						if (c_type == 'mine' && Math.round($.now()/1000) - elgg.get_logged_in_user_entity().time_created  < (60*60*24*7)) c_type = 'now';
						TheColumn.find('.elgg-river').html($('<table>', {height: '100%', width: '100%'}).append(
							$('<tr>').append(
								$('<td>', {'class': 'helper'}).html(elgg.echo('deck_river:helper:'+c_type, [user.location])))
						));
					}
				}
				TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
			}
		});
	}
};


/**
 * Load activity of user or group @todo merge with elgg.deck_river.LoadColumn
 *
 * Makes Ajax call to persist column and inserts the column html
 *
 * @param {TheEntity} the entity
 * @param {TheColumn} the column where response will be displayed
 * @return void
 */
elgg.deck_river.LoadEntity = function(TheEntity, TheColumn) {
	elgg.post('ajax/view/deck_river/ajax/entity_river', {
		dataType: "json",
		data: {
			guid: TheEntity,
		},
		success: function(response) {
			if (response) {
				TheColumn.find('.elgg-river').html(elgg.deck_river.displayItems(response));
				$('.elgg-submenu-river > .elgg-module-popup').mouseleave(function() {
					$('.elgg-submenu-river').removeClass('hover');
				});
				if ( TheColumn.find('.elgg-list-item').length >= 20 ) {
					TheColumn.find('.elgg-river').append($('<li>', {'class': 'moreItem'}).html(elgg.echo('deck_river:more')));
	
					// load more items
					TheColumn.find('.moreItem').click(function() {
						var TheColumn = $(this).closest('.elgg-river').parent();
						var LastItem = TheColumn.find('.elgg-river .elgg-list-item:last');
						TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + 'mod/elgg-deck_river/graphics/elgg_refresh.gif") no-repeat scroll -1px -1px transparent');
						elgg.post('ajax/view/deck_river/ajax/entity_river', {
								dataType: 'json',
								data: {
									guid: TheEntity,
									time_method: 'upper',
									time_posted: LastItem.find('.elgg-friendlytime span').text(),
							},
								success: function(response) {
									TheColumn.find('.elgg-river').append(elgg.deck_river.displayItems(response))
										.find('.moreItem').appendTo(TheColumn.find('.elgg-river'));
									//	var pos = LastItem.next().position();
									//TheColumn.find('.elgg-river').scrollTo('+='+pos.top+'px', 2500, {easing:'easeOutQuart'});
									TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
								}
						});
					});
				}
			}  else {
				TheColumn.find('.elgg-river').html(elgg.echo('deck_river:activity:none'));
			}
			TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
		}
	});
};

/**
 * duplicate LoadEntity for mentions. @todo merge with elgg.deck_river.LoadColumn
 *
 * Makes Ajax call to persist column and inserts the column html
 *
 * @param {TheEntity} the entity
 * @param {TheColumn} the column where response will be displayed
 * @return void
 */
elgg.deck_river.LoadMentions = function(TheEntity, TheColumn) {
	elgg.post('ajax/view/deck_river/ajax/entity_mention', {
		dataType: "json",
		data: {
			guid: TheEntity,
		},
		success: function(response) {
			if (response) {
				TheColumn.find('.elgg-river').html(elgg.deck_river.displayItems(response));
				$('.elgg-submenu-river > .elgg-module-popup').mouseleave(function() {
					$('.elgg-submenu-river').removeClass('hover');
				});
				if ( TheColumn.find('.elgg-list-item').length >= 20 ) {
					TheColumn.find('.elgg-river').append($('<li>', {'class': 'moreItem'}).html(elgg.echo('deck_river:more')));
	
					// load more items
					TheColumn.find('.moreItem').click(function() {
						var TheColumn = $(this).closest('.elgg-river').parent();
						var LastItem = TheColumn.find('.elgg-river .elgg-list-item:last');
						TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + 'mod/elgg-deck_river/graphics/elgg_refresh.gif") no-repeat scroll -1px -1px transparent');
						elgg.post('ajax/view/deck_river/ajax/entity_mention', {
								dataType: 'json',
								data: {
									guid: TheEntity,
									time_method: 'upper',
									time_posted: LastItem.find('.elgg-friendlytime span').text(),
							},
								success: function(response) {
									TheColumn.find('.elgg-river').append(elgg.deck_river.displayItems(response))
										.find('.moreItem').appendTo(TheColumn.find('.elgg-river'));
									//	var pos = LastItem.next().position();
									//TheColumn.find('.elgg-river').scrollTo('+='+pos.top+'px', 2500, {easing:'easeOutQuart'});
									TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
								}
						});
					});
				}
			}  else {
				TheColumn.find('.elgg-river').html(elgg.echo('deck_river:activity:none'));
			}
			TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
		}
	});
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
	TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + 'mod/elgg-deck_river/graphics/elgg_refresh.gif") no-repeat scroll -1px -1px transparent');
	TheColumn.find('.elgg-list-item').removeClass('newRiverItem');
	var since_id = TheColumn.find('.elgg-river .elgg-list-item:first .elgg-friendlytime span').first().text() || 0;
	
	if (TheColumn.find('h3').attr('data-direct') == 'true') {
		$.ajax({
			url: 'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=ManUtopiK&count=20&include_rts=1&since_id='+ since_id +'&callback=?',
			dataType: 'json',
			success: function(response) {
				var res = elgg.deck_river.TwitterDisplayItems(response);
				res.filter('.elgg-list-item').addClass('newRiverItem');
				TheColumn.find('.elgg-river').prepend(res).find('.newRiverItem').fadeIn('slow');
			}
		});
		TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
	} else {
		elgg.post('ajax/view/deck_river/ajax/column_river', {
			dataType: "json",
			data: {
				tab: $('.deck-river-lists').attr('id'),
				column: TheColumn.attr('id'),
				time_method: 'lower',
				time_posted: since_id,
			},
			success: function(response) {
				response.TheColumn = TheColumn;
				$output = elgg.trigger_hook('deck-river', 'column:'+response.column_type, response, 'nohook');
				if ($output == 'nohook') {
					var res = elgg.deck_river.displayItems(response);
					res.filter('.elgg-list-item').addClass('newRiverItem');
					if (res.length) TheColumn.find('.elgg-river > table').remove();
					TheColumn.find('.elgg-river').prepend(res).find('.newRiverItem').fadeIn('slow');
					TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
				}
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
	
	if (athread.attr('data-direct')) {
		//http://api.twitter.com/1/related_results/show/254208368070258688.json?include_entities=1
		$.ajax({
			url: 'https://api.twitter.com/1.1/statuses/show.json?id='+ athread.attr('data-thread'),
			//url: 'http://api.twitter.com/1/related_results/show/'+ athread.attr('data-thread') +'.json?include_entities=1',
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

		elgg.post('ajax/view/deck_river/ajax/load_discussion', {
			dataType: "json",
			data: {
				discussion: athread.attr('data-thread'),
			},
			success: function(response) {
				athread.parent('.elgg-river-responses').find('.response-loader').addClass('hidden');
				var idToggle = athread.parents('.column-river').attr('id') + '-' + athread.parents('.elgg-list-item').attr('class').match(/item-river-\d+/);
				
				athread.parent('.elgg-river-responses').append($('<div>', {id: idToggle, 'class': 'thread mts float'}).html(elgg.deck_river.displayItems(response, true)));
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

/**
 * Javascript template for river element @todo waiting for Elgg core developers to see wich library they will use (ember.js, ...) in elgg 1.9 or 2 and replace it with a js MVC system.
 *
 * @param {array}	json response
 */
elgg.deck_river.displayItems = function(response, thread) {
	var thread = thread || false,
		output = $(),
		wirearea = $('#thewire-textarea');
	
	var responseToWire = function(wireGuidValue, responseToUser, WireID) {
		$('.elgg-list-item.thewire').removeClass('responseAt');
		var wireForm = wirearea.parents('fieldset'),
			message = $('.item-river-'+WireID).addClass('responseAt').find('.elgg-river-message').first().text();;
		
		if (wireForm.find('input[name=parent_guid]').length) {
			wireForm.find('input[name=parent_guid]').val(wireGuidValue);
			wireForm.find('.responseTo').html(elgg.echo('responseToHelper:text', [responseToUser, message])).attr('title', elgg.echo('responseToHelper:delete', [responseToUser]));
		} else {
			wireForm.append($('<input>', {type: 'hidden', value: wireGuidValue, name: 'parent_guid'}));
			wireForm.find('.options').prepend($('<div>', {
				'class': 'responseTo tooltip s',
				title: elgg.echo('responseToHelper:delete', [responseToUser])
			}).html(elgg.echo('responseToHelper:text', [responseToUser, message])));
		}
	}

	$.each(response.activity, function(key, value) {
		var user = $.grep(response.users, function(e){ return e.guid == value.subject_guid; })[0],
			menuOutput = subMenuOutput = riverResponses = $();

		// make menu and submenu
		if (value.subtype == 'thewire' && !thread) {
			menuOutput = menuOutput.after(
				$('<li>').append(
					$('<span>', {'class': 'elgg-icon elgg-icon-response gwf tooltip s', title: elgg.echo('reply')}).html('&lt;').click(function() {
						responseToWire(value.object_guid, user.username, value.id);
						wirearea.val('@' + user.username).focus().keydown();
					})
				)
			);
			menuOutput = menuOutput.after(
				$('<li>').append(
					$('<span>', {'class': 'elgg-icon elgg-icon-retweet gwf tooltip s', title: elgg.echo('retweet')}).html('^').click(function() {
						//wirearea.val('RT @' + user.username + ' ' + value.message.replace(/<a.*?>|<\/a>/ig, '')).focus().keydown();
						wirearea.val('RT @' + user.username + ' ' + $(this).parents('.elgg-river').find('.item-river-'+value.id+' .elgg-river-message').text().replace(/^rt /i, '')).focus().keydown();
					})
				)
			);
			var match_users = value.message.match(/@\w{1,}/g); //@[A-Za-z0-9_]*.join(' ')
			if (match_users && !(match_users.length == 1 && $.inArray('@'+user.username, match_users) !== -1)) { // test if there are only river owner
				subMenuOutput = subMenuOutput.after(
					$('<li>').append(
						$('<span>', {'class': 'elgg-icon elgg-icon-response-all gwf'}).html('&le;').click(function() {
							match_users = value.message.match(/@\w{1,}/g);
							match_users = $.grep(match_users, function(value) {return value != '@'+user.username}); // Delete username of the item river owner  //($.inArray('@manu12', uie) !== -1)
							match_users = '@'+user.username + ' ' + match_users.join(' '); // and put it at the begining
							responseToWire(value.object_guid, user.username, value.id);
							wirearea.val(match_users).focus().keydown();
						})
					).append(elgg.echo('replyall'))
				);
			}
		}
		if (value.menu && value.menu.indexOf('delete') > -1) {
			subMenuOutput = subMenuOutput.after(
				$('<li>').append(
					$('<a>', {'class':'elgg-requires-confirmation', rel: elgg.echo('deleteconfirm'), is_action: 'is_action', 
					href: elgg.security.addToken(elgg.get_site_url() + 'action/river/delete?id=' + value.id)}).append(
						$('<span>', {'class':'elgg-icon elgg-icon-delete'}).html(elgg.echo('delete'))
			)))
		}
		if (value.responses != null && !thread) {
			riverResponses = riverResponses.after(
				$('<span>', {'class': 'elgg-icon elgg-icon-comment-sub gwf'}).html('c').after(
					$('<a>', {href: '#', 'class': 'thread float', 'data-thread': value.responses}).html(elgg.echo('deck_river:show_discussion')).after(
						$('<div>', {'class': "response-loader hidden"})
			)));
		}
		
		output = output.after(
			$('<li>', {'class': 'elgg-list-item item-river-'+ value.id +' '+ value.subtype +' '+ value.action_type}).mouseleave(function() {
				$(this).find('.elgg-submenu-river').removeClass('hover');
			}).append(
				$('<div>', {'class': 'elgg-image-block elgg-river-item clearfix'}).append(
					$('<div>', {'class': 'elgg-image'}).append(
						$('<div>', {'class': 'elgg-avatar elgg-avatar-small'}).append(
							$('<div>', {'class': 'user-info-popup', title: user.username}).append(
								$('<img>', {title: user.username, alt: user.username, src: user.icon})
				)))).append(
					$('<div>', {'class': 'elgg-body'}).append(function() {
						if (menuOutput.length) return $('<ul>', {'class': 'elgg-menu elgg-menu-river elgg-menu-hz elgg-menu-river-default'}).append(
							menuOutput.after(
								$('<li>', {'class': 'elgg-submenu-river'}).click(function() {
									$(this).addClass('hover');
								}).append(function() {
									if (subMenuOutput.length) return $('<span>', {'class':'elgg-icon elgg-icon-submenu-river gwf'}).html(']').after(
										$('<ul>', {'class': 'elgg-module-popup hidden'}).append(subMenuOutput).mouseleave(function() {
											$('.elgg-submenu-river').removeClass('hover');
										
								}));
						})));
					}).append(
						$('<div>', {'class': 'elgg-river-summary'}).html(value.summary+'<br/>').append(
							$('<span>', {'class': 'elgg-river-timestamp'}).append(
								$('<span>', {'class': 'elgg-friendlytime'}).append(
									$('<acronym>', {title: value.posted_acronym, 'class': 'tooltip w'}).html(elgg.friendly_time(value.posted)).after(
									$('<span>', {'class': 'hidden'}).html(value.posted)
					))))).append(
						$('<div>', {'class': 'elgg-river-message'}).html(value.message)
					).append(function() {
						if (riverResponses.length) return $('<div>', {'class': 'elgg-river-responses'}).html(riverResponses);
					})
				)
			)
		);
	});
	return output;
};



/**
 * Javascript template for river element @todo waiting for Elgg core developers to see wich library they will use (ember.js, ...) in elgg 1.9 or 2 and replace it with a js MVC system.
 *
 * @param {array}	json response
 */
elgg.deck_river.TwitterDisplayItems = function(response, thread) {
	var thread = thread || false,
		output = $(),
		wirearea = $('#thewire-textarea');
	
	$.each(response, function(key, value) {
		var menuOutput = subMenuOutput = riverResponses = $();
//console.log(value);
		// make menu and submenu
		if (!thread) {
			menuOutput = menuOutput.after(
				$('<li>').append(
					$('<span>', {'class': 'elgg-icon elgg-icon-response gwf tooltip s', title: elgg.echo('reply')}).html('&lt;').click(function() {
						wirearea.val('@' + value.user.screen_name).focus().keydown();
						//wirearea.parents('fieldset').append('<input type="hidden" value="'+ value.object_guid +'" name="parent_guid">');
					})
				)
			);
			menuOutput = menuOutput.after(
				$('<li>').append(
					$('<span>', {'class': 'elgg-icon elgg-icon-retweet gwf tooltip s', title: elgg.echo('retweet')}).html('^').click(function() {
						//wirearea.val('RT @' + user.username + ' ' + value.message.replace(/<a.*?>|<\/a>/ig, '')).focus().keydown();
						wirearea.val('RT @' + value.user.screen_name + ' ' + $(this).parents('.elgg-list-item').find('.elgg-river-message').text().replace(/^rt /i, '')).focus().keydown();
					})
				)
			);
			var match_users = value.text.match(/@\w{1,}/g); //@[A-Za-z0-9_]*.join(' ')
			if (match_users && !(match_users.length == 1 && $.inArray('@'+value.user.screen_name, match_users) !== -1)) { // test if there are only river owner
				subMenuOutput = subMenuOutput.after(
					$('<li>').append(
						$('<span>', {'class': 'elgg-icon elgg-icon-response-all gwf'}).html('&le;').click(function() {
							match_users = value.text.match(/@\w{1,}/g);
							match_users = $.grep(match_users, function(value2) {return value2 != '@'+value.user.screen_name}); // Delete username of the item river owner  //($.inArray('@manu12', uie) !== -1)
							match_users = '@'+value.user.screen_name + ' ' + match_users.join(' '); // and put it at the begining
							wirearea.val(match_users).focus().keydown();
						})
					).append(elgg.echo('replyall'))
				);
			}
		}
		if (value.retweet_count != 0) {
			if (value.retweet_count == '1') {
				riverResponses = $('<span>', {'class': 'elgg-icon elgg-icon-retweet-sub gwf'}).html('^').after($('<span>', {'class': 'prm'}).html(elgg.echo('retweet:one', [value.retweet_count])));
			} else {
				riverResponses = $('<span>', {'class': 'elgg-icon elgg-icon-retweet-sub gwf'}).html('^').after($('<span>', {'class': 'prm'}).html(elgg.echo('retweet:twoandmore', [value.retweet_count])));
			}
		}
		//console.log(value.in_reply_to_status_id);
		if (value.in_reply_to_status_id != null && !thread) {
			riverResponses = riverResponses.after(
				$('<span>', {'class': 'elgg-icon elgg-icon-comment-sub gwf'}).html('c').after(
					$('<a>', {href: '#', 'class': 'thread float', 'data-direct': 'true', 'data-thread': value.in_reply_to_status_id_str}).html(elgg.echo('deck_river:show_discussion')).after(
						$('<div>', {'class': "response-loader hidden"})
			)));
			// conversation > http://api.twitter.com/1/related_results/show/254208368070258688.json?include_entities=1
		}
		//console.log(riverResponses);

		output = output.after(
			$('<li>', {'class': 'elgg-list-item item-twitter-'+ value.id}).mouseleave(function() {
				$(this).find('.elgg-submenu-river').removeClass('hover');
			}).append(
				$('<div>', {'class': 'elgg-image-block elgg-river-item clearfix'}).append(
					$('<div>', {'class': 'elgg-image'}).append(
						$('<div>', {'class': 'elgg-avatar elgg-avatar-small'}).append(
							$('<div>', {'class': 'user-info-popup', title: value.user.screen_name}).append(
								$('<img>', {title: value.user.screen_name, alt: value.user.screen_name, src: value.user.profile_image_url})
				)))).append(
					$('<div>', {'class': 'elgg-body'}).append(function() {
						if (menuOutput.length) return $('<ul>', {'class': 'elgg-menu elgg-menu-river elgg-menu-hz elgg-menu-river-default'}).append(
							menuOutput.after(
								$('<li>', {'class': 'elgg-submenu-river'}).click(function() {
									$(this).addClass('hover');
								}).append(function() {
									if (subMenuOutput.length) return $('<span>', {'class':'elgg-icon elgg-icon-submenu-river gwf'}).html(']').after(
										$('<ul>', {'class': 'elgg-module-popup hidden'}).append(subMenuOutput).mouseleave(function() {
											$('.elgg-submenu-river').removeClass('hover');
										
								}));
						})));
					}).append(
						$('<div>', {'class': 'elgg-river-summary'}).html(value.user.screen_name+'<br/>').append(
							$('<span>', {'class': 'elgg-river-timestamp'}).append(
								$('<span>', {'class': 'elgg-friendlytime'}).append(
									$('<acronym>', {title: value.created_at, 'class': 'tooltip w'}).html(elgg.friendly_time(value.created_at)).after(
									$('<span>', {'class': 'hidden'}).html(value.id)
					))))).append(
						$('<div>', {'class': 'elgg-river-message'}).html(value.text.TwitterParseURL().TwitterParseUsername().TwitterParseHashtag())
					).append(function() {
						if (riverResponses.length) return $('<div>', {'class': 'elgg-river-responses'}).html(riverResponses);
					})
				)
			)
		);
	});
	return output;
};


String.prototype.TwitterParseURL = function () {
	return this.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&\?\/.=]+/g, function (url) {
		return '<a target="_blank" rel="nofollow" href="'+url+'">'+url+'</a>';
	});
};
String.prototype.TwitterParseUsername = function () {
	return this.replace(/@[A-Za-z0-9-_]+/g, function (u) {
		var username = u.replace("@", "")
		return u.link("http://twitter.com/" + username);
	});
};
String.prototype.TwitterParseHashtag = function () {
	return this.replace(/#[A-Za-z0-9_-àæéèêëîïôöœùûüç]+/g, function (t) {
		var tag = t.replace("#", "%23")
		return t.link("http://search.twitter.com/search?q=" + tag);
	});
};
