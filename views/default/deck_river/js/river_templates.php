/**
 *	Elgg-deck_river plugin
 *	@package elgg-deck_river
 *	@author Emmanuel Salomon @ManUtopiK
 *	@license GNU Affero General Public License, version 3 or late
 *	@link https://github.com/ManUtopiK/elgg-deck_river
 *
 *	Elgg-deck_river river templates js
 *
 */

/**
 * Javascript template for river element @todo waiting for Elgg core developers to see wich library they will use (ember.js, ...) in elgg 1.9 or 2 and replace it with a js MVC system.
 *
 * @param {array}	json response
 */
elgg.deck_river.elggDisplayItems = function(response, thread) {
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
									if (subMenuOutput.length) return $('<span>', {'class':'elgg-icon elgg-icon-submenu-river gwf'}).html('+').after(
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
elgg.deck_river.twitterDisplayItems = function(response, thread) {
	var thread = thread || false,
		output = $(),
		wirearea = $('#thewire-textarea');

	if (response.results) { // json returned by Twitter is different between twitter search api and twitter main api
		response = response.results;
		$.each(response, function(key, value) {
			response[key].user = {screen_name: value.from_user};
			response[key].user.profile_image_url = response[key].profile_image_url;
		});
	}

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

		if (value.retweet_count == 1) {
			riverResponses = $('<span>', {'class': 'elgg-icon elgg-icon-retweet-sub gwf'}).html('^').after($('<span>', {'class': 'prm float'}).html(elgg.echo('retweet:one', [value.retweet_count])));
		} else if (value.retweet_count > 1) {
			riverResponses = $('<span>', {'class': 'elgg-icon elgg-icon-retweet-sub gwf'}).html('^').after($('<span>', {'class': 'prm float'}).html(elgg.echo('retweet:twoandmore', [value.retweet_count])));
		}
		//console.log(value.in_reply_to_status_id);
		if (value.in_reply_to_status_id != null && !thread) {
			riverResponses = riverResponses.after(
				$('<span>', {'class': 'elgg-icon elgg-icon-comment-sub gwf'}).html('c').after(
					$('<a>', {href: '#', 'class': 'thread float', 'data-network': 'true', 'data-thread': value.in_reply_to_status_id_str}).html(elgg.echo('deck_river:show_discussion')).after(
						$('<div>', {'class': "response-loader hidden"})
			)));
			// conversation > http://api.twitter.com/1/related_results/show/254208368070258688.json?include_entities=1
		}
		//console.log(riverResponses);

		var postedTimestamp = value.created_at.TwitterFormatDate();
		output = output.after(
			$('<li>', {'class': 'elgg-list-item item-twitter-'+ value.id}).mouseleave(function() {
				$(this).find('.elgg-submenu-river').removeClass('hover');
			}).append(
				$('<div>', {'class': 'elgg-image-block elgg-river-item clearfix'}).append(
					$('<div>', {'class': 'elgg-image'}).append(
						$('<div>', {'class': 'elgg-avatar elgg-avatar-small'}).append(
							$('<div>', {'class': 'twitter-user-info-popup', title: value.user.screen_name}).append(
								$('<img>', {title: value.user.screen_name, alt: value.user.screen_name, src: value.user.profile_image_url})
				)))).append(
					$('<div>', {'class': 'elgg-body'}).append(function() {
						if (menuOutput.length) return $('<ul>', {'class': 'elgg-menu elgg-menu-river elgg-menu-hz elgg-menu-river-default'}).append(
							menuOutput.after(
								$('<li>', {'class': 'elgg-submenu-river'}).click(function() {
									$(this).addClass('hover');
								}).append(function() {
									if (subMenuOutput.length) return $('<span>', {'class':'elgg-icon elgg-icon-submenu-river gwf'}).html('+').after(
										$('<ul>', {'class': 'elgg-module-popup hidden'}).append(subMenuOutput).mouseleave(function() {
											$('.elgg-submenu-river').removeClass('hover');

								}));
						})));
					}).append(
						$('<div>', {'class': 'elgg-river-summary'}).html($('<span>', {'class': 'twitter-user-info-popup', title: value.user.screen_name}).html(value.user.screen_name+'<br/>')).append(
							$('<span>', {'class': 'elgg-river-timestamp'}).append(
								$('<span>', {'class': 'elgg-friendlytime'}).append(
									$('<acronym>', {title: value.created_at, 'class': 'tooltip w'}).html(elgg.friendly_time(postedTimestamp)).after(
									$('<span>', {'class': 'hidden'}).html(postedTimestamp)
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

String.prototype.TwitterFormatDate = function () {
	return $.datepicker.formatDate('@', new Date(this))/1000;
};
String.prototype.TwitterParseURL = function () {
	return this.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&\?\/.=]+/g, function (url) {
		return '<a target="_blank" rel="nofollow" href="'+url+'">'+url+'</a>';
	});
};
String.prototype.TwitterParseUsername = function () {
	return this.replace(/@[A-Za-z0-9-_]+/g, function (u) {
		return '<a href="#" class="twitter-user-info-popup" title="'+u.replace("@", "")+'">'+u+'</a>';
	});
};
String.prototype.TwitterParseHashtag = function () {
	return this.replace(/#[A-Za-z0-9_-àæéèêëîïôöœùûüç]+/g, function (t) {
		var tag = t.replace("#", "%23")
		return t.link("http://search.twitter.com/search?q=" + tag);
	});
};
