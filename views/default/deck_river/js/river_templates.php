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
 * Return html river
 */
elgg.deck_river.displayRiver = function(response, network, thread) {
	var network = network || 'elgg',
		thread = thread || false;

	if (elgg.isString(response.results)) {
		return $(response.results);
	} else if (response.results && response.results.length != 0) {
		return elgg.deck_river[network + 'DisplayItems'](response, thread);
	}
};



/**
 * Javascript template for river element @todo waiting for Elgg core developers to see wich library they will use (ember.js, ...) in elgg 1.9 or 2 and replace it with a js MVC system.
 *
 * @param {array}	json response
 */
elgg.deck_river.elggDisplayItems = function(response, thread) {
	var output = '',
		elggRiverTemplate = Mustache.compile($('#elgg-river-template').html());

	// Put users and groups in global var DataEntities
	$.each(response.users, function(i, entity) {
		elgg.deck_river.storeEntity(entity);
	});

	$.each(response.results, function(key, value) {

		// add user object
		value.user = $.grep(response.users, function(e){ return e.guid == value.subject_guid; })[0];
		// add friendly_time
		value.friendly_time = elgg.friendly_time(value.posted);

		value.text = elgg.isArray(value.message) ? null : value.message;
		if (value.type == 'object' && value.text) {
			value.message = value.text.ParseGroup().ParseEverythings('elgg');
			value.text = $('<div>').html(value.text).text();
		}

		if (value.method == 'site') delete value.method;

		// Remove responses if in thread
		if (thread && !elgg.isNull(value.responses)) delete value.responses;

		output += elggRiverTemplate(value);

	});
	return $(output);
};



/**
 * Javascript template for river element @todo waiting for Elgg core developers to see wich library they will use (ember.js, ...) in elgg 1.9 or 2 and replace it with a js MVC system.
 *
 * @param {array}	json response
 */
elgg.deck_river.twitterDisplayItems = function(response, thread) {
	var output = '',
		elggRiverTemplate = Mustache.compile($('#elgg-river-twitter-template').html());

	$.each(response.results, function(key, value) {
		var retweet = false,
			reply = false;

		if (!response.column_type) { // direct link. json returned by Twitter is different between twitter search api and twitter main api
			value.user = {screen_name: value.from_user, profile_image_url_https: value.profile_image_url_https};
		} else if (response.column_type == 'get_direct_messages') { // json is different with direct_messages
			value.user = value.sender;
		} else if (response.column_type == 'get_direct_messagesSent') { // json is different with direct_messages
			value.user = value.recipient;
		}

		// store information about twitter user
		elgg.deck_river.storeEntity(value.user, 'twitter');

		// this is a reteweet
		if (value.retweeted_status) {
			var which = ' <span class="twitter-user-info-popup" title="' + value.user.screen_name + '">' + value.user.screen_name + '</span>';

			if (value.retweet_count === 1) {
				retweet = elgg.echo('retweeted_by', [which]);
			} else { // there is retweeted_satus so if is not 1 this is > 1
				retweet = elgg.echo('retweeted_which', [value.retweet_count, which]);
			}

			delete value.retweeted_status.created_at; // we remove this key to keep created_at and id_str of last retweet.
			delete value.retweeted_status.id_str;
			$.extend(value, value.retweeted_status); // retweet_status contain all information about origin tweet, so we swich it.

			elgg.deck_river.storeEntity(value.user, 'twitter'); // store original user
		} else if (value.retweet_count === 1) {
			retweet = elgg.echo('retweet:one');
		} else if (value.retweet_count > 1) { // there is retweeted_satus so if is not 1 this is > 1
			retweet = elgg.echo('retweet:twoandmore', [value.retweet_count]);
		}

		// format date and add friendly_time
		value.posted = value.created_at.FormatDate();
		value.friendly_time = elgg.friendly_time(value.posted);
		if (value.source) {
			value.source = value.source[0] == '&' ? $('<div>').html(value.source).text() : value.source ; // twitter search api retun encoded string, not main api
		}

		// make menus
		if (!thread) {
			// add replyall in submenu
			if (/@\w{1,}/g.test(value.text)) {
				value.submenu = [{
					name: 'response-all',
					content: elgg.echo('replyall')
				}];
			}
		}

		// Fill responses (retweet and discussion link)
		value.responses = {
			retweet: retweet ? retweet : false,
			reply: value.in_reply_to_status_id != null && !thread // thread id is filled by id_str in mustache template. Only true/false is sending.
		};

		// parse tweet text
		value.message = value.text.ParseEverythings('twitter');

		output += elggRiverTemplate(value);

	});
	return $(output);
};



/**
 * Javascript template for river element @todo waiting for Elgg core developers to see wich library they will use (ember.js, ...) in elgg 1.9 or 2 and replace it with a js MVC system.
 *
 * @param {array}	json response
 */
elgg.deck_river.facebookDisplayItems = function(response, thread) {
	var output = '',
		imgs = [],
		elggRiverTemplate = Mustache.compile($('#elgg-river-facebook-template').html());
		Mustache.compilePartial('erFBt-comment', $('#erFBt-comment').html());

	$.each(response.data, function(key, value) {
		// store information about facebook user
		//elgg.deck_river.storeEntity(value.user, 'twitter');


		// format date and add friendly_time
		if (!value.updated_time) value.updated_time = value.created_time;
		value.posted = value.updated_time.FormatDate();
		value.friendly_time = elgg.friendly_time(value.posted);

		// parse tweet text
		//value.text = value.message.ParseEverythings('facebook');
		if (!value.message) value.message = value.story; // somes stranges status post doesn't have message but story instead
		if (value.message) {
			value.message_original = value.message;
			value.message = value.message.TruncateString().ParseEverythings('facebook');
		}

		if (value.likes) {
			var vld = value.likes.data, u = '';

			value.likes.string = elgg.echo('deck_river:facebook:like'+(vld.length == 1 ? '':'s'), [vld.length]);
			$.each(vld, function(i, e) {
				u += ','+e.id
			});
			value.likes.users = u.substr(1);
		}
		if (value.shares) {
			var vsc = value.shares.count;
			value.shares.string = elgg.echo('deck_river:facebook:share'+(vsc == 1 ? '':'s'), [vsc]);
		}

		if (value.comments) {
			var vcd = value.comments.data;
			$.each(vcd, function(i,e) {
				var ef = value.comments.data[i].posted = e.created_time.FormatDate();
				value.comments.data[i].friendly_time = elgg.friendly_time(ef);
				value.comments.data[i].message = e.message.TruncateString().ParseEverythings('facebook');
			});
			if (vcd.length > 4) {
				value.comments.dataBefore = vcdb = value.comments.data.splice(0, vcd.length-3);
				value.comments.before = elgg.echo('deck_river:facebook:show_comments', [vcdb.length]);
			}
		}

		value.rand = (Math.random()+"").replace('.','');

		value['type'+value.type] = true; // used for mustache
		if (value.status_type == 'created_note') {
			value.typenote = 1;
		}

		if (!value.full_picture) value.full_picture = value.picture;
		if (value.full_picture) imgs.push({src: value.full_picture, id: value.id});
		output += elggRiverTemplate(value);

	});

	// resize images
	$.each(imgs, function(i, e) {
		var img = new Image();

		img.src = e.src;
		img.onload = function() {
			var tw = this.width, th = this.height,
				$eri = $('#img'+e.id).data('img', [tw, th]).parent();

			if (tw >= $eri.width() || tw >= 600 || $eri.find('.elgg-body').html().replace(/\s+/, '') == '') $('#img'+e.id).height(Math.min($eri.addClass('big').width(), '600')/tw*th);
			if (tw <= 1) $('#img'+e.id).remove(); // Don' know why, but sometimes facebook return a "safe_image" with 1x1 pixels
		};
		img.onerror = function() {$('#img'+e.id).remove()};
	});

	return $(output);
};


