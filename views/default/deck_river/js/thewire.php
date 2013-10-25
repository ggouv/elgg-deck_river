
/**
 * Counter for thewire area
 */
elgg.provide('elgg.thewire');

elgg.thewire.init = function() {
	var linkParsed = null;

	$('#linkbox div.image-wrapper').live('click', function() {
		$(this).toggleClass('noimg');
		return false;
	});

	$('#linkbox .elgg-menu .elgg-icon-delete').live('click', function() {
		$('#linkbox').addClass('hidden').html($('<div>', {'class': 'elgg-ajax-loader'}));
		elgg.thewire.resize('open');
		return false;
	});

	$(document).mousedown(function(e) {
		$(document).bind('mousemove.thewire', function(e){
			if ($(e.target).attr('id') == 'thewire-textarea') {
				elgg.thewire.resize();
				$('.ui-draggable-dragging').addClass('canDrop');
			}
		});
	})
	.mouseup(function() {
		$(document).unbind('mousemove.thewire');
	});
	$('#thewire-textarea').focusin(function() {
		elgg.thewire.resize();
	}).droppable({
		accept: '.user-info-popup, .group-info-popup, .hashtag-info-popup, .twitter-user-info-popup, .linkbox-droppable',
		tolerance: 'touch',
		drop: function(e, ui) {
			var txt = prep = '',
				$uih = $(ui.helper);

			if ($uih.hasClass('linkbox-droppable')) {
				var data = $uih.find('.elgg-river-image').data();

				$('#linkbox').removeClass('hidden').html(Mustache.render($('#linkbox-template').html(), data));
				elgg.thewire.resize();
			} else {
				if ($uih.hasClass('user-info-popup') || $uih.hasClass('twitter-user-info-popup')) prep = '@';
				if ($uih.hasClass('group-info-popup')) prep = '!';
				elgg.thewire.insertInThewire(prep + $(ui.helper).attr('title'));
			}
		},
		over: function(e, ui) {
			ui.helper.addClass('canDrop');
		},
		out: function(e, ui) {
			ui.helper.removeClass('canDrop');
		}
	}).live('keyup', function() {
		var $twF = $(this).closest('form'),
			$lb = $('#linkbox'),
			urls = elgg.thewire.textCounter();

		// scrap first url
		// We check before if there is network which need scrapping with data-scrap
		if (urls && $twF.find('input[name="networks[]"][data-scrap]').length && linkParsed != urls[0]) {
			linkParsed = urls[0];
			elgg.thewire.scrapWebpage(urls[0], {
				beforeSend: function() {
					$lb.removeClass('hidden');
					elgg.thewire.resize();
				},
				success: function(data) {
					if (data) {
						if (!data.title || elgg.isNull(data.title)) data.title = data.url;
						data.title = $('<div>').html(data.title).text(); // decode html entities
						if (data.metatags) {
							$.grep(data.metatags, function(e) {
								if (e[0] == 'description') data.description = $('<div>').html(e[1]).text();
							});
						}
						data.mainimage = data.images[0].src;
						data.images.shift();
						data.src = function() {
							return this.src;
						};

						$lb.html(Mustache.render($('#linkbox-template').html(), data));
						elgg.thewire.resize();

						$lb.find('li.image-wrapper').click(function() {
							var $ei = $('#linkbox .elgg-image'),
								first = $ei.children().first(),
								firstHtml = first.html();

							first.html(this.innerHTML);
							$(this).html(firstHtml);
							return false;
						});

					} else {
						$lb.html(elgg.echo('error'));
					}
				},
				error: function() {
					console.log('erreur');
				}
			});
		}
	});
	$('html').die().live('click', function(e) { //Hide thewire menu if visible
		if (!$(e.target).closest('.elgg-form-deck-river-wire-input').length) {
			elgg.thewire.resize('close');
		}
	});
	$('.elgg-form-deck-river-wire-input *[contenteditable="true"]').live('keyup', function() {
		elgg.thewire.resize();
	});

	// response to a wire post
	$('#thewire-header .responseTo').die().live('click', function() {
		$(this).addClass('hidden').next('.parent').val('').removeAttr('name');
		$('.tipsy').remove();
		$('.elgg-list-item').removeClass('responseAt');
		elgg.thewire.resize();
	});

	// networks
	$('#thewire-network .elgg-icon-delete').die().live('click', function() {
		var net_input = $(this).closest('.net-profile').find('input');
		if ($(this).hasClass('hidden')) {
			net_input.attr('name', '_networks[]');
			$(this).removeClass('hidden');
		} else {
			net_input.attr('name', 'networks[]');
			$(this).addClass('hidden');
		}
		return false;
	});
	$('#thewire-network .more_networks, #thewire-network .selected-profile').die().live('click', function() {
		$('#thewire-network').toggleClass('extended');
		return false;
	});
	$('#thewire-network .pin').die().live('click', function() {
		$(this).closest('.net-profile').toggleClass('pinned');
		elgg.thewire.manageNetworks();
		return false;
	});
	elgg.thewire.move_account();


	// thewire live post
	$('#thewire-submit-button').die().live('click', function(){
		var thewireForm = $(this).closest('form'),
			msg = $('#thewire-textarea').val();

		if (msg == '' || /^@\S*\s*$/.test()) { // no text or mention alone : "@user "
			elgg.register_error(elgg.echo('deck_river:message:blank'));
		} else if (thewireForm.find('input[name="networks[]"]').length == 0) { // no network actived
			elgg.register_error(elgg.echo('deck_river:nonetwork'));
		} else if (thewireForm.find('input[name="networks[]"]').length > 5) { // too network ?
			elgg.register_error(elgg.echo('deck_river:toonetwork'));
		} else {
			thisSubmit = this;
			if ($.data(this, 'clicked')) { // Prevent double-click
				return false;
			} else {
				$.data(this, 'clicked', true);
				$('#submit-loader').removeClass('hidden');
				var dataObject = thewireForm.serializeObject(),
					networksCount = dataObject.networks.length;

				$.each(dataObject.networks, function(i, e) {
					var dataString = dataObject;

					dataString.networks = [e]; // format data for each network
					dataString.link_name = thewireForm.find('.link_name').html();
					dataString.link_description = thewireForm.find('.link_description').html();
					dataString.link_picture = thewireForm.find('.link_picture').not('.noimg').children().attr('src');
					dataString = $.param(dataString);

					elgg.action('deck_river/add_message', {
						data: dataString,
						success: function(json) {
							if (networksCount == 1) {
								$.data(thisSubmit, 'clicked', false);
								$('#submit-loader').addClass('hidden');
								$("#thewire-characters-remaining span").html('0');
								$('#thewire-textarea').val('').closest('.elgg-form').find('.responseTo').addClass('hidden').next('.parent').val('').removeAttr('name');
								$('.elgg-list-item').removeClass('responseAt');
								$('#linkbox').addClass('hidden').html($('<div>', {'class': 'elgg-ajax-loader'}));
								elgg.thewire.resize('close');
								linkParsed = null;
							} else {
								networksCount--;
							}
						},
						error: function(){
							$('#submit-loader').addClass('hidden');
							$.data(thisSubmit, 'clicked', false);
						}
					});
				});
			}
		}
		return false;
	});

};
elgg.register_hook_handler('init', 'system', elgg.thewire.init);


/**
 * Resize thewire box
 * @param  {[type]} action 'open', 'close' or a value
 */
elgg.thewire.resize = function(action) {
	var action = action || 0,
		$twH = $('#thewire-header'),
		$twTB = $('#thewire-textarea-border'),
		$twN = $('#thewire-network');

	if (action == 'close') {
		$twH.add($twTB).css({height: 33});
		$twH.add($twN).removeClass('extended');
	} else {
		$twH.add($twTB).css({height: ($twH.addClass('extended').find('.options').height()+117)});
	}
};



/**
 * Update the number of characters with every keystroke
 *
 * @return array of urls in text
 */
elgg.thewire.textCounter = function() {
	var $twT = $('#thewire-textarea'),
		$twCR = $('#thewire-characters-remaining span'),
		$twF = $twT.closest('form'),
		$networks = $twF.find('input[name="networks[]"]'),
		expression = /http:\/\/[-a-zA-Z0-9_.~]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+,.~#?&//=]*)?/gi,
		regex = new RegExp(expression),
		urls = $twT.val().match(regex),
		remaining_chars = $twT.val().length;

	$twCR.html(remaining_chars);

	if (remaining_chars > 140) {
		$twCR.css("color", "#D40D12");
		$(".thewire-button").addClass('elgg-state-disabled').children().attr('disabled', 'disabled');
	} else {
		$twCR.css("color", "");
		$(".thewire-button").removeClass('elgg-state-disabled').children().removeAttr('disabled', 'disabled');
	}

	return urls;
};



/**
 * Insert text in Thewire textarea. Wrap text with space if needed. Keep caret position.
 * @param  {[string]} text text to insert
 * @return {[type]}      [description]
 */
elgg.thewire.insertInThewire = function(text) {
	var $twT = $('#thewire-textarea'),
		twTval = $twT.val(),
		strPos = $twT.getCursorPosition(),
		front = (twTval).substring(0,strPos),
		back = (twTval).substring(strPos,twTval.length);

	if (front.substring(front.length, front.length-1) != ' ' && front.length != 0) text = ' ' + text;
	if (back.substring(0, 1) != ' ' && back.length != 0) text = text + ' ';
	$twT.val(front + text + back).focus().setCursorPosition(strPos + text.length);
	elgg.thewire.textCounter();
};


/*$(".deck-river-lists-container").sortable({
		items: '.column-river',
		connectWith: '.deck-river-lists-container',
		handle: '.column-header',
		forcePlaceholderSize: true,
		placeholder: 'column-placeholder',
		opacity: 0.8,
		revert: 500,
		start: function(event, ui) { $('.column-placeholder').css('width', $('.column-header').width()-3); },
		update: elgg.deck_river.MoveColumn
	});*/
/**
 * Initiate draggable and droppable for net-profile in thewire network
 */
elgg.thewire.move_account = function() {
	$('#thewire-network .selected-profile, #thewire-network .non-pinned .net-profiles').sortable({
		items: $('.net-profile').not('.ggouv'),
		connectWith: $('#thewire-network .selected-profile, #thewire-network .non-pinned .net-profiles'),
		helper: 'clone',
		revert: 300,
		dropOnEmpty: true,
		revert: 500,
		zIndex: 9999,
		opacity: 0.8,
		receive: function(e, ui) {
			if ($(this).hasClass('selected-profile') && $(this).find('.net-profile').length > 5) {
				elgg.register_error(elgg.echo('deck_river:error:network:active:too_much'));
				ui.sender.sortable("cancel");
			}
		},
		update: function(e, ui) {
			if ($(this).hasClass('net-profiles') && ui.position.top > 0) elgg.thewire.manageNetworks();
		}
	}).droppable({
		accept:      $('.net-profile').not('.ggouv'),
		activeClass: 'ui-state-highlight',
		hoverClass:  'ui-state-active',
		drop: function(e, ui) {
			$('#thewire-network *').removeClass('ui-start');
			if ($(this).hasClass('selected-profile')) {
				if ($(this).find('.net-profile').length <= 5) {
					ui.draggable.appendTo($(this)).find('input').attr('name', 'networks[]');
					ui.draggable.find('.elgg-icon-delete').addClass('hidden');
				} else {
					elgg.register_error(elgg.echo('deck_river:error:network:active:too_much'));
				}
			} else {
				ui.draggable.prependTo($(this)).find('input').attr('name', '_networks[]');
			}
		},
		activate: function(e, ui) {
			ui.draggable.parent().addClass('ui-start');
		}
	});
};


/**
 * Fired when account is pinned or sorted. This function format order and send it to server.
 * @return {[type]} [description]
 */
elgg.thewire.manageNetworks = function() {
	var $nps = $('#thewire-network .net-profile:not(.ggouv)'),
		position = {},
		pinned = [],
		place = function(i, e) {
			if (elgg.isUndefined(position[i])) {
				position[i] = e.children('input').val();
				e.data('position', i);
			} else {
				i++;
				place(i, e);
			}
		};

	$.each($nps, function(i, e) {
		var $e = $(e),
			p = $e.data('position');

		if (!$e.hasClass('pinned')) {
			if ($e.closest('.selected-profile').length) {
				place(p, $e);
			} else {
				place(0, $e);
			}
		} else {
			pinned.push($e.children('input').val());
		}
	});

	// send to save
	elgg.action('deck_river/network/manageNetworks', {
		data: {
			networks: {
				position: position,
				pinned: pinned
			}
		},
		error: function (response) {
			elgg.register_error(response);
		}
	});
};


/**
 * Scrap a webpage and return matatags, images and links.
 * @param  [string]            url of the webpage to parse
 * @param  [object]            options
 * @return [object]            parsed datas
 */
elgg.thewire.scrapWebpage = function(url, options) {
	options = $.extend({
					minSize: 120,                       // [string]            Title of the popup
					beforeSend: $.noop,                 // [function]          function will be executed just before request
					success: $.noop,                    // [function]          function will be executed when success
					error: $.noop,                      // [function]          function will be executed on error
				}, options);

	elgg.get(elgg.get_site_url() + 'mod/elgg-deck_river/lib/scraper.php', {
		data: {
			url: url
		},
		dataType: 'json',
		beforeSend: options.beforeSend,
		success: function(response) {

			// response.message is filled by scraper only if there is an error
			if (response.message) {
				options.success(false);
				return false;
			}

			var Images = [],
				imgsLength = response.images.length,
				nbrLoads = 0,
				imgLoaded = function(img) {
					nbrLoads++;
					if (nbrLoads >= imgsLength) {
						Images.sort(function(a, b) {
							return (a.nDim > b.nDim) ? -1 : (a.nDim < b.nDim) ? 1 : 0;
						});
						// put og:image first
						if (response.metatags) {
							$.grep(response.metatags, function(e){
								if (e[0] == 'og:image') Images.unshift({'src': e[1]});
							});
						}
						response.images = Images;

						// Scrapping ended. We execute success function
						options.success(response);
					}
				};

			if (imgsLength) {
				$.each(response.images, function(i, e) {
					var img = new Image(),
						iD = {};

					iD.src = img.src = e;
					img.onload = function() {
						iD.width = this.width;
						iD.height = this.height;
						iD.nDim = parseFloat(iD.width) * parseFloat(iD.height);
						if (options.minSize != 0 && options.minSize <= iD.width && options.minSize <= iD.height) {
							Images.push(iD);
						} else if (options.minSize == 0) {
							Images.push(iD);
						}
						imgLoaded(img);
					};
					img.onerror = function() {imgLoaded(img);};
				});
			} else {
				options.success(response);
			}
		},
		error: options.error
	});
};