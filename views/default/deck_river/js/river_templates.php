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


$(document).ready(function() {

FB.init({
	appId: '213084262191194',
	channelUrl: elgg.get_site_url()+'mod/elgg-deck_river/lib/channel.php',
	oauth: true
});

	$('.elgg-menu-item-response a').live('click', function() {
		var item = $(this).closest('.elgg-list-item');
		elgg.deck_river.responseToWire(item, '@' + item.data('username') + ' ');
	});

	$('.elgg-menu-item-retweet a').live('click', function() {
		var item = $(this).closest('.elgg-list-item');
		$('#thewire-textarea').val(
			'RT @' + item.data('username') + ': ' + item.find('.elgg-river-message').first().text().replace(/^rt /i, '')
		).focus().keydown();
	});

	$('.elgg-menu-item-response-all a').live('click', function() {
		var item = $(this).closest('.elgg-list-item'),
			match_users = item.find('.elgg-river-message').first().text().match(/\s{1}@\w{1,}/g);

		match_users = $.grep(match_users, function(val, i) { // don't mention himself
			return val != ' @'+elgg.session.user.username;
		});
		match_users = '@'+item.data('username') + $.grep(match_users, function(val, i) { // Prepend the username of the item river owner
			return val != ' @'+item.data('username');
		}).join('') + ' ';
		elgg.deck_river.responseToWire(item, match_users);
	});

	$('a[data-twitter_action]').live('click', function() {
		var action = $(this).data('twitter_action'),
			userId = $(this).data('user_id'),
			twitterAccount = $(this).data('twitter_account'),
			accounts = $('#thewire-network .net-profile.twitter');

		if (accounts.length > 1 && elgg.isUndefined(twitterAccount)) {
			var accountsString = '';

			elgg.deck_river.createPopup('choose-twitter-account-popup', elgg.echo('deck_river:twitter:choose_account'), function() {
				$('#choose-twitter-account-popup').find('.elgg-icon-push-pin').remove();
			});
			$.each(accounts, function(i, e) {
				accountsString += '<li><a href="#" data-twitter_action="'+action+'" data-user_id="'+userId+'" data-twitter_account="'+$(e).find('input').val()+'">'+$(e).find('.twitter-user-info-popup').attr('title')+'</a></li>';
			});
			$('#choose-twitter-account-popup > .elgg-body').html('<ul>'+accountsString+'</ul>');
		} else {
			elgg.action('deck_river/twitter', {
				data: {
					twitter_account: twitterAccount || accounts.find('input').val(),
					method: action,
					options: {'user_id': $(this).data('user_id')}
				},
				dataType: 'json',
				success: function(json) {
					if (!elgg.isUndefined(json.output.result)) {
						var response = json.output.result;

						if (action == 'post_friendshipsCreate') response.followers_count++;
						if (action == 'post_friendshipsDestroy') response.followers_count--;
						elgg.deck_river.storeEntity(response, 'twitter');
						response.profile_image_url = response.profile_image_url.replace(/_normal/, '');
						response.description = response.description.ParseEverythings('twitter');
						$('#user-info-popup > .elgg-body').html(Mustache.render($('#twitter-user-profile-template').html(), response));
						elgg.system_message(elgg.echo('deck_river:twitter:post:'+action, [response.screen_name]));
						$('#choose-twitter-account-popup').remove();
					}
				},
				error: function() {
					elgg.register_error(elgg.echo('deck_river:twitter:error'));
				}
			});
		}

		return false;
	});

});

elgg.deck_river.responseToWire = function(riverItem, message) {
	var network = riverItem.closest('.column-river').find('.column-header').data('network') || 'elgg';
	$('.elgg-list-item').removeClass('responseAt');
	$('.item-'+network+'-'+riverItem.attr('data-id')).addClass('responseAt');
	$('#thewire-header').find('.responseTo')
		.removeClass('hidden')
		.html(elgg.echo('responseToHelper:text', [riverItem.data('username'), riverItem.find('.elgg-river-message').first().text()]))
		.attr('title', elgg.echo('responseToHelper:delete', [riverItem.data('username')]))
	.next('.parent').val(riverItem.attr('data-object_guid')).attr('name', network+'_parent');
	$('#thewire-textarea').val(message).focus().keydown();
};



/**
 * Return html river
 */
elgg.deck_river.displayRiver = function(response, TheColumnHeader, thread) {
	var network = TheColumnHeader.data('network') || 'elgg',
		thread = thread || false;

	if (response.column_message) elgg.deck_river.column_message(response.column_message, TheColumnHeader);
	if (response.column_error) elgg.deck_river.column_error(response.column_error, TheColumnHeader);

	if (elgg.isString(response.results)) {
		return $(response.results);
	} else if (elgg.isString(response.results)) {
		return $(response.results);
	} else if (response.results && response.results.length != 0) {
		return elgg.deck_river[network + 'DisplayItems'](response, thread);
	}
};



/**
 * Put users and groups in global var DataEntities
 */
elgg.deck_river.storeEntity = function(entity, network) {
	var network = network || 'elgg';

	if (network == 'elgg') {
		if (!$.grep(DataEntities.elgg, function(e){ return e.guid === entity.guid; }).length) DataEntities.elgg.push(entity);
	} else if (network == 'twitter') {
		// Put user in global var DataEntities.twitter
		if (DataEntities.twitter.length) {
			var found = false;
			$.each(DataEntities.twitter, function(i, e) {
				if (e.screen_name === entity.screen_name) { // the same !
					if (!elgg.isUndefined(entity.id_str)) { // new user is complete
						DataEntities.twitter[i] = entity; // We can fill more the profile !
						found = true;
						return false;
					}
					found = true;
				}
			});
			if (!found) DataEntities.twitter.push(entity); // new
		} else {
			DataEntities.twitter.push(entity); // new
		}
	}
};



/**
 * Find a user in DataEntities, query
 * @param  {[type]} name    [description]
 * @param  {[type]} network [description]
 * @return {[type]}         [description]
 */
elgg.deck_river.findUser = function(name, network, key) {
	var network = network || 'elgg',
		key = key || (network == 'twitter') ? 'screen_name' : 'username';

	return $.grep(DataEntities[network], function(e) {
		return e[key] == name;
	})[0];
};



/**
 * Search users in DataEntities (eg: twitt return all user with name started by 'twitt')
 * @param  {string}  query    The name of the user to match
 * @param  {string}  network  The network to search, default Elgg
 * @return {array}            An array of matches
 */
elgg.deck_river.searchUsers = function(query, network, key) {
	var network = network || 'elgg',
		key = key || 'username';

	if (network == 'twitter') key = key || 'screen_name';
	if (network == 'all') {
		var ret = [];
		$.each(DataEntities, function(e) {
			$.extend(ret, elgg.deck_river.searchUsers(query, e));
		});
		return ret;
	} else {
		return $.grep(DataEntities[network], function(e) {
			return e[key].match(new RegExp(query+'.*', 'i'));
		});
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

		// add replyall in submenu
		if (!thread && value.subtype == 'thewire') {
			var match_users = value.message.match(/@\w{1,}/g);
			if (match_users && match_users.length > 1) {
				value.menu.submenu.unshift({
					name: 'response-all',
					content: '<a href="#">'+'<span class="elgg-icon elgg-icon-response"></span>'+elgg.echo('replyall')+'</a>'
				});
			}
		}

		// make menus
		var tempMenu = {};
		$.each(value.menu, function(i, e) {
			var eHTML = '';
			$.each(e, function(j, h) {
				eHTML += '<li class="elgg-menu-item-'+h.name+'">'+h.content+'</li>';
			});
			tempMenu[i] = eHTML;
		});
		value.menu = tempMenu;

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
			value.menu = {'default': [{
				name: 'response',
				content: '<a href="" title="' + elgg.echo('reply') + '" class="gwfb tooltip s"><span class="elgg-icon elgg-icon-response "></span></a>'
			},{
				name: 'retweet',
				content: '<a href="" title="' + elgg.echo('retweet') + '" class="gwfb tooltip s"><span class="elgg-icon elgg-icon-share "></span></a>'
			}], submenu: []};
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
			var match_users = value.text.match(/@\w{1,}/g);
			if (match_users && match_users.length > 1) {
				value.menu.submenu.unshift({
					name: 'response-all',
					content: '<a href="#">'+'<span class="elgg-icon elgg-icon-response"></span>'+elgg.echo('replyall')+'</a>'
				});
			}

			var tempMenu = {};
			$.each(value.menu, function(i, e) {
				var eHTML = '';
				$.each(e, function(j, h) {
					eHTML += '<li class="elgg-menu-item-'+h.name+'">'+h.content+'</li>';
				});
				tempMenu[i] = eHTML;
			});
			value.menu = tempMenu;
		}

		// Fill responses (retweet and discussion link)
		value.responses = {
			retweet: retweet ? retweet : false,
			reply: value.in_reply_to_status_id != null && !thread // thread id is filled by id_str in mustache template. Only true/false is sending.
		};

		// parse tweet text
		value.text = value.text.ParseEverythings('twitter');

		output += elggRiverTemplate(value);

	});
	return $(output);
};

String.prototype.FormatDate = function () {
	return $.datepicker.formatDate('@', new Date(this))/1000;
};
String.prototype.ParseURL = function () {
	return this.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:,%&\?\/.=]+/g, function (url) {
		return '<a target="_blank" rel="nofollow" href="'+url+'">'+url+'</a>';
	});
};
String.prototype.ParseUsername = function () {
	return this.replace(/@[A-Za-z0-9-_]+/g, function (u) {
		return '<a href="#" class="twitter-user-info-popup" title="'+u.replace("@", "")+'">'+u+'</a>';
	});
};
String.prototype.ParseHashtag = function (network) {
	return this.replace(/([^"]|^)(#[A-Za-z0-9_-àâæéèêëîïôöœùûüç]+)/g, function (h, $1, $2) {
		return $1+'<a href="#" class="hashtag-info-popup" title="'+$2+'" data-network="'+network+'">'+$2+'</a>';
	});
};
String.prototype.ParseEverythings = function (network) {
	return this.ParseURL().ParseUsername().ParseHashtag(network);
};
String.prototype.TruncateString = function (length, more) {
	var length = length || 140,
		more = more || '[...]',
		trunc = '';

	do {
		length++;
		trunc = this.substring(0, length);
	} while (trunc.length !== this.length && trunc.slice(-1) != ' ');
	if (length < this.length) {
		var rand = (Math.random()+"").replace('.','');
		return this.substring(0, length-1) +
				'<span id="text-part-'+rand+'" class="hidden">' + this.substring(length-1, this.length) + '</span>' + 
				'<a rel="toggle" href="#text-part-'+rand+'"> ' + more + '</a>';
	} else {
		return trunc;
	}
};

var rpd = [];
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

rpd.push(response);
	$.each(response.data, function(key, value) {
		// store information about facebook user
		//elgg.deck_river.storeEntity(value.user, 'twitter');


		// format date and add friendly_time
		value.posted = value.created_time.FormatDate();
		value.friendly_time = elgg.friendly_time(value.posted);

		// make menus
		value.menu = {
			default: 
				'<li><a href="#" class="gwfb tooltip s" title="'+elgg.echo('deck_river:facebook:action:like')+'"><span class="elgg-icon elgg-icon-like"></span></a></li>'+
				'<li><a href="#" class="gwfb tooltip s" title="'+elgg.echo('deck_river:facebook:action:share')+'"><span class="elgg-icon elgg-icon-share"></span></a></li>'
		};

		// parse tweet text
		//value.text = value.message.ParseEverythings('facebook');
		if (!value.message) value.message = value.story; // somes stranges status post doesn't have message but story instead
		if (value.message) value.message = value.message.TruncateString().ParseEverythings('facebook');

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
			if (vcd.length > 3) {
				value.comments.dataBefore = vcdb = value.comments.data.splice(0, vcd.length-3);
				value.comments.before = elgg.echo('deck_river:facebook:show_comments', [vcdb.length]);
			}
		}

		value['type'+value.type] = true; // used for mustache
		if (value.status_type == 'created_note') {
			//delete value.typelink;
			value.typenote = 1;
			console.log(value);
		}

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

			if (tw >= $eri.width() || tw >= 600) $('#img'+e.id).height(Math.min($eri.addClass('big').width(), '600')/tw*th);
			if (tw <= 1) $('#img'+e.id).remove(); // Don' know why, but sometimes facebook return a "safe_image" with 1x1 pixels
		};
		img.onerror = function() {$('#img'+e.id).remove()};
	});

	return $(output);
};

FBgraph = function(query, callback) {
	$.ajax({
		url: 'https://graph.facebook.com/' + query,
		dataType: 'json',
	})
	.done(function(rep) {
		callback(rep);
	})
	.fail(function() {
		return false;
	});
};

FBfql = function(query, callback) { //.replace(/foo/g, "bar")
	$.ajax({
		url: 'https://graph.facebook.com/' + query,
		dataType: 'json',
	})
	.done(function(rep) {
		callback(rep);
	})
	.fail(function() {
		return false;
	});
};

elgg.deck_river.resizeRiverImages = function() {
	$.each($('.elgg-page-body #deck-river-lists .elgg-river-image .elgg-image'), function(i, e) {
		var s = $(e).data('img'),
			$eri = $(e).parent();

		if (s[0] >= $eri.width() || s[0] >= 600) {
			$(e).height(Math.min($eri.addClass('big').width(),'600')/s[0]*s[1]);
		} else {
			$eri.removeAttr('big')
		}
	});
};


/*! Installing mustache for waiting which MVC elgg core team going to choose.
 * mustache.js - Logic-less {{mustache}} templates with JavaScript
 * http://github.com/janl/mustache.js
 */
(function(a,b){if(typeof exports==="object"&&exports){module.exports=b}else{if(typeof define==="function"&&define.amd){define(b)}else{a.Mustache=b}}}(this,(function(){var v={};v.name="mustache.js";v.version="0.7.2";v.tags=["{{","}}"];v.Scanner=t;v.Context=r;v.Writer=p;var d=/\s*/;var k=/\s+/;var h=/\S/;var g=/\s*=/;var m=/\s*\}/;var s=/#|\^|\/|>|\{|&|=|!/;var i=RegExp.prototype.test;var u=Object.prototype.toString;function n(y,x){return i.call(y,x)}function f(x){return !n(h,x)}var j=Array.isArray||function(x){return u.call(x)==="[object Array]"};function e(x){return x.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&")}var c={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#x2F;"};function l(x){return String(x).replace(/[&<>"'\/]/g,function(y){return c[y]})}v.escape=l;function t(x){this.string=x;this.tail=x;this.pos=0}t.prototype.eos=function(){return this.tail===""};t.prototype.scan=function(y){var x=this.tail.match(y);if(x&&x.index===0){this.tail=this.tail.substring(x[0].length);this.pos+=x[0].length;return x[0]}return""};t.prototype.scanUntil=function(y){var x,z=this.tail.search(y);switch(z){case -1:x=this.tail;this.pos+=this.tail.length;this.tail="";break;case 0:x="";break;default:x=this.tail.substring(0,z);this.tail=this.tail.substring(z);this.pos+=z}return x};function r(x,y){this.view=x;this.parent=y;this._cache={}}r.make=function(x){return(x instanceof r)?x:new r(x)};r.prototype.push=function(x){return new r(x,this)};r.prototype.lookup=function(x){var A=this._cache[x];if(!A){if(x=="."){A=this.view}else{var z=this;while(z){if(x.indexOf(".")>0){A=z.view;var B=x.split("."),y=0;while(A&&y<B.length){A=A[B[y++]]}}else{A=z.view[x]}if(A!=null){break}z=z.parent}}this._cache[x]=A}if(typeof A==="function"){A=A.call(this.view)}return A};function p(){this.clearCache()}p.prototype.clearCache=function(){this._cache={};this._partialCache={}};p.prototype.compile=function(z,x){var y=this._cache[z];if(!y){var A=v.parse(z,x);y=this._cache[z]=this.compileTokens(A,z)}return y};p.prototype.compilePartial=function(y,A,x){var z=this.compile(A,x);this._partialCache[y]=z;return z};p.prototype.getPartial=function(x){if(!(x in this._partialCache)&&this._loadPartial){this.compilePartial(x,this._loadPartial(x))}return this._partialCache[x]};p.prototype.compileTokens=function(z,y){var x=this;return function(A,C){if(C){if(typeof C==="function"){x._loadPartial=C}else{for(var B in C){x.compilePartial(B,C[B])}}}return o(z,x,r.make(A),y)}};p.prototype.render=function(z,x,y){return this.compile(z)(x,y)};function o(E,y,x,H){var B="";var z,F,G;for(var C=0,D=E.length;C<D;++C){z=E[C];F=z[1];switch(z[0]){case"#":G=x.lookup(F);if(typeof G==="object"){if(j(G)){for(var A=0,J=G.length;A<J;++A){B+=o(z[4],y,x.push(G[A]),H)}}else{if(G){B+=o(z[4],y,x.push(G),H)}}}else{if(typeof G==="function"){var I=H==null?null:H.slice(z[3],z[5]);G=G.call(x.view,I,function(K){return y.render(K,x)});if(G!=null){B+=G}}else{if(G){B+=o(z[4],y,x,H)}}}break;case"^":G=x.lookup(F);if(!G||(j(G)&&G.length===0)){B+=o(z[4],y,x,H)}break;case">":G=y.getPartial(F);if(typeof G==="function"){B+=G(x)}break;case"&":G=x.lookup(F);if(G!=null){B+=G}break;case"name":G=x.lookup(F);if(G!=null){B+=v.escape(G)}break;case"text":B+=F;break}}return B}function w(D){var y=[];var C=y;var E=[];var A;for(var z=0,x=D.length;z<x;++z){A=D[z];switch(A[0]){case"#":case"^":E.push(A);C.push(A);C=A[4]=[];break;case"/":var B=E.pop();B[5]=A[2];C=E.length>0?E[E.length-1][4]:y;break;default:C.push(A)}}return y}function a(C){var z=[];var B,y;for(var A=0,x=C.length;A<x;++A){B=C[A];if(B){if(B[0]==="text"&&y&&y[0]==="text"){y[1]+=B[1];y[3]=B[3]}else{y=B;z.push(B)}}}return z}function q(x){return[new RegExp(e(x[0])+"\\s*"),new RegExp("\\s*"+e(x[1]))]}v.parse=function(N,D){N=N||"";D=D||v.tags;if(typeof D==="string"){D=D.split(k)}if(D.length!==2){throw new Error("Invalid tags: "+D.join(", "))}var H=q(D);var z=new t(N);var F=[];var E=[];var C=[];var O=false;var M=false;function L(){if(O&&!M){while(C.length){delete E[C.pop()]}}else{C=[]}O=false;M=false}var A,y,G,I,B;while(!z.eos()){A=z.pos;G=z.scanUntil(H[0]);if(G){for(var J=0,K=G.length;J<K;++J){I=G.charAt(J);if(f(I)){C.push(E.length)}else{M=true}E.push(["text",I,A,A+1]);A+=1;if(I=="\n"){L()}}}if(!z.scan(H[0])){break}O=true;y=z.scan(s)||"name";z.scan(d);if(y==="="){G=z.scanUntil(g);z.scan(g);z.scanUntil(H[1])}else{if(y==="{"){G=z.scanUntil(new RegExp("\\s*"+e("}"+D[1])));z.scan(m);z.scanUntil(H[1]);y="&"}else{G=z.scanUntil(H[1])}}if(!z.scan(H[1])){throw new Error("Unclosed tag at "+z.pos)}B=[y,G,A,z.pos];E.push(B);if(y==="#"||y==="^"){F.push(B)}else{if(y==="/"){if(F.length===0){throw new Error('Unopened section "'+G+'" at '+A)}var x=F.pop();if(x[1]!==G){throw new Error('Unclosed section "'+x[1]+'" at '+A)}}else{if(y==="name"||y==="{"||y==="&"){M=true}else{if(y==="="){D=G.split(k);if(D.length!==2){throw new Error("Invalid tags at "+A+": "+D.join(", "))}H=q(D)}}}}}var x=F.pop();if(x){throw new Error('Unclosed section "'+x[1]+'" at '+z.pos)}E=a(E);return w(E)};var b=new p();v.clearCache=function(){return b.clearCache()};v.compile=function(y,x){return b.compile(y,x)};v.compilePartial=function(y,z,x){return b.compilePartial(y,z,x)};v.compileTokens=function(y,x){return b.compileTokens(y,x)};v.render=function(z,x,y){return b.render(z,x,y)};v.to_html=function(A,y,z,B){var x=v.render(A,y,z);if(typeof B==="function"){B(x)}else{return x}};return v}())));
