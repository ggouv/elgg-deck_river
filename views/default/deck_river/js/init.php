
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
var site_shorturl = <?php $site_shorturl = elgg_get_plugin_setting('site_shorturl', 'elgg-deck_river'); echo json_encode($site_shorturl ? $site_shorturl : false); ?>;
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

		if ( $('.elgg-page .deck-river').length ) {

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
					arrows = $('.elgg-page-body .deck-river-scroll-arrow');

				if ($this.scrollLeft() == 0) {
					arrows.filter('.left').find('span').addClass('hidden').next().html('');
				} else if ($this.scrollLeft() > containerWidth-2) { // -2 cause scroll bar on OSX
					arrows.filter('.right').find('span').addClass('hidden').prev().html('');
				} else {
					arrows.find('span').removeClass('hidden');
				}
			});
			if ($('#deck-river-lists').get(0).scrollWidth == $('#deck-river-lists').get(0).clientWidth) $('.elgg-page-body .deck-river-scroll-arrow span').addClass('hidden');

			$('.elgg-page .elgg-river').unbind('scroll.moreItem').bind('scroll.moreItem', function() {
				if ($(this).scrollTop()+$(this).height() == $(this).get(0).scrollHeight) {
					$(this).find('.moreItem').click();
				}
			});

		} else {
			$('body').removeClass('fixed-deck');
		}

		if ($('#json-river-thread').length) { // single river item view, dispalyed in his thread
			var rThread = $('#json-river-thread');
			$('.elgg-river.single-view').html(elgg.deck_river.elggDisplayItems($.parseJSON(rThread.text())));
			$('.single-view .item-elgg-'+rThread.data('message-id')).addClass('viewed');
		}
		if ($('#json-river-owner').length) { // owner river view
			elgg.deck_river.LoadRiver($('.elgg-page .column-river'), $('#json-river-owner').val());
		}

	});

	$(window).bind('resize.deck_river', function() {
		if ( $('.deck-river').length ) {
			elgg.deck_river.SetColumnsHeight();
			elgg.deck_river.SetColumnsWidth();
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
	$('#authorize-twitter, #authorize-facebook').die().live('click', function(){
		elgg.action('deck_river/network/getLoginUrl', {
			data: {
				network: $(this).attr('id').replace('authorize-', '')
			},
			success: function(json) {
				window.open(json.output, 'ConnectWithOAuth', 'location=0,status=0,width=800,height=400');
			}
		});
		return false;
	});

	// refresh column, use 'live' for new column
	$('.elgg-column-refresh-button').die().live('click', function() {
		elgg.deck_river.RefreshColumn($(this).closest('.column-river'));
	});

	// refresh all columns
	$('.elgg-refresh-all-button').die().live('click', function() {
		$('.elgg-page-body .deck-river-scroll-arrow div').html('');
		$('.elgg-page-body .elgg-column-refresh-button').each(function() {
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
		if ($('#deck-river-lists .column-river').length >= deck_river_max_nbr_column) {
			elgg.system_message(elgg.echo('deck_river:limitColumnReached'));
		} else {
			elgg.deck_river.ColumnSettings();
		}
	});

	// make columns sortable
	$(".deck-river-lists-container").sortable({
		items: '.column-river',
		connectWith: '.deck-river-lists-container',
		handle: '.column-header',
		forcePlaceholderSize: true,
		placeholder: 'column-placeholder',
		opacity: 0.8,
		revert: 500,
		start: function(event, ui) { $('.column-placeholder').css('width', $('.column-header').width()-3); },
		update: elgg.deck_river.MoveColumn
	});

	// load discussion
	$('.elgg-river-responses a.thread').die().live('click', function() {
		elgg.deck_river.LoadDiscussion($(this));
	});

	// dropdown menu
	$('.elgg-submenu').die().live('click', function() {
		var $this = $(this);

		$this.addClass('elgg-state-active');
		if (!$this.hasClass('elgg-button-dropdown')) {
			$this.find('.elgg-module-popup').add($this.closest('.elgg-list-item')).mouseleave(function() {
				$this.removeClass('elgg-state-active');
				$(document).unbind('click.submenu');
			});
		} else if ($this.hasClass('invert')) {
			var m = $this.find('.elgg-menu');
			m.css('top', - m.height() -5);
		}
		$(document).unbind('click.submenu').bind('click.submenu', function() {
			$this.removeClass('elgg-state-active');
			$(document).unbind('click.submenu');
		});
	});

};
elgg.register_hook_handler('init', 'system', elgg.deck_river.init);



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
			column: columnID
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
				var bs = $(this).closest('.box-settings'),
					select = bs.find('select[name="twitter-lists"]'),
					network_account = bs.find('.in-module').val(); // * because can be select or input

				bs.find('li').not(':first-child').hide();
				bs.find('.'+$(this).val()+'-options').show();

				// Get lists for Twitter
				if ($(this).val() == 'get_listsStatuses' && !(select.data('list_loaded') == network_account) && select.parent().hasClass('hidden')) {
					bs.find('.get_listsStatuses-options div').removeClass('hidden');
					elgg.action('deck_river/twitter', {
						data: {
							twitter_account: network_account,
							method: 'get_listsList'
						},
						dataType: 'json',
						success: function(json) {
							$.each(json.output.result, function(i, e) {
								if (!select.find('option[value="'+e.id+'"]').length) select.append($('<option>').val(e.id).html(e.full_name));
							});
							bs.find('.get_listsStatuses-options div').addClass('hidden');
							select.data('list_loaded', network_account);
						},
						error: function() {
							return false;
						}
					});
				}
			}).trigger('change');
			cs.find('.in-module').change(function() {
				var network = $(this).attr('name').replace('-account', '');

				$(this).closest('.box-settings').find('.multi').addClass('hidden').filter('.' + $(this).val()).removeClass('hidden');
				cs.find('select[name="'+network+'-lists"]').html('');
				cs.find('.column-type').trigger('change');
			}).trigger('change');

			// checkboxes
			cs.find('.filter .elgg-input-checkbox').click(function() {
				if ( $(this).val() == 'All' ) cs.find('.filter .elgg-input-checkbox').not($(this)).removeAttr('checked');
				if ( $(this).val() != 'All' ) cs.find('.filter .elgg-input-checkbox[value="All"]').removeAttr('checked');
			});

			$(".elgg-foot .elgg-button").click(function() {
				var submitType = $(this).attr('name'),
					CSForm = $(this).closest('.deck-river-form-column-settings');

				if (submitType == 'delete' && !confirm(elgg.echo('deck-river:delete:column:confirm'))) return false;

				elgg.action('deck_river/column/settings', {
					data: CSForm.serialize() + '&submit=' + submitType + '&twitter_list_name=' + CSForm.find('select[name="twitter-lists"] option:selected').text(),
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
				return false;
			});
		}
	});
};



/**
 * Called by twitter and facebook callback
 *
 * Add new account in non-pinned network and reload the column-settings if open
 *
 * @param {token} false if network error, else it contain the account view
 * @return void
 */
elgg.deck_river.network_authorize = function(token) {
	var p = window.opener || window; // function called from a popup window on from main window

	if (token == false) {
		$.each(authorizeError, function(i, e) {
			p.elgg.register_error(e);
		})
		window.close();
	} else {
		var tn = token.network;
		// reload column settings popup if it's open
		if (p.$('#column-settings').length) {
			var c = p.$('#'+p.$('#column-settings input[name="column"]').val());
			p.$('#column-settings').data('network', tn);
			if (c.length == 1) {
				c.find('.elgg-column-edit-button').click();
			} else {
				p.$('.elgg-add-new-column').click();
			}
		}

		// add new network in applications page
		if (p.$('.elgg-module-'+tn).length) {
			var $em = p.$('.elgg-module-'+tn);
			$em.find('.elgg-module-featured').replaceWith($('<ul>', {'class': 'elgg-list elgg-list-entity'}));
			$em.find('.elgg-list').prepend(token.full).children().first().effect("highlight", {}, 3000);
		}

		// remove add-network popup
		p.$('#add_social_network').remove();

		// add new network account in #thewire-network
		p.$('#thewire-network .non-pinned .net-profiles').prepend(token.network_box);
		p.elgg.thewire.move_account();

		// execute code
		p.eval(token.code);

		// show message
		p.elgg.system_message(p.elgg.echo('deck_river:'+tn+':authorize:success'));
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
			var $body = $('body'),
				w = $body.css('overflow', 'hidden').width();
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
	var WindowWidth = $('#deck-river-lists').width(),
		CountLists = $('.elgg-page-body #deck-river-lists .column-river').length,
		ListWidth = 0,
		i = 0;

	while ( ListWidth < deck_river_min_width_column ) {
		ListWidth = (WindowWidth) / ( CountLists - i );
		i++;
	}
	$('.elgg-page-body').find('.elgg-river, .column-header, .column-placeholder').width(ListWidth - 2);
	$('.elgg-page-body .deck-river-lists-container').removeClass('hidden').width(ListWidth * CountLists);
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
 * Function serializeObject
 * Copied from https://github.com/macek/jquery-serialize-object
 * Version 1.0.0
 */
(function(f){return f.fn.serializeObject=function(){var k,l,m,n,p,g,c,h=this;g={};c={};k=/^[a-zA-Z_][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/;l=/[a-zA-Z0-9_]+|(?=\[\])/g;m=/^$/;n=/^\d+$/;p=/^[a-zA-Z0-9_]+$/;this.build=function(d,e,a){d[e]=a;return d};this.push_counter=function(d){void 0===c[d]&&(c[d]=0);return c[d]++};f.each(f(this).serializeArray(),function(d,e){var a,c,b,j;if(k.test(e.name)){c=e.name.match(l);b=e.value;for(j=e.name;void 0!==(a=c.pop());)m.test(a)?(a=RegExp("\\["+a+"\\]$"),j=
j.replace(a,""),b=h.build([],h.push_counter(j),b)):n.test(a)?b=h.build([],a,b):p.test(a)&&(b=h.build({},a,b));return g=f.extend(!0,g,b)}});return g}})(jQuery);



// End of js for deck-river plugin
