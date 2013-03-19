
/**
 *	Elgg-deck_river plugin
 *	@package elgg-deck_river
 *	@author Emmanuel Salomon @ManUtopiK
 *	@license GNU Affero General Public License, version 3 or late
 *	@link https://github.com/ManUtopiK/elgg-deck_river
 *
 *	Elgg-deck_river init js
 *
 */
var deck_river_min_width_column = <?php $mwc = elgg_get_plugin_setting('min_width_column', 'elgg-deck_river'); echo $mwc ? $mwc : 300; ?>;
var deck_river_max_nbr_column = <?php $mnc = elgg_get_plugin_setting('max_nbr_column', 'elgg-deck_river');  echo $mnc ? $mnc : 10; ?>;

// Global var for Entities : users and groups from elgg, users from Twitter
var DataEntities = DataEntities || {elgg: [], twitter: []};

/**
 * Elgg-deck_river initialization
 *
 * @return void
 */
elgg.provide('elgg.deck_river');

elgg.deck_river.init = function() {
	$(document).ready(function() {
		if ( $('.deck-river').length ) {

			$('body').addClass('fixed-deck');
			$('.elgg-page-default .elgg-page-body > .elgg-inner').css('width','100%');
			elgg.deck_river.SetColumnsHeight();
			elgg.deck_river.SetColumnsWidth();

			// load columns
			$('.elgg-page .column-river').each(function() {
				elgg.deck_river.LoadRiver($(this));
			});

			// arrow to scroll deck columns
			$('.deck-river-scroll-arrow.left span').click(function() {
				$('#deck-river-lists').scrollTo(0, 500, {easing:'easeOutQuart'});
				$('.deck-river-scroll-arrow.right span').removeClass('hidden');
			});
			$('.deck-river-scroll-arrow.right span').click(function() {
				$('#deck-river-lists').scrollTo(10000, 500, {easing:'easeOutQuart'});
				$('.deck-river-scroll-arrow.left span').removeClass('hidden');
			});
			$('#deck-river-lists').unbind('scroll').scroll(function() {
				var $this = $(this),
					containerWidth = $('.deck-river-lists-container').width() - $this.width()
					arrows = $('.deck-river-scroll-arrow');

				if ($this.scrollLeft() == 0) {
					arrows.filter('.left').find('span').addClass('hidden').next().html('');
				} else if ($this.scrollLeft() > containerWidth-2) { // -2 cause scroll bar on OSX
					arrows.filter('.right').find('span').addClass('hidden').prev().html('');
				} else {
					arrows.find('span').removeClass('hidden');
				}
			});
			if ($('#deck-river-lists').get(0).scrollWidth == $('#deck-river-lists').get(0).clientWidth) $('.deck-river-scroll-arrow span').addClass('hidden');

		} else {
			$('body').removeClass('fixed-deck');
		}

		if ($('#json-river-thread').length) { // single river item view, dispalyed in his thread
			var rThread = $('#json-river-thread');
			$('.elgg-river.single-view').html(elgg.deck_river.elggDisplayItems($.parseJSON(rThread.text())));
			$('.single-view .item-elgg-'+rThread.data('message-id')).addClass('viewed');
		}
		if ($('#json-river-owner').length) { // owner river view
			elgg.deck_river.LoadRiver($('.column-river'), $('#json-river-owner').val());
		}
	});

	$(window).bind("resize", function() {
		if ( $('.deck-river').length ) {
			elgg.deck_river.SetColumnsHeight();
			elgg.deck_river.SetColumnsWidth();
		}
	});

	$('#thewire-textarea').focusin(function() {
		var optionsHeight = $('#thewire-header').addClass('extended').find('.options').height();
		$('#thewire-header').height(optionsHeight+117);
		$('#thewire-textarea-border').height(optionsHeight+118);
	});

	// networks
	$('#thewire-network .elgg-icon-delete').die().live('click', function(e) {
		var net_input = $(this).closest('.net-profile').find('input');
		if ($(this).hasClass('hidden')) {
			net_input.attr('name', '_networks[]');
			$(this).removeClass('hidden');
		} else {
			net_input.attr('name', 'networks[]');
			$(this).addClass('hidden');
		}
		e.stopPropagation();
	});
	$('#thewire-network .more_networks, #thewire-network .selected-profile').die().live('click', function(e) {
		$('#thewire-network').toggleClass('extended');
		e.stopPropagation();
	});
	$('#thewire-network .pin').die().live('click', function() {
		var netProfile = $(this).closest('.net-profile');
		elgg.action('deck_river/network/pin', {
			data: {
				network: netProfile.find('input').val()
			},
			success: function (response) {
				if (response.output) {
					netProfile.toggleClass('pinned');
				}
			},
			error: function (response) {
				elgg.register_error(response);
			}
		});
	});
	elgg.deck_river.move_account();

	$('html').die().live('click', function(e) { //Hide thewire menu if visible
		if (!$(e.target).closest('.elgg-form-deck-river-wire-input').length) {
			$('#thewire-network').removeClass('extended');
			$('#thewire-header').height(33).removeClass('extended');
		}
	});

	$('.add_social_network').die().live('click', function() {
		elgg.deck_river.createPopup('add_social_network', elgg.echo('deck-river:add:network'), function() {
			$('#add_social_network').find('.elgg-icon-push-pin').remove();
		});
		elgg.post('ajax/view/deck_river/ajax_view/add_social_network', {
			dataType: "html",
			success: function(response) {
				$('#add_social_network').find('.elgg-body').html(response);
			}
		});
	});
	$('#authorize-twitter').die().live('click', function(e){
		var oauthWindow = window.open($(this).data('url'), 'ConnectWithOAuth', 'location=0,status=0,width=800,height=400');
		e.preventDefault();
		return false;
	});

	// thewire live post
	$('#thewire-submit-button').die().live('click', function(e){
		var thewireForm = $(this).closest('form');
		if ($('#thewire-textarea').val() == '') { // no text
			elgg.register_error('deck_river:message:blank');
		} else if (thewireForm.find('input[name="networks[]"]').length == 0) { // no network actived
			elgg.register_error('deck_river:nonetwork');
		} else if (thewireForm.find('input[name="networks[]"]').length > 5) { // too network ?
			elgg.register_error('deck_river:toonetwork');
		} else {
			thisSubmit = this;
			if ($.data(this, 'clicked')) { // Prevent double-click
				return false;
			} else {
				//$.data(this, 'clicked', true);
				$('#submit-loader').removeClass('hidden');
				var dataObject = thewireForm.serializeObject(),
					networksCount = dataObject.networks.length;

				$.each(dataObject.networks, function(i, e) {
					var dataString = dataObject;
					// format data for each network
					dataString.networks = [e];
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
								$('#thewire-header').height(33).removeClass('extended');
								$('#thewire-network').removeClass('extended');
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
		e.preventDefault();
		return false;
	});

	// response to a wire post
	$('#thewire-header .responseTo').die().live('click', function() {
		$(this).addClass('hidden').next('.parent').val('').removeAttr('name');
		$('.tipsy').remove();
		$('#thewire-header, #thewire-textarea-border').css({height: '+=-22'});
		$('.elgg-list-item').removeClass('responseAt');
	});

	// refresh column, use 'live' for new column
	$('.elgg-column-refresh-button').die().live('click', function() {
		elgg.deck_river.RefreshColumn($(this).closest('.column-river'));
	});

	// refresh all columns
	$('.elgg-refresh-all-button').die().live('click', function() {
		$('.deck-river-scroll-arrow div').html('');
		$('.elgg-column-refresh-button').each(function() {
			elgg.deck_river.RefreshColumn($(this).closest('.column-river'));
		});
	});

	// load more in column
	$('.moreItem').die().live('click', function() {
		var TheColumn = $(this).closest('.column-river');
		elgg.deck_river.LoadMore(TheColumn, TheColumn.children('.column-header').data('entity'));
	});

	// hide alert or other messages in column
	$('.column-messages').die().live('click', function() {
		$(this).stop(true, true).toggle('slide',{direction: 'up'}, 300, function() {$(this).html('')});
	});

	// Column settings
	$('.elgg-column-edit-button').die().live('click', function() {
		elgg.deck_river.ColumnSettings($(this).closest('.column-river'));
	});

	// Delete tabs
	$('.delete-tab').die().live('click', function() {
		var tab = $(this).closest('li').text();
		var tab_string = tab.charAt(0).toUpperCase() + tab.slice(1);
		if (confirm(elgg.echo('deck_river:delete:tab:confirm', [tab_string]))) {
			// delete tab through ajax
			elgg.action('deck_river/tab/delete', {
				data: {
					tab: tab
				},
				success: function(response) {
					if (response.status == 0 ) $('li.elgg-menu-item-'+tab).remove();
				}
			});
		}
		return false;
	});

	// rename column button
	$('.elgg-form-deck-river-tab-rename .elgg-button-submit').die().live('click', elgg.deck_river.tabRename);

	// Add new column
	$('.elgg-add-new-column').die().live('click', function() {
		var NbrColumn = $('.column-river').length;
		if (NbrColumn == deck_river_max_nbr_column) {
			elgg.system_message(elgg.echo('deck_river:limitColumnReached'));
		} else {
			elgg.deck_river.ColumnSettings();
		}
	});

	// make columns sortable
	$(".deck-river-lists-container").sortable({
		items:				'.column-river',
		connectWith:		  '.deck-river-lists-container',
		handle:			   '.column-header',
		forcePlaceholderSize: true,
		placeholder:		  'column-placeholder',
		opacity:			  0.8,
		revert:			   500,
		start: function(event, ui) { $('.column-placeholder').css('width', $('.column-header').width()-3); },
		update:				elgg.deck_river.MoveColumn
	});

	// load discussion
	$('.elgg-river-responses a.thread').die().live('click', function() {
		elgg.deck_river.LoadDiscussion($(this));
	});

};
elgg.register_hook_handler('init', 'system', elgg.deck_river.init);


/**
 * Counter for thewire area
 */
elgg.provide('elgg.thewire');

elgg.thewire.init = function() {
	$("#thewire-textarea").live('keydown', function() {
		elgg.thewire.textCounter($(this), $("#thewire-characters-remaining span"), 140);
	});
	$("#thewire-textarea").live('keyup', function() {
		elgg.thewire.textCounter($(this), $("#thewire-characters-remaining span"), 140);
	});
}

/**
 * Update the number of characters with every keystroke
 *
 * @param {Object}  textarea
 * @param {Object}  status
 * @param {integer} limit
 * @return void
 */
elgg.thewire.textCounter = function(textarea, status, limit) {
	var remaining_chars = textarea.val().length;
	status.html(remaining_chars);

	if (remaining_chars > limit) {
		status.css("color", "#D40D12");
		$(".thewire-button").addClass('elgg-state-disabled').children().attr('disabled', 'disabled');
	} else {
		status.css("color", "");
		$(".thewire-button").removeClass('elgg-state-disabled').children().removeAttr('disabled', 'disabled');
	}
};
elgg.register_hook_handler('init', 'system', elgg.thewire.init);




/**
 * Call settings of a column in popup
 *
 * Makes Ajax call to display settings of a column and perform change
 *
 * @param {TheColumn} the column
 * @return void
 */
elgg.deck_river.ColumnSettings = function(TheColumn) {
	if (!$('#column-settings').length) {
		elgg.deck_river.createPopup('column-settings', elgg.echo('deck_river:settings'), function() {
			$('#column-settings').find('.elgg-icon-push-pin').remove();
		});
	} else {
		$('#column-settings .elgg-body').html($('<div>', {'class': 'elgg-ajax-loader'}));
	}

	var columnSettings = $('#column-settings'),
		columnID = TheColumn ? TheColumn.attr('id') : 'new';

	elgg.post('ajax/view/deck_river/ajax_view/column_settings', {
		dataType: "html",
		data: {
			tab: $('#deck-river-lists').data('tab'),
			column: columnID,
		},
		success: function(response) {
			var cs = $('#column-settings');
			cs.find('.elgg-body').html(response);

			elgg.autocomplete.init();

			// network vertical tabs
			cs.find('.elgg-tabs.networks a').click(function() {
				var net = $(this).attr('class');
				cs.find('.elgg-tabs > li').removeClass('elgg-state-selected');
				$(this).parent('li').addClass('elgg-state-selected');
				cs.find('.tab, input.elgg-button-submit').addClass('hidden');
				cs.find('.tab.'+net+', input.elgg-button-submit.'+net).removeClass('hidden');
				cs.find('.column-type').trigger('change');
			});
			if (cs.data('network')) cs.find('.elgg-tabs.networks a.'+cs.data('network')).click(); // used when authorize social network callback

			// dropdown
			cs.find('.' + cs.find('.tab:visible .column-type').val()+'-options').show();
			$('.column-type').change(function() {
				var bs = $(this).closest('.box-settings');
				bs.find('li').not(':first-child').hide();
				bs.find('.'+$(this).val()+'-options').show();
			});
			$('select[name="twitter-account"]').change(function() {
				$(this).closest('.box-settings').find('.multi').addClass('hidden').filter('.' + $(this).val()).removeClass('hidden');
			});

			// checkboxes
			cs.find('.filter .elgg-input-checkbox').click(function() {
				if ( $(this).val() == 'All' ) cs.find('.filter .elgg-input-checkbox').not($(this)).removeAttr('checked');
				if ( $(this).val() != 'All' ) cs.find('.filter .elgg-input-checkbox[value="All"]').removeAttr('checked');
			});

			$(".elgg-foot .elgg-button").click(function(e) {
				var submitType = $(this).attr('name');

				if (submitType == 'delete' && !confirm(elgg.echo('deck-river:delete:column:confirm'))) return false;

				elgg.action('deck_river/column/settings', {
					data: $('.deck-river-form-column-settings').serialize() + "&submit=" + submitType,
					dataType: 'json',
					success: function(json) {
						var response = json.output;

						if (columnID == 'new') {
							$('.deck-river-lists-container').append(
								$('<li>', {'class': 'column-river', id: response.column}).append(
									$('<ul>', {'class': 'column-header'}).after(
										$('<ul>', {'class': 'elgg-river elgg-list'})
							)));
							elgg.deck_river.SetColumnsHeight();
							elgg.deck_river.SetColumnsWidth();
							$('#deck-river-lists').animate({ scrollLeft: $('#deck-river-lists').width()});
						}

						var TheColumn = $('#'+response.column); // redeclare because maybe it was just created.

						if (submitType == 'delete' && response.action == 'delete') {
							TheColumn.find('*').css('background-color', '#FF7777');
							TheColumn.fadeOut(400, function() {
								$(this).animate({'width':0},'', function() {
									$(this).remove();
									elgg.deck_river.SetColumnsWidth();
								});
							});
							cs.remove();
							return false;
						}

						TheColumn.find('.elgg-list').html($('<div>', {'class': 'elgg-ajax-loader'}));
						TheColumn.find('.column-header').replaceWith(response.header);
						elgg.deck_river.LoadRiver(TheColumn);

						cs.remove();
					},
					error: function() {
						return false;
					}
				});
				e.preventDefault();
				return false;
			});
		}
	});
};



/**
 * Initiate draggable and droppable for net-profile in thewire network
 */
elgg.deck_river.move_account = function() {
	$('#thewire-network .net-profile').draggable({
		revert: true,
		revertDuration: 0,
		zIndex: 9999,
	});
	$('#thewire-network .selected-profile, #thewire-network .non-pinned .net-profiles').droppable({
		accept:				 $('.net-profile').not('.ggouv'),
		activeClass:			'ui-state-highlight',
		hoverClass:			 'ui-state-active',
		drop: function(e, ui) {
			$('#thewire-network *').removeClass('ui-start');
			if ($(this).hasClass('selected-profile')) {
				if ($(this).find('input[name="networks[]"]').length < 5) {
					ui.draggable.appendTo($(this)).find('input').attr('name', 'networks[]');
					ui.draggable.find('.elgg-icon-delete').addClass('hidden');
				} else {
					elgg.register_error('deck_river:error:pin:too_much');
				}
			} else {
				ui.draggable.appendTo($(this)).find('input').attr('name', '_networks[]');
			}
		},
		activate: function(e, ui) {
			ui.draggable.parent().addClass('ui-start');
		}
	});
}



/**
 * Called by twitter callback
 *
 * Add new account in non-pinned network and reload the column-settings if open
 *
 * @param {token} false if twitter error, else it contain the account view
 * @return void
 */
elgg.deck_river.twitter_authorize = function(token) {
	var p = window.opener;
	if (token == false) {
		p.elgg.system_message(p.elgg.echo('deck_river:twitter:authorize:already_done'));
		window.close();
	} else {
		// reload column settings popup if it's open
		if (p.$('#column-settings').length) {
			var c = p.$('#'+p.$('#column-settings input[name="column"]').val());
			p.$('#column-settings').data('network', 'twitter');
			if (c.length == 1) {
				c.find('.elgg-column-edit-button').click();
			} else {
				p.$('.elgg-add-new-column').click();
			}
		}

		// add new network in applications page
		if (p.$('.elgg-module-twitter').length) {
			p.$('.elgg-module-twitter .elgg-list').prepend(token.full);
		}

		// remove add-network popup
		p.$('#add_social_network').remove();

		// add new twitter account in #thewire-network
		p.$('#thewire-network .non-pinned .net-profiles').prepend(token.network_box);
		p.elgg.deck_river.move_account();

		// show message
		p.elgg.system_message(p.elgg.echo('deck_river:twitter:authorize:success'));
		window.close();
	}
}

/**
 * Rename a column
 *
 * Event callback the uses Ajax to rename the column and change its HTML
 *
 * @param {Object} event
 * @return void
 */
elgg.deck_river.tabRename = function(event) {
	elgg.action('deck_river/tab/rename', {
		data: $('.elgg-form-deck-river-tab-rename').serialize(),
		success: function(json) {
			if (json.status != -1) {
				$('#deck-river-lists').data('tab', json.output.tab_name);
				$('.elgg-menu-item-'+json.output.old_tab_name+' a').text(json.output.tab_name.charAt(0).toUpperCase() + json.output.tab_name.slice(1));
				$('.elgg-menu-item-'+json.output.old_tab_name).removeClass('elgg-menu-item-'+json.output.old_tab_name).addClass('elgg-menu-item-'+json.output.tab_name);
			}
		}
	});
	$('#rename-deck-river-tab').hide();
	event.preventDefault();
};

/**
 * Persist the column's new position
 *
 * @param {Object} event
 * @param {Object} ui
 *
 * @return void
 */
elgg.deck_river.MoveColumn = function(event, ui) {

	elgg.action('deck_river/column/move', {
		data: {
			tab: ui.item.closest('#deck-river-lists').data('tab'),
			column: ui.item.attr('id'),
			position: ui.item.index()
		}
	});

	// @hack fixes jquery-ui/opera bug where draggable elements jump
	ui.item.css('top', 0);
	ui.item.css('left', 0);
};


/**
 * Resize columns height
 */
elgg.deck_river.SetColumnsHeight = function() {
	function scrollbarWidth() {
		if (!$._scrollbarWidth) {
			var $body = $('body');
			var w = $body.css('overflow', 'hidden').width();
			$body.css('overflow','scroll');
			w -= $body.width();
			if (!w) w=$body.width()-$body[0].clientWidth; // IE in standards mode
			$body.css('overflow','');
			$._scrollbarWidth = w+1;
		}
		return $._scrollbarWidth;
	}
	var offset = $('#deck-river-lists').offset();
	$('.elgg-page-body .elgg-river').height($(window).height() - offset.top - $('.column-header').height() - scrollbarWidth());
	$('#deck-river-lists').height($(window).height() - offset.top);
};


/**
 * Resize columns width
 */
elgg.deck_river.SetColumnsWidth = function() {
	var WindowWidth = $('#deck-river-lists').width();
	var CountLists = $('#deck-river-lists .column-river').length;
	var ListWidth = 0; var i = 0;
	while ( ListWidth < deck_river_min_width_column ) {
		ListWidth = (WindowWidth) / ( CountLists - i );
		i++;
	}
	$('.elgg-page-body').find('.elgg-river, .column-header, .column-placeholder').width(ListWidth - 2);
	$('.deck-river-lists-container').removeClass('hidden').width(ListWidth * CountLists);
};


/**
 * Update each minute all friendly times
 *
 */
elgg.provide('elgg.friendly_time');

elgg.friendly_time = function(time) {

	//TODO friendly:time hook

	diff = new Date().getTime()/1000 - parseInt(time);

	minute = 60;
	hour = minute * 60;
	day = hour * 24;

	if (diff < minute) {
			return elgg.echo("friendlytime:justnow");
	} else if (diff < hour) {
		diff = Math.round(diff / minute);
		if (diff == 0) {
			diff = 1;
		}

		if (diff > 1) {
			return elgg.echo("friendlytime:minutes", [diff]);
		} else {
			return elgg.echo("friendlytime:minutes:singular", [diff]);
		}
	} else if (diff < day) {
		diff = Math.round(diff / hour);
		if (diff == 0) {
			diff = 1;
		}

		if (diff > 1) {
			return elgg.echo("friendlytime:hours", [diff]);
		} else {
			return elgg.echo("friendlytime:hours:singular", [diff]);
		}
	} else {
		diff = Math.round(diff / day);
		if (diff == 0) {
			diff = 1;
		}

		if (diff > 1) {
			return elgg.echo("friendlytime:days", [diff]);
		} else {
			return elgg.echo("friendlytime:days:singular", [diff]);
		}
	}
}

elgg.friendly_time.update = function() {
	$('.elgg-page .elgg-friendlytime').each(function(){
		var acronym = $(this).find('acronym');
		acronym.html(elgg.friendly_time(acronym.attr('time')));
	});
}

elgg.friendly_time.init = function() {
	elgg.friendly_time.update();
	setInterval(elgg.friendly_time.update, 1000*60); // each 60 sec
};

elgg.register_hook_handler('init', 'system', elgg.friendly_time.init);



/**
 *  Javascript AlphabeticID class
 *  (based on a script by Kevin van Zonneveld <kevin@vanzonneveld.net>)
 *
 *  Author: Even Simon <even.simon@gmail.com>
 *
 *  Description: Translates a numeric identifier into a short string and backwords.
 *
 *  Usage:
 *    var str = AlphabeticID.encode(9007199254740989); // str = 'fE2XnNGpF'
 *    var id = AlphabeticID.decode('fE2XnNGpF'); // id = 9007199254740989;
 **/

var AlphabeticID = {
  index:'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',

  /**
   *  [@function](http://twitter.com/function) AlphabeticID.encode
   *  [@description](http://twitter.com/description) Encode a number into short string
   *  [@param](http://twitter.com/param) integer
   *  [@return](http://twitter.com/return) string
   **/
  encode:function(_number){
    if('undefined' == typeof _number){
      return null;
    }
    else if('number' != typeof(_number)){
      throw new Error('Wrong parameter type');
    }

    var ret = '';

    for(var i=Math.floor(Math.log(parseInt(_number))/Math.log(AlphabeticID.index.length));i>=0;i--){
      ret = ret + AlphabeticID.index.substr((Math.floor(parseInt(_number) / AlphabeticID.bcpow(AlphabeticID.index.length, i)) % AlphabeticID.index.length),1);
    }

    return ret.reverse();
  },

  /**
   *  [@function](http://twitter.com/function) AlphabeticID.decode
   *  [@description](http://twitter.com/description) Decode a short string and return number
   *  [@param](http://twitter.com/param) string
   *  [@return](http://twitter.com/return) integer
   **/
  decode:function(_string){
    if('undefined' == typeof _string){
      return null;
    }
    else if('string' != typeof _string){
      throw new Error('Wrong parameter type');
    }

    var str = _string.reverse();
    var ret = 0;

    for(var i=0;i<=(str.length - 1);i++){
      ret = ret + AlphabeticID.index.indexOf(str.substr(i,1)) * (AlphabeticID.bcpow(AlphabeticID.index.length, (str.length - 1) - i));
    }

    return ret;
  },

  /**
   *  [@function](http://twitter.com/function) AlphabeticID.bcpow
   *  [@description](http://twitter.com/description) Raise _a to the power _b
   *  [@param](http://twitter.com/param) float _a
   *  [@param](http://twitter.com/param) integer _b
   *  [@return](http://twitter.com/return) string
   **/
  bcpow:function(_a, _b){
    return Math.floor(Math.pow(parseFloat(_a), parseInt(_b)));
  }
};

/**
 *  [@function](http://twitter.com/function) String.reverse
 *  [@description](http://twitter.com/description) Reverse a string
 *  [@return](http://twitter.com/return) string
 **/
String.prototype.reverse = function(){
  return this.split('').reverse().join('');
};



/**
 * Function serializeObject
 * Copied from https://github.com/macek/jquery-serialize-object
 * Version 1.0.0
 */
(function(f){return f.fn.serializeObject=function(){var k,l,m,n,p,g,c,h=this;g={};c={};k=/^[a-zA-Z_][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/;l=/[a-zA-Z0-9_]+|(?=\[\])/g;m=/^$/;n=/^\d+$/;p=/^[a-zA-Z0-9_]+$/;this.build=function(d,e,a){d[e]=a;return d};this.push_counter=function(d){void 0===c[d]&&(c[d]=0);return c[d]++};f.each(f(this).serializeArray(),function(d,e){var a,c,b,j;if(k.test(e.name)){c=e.name.match(l);b=e.value;for(j=e.name;void 0!==(a=c.pop());)m.test(a)?(a=RegExp("\\["+a+"\\]$"),j=
j.replace(a,""),b=h.build([],h.push_counter(j),b)):n.test(a)?b=h.build([],a,b):p.test(a)&&(b=h.build({},a,b));return g=f.extend(!0,g,b)}});return g}})(jQuery);

// End of js for deck-river plugin
