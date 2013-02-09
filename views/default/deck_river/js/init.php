
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
			$('.column-river').each(function() {
				elgg.deck_river.LoadColumn($(this));
			});

		} else {
			$('body').removeClass('fixed-deck');
		}

		if ($('#json-river-thread').length) { // single river item view, dispalyed in his thread
			var rThread = $('#json-river-thread');
			$('.elgg-river.single-view').html(elgg.deck_river.displayItems($.parseJSON(rThread.text())));
			$('.single-view .item-river-'+rThread.attr('data-message-id')).addClass('viewed');
		}
		if ($('#json-river-owner').length) { // owner river view
			elgg.deck_river.LoadEntity($('#json-river-owner').val(), $('#column'));
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
		$('#thewire-textarea-border').height(optionsHeight+111);
	}).focusout(function() {
	 	if ($('#thewire-header').is(':hover')) {
		} else {
			$('#thewire-header').height(33).removeClass('extended');
		}
	});
	$('#thewire-network .elgg-icon-delete').die().live('click', function() {
		var net_input = $(this).parent('.net-profile').find('input'),
			delete_icon = $(this).parent('.net-profile').find('.elgg-icon-delete');
		if (net_input.val() === 'false') {
			net_input.val(true);
			delete_icon.addClass('hidden');
		} else {
			net_input.val(false);
			delete_icon.removeClass('hidden');
		}
	});
	
	// thewire live post
	$('#thewire-submit-button').die().live('click', function(e){
		if ($('#thewire-textarea').val() == '') { // no text
			elgg.system_message('thewire:blank');
		} else if ($('#thewire-network input[value=true]').length == 0) { // no network actived
			elgg.system_message('thewire:nonetwork');
		} else {
			thisSubmit = this;
			if ($.data(this, 'clicked')) { // Prevent double-click
				return false;
			} else {
				$.data(this, 'clicked', true);
				dataString = $(this).parents('form').serialize();
				elgg.action('deck_river/wire_input', {
					data: dataString,
					success: function(json) {
						$.data(thisSubmit, 'clicked', false);
						$("#thewire-characters-remaining span").html('0');
						$('#thewire-textarea').val('').parents('.elgg-form').find('input[name=parent_guid], .responseTo').remove();
						$('#thewire-header').height(33).removeClass('extended');
						$('.elgg-list-item.thewire').removeClass('responseAt');
					},
					error: function(){
						$.data(thisSubmit, 'clicked', false);
					}
				});
			}
		}
		e.preventDefault();
		return false;
	});
	
	// response to a wire post
	$('#thewire-header .responseTo').die().live('click', function() {
		$(this).parents('fieldset').find('input[name=parent_guid]').remove();
		$(this).remove();
		$('.tipsy').remove();
		$('#thewire-header, #thewire-textarea-border').css({height: '+=-22'});
		$('.elgg-list-item.thewire').removeClass('responseAt');
	});
	
	// refresh column, use 'live' for new column
	$('.elgg-column-refresh-button').die().live('click', function() {
		elgg.deck_river.RefreshColumn($(this).parents('.column-river'));
	});

	// refresh all columns
	$('.elgg-refresh-all-button').die().live('click', function() {
		$('.elgg-column-refresh-button').each(function() {
			elgg.deck_river.RefreshColumn($(this).parents('.column-river'));
		});
	});

	// Column settings
	$('.elgg-column-edit-button').die().live('click', function() {
		elgg.deck_river.ColumnSettings($(this).parents('.column-river'));
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
			/*var NumColumn = [];
			$('.column-river').each(function(){
				NumColumn.push($(this).attr('id').split('-')[1]);
			});*/
			elgg.deck_river.ColumnSettings();
		}
	});

	// make columns sortable
	$(".deck-river-lists-container").sortable({
		items:                '.column-river',
		connectWith:          '.deck-river-lists-container',
		handle:               '.column-header',
		forcePlaceholderSize: true,
		placeholder:          'column-placeholder',
		opacity:              0.8,
		revert:               500,
		start: function(event, ui) { $('.column-placeholder').css('width', $('.column-header').width()-3); },
		update:                elgg.deck_river.MoveColumn
	});
	
	// load discussion
	$('.elgg-river-responses a.thread').die().live('click', function() {
		elgg.deck_river.LoadDiscussion($(this));
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
	if (!$('#column-settings').length) elgg.deck_river.createPopup('column-settings', elgg.echo('deck_river:settings'), function() {
		$('#column-settings').find('.elgg-icon-push-pin').remove();
	});
	var columnSettings = $('#column-settings');

	elgg.post('ajax/view/deck_river/ajax/column_settings', {
		dataType: "html",
		data: {
			tab: $('.deck-river-lists').attr('id'),
			column: TheColumn ? TheColumn.attr('id') : 'new',
		},
		success: function(response) {
			var cs =$('#column-settings');
			cs.find('.elgg-body').html(response);
			
			elgg.autocomplete.init();
			
			// network vertical tabs
			cs.find('.elgg-tabs.networks a').click(function() {
				var net = $(this).attr('class');
				cs.find('.elgg-tabs > li').removeClass('elgg-state-selected');
				$(this).parent('li').addClass('elgg-state-selected');
				cs.find('#deck-column-settings > div').addClass('hidden');
				cs.find('#deck-column-settings > div.'+net).removeClass('hidden');
				cs.find('input.elgg-button-submit').addClass('hidden');
				cs.find('input.elgg-button-submit.'+net).removeClass('hidden');
				cs.find('.column-type').trigger('change');
			});
			if (cs.attr('data-network')) cs.find('.elgg-tabs.networks a.'+cs.attr('data-network')).click(); // used when authorize social network callback
			
			$('#authorize-twitter').click(function(e){
				var oauthWindow = window.open($(this).attr('data-url'), 'ConnectWithOAuth', 'location=0,status=0,width=800,height=400');
				e.preventDefault();
				return false;
			});
			
			// dropdown
			cs.find('.' + cs.find('.tab:visible .column-type').val().replace(/[:\/]/g, '-')+'-options').show();
			$('.column-type').change(function() {
				$(this).parents('.box-settings').find('li').not(':first-child').hide();
				$(this).parents('.box-settings').find('.'+$(this).val().replace(/[:\/]/g, '-')+'-options').show();
			});
			
			// checkboxes
			cs.find('.filter .elgg-input-checkbox').click(function() {
				if ( $(this).val() == 'All' ) $("#deck-column-settings .filter .elgg-input-checkbox").not($(this)).removeAttr("checked");
				if ( $(this).val() != 'All' ) $("#deck-column-settings .filter .elgg-input-checkbox[value='All']").removeAttr("checked");
			});
			
			$('.deck-river-form-column-settings').submit(function() { return false; });
			$(".elgg-foot .elgg-button").click(function(e) {
				if ($(this).attr('name') == 'delete' && !confirm(elgg.echo('deck-river:delete:column:confirm'))) return false;
				elgg.action('deck_river/column/settings', {
					data: $('.deck-river-form-column-settings').serialize() + "&submit=" + $(this).attr("name"),
					dataType: 'json',
					success: function(json) {
						response = json.output;
						if (response.action == 'delete') {
							$('li.column-river[id="'+response.column+'"] .elgg-list, li.column-river[id="'+response.column+'"] .elgg-list-item').css('background-color', '#FF7777');
							$('li.column-river[id="'+response.column+'"]').fadeOut(400, function() {
								$(this).animate({'width':0},'', function() {
									$(this).remove();
									elgg.deck_river.SetColumnsWidth();
								});
							});
						} else {
							if (response.action == 'new') {
								$('.deck-river-lists-container').append(
									$('<li>', {'class': 'column-river', id: response.column}).append(
										$('<ul>', {'class': 'column-header'}).after(
											$('<ul>', {'class': 'elgg-river elgg-list'})
								)));
								$('li.column-river:first-child .column-header').clone().appendTo($('li.column-river[id="'+response.column+'"] .column-header'))
								elgg.deck_river.SetColumnsHeight();
								elgg.deck_river.SetColumnsWidth();
								$('li.column-river[id="'+response.column+'"] .elgg-list').html($('<div>', {'class': 'elgg-ajax-loader'}));
								elgg.deck_river.LoadColumn($('li.column-river[id="'+response.column+'"]'));
								$('.deck-river-lists').animate({ scrollLeft: $('.deck-river-lists').width()});
							} else if (response.action == 'change') {
								$('li.column-river[id="'+response.column+'"] .elgg-list').html($('<div>', {'class': 'elgg-ajax-loader'}));
								elgg.deck_river.LoadColumn($('li.column-river[id="'+response.column+'"]'));
							}
							$('li.column-river[id="'+response.column+'"] .column-header h3').replaceWith($('<h3>', {'class': 'title'}).html(response.column_title));
							$('li.column-river[id="'+response.column+'"] .column-header h6').html(response.column_subtitle);
							console.log(response.direct == 'true');
							if (response.direct == 'true') {
								$('li.column-river[id="'+response.column+'"] h3').attr('data-direct', 'true');
								elgg.deck_river.LoadColumn($('li.column-river[id="'+response.column+'"]'));
							}
						}
						cs.remove();
						return false;
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

elgg.deck_river.authorize = function() { //system_message(elgg_echo('twitter_api:authorize:success'));
	var c = window.opener.$('#'+window.opener.$('.deck-river-form-column-settings input[name="column"]').val());
	window.opener.$('#column-settings').attr('data-network', 'twitter');
	if (c.length == 1) {
		c.find('.elgg-column-edit-button').click();
	} else {
		window.opener.$('.elgg-add-new-column').click();
	}
	window.opener.parent.elgg.system_message(elgg.echo('deck_river:twitter:authorize:success'));
	window.close();
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
				$('.deck-river-lists').attr('id', json.output.tab_name);
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
			tab: ui.item.parents('.deck-river-lists').attr('id'),
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
	var offset = $('.deck-river-lists').offset();
	$('.elgg-river').height($(window).height() - offset.top - $('.column-header').height() - scrollbarWidth());
	$('.deck-river-lists').height($(window).height() - offset.top);
};


/**
 * Resize columns width
 */
elgg.deck_river.SetColumnsWidth = function() {
	var WindowWidth = $('.deck-river-lists').width();
	var CountLists = $('.column-river').length;
	var ListWidth = 0; var i = 0;
	while ( ListWidth < deck_river_min_width_column ) {
		ListWidth = (WindowWidth) / ( CountLists - i );
		i++;
	}
	$('.elgg-river, .column-header, .column-placeholder').width(ListWidth - 2);
	$('.deck-river-lists-container').width(ListWidth * CountLists);
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
	$('.elgg-friendlytime').each(function(){
		friendlytime = elgg.friendly_time($(this).find('span').text())
		$(this).find('acronym').text(friendlytime);
	});
}

elgg.friendly_time.init = function() {
	elgg.friendly_time.update();
	setInterval(elgg.friendly_time.update, 1000*60); // each 60 sec
};

elgg.register_hook_handler('init', 'system', elgg.friendly_time.init);



// End of js for deck-river plugin
