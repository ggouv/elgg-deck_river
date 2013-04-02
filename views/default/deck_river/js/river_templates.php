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

	$('.elgg-submenu-river').live('click', function() {
		$(this).addClass('hover').find('.elgg-module-popup').add($(this).closest('.elgg-list-item')).mouseleave(function() {
			$('.elgg-submenu-river').removeClass('hover');
		});
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

	if (response.activity instanceof String) {
		return response.activity;
	} else if (response.activity || response.results) {
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



elgg.deck_river.findUser = function(name, network) {
	var network = network || 'elgg',
		eName = 'username';

	if (network == 'twitter') eName = 'screen_name';
	return $.grep(DataEntities[network], function(e){ return e[eName] === name; })[0];
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

	$.each(response.activity, function(key, value) {

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

		if (!response.column_type) { // direct link. json returned by Twitter is different between twitter search api and twitter main api
			value.user = {screen_name: value.from_user, profile_image_url_https: value.profile_image_url_https};
			value.menu = {'default': [{
				name: 'response',
				content: '<a href="" title="Répondre" class="gwfb tooltip s"><span class="elgg-icon elgg-icon-response "></span></a>'
			},{
				name: 'retweet',
				content: '<a href="" title="Retweeter" class="gwfb tooltip s"><span class="elgg-icon elgg-icon-share "></span></a>'
			}], submenu: []};
		} else if (response.column_type == 'get_direct_messages') { // json is different with direct_messages
			value.user = value.sender;
		} else if (response.column_type == 'get_direct_messagesSent') { // json is different with direct_messages
			value.user = value.recipient;
		}

		elgg.deck_river.storeEntity(value.user, 'twitter');

		// format date and add friendly_time
		value.posted = value.created_at.TwitterFormatDate();
		value.friendly_time = elgg.friendly_time(value.posted);

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
		var retweet = false, reply = false;
		if (value.retweet_count == 1) {
			retweet = elgg.echo('retweet:one', [value.retweet_count]);
		} else if (value.retweet_count > 1) {
			retweet = elgg.echo('retweet:twoandmore', [value.retweet_count]);
		}
		value.responses = {
			retweet: retweet ? retweet : false,
			reply: value.in_reply_to_status_id != null && !thread // thread id is filled by in_reply_to_status in mustache template. Only true/false is sending.
		};

		// parse tweet text
		value.text = value.text.TwitterParseURL().TwitterParseUsername().TwitterParseHashtag();

		output += elggRiverTemplate(value);

	});
	return $(output);
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
	return this.replace(/#[A-Za-z0-9_-àâæéèêëîïôöœùûüç]+/g, function (h) {
		//var tag = t.replace("#", "%23")
		return '<a href="#" class="hashtag-info-popup" title="'+h+'" data-network="twitter">'+h+'</a>';
		//return t.link("http://search.twitter.com/search?q=" + tag);
	});
};




/*! Installing mustache for waiting which MVC elgg core team going to choose.
 * mustache.js - Logic-less {{mustache}} templates with JavaScript
 * http://github.com/janl/mustache.js
 */
(function(a,b){if(typeof exports==="object"&&exports){module.exports=b}else{if(typeof define==="function"&&define.amd){define(b)}else{a.Mustache=b}}}(this,(function(){var v={};v.name="mustache.js";v.version="0.7.2";v.tags=["{{","}}"];v.Scanner=t;v.Context=r;v.Writer=p;var d=/\s*/;var k=/\s+/;var h=/\S/;var g=/\s*=/;var m=/\s*\}/;var s=/#|\^|\/|>|\{|&|=|!/;var i=RegExp.prototype.test;var u=Object.prototype.toString;function n(y,x){return i.call(y,x)}function f(x){return !n(h,x)}var j=Array.isArray||function(x){return u.call(x)==="[object Array]"};function e(x){return x.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g,"\\$&")}var c={"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;","/":"&#x2F;"};function l(x){return String(x).replace(/[&<>"'\/]/g,function(y){return c[y]})}v.escape=l;function t(x){this.string=x;this.tail=x;this.pos=0}t.prototype.eos=function(){return this.tail===""};t.prototype.scan=function(y){var x=this.tail.match(y);if(x&&x.index===0){this.tail=this.tail.substring(x[0].length);this.pos+=x[0].length;return x[0]}return""};t.prototype.scanUntil=function(y){var x,z=this.tail.search(y);switch(z){case -1:x=this.tail;this.pos+=this.tail.length;this.tail="";break;case 0:x="";break;default:x=this.tail.substring(0,z);this.tail=this.tail.substring(z);this.pos+=z}return x};function r(x,y){this.view=x;this.parent=y;this._cache={}}r.make=function(x){return(x instanceof r)?x:new r(x)};r.prototype.push=function(x){return new r(x,this)};r.prototype.lookup=function(x){var A=this._cache[x];if(!A){if(x=="."){A=this.view}else{var z=this;while(z){if(x.indexOf(".")>0){A=z.view;var B=x.split("."),y=0;while(A&&y<B.length){A=A[B[y++]]}}else{A=z.view[x]}if(A!=null){break}z=z.parent}}this._cache[x]=A}if(typeof A==="function"){A=A.call(this.view)}return A};function p(){this.clearCache()}p.prototype.clearCache=function(){this._cache={};this._partialCache={}};p.prototype.compile=function(z,x){var y=this._cache[z];if(!y){var A=v.parse(z,x);y=this._cache[z]=this.compileTokens(A,z)}return y};p.prototype.compilePartial=function(y,A,x){var z=this.compile(A,x);this._partialCache[y]=z;return z};p.prototype.getPartial=function(x){if(!(x in this._partialCache)&&this._loadPartial){this.compilePartial(x,this._loadPartial(x))}return this._partialCache[x]};p.prototype.compileTokens=function(z,y){var x=this;return function(A,C){if(C){if(typeof C==="function"){x._loadPartial=C}else{for(var B in C){x.compilePartial(B,C[B])}}}return o(z,x,r.make(A),y)}};p.prototype.render=function(z,x,y){return this.compile(z)(x,y)};function o(E,y,x,H){var B="";var z,F,G;for(var C=0,D=E.length;C<D;++C){z=E[C];F=z[1];switch(z[0]){case"#":G=x.lookup(F);if(typeof G==="object"){if(j(G)){for(var A=0,J=G.length;A<J;++A){B+=o(z[4],y,x.push(G[A]),H)}}else{if(G){B+=o(z[4],y,x.push(G),H)}}}else{if(typeof G==="function"){var I=H==null?null:H.slice(z[3],z[5]);G=G.call(x.view,I,function(K){return y.render(K,x)});if(G!=null){B+=G}}else{if(G){B+=o(z[4],y,x,H)}}}break;case"^":G=x.lookup(F);if(!G||(j(G)&&G.length===0)){B+=o(z[4],y,x,H)}break;case">":G=y.getPartial(F);if(typeof G==="function"){B+=G(x)}break;case"&":G=x.lookup(F);if(G!=null){B+=G}break;case"name":G=x.lookup(F);if(G!=null){B+=v.escape(G)}break;case"text":B+=F;break}}return B}function w(D){var y=[];var C=y;var E=[];var A;for(var z=0,x=D.length;z<x;++z){A=D[z];switch(A[0]){case"#":case"^":E.push(A);C.push(A);C=A[4]=[];break;case"/":var B=E.pop();B[5]=A[2];C=E.length>0?E[E.length-1][4]:y;break;default:C.push(A)}}return y}function a(C){var z=[];var B,y;for(var A=0,x=C.length;A<x;++A){B=C[A];if(B){if(B[0]==="text"&&y&&y[0]==="text"){y[1]+=B[1];y[3]=B[3]}else{y=B;z.push(B)}}}return z}function q(x){return[new RegExp(e(x[0])+"\\s*"),new RegExp("\\s*"+e(x[1]))]}v.parse=function(N,D){N=N||"";D=D||v.tags;if(typeof D==="string"){D=D.split(k)}if(D.length!==2){throw new Error("Invalid tags: "+D.join(", "))}var H=q(D);var z=new t(N);var F=[];var E=[];var C=[];var O=false;var M=false;function L(){if(O&&!M){while(C.length){delete E[C.pop()]}}else{C=[]}O=false;M=false}var A,y,G,I,B;while(!z.eos()){A=z.pos;G=z.scanUntil(H[0]);if(G){for(var J=0,K=G.length;J<K;++J){I=G.charAt(J);if(f(I)){C.push(E.length)}else{M=true}E.push(["text",I,A,A+1]);A+=1;if(I=="\n"){L()}}}if(!z.scan(H[0])){break}O=true;y=z.scan(s)||"name";z.scan(d);if(y==="="){G=z.scanUntil(g);z.scan(g);z.scanUntil(H[1])}else{if(y==="{"){G=z.scanUntil(new RegExp("\\s*"+e("}"+D[1])));z.scan(m);z.scanUntil(H[1]);y="&"}else{G=z.scanUntil(H[1])}}if(!z.scan(H[1])){throw new Error("Unclosed tag at "+z.pos)}B=[y,G,A,z.pos];E.push(B);if(y==="#"||y==="^"){F.push(B)}else{if(y==="/"){if(F.length===0){throw new Error('Unopened section "'+G+'" at '+A)}var x=F.pop();if(x[1]!==G){throw new Error('Unclosed section "'+x[1]+'" at '+A)}}else{if(y==="name"||y==="{"||y==="&"){M=true}else{if(y==="="){D=G.split(k);if(D.length!==2){throw new Error("Invalid tags at "+A+": "+D.join(", "))}H=q(D)}}}}}var x=F.pop();if(x){throw new Error('Unclosed section "'+x[1]+'" at '+z.pos)}E=a(E);return w(E)};var b=new p();v.clearCache=function(){return b.clearCache()};v.compile=function(y,x){return b.compile(y,x)};v.compilePartial=function(y,z,x){return b.compilePartial(y,z,x)};v.compileTokens=function(y,x){return b.compileTokens(y,x)};v.render=function(z,x,y){return b.render(z,x,y)};v.to_html=function(A,y,z,B){var x=v.render(A,y,z);if(typeof B==="function"){B(x)}else{return x}};return v}())));
