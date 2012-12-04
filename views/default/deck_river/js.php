
/**
 *	Elgg-deck_river plugin
 *	@package elgg-deck_river
 *	@author Emmanuel Salomon @ManUtopiK
 *	@license GNU Affero General Public License, version 3 or late
 *	@link https://github.com/ManUtopiK/elgg-deck_river
 *
 *	Elgg-deck_river js
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
			$('body').addClass('fixed');
			$('.elgg-page-default .elgg-page-body > .elgg-inner').css('width','100%');
			elgg.deck_river.SetColumnsHeight();
			elgg.deck_river.SetColumnsWidth();

			// load columns
			$('.column-river').each(function() {
				elgg.deck_river.LoadColumn($(this));
			});

		} else {
			$('body').removeClass('fixed');
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
	
	// shortener url
	$('#thewire-header .url-shortener .elgg-input-text').focusin(function() {
		if (this.value == elgg.echo('deck-river:reduce_url:string')) {
			this.value = '';
		}
	}).focusout(function() {
		if (this.value == '') {
			this.value = elgg.echo('deck-river:reduce_url:string');
			$(this).parent().find('.elgg-button-action, .elgg-icon').addClass('hidden');
		}
	});
	$('#thewire-header .url-shortener .elgg-button-submit').die().live('click', function() {
		var longUrl = $(this).parent().find('.elgg-input-text');
		if (longUrl.val() == elgg.echo('deck-river:reduce_url:string')) {
			elgg.register_error(elgg.echo('deck_river:url-not-exist'));
		} else if (longUrl.val() != '') {
			elgg.deck_river.ShortenerUrl(longUrl.val(), longUrl);
		}
	});
	$('#thewire-header .url-shortener .elgg-button-action').die().live('click', function() {
		var txtarea = $('#thewire-textarea'),
			shortUrl = $(this).parent().find('.elgg-input-text').val(),
			strPos = txtarea.getCursorPosition(),
			front = (txtarea.val()).substring(0,strPos),
			back = (txtarea.val()).substring(strPos,txtarea.val().length); 
		
		if (shortUrl == elgg.echo('deck-river:reduce_url:string')) return;
		if (front.substring(front.length, front.length-1) != ' ' && front.length != 0) front = front + ' ';
		if (back.substring(0, 1) != ' ' && back.length != 0) back = ' ' + back;
		txtarea.val(front + shortUrl + back).focus().keydown();
	});
	$('#thewire-header .url-shortener .elgg-icon').die().live('click', function() {
		var urlShortner = $(this).parent();
		urlShortner.find('.elgg-input-text').val(elgg.echo('deck-river:reduce_url:string'));
		urlShortner.find('.elgg-button-action, .elgg-icon').addClass('hidden');
		$('.tipsy').remove();
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
	$('.delete-tab').click(function() {
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
	
	// user info popup
	$('.user-info-popup').die().live('click', function() {
		if (!$('#user-info-popup').length) {
			//var method = 'append';
			//$('.elgg-page-body')[method](
			$('.elgg-page-body').append(
				$('<div>', {id: 'user-info-popup', class: 'elgg-module-popup'}).draggable({ handle: ".elgg-head" }).append(
					$('<div>', {class: 'elgg-head'}).append(
						$('<h3>').html(elgg.echo('deck_river:user-info-header')).after(
						$('<a>').append(
							$('<span>', {class: 'elgg-icon elgg-icon-delete-alt'})
						).click(function() {
							$('#user-info-popup').remove();
						})
					)).after(
						$('<div>', {class: 'elgg-body'}).append(
							$('<div>', {class: 'elgg-ajax-loader'})
			))));
		} else {
			$('#user-info-popup > .elgg-body').html($('<div>', {class: 'elgg-ajax-loader'}));
		}
		elgg.post('ajax/view/deck_river/ajax/user_info', {
			dataType: "html",
			data: {
				user: $(this).attr('title'),
			},
			success: function(response) {
				$('#user-info-popup > .elgg-body').html(response);
				$('#user-info-popup .elgg-tabs > li > a').click(function() {
					var tab = $(this).attr('href');
					if ($('#user-info-popup ' + tab).hasClass('hidden')) {
						$('#user-info-popup .elgg-tabs > li').removeClass('elgg-state-selected');
						$(this).parent('li').addClass('elgg-state-selected');
						$('#user-info-popup .elgg-body > li').addClass('hidden');
						$('#user-info-popup ' + tab).removeClass('hidden');
					}
					if (tab == '#user-info-activity' && $('#user-info-activity .elgg-ajax-loader').length) {
						elgg.deck_river.LoadEntity($('#user-info-activity > .elgg-river').attr('data-user'), $('#user-info-popup #user-info-activity'));
					}
				});
			},
			error: function() {
				$('#user-info-popup > .elgg-body').html(elgg.echo('deck_river:ajax:erreur'));
			}
		});
	});
	
	// group info popup
	$('.group-info-popup').die().live('click', function() {
		if (!$('#group-info-popup').length) {
			$('.elgg-page-body').append(
				$('<div>', {id: 'group-info-popup', class: 'elgg-module-popup'}).draggable({ handle: ".elgg-head" }).append(
					$('<div>', {class: 'elgg-head'}).append(
						$('<h3>').html(elgg.echo('deck_river:group-info-header')).after(
						$('<a>').append(
							$('<span>', {class: 'elgg-icon elgg-icon-delete-alt'})
						).click(function() {
							$('#group-info-popup').remove();
						})
					)).after(
						$('<div>', {class: 'elgg-body'}).append(
							$('<div>', {class: 'elgg-ajax-loader'})
			))));
		} else {
			$('#group-info-popup > .elgg-body').html($('<div>', {class: 'elgg-ajax-loader'}));
		}
		elgg.post('ajax/view/deck_river/ajax/group_info', {
			dataType: "html",
			data: {
				group: $(this).attr('title'),
			},
			success: function(response) {
				$('#group-info-popup > .elgg-body').html(response);
				$('#group-info-popup .elgg-tabs > li > a').click(function() {
					var tab = $(this).attr('href');
					if ($('#group-info-popup ' + tab).hasClass('hidden')) {
						$('#group-info-popup .elgg-tabs > li').removeClass('elgg-state-selected');
						$(this).parent('li').addClass('elgg-state-selected');
						$('#group-info-popup .elgg-body > li').addClass('hidden');
						$('#group-info-popup ' + tab).removeClass('hidden');
					}
					if (tab == '#group-info-activity' && $('#group-info-activity .elgg-ajax-loader').length) {
						elgg.deck_river.LoadEntity($('#group-info-activity > .elgg-river').attr('data-group'), $('#group-info-popup #group-info-activity'));
					}
				});
			},
			error: function() {
				$('#group-info-popup > .elgg-body').html(elgg.echo('deck_river:ajax:erreur'));
			}
		});
	});
	
	// hashtag info popup
	$('.hashtag-info-popup').die().live('click', function() {
		if (!$('#hashtag-info-popup').length) {
			$('.elgg-page-body').append(
				$('<div>', {id: 'hashtag-info-popup', class: 'elgg-module-popup'}).draggable({ handle: ".elgg-head" }).append(
					$('<div>', {class: 'elgg-head'}).append(
						$('<h3>').html(elgg.echo('deck_river:hashtag-info-header', [$(this).attr('title')])).after(
						$('<a>').append(
							$('<span>', {class: 'elgg-icon elgg-icon-delete-alt'})
						).click(function() {
							$('#hashtag-info-popup').remove();
						})
					)).after(
						$('<div>', {class: 'elgg-body'}).append(
							$('<ul>', {class: 'elgg-river elgg-list'}).append(
								$('<div>', {class: 'elgg-ajax-loader'})
			)))));
		} else {
			$('#hashtag-info-popup > .elgg-river').html($('<div>', {class: 'elgg-ajax-loader'}));
		}
		elgg.deck_river.LoadEntity($(this).attr('title'), $('#hashtag-info-popup'));
	});

	// load discussion
	$('.elgg-river-responses a.thread').die().live('click', function() {
		var athread = $(this);
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
					
					athread.parent('.elgg-river-responses').append($('<div>', {id: idToggle, class: 'thread mts float'}).html(elgg.deck_river.TwitterDisplayItems(response, true)));
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
					
					athread.parent('.elgg-river-responses').append($('<div>', {id: idToggle, class: 'thread mts float'}).html(elgg.deck_river.displayItems(response, true)));
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
	});

};
elgg.register_hook_handler('init', 'system', elgg.deck_river.init);


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
				TheColumn.find('.elgg-river').append($('<li>', {class: 'moreItem'}).html(elgg.echo('deck_river:more')));
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
						TheColumn.find('.elgg-river').append($('<li>', {class: 'moreItem'}).html(elgg.echo('deck_river:more')));
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
								$('<td>', {class: 'helper'}).html(elgg.echo('deck_river:helper:'+c_type, [user.location])))
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
					TheColumn.find('.elgg-river').append($('<li>', {class: 'moreItem'}).html(elgg.echo('deck_river:more')));
	
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
 * Call settings of a column in popup
 *
 * Makes Ajax call to display settings of a column and perform change
 *
 * @param {TheColumn} the column
 * @return void
 */
elgg.deck_river.ColumnSettings = function(TheColumn) {
	elgg.post('ajax/view/deck_river/ajax/column_settings', {
		dataType: "html",
		data: {
			tab: $('.deck-river-lists').attr('id'),
			column: TheColumn ? TheColumn.attr('id') : 'new',
		},
		success: function(response) {
			if (!$('#column-settings').length) { $('.deck-river-lists').append('<div id="column-settings" class="elgg-module-popup"></div>'); }
			
			$('#column-settings').html(response).draggable({ handle: ".elgg-head" });
			$('#column-settings .elgg-head a').click(function() {
				$('#column-settings').remove();
			});
			$('#column-settings .elgg-tabs.networks a').click(function() {
				var net = $(this).attr('class');
				$('#column-settings #deck-column-settings > .elgg-tabs > li').removeClass('elgg-state-selected');
				$(this).parent('li').addClass('elgg-state-selected');
				$('#column-settings #deck-column-settings > div').addClass('hidden');
				$('#column-settings #deck-column-settings > div.'+net).removeClass('hidden');
				$('#column-settings input.elgg-button-submit').addClass('hidden');
				$('#column-settings input.elgg-button-submit.'+net).removeClass('hidden');
			});
			$('#column-settings .' + $('.column-type option:selected').val() + '-options').show();
			$('.column-type').change(function() {
				$('#column-settings .box-settings li').not(':first-child').hide();
				$('#column-settings .' + $('.column-type option:selected').val() + '-options').show();
			});
			$('#deck-column-settings .filter .elgg-input-checkbox').click(function() {
				if ( $(this).val() == 'All' ) $("#deck-column-settings .filter .elgg-input-checkbox").not($(this)).removeAttr("checked");
				if ( $(this).val() != 'All' ) $("#deck-column-settings .filter .elgg-input-checkbox[value='All']").removeAttr("checked");
			});
			
			$('.deck-river-form-column-settings').submit(function() { return false; });
			$(".elgg-button").click(function(event) {
				if ($(this).parent("form").beenSubmitted) // Prevent double-click
					return false;
				else {
					$(this).parent("form").beenSubmitted = true;
					if ($(this).attr('name') == 'delete' && !confirm(elgg.echo('deck-river:delete:column:confirm'))) return false;
					var dataString = $('.deck-river-form-column-settings').serialize() + "&submit=" + $(this).attr("name");
					elgg.action('deck_river/column/settings', {
						data: dataString,
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
										$('<li>', {class: 'column-river', id: response.column}).append(
											$('<ul>', {class: 'column-header'}).after(
												$('<ul>', {class: 'elgg-river elgg-list'})
									)));
									$('li.column-river:first-child .column-header').clone().appendTo($('li.column-river[id="'+response.column+'"] .column-header'))
									elgg.deck_river.SetColumnsHeight();
									elgg.deck_river.SetColumnsWidth();
									$('li.column-river[id="'+response.column+'"] .elgg-list').html($('<div>', {class: 'elgg-ajax-loader'}));
									elgg.deck_river.LoadColumn($('li.column-river[id="'+response.column+'"]'));
									$('.deck-river-lists').animate({ scrollLeft: $('.deck-river-lists').width()});
								} else if (response.action == 'change') {
									$('li.column-river[id="'+response.column+'"] .elgg-list').html($('<div>', {class: 'elgg-ajax-loader'}));
									elgg.deck_river.LoadColumn($('li.column-river[id="'+response.column+'"]'));
								}
								$('li.column-river[id="'+response.column+'"] .column-header h3').replaceWith($('<h3>', {class: 'title'}).html(response.column_title));
								$('li.column-river[id="'+response.column+'"] .column-header h6').html(response.column_subtitle);
								if (response.direct == 'true') $('li.column-river[id="'+response.column+'"] h3').attr('data-direct', 'true');
							}
							$('#column-settings').remove();
							return false;
						},
						error: function() {
						
						}
					});
					return false;
				}
			});
		}
	});
};

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
 * Shortener url
 */
elgg.deck_river.ShortenerUrl = function(url, input) {
	elgg.post('ajax/view/deck_river/ajax/url_shortener', {
		dataType: "html",
		data: {
			url: url,
		},
		success: function(response) {
			if (response == 'badurl') {
				elgg.register_error(elgg.echo('deck_river:url-bad-format'));
			} else {
				input.val(response);
				input.parent().find('.elgg-button-action, .elgg-icon').removeClass('hidden');
			}
		},
		error: function(response) {
			// error with server
		}
	});
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
				class: 'responseTo tooltip s',
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
					$('<span>', {class: 'elgg-icon elgg-icon-response gwf tooltip s', title: elgg.echo('reply')}).html('&lt;').click(function() {
						responseToWire(value.object_guid, user.username, value.id);
						wirearea.val('@' + user.username).focus().keydown();
					})
				)
			);
			menuOutput = menuOutput.after(
				$('<li>').append(
					$('<span>', {class: 'elgg-icon elgg-icon-retweet gwf tooltip s', title: elgg.echo('retweet')}).html('^').click(function() {
						//wirearea.val('RT @' + user.username + ' ' + value.message.replace(/<a.*?>|<\/a>/ig, '')).focus().keydown();
						wirearea.val('RT @' + user.username + ' ' + $(this).parents('.elgg-river').find('.item-river-'+value.id+' .elgg-river-message').text().replace(/^rt /i, '')).focus().keydown();
					})
				)
			);
			var match_users = value.message.match(/@\w{1,}/g); //@[A-Za-z0-9_]*.join(' ')
			if (match_users && !(match_users.length == 1 && $.inArray('@'+user.username, match_users) !== -1)) { // test if there are only river owner
				subMenuOutput = subMenuOutput.after(
					$('<li>').append(
						$('<span>', {class: 'elgg-icon elgg-icon-response-all gwf'}).html('&le;').click(function() {
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
					$('<a>', {class:'elgg-requires-confirmation', rel: elgg.echo('deleteconfirm'), is_action: 'is_action', 
					href: elgg.security.addToken(elgg.get_site_url() + 'action/river/delete?id=' + value.id)}).append(
						$('<span>', {class:'elgg-icon elgg-icon-delete'}).html(elgg.echo('delete'))
			)))
		}
		if (value.responses != null && !thread) {
			riverResponses = riverResponses.after(
				$('<span>', {class: 'elgg-icon elgg-icon-comment-sub gwf'}).html('c').after(
					$('<a>', {href: '#', class: 'thread float', 'data-thread': value.responses}).html(elgg.echo('deck_river:show_discussion')).after(
						$('<div>', {class: "response-loader hidden"})
			)));
		}
		
		output = output.after(
			$('<li>', {class: 'elgg-list-item item-river-'+ value.id +' '+ value.subtype +' '+ value.action_type}).mouseleave(function() {
				$(this).find('.elgg-submenu-river').removeClass('hover');
			}).append(
				$('<div>', {class: 'elgg-image-block elgg-river-item clearfix'}).append(
					$('<div>', {class: 'elgg-image'}).append(
						$('<div>', {class: 'elgg-avatar elgg-avatar-small'}).append(
							$('<div>', {class: 'user-info-popup', title: user.username}).append(
								$('<img>', {title: user.username, alt: user.username, src: user.icon})
				)))).append(
					$('<div>', {class: 'elgg-body'}).append(function() {
						if (menuOutput.length) return $('<ul>', {class: 'elgg-menu elgg-menu-river elgg-menu-hz elgg-menu-river-default'}).append(
							menuOutput.after(
								$('<li>', {class: 'elgg-submenu-river'}).click(function() {
									$(this).addClass('hover');
								}).append(function() {
									if (subMenuOutput.length) return $('<span>', {class:'elgg-icon elgg-icon-submenu-river gwf'}).html(']').after(
										$('<ul>', {class: 'elgg-module-popup hidden'}).append(subMenuOutput).mouseleave(function() {
											$('.elgg-submenu-river').removeClass('hover');
										
								}));
						})));
					}).append(
						$('<div>', {class: 'elgg-river-summary'}).html(value.summary+'<br/>').append(
							$('<span>', {class: 'elgg-river-timestamp'}).append(
								$('<span>', {class: 'elgg-friendlytime'}).append(
									$('<acronym>', {title: value.posted_acronym, class: 'tooltip w'}).html(elgg.friendly_time(value.posted)).after(
									$('<span>', {class: 'hidden'}).html(value.posted)
					))))).append(
						$('<div>', {class: 'elgg-river-message'}).html(value.message)
					).append(function() {
						if (riverResponses.length) return $('<div>', {class: 'elgg-river-responses'}).html(riverResponses);
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
					$('<span>', {class: 'elgg-icon elgg-icon-response gwf tooltip s', title: elgg.echo('reply')}).html('&lt;').click(function() {
						wirearea.val('@' + value.user.screen_name).focus().keydown();
						//wirearea.parents('fieldset').append('<input type="hidden" value="'+ value.object_guid +'" name="parent_guid">');
					})
				)
			);
			menuOutput = menuOutput.after(
				$('<li>').append(
					$('<span>', {class: 'elgg-icon elgg-icon-retweet gwf tooltip s', title: elgg.echo('retweet')}).html('^').click(function() {
						//wirearea.val('RT @' + user.username + ' ' + value.message.replace(/<a.*?>|<\/a>/ig, '')).focus().keydown();
						wirearea.val('RT @' + value.user.screen_name + ' ' + $(this).parents('.elgg-list-item').find('.elgg-river-message').text().replace(/^rt /i, '')).focus().keydown();
					})
				)
			);
			var match_users = value.text.match(/@\w{1,}/g); //@[A-Za-z0-9_]*.join(' ')
			if (match_users && !(match_users.length == 1 && $.inArray('@'+value.user.screen_name, match_users) !== -1)) { // test if there are only river owner
				subMenuOutput = subMenuOutput.after(
					$('<li>').append(
						$('<span>', {class: 'elgg-icon elgg-icon-response-all gwf'}).html('&le;').click(function() {
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
				riverResponses = $('<span>', {class: 'elgg-icon elgg-icon-retweet-sub gwf'}).html('^').after($('<span>', {class: 'prm'}).html(elgg.echo('retweet:one', [value.retweet_count])));
			} else {
				riverResponses = $('<span>', {class: 'elgg-icon elgg-icon-retweet-sub gwf'}).html('^').after($('<span>', {class: 'prm'}).html(elgg.echo('retweet:twoandmore', [value.retweet_count])));
			}
		}
		//console.log(value.in_reply_to_status_id);
		if (value.in_reply_to_status_id != null && !thread) {
			riverResponses = riverResponses.after(
				$('<span>', {class: 'elgg-icon elgg-icon-comment-sub gwf'}).html('c').after(
					$('<a>', {href: '#', class: 'thread float', 'data-direct': 'true', 'data-thread': value.in_reply_to_status_id_str}).html(elgg.echo('deck_river:show_discussion')).after(
						$('<div>', {class: "response-loader hidden"})
			)));
			// conversation > http://api.twitter.com/1/related_results/show/254208368070258688.json?include_entities=1
		}
		//console.log(riverResponses);

		output = output.after(
			$('<li>', {class: 'elgg-list-item item-twitter-'+ value.id}).mouseleave(function() {
				$(this).find('.elgg-submenu-river').removeClass('hover');
			}).append(
				$('<div>', {class: 'elgg-image-block elgg-river-item clearfix'}).append(
					$('<div>', {class: 'elgg-image'}).append(
						$('<div>', {class: 'elgg-avatar elgg-avatar-small'}).append(
							$('<div>', {class: 'user-info-popup', title: value.user.screen_name}).append(
								$('<img>', {title: value.user.screen_name, alt: value.user.screen_name, src: value.user.profile_image_url})
				)))).append(
					$('<div>', {class: 'elgg-body'}).append(function() {
						if (menuOutput.length) return $('<ul>', {class: 'elgg-menu elgg-menu-river elgg-menu-hz elgg-menu-river-default'}).append(
							menuOutput.after(
								$('<li>', {class: 'elgg-submenu-river'}).click(function() {
									$(this).addClass('hover');
								}).append(function() {
									if (subMenuOutput.length) return $('<span>', {class:'elgg-icon elgg-icon-submenu-river gwf'}).html(']').after(
										$('<ul>', {class: 'elgg-module-popup hidden'}).append(subMenuOutput).mouseleave(function() {
											$('.elgg-submenu-river').removeClass('hover');
										
								}));
						})));
					}).append(
						$('<div>', {class: 'elgg-river-summary'}).html(value.user.screen_name+'<br/>').append(
							$('<span>', {class: 'elgg-river-timestamp'}).append(
								$('<span>', {class: 'elgg-friendlytime'}).append(
									$('<acronym>', {title: value.created_at, class: 'tooltip w'}).html(elgg.friendly_time(value.created_at)).after(
									$('<span>', {class: 'hidden'}).html(value.id)
					))))).append(
						$('<div>', {class: 'elgg-river-message'}).html(value.text.TwitterParseURL().TwitterParseUsername().TwitterParseHashtag())
					).append(function() {
						if (riverResponses.length) return $('<div>', {class: 'elgg-river-responses'}).html(riverResponses);
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
