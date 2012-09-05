
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
var deck_river_min_width_column = <?php echo elgg_get_plugin_setting('min_width_column', 'elgg-deck_river'); ?>;
var deck_river_max_nbr_column = <?php echo elgg_get_plugin_setting('max_nbr_column', 'elgg-deck_river'); ?>;

/**
 * Elgg-deck_river initialization
 *
 * @return void
 */
elgg.provide('elgg.deck_river');

elgg.deck_river.init = function() {
	$(document).ready(function() {
		if ( $('.deck-river').length ) {
			$('body').css('position','fixed');
			$('.elgg-page-default .elgg-page-body > .elgg-inner').css('width','100%');
			elgg.deck_river.SetColumnsHeight();
			elgg.deck_river.SetColumnsWidth();

			// load columns
			$('.column-river').each(function() {
				elgg.deck_river.LoadColumn($(this));
			});

			// refresh column, use 'live' for new column
			$('.elgg-column-refresh-button').live('click', function() {
				elgg.deck_river.RefreshColumn($(this).parents('.column-river'));
			});

			// refresh all columns
			$('.elgg-refresh-all-button').live('click', function() {
				$('.elgg-column-refresh-button').each(function() {
					elgg.deck_river.RefreshColumn($(this).parents('.column-river'));
				});
			});

			// Column settings
			$('.elgg-column-edit-button').live('click', function() {
				elgg.deck_river.ColumnSettings($(this).parents('.column-river').attr('id'));
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
			$('.elgg-form-deck-river-tab-rename .elgg-button-submit').live('click', elgg.deck_river.tabRename);

			// Add new column
			$('.elgg-add-new-column').live('click', function() {
				var NbrColumn = $('.column-river').length;
				if (NbrColumn == deck_river_max_nbr_column) {
					elgg.system_message(elgg.echo('deck_river:limitColumnReached'));
				} else {
					var NumColumn = [];
					$('.column-river').each(function(){
						NumColumn.push($(this).attr('id').split('-')[1]);
					});
					elgg.deck_river.ColumnSettings( 'column-' + ( ( Math.max.apply(null, NumColumn) ) +1 ) );
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
				update:                 elgg.deck_river.MoveColumn
			});

		} else {
			$('body').css('position','relative');
		}
	});
	$(window).bind("resize", function() {
		if ( $('.deck-river').length ) {
			elgg.deck_river.SetColumnsHeight();
			elgg.deck_river.SetColumnsWidth();
		}
	});
	
	// submenu river
	$('.elgg-submenu-river').die().live('click', function() {
		$(this).addClass('hover');
	});
	/*$('.elgg-submenu-river > .elgg-module-popup').die().live('mouseleave', function() {
		$('.elgg-submenu-river').removeClass('hover');
	});
	$('.elgg-river > li.elgg-list-item').die().live('mouseleave', function() {
			$('.elgg-submenu-river').removeClass('hover');
	});*/ //@todo don't work because mouseleave is non-standard event and broke with live.
	
	// user info popup
	$('.user-info-popup').die().live('click', function() {
		if (!$('#user-info-popup').length) {
			$('.elgg-page-body').append('<div id="user-info-popup" class="elgg-module-popup"><div class="elgg-ajax-loader"></div></div>');
		} else {
			$('#user-info-popup').html('<div class="elgg-ajax-loader"></div>');
		}
		elgg.post('ajax/view/deck_river/ajax/user_info', {
			dataType: "html",
			data: {
				user: $(this).attr('title'),
			},
			success: function(response) {
				$('#user-info-popup').html(response).draggable({ handle: ".elgg-head" });
				$('#user-info-popup .elgg-head a').click(function() {
					$('#user-info-popup').remove();
				});
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
				$('#user-info-popup').html(elgg.echo('deck_river:ajax:erreur'));
			}
		});
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
	elgg.post('ajax/view/deck_river/ajax/column_river', {
		dataType: 'json',
		data: {
			tab: $('.deck-river-lists').attr('id'),
			column: TheColumn.attr('id'),
		},
		success: function(response) {
		console.log(elgg.deck_river.displayItems(response));
			TheColumn.find('.elgg-river').html(elgg.deck_river.displayItems(response));
			if ( TheColumn.find('.elgg-list-item').length >= 20 ) {
				TheColumn.find('.elgg-river').append('<li class="moreItem">More...</li>');
				$('.elgg-submenu-river > .elgg-module-popup').mouseleave(function() {
					$('.elgg-submenu-river').removeClass('hover');
				});
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
								TheColumn.find('.elgg-river').append(elgg.deck_river.displayItems(response));
								TheColumn.find('.moreItem').appendTo(TheColumn.find('.elgg-river'));
								$('.elgg-submenu-river > .elgg-module-popup').mouseleave(function() {
									$('.elgg-submenu-river').removeClass('hover');
								});
								//	var pos = LastItem.next().position();
								//TheColumn.find('.elgg-river').scrollTo('+='+pos.top+'px', 2500, {easing:'easeOutQuart'});
								TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
							}
					});
				});
			}
			TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
		}
	});
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
			TheColumn.find('.elgg-river').html(elgg.deck_river.displayItems(response));
			$('.elgg-submenu-river > .elgg-module-popup').mouseleave(function() {
				$('.elgg-submenu-river').removeClass('hover');
			});
			if ( TheColumn.find('.elgg-list-item').length >= 20 ) {
				TheColumn.find('.elgg-river').append('<li class="moreItem">More...</li>');

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
								TheColumn.find('.elgg-river').append(elgg.deck_river.displayItems(response));
								TheColumn.find('.moreItem').appendTo(TheColumn.find('.elgg-river'));
								$('.elgg-submenu-river > .elgg-module-popup').mouseleave(function() {
									$('.elgg-submenu-river').removeClass('hover');
								});
								//	var pos = LastItem.next().position();
								//TheColumn.find('.elgg-river').scrollTo('+='+pos.top+'px', 2500, {easing:'easeOutQuart'});
								TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
							}
					});
				});
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
	elgg.post('ajax/view/deck_river/ajax/column_river', {
		dataType: "json",
		data: {
			tab: $('.deck-river-lists').attr('id'),
			column: TheColumn.attr('id'),
			time_method: 'lower',
			time_posted: TheColumn.find('.elgg-river .elgg-list-item:first .elgg-friendlytime span').text() || 0,
		},
		success: function(response) {
			var $response = $('<div>').html(elgg.deck_river.displayItems(response));
			$response.find('.elgg-list-item').addClass('newRiverItem');
			TheColumn.find('.elgg-river').prepend($response.html());
			$('.elgg-submenu-river > .elgg-module-popup').mouseleave(function() {
				$('.elgg-submenu-river').removeClass('hover');
			});
			TheColumn.find('.newRiverItem').fadeIn('slow');
			TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
		}
	});
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
			column: TheColumn,
		},
		success: function(response) {
			if (!$('#column-settings').length) { $('.deck-river-lists').append('<div id="column-settings" class="elgg-module-popup"></div>'); }
			
			$('#column-settings').html(response).draggable({ handle: ".elgg-head" });
			$('#column-settings .elgg-head a').click(function() {
				$('#column-settings').remove();
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
					var dataString = $('.deck-river-form-column-settings').serialize() + "&submit=" + $(this).attr("value");
					elgg.action('deck_river/column/settings', {
						data: dataString,
						success: function(json) {
							TheResponse = json['output'].split(',');
							if (TheResponse[2]) $('li.column-river[id="'+TheResponse[1]+'"] h3').html(TheResponse[2]);
							if (TheResponse[0] == 'change') {
								$('li.column-river[id="'+TheResponse[1]+'"] .elgg-list').html('<div class="elgg-ajax-loader "></div>');
								elgg.deck_river.LoadColumn($('li.column-river[id="'+TheResponse[1]+'"]'));
							}
							if (TheResponse[0] == 'delete') {
								$('li.column-river[id="'+TheResponse[1]+'"]').css('background-color', '#FF7777').fadeOut(400, function() {
									$(this).animate({'width':0},'', function() {
										$(this).remove();
										elgg.deck_river.SetColumnsWidth();
									});
								});
							}
							if (TheResponse[0] == 'new') {
								$('.deck-river-lists-container').append('<li class="column-river" id="'+TheResponse[1]+'"><ul class="column-header"></ul><ul class="elgg-river elgg-list"></ul></li>');
								$('li.column-river[id="'+TheResponse[1]+'"] .column-header').html($('li.column-river[id="column-1"] .column-header').html());
								elgg.deck_river.SetColumnsHeight();
								elgg.deck_river.SetColumnsWidth();
								$('li.column-river[id="'+TheResponse[1]+'"] .elgg-list').html('<div class="elgg-ajax-loader "></div>');
								elgg.deck_river.LoadColumn($('li.column-river[id="'+TheResponse[1]+'"]'));
								$('li.column-river[id="'+TheResponse[1]+'"] h3').html(TheResponse[2]);
								$('.deck-river-lists').animate({ scrollLeft: $('.deck-river-lists').width()});
							}
							$('#column-settings').remove();
							return false;
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
 * Javascript template for river element @todo waiting for Elgg core developers to see wich library they will use (ember.js, ...) in elgg 1.9 or 2 and replace it with a js MVC system.
 *
 * @param {array}	json response
 */
elgg.deck_river.displayItems = function(response) {
	var output = $();
	$.each(response.activity, function(key, value) {
		var user = $.grep(response.users, function(e){ return e.guid == value.subject_guid; })[0],
			menuOutput = subMenuOutput = $();

		if (value.subtype == 'thewire') {
			menuOutput = menuOutput.after(
				$('<li>').append(
					$('<span>', {class: 'elgg-icon elgg-icon-response gwf', title: elgg.echo('reply')}).html('&lt;')
				)
			);
			menuOutput = menuOutput.after(
				$('<li>').append(
					$('<span>', {class: 'elgg-icon elgg-icon-retweet gwf', title: elgg.echo('retweet')}).html('^')
				)
			);
			if (value.message.match(/@\w{1,}/g)) {
				subMenuOutput = subMenuOutput.after(
					$('<li>').append(
						$('<span>', {class: 'elgg-icon elgg-icon-response-all gwf', title: elgg.echo('replyall')}).html('&le;')
					).append(elgg.echo('replyall'))
				);
			}
		}
		if (value.menu && value.menu.indexOf('delete') > -1) {
			subMenuOutput = subMenuOutput.after(
				$('<li>').append(
					$('<a>', {class:'elgg-requires-confirmation', rel: elgg.echo('deleteconfirm'), is_action: 'is_action', title: elgg.echo('delete:this'), 
					href: elgg.security.addToken(elgg.get_site_url() + 'action/river/delete?id=' + value.id)}).append(
						$('<span>', {class:'elgg-icon elgg-icon-delete'}).html(elgg.echo('delete'))
			)))
		}
				
		output = output.after(
			$('<li>', {class: 'elgg-list-item item-river-'+ value.id +' '+ value.subtype +' '+ value.action_type}).append(
				$('<div>', {class: 'elgg-image-block elgg-river-item clearfix'}).append(
					$('<div>', {class: 'elgg-image'}).append(
						$('<div>', {class: 'elgg-avatar elgg-avatar-small'}).append(
							$('<div>', {class: 'user-info-popup', title: user.username}).append(
								$('<img>', {title: user.username, alt: user.username, src: user.icon})
				)))).append(
					$('<ul>', {class: 'elgg-menu elgg-menu-river elgg-menu-hz elgg-menu-river-default'}).append(
						menuOutput.after(
							$('<li>', {class: 'elgg-submenu-river'}).append(
								$('<span>', {class:'elgg-icon elgg-icon-submenu-river gwf'}).html(']').after(
								$('<ul>', {class: 'elgg-module-popup hidden'}).append(subMenuOutput)
				))))).append(
					$('<div>', {class: 'elgg-body'}).append(
						$('<div>', {class: 'elgg-river-summary'}).html(value.summary+'<br/>').append(
							$('<span>', {class: 'elgg-river-timestamp'}).append(
								$('<span>', {class: 'elgg-friendlytime'}).append(
									$('<acronym>', {title: value.posted_acronym}).html(elgg.friendly_time(value.posted)).after(
									$('<span>', {class: 'hidden'}).html(value.posted)
					))))).append(
						$('<div>', {class: 'elgg-river-message'}).html(value.message)
					)
				)
			)
		);
		console.log(output.last());
		/*[
		'<li class="elgg-list-item item-river-', value.id ,' ', value.subtype ,' ', value.action_type ,'">',
			'<div class="elgg-image-block elgg-river-item clearfix">',
				'<div class="elgg-image">',
					'<div class="elgg-avatar elgg-avatar-small">',
						'<div class="user-info-popup" title="', user.username ,'">',
							'<img title="', user.username ,'" alt="', user.username ,'" src="', user.icon ,'">',
						'</div>',
					'</div>',
				'</div>'].join('');
				if (value.subtype == 'thewire') {
					menuOutput += '<li><span class="elgg-icon elgg-icon-response gwf" title="' + elgg.echo('reply') + '">&lt;</span></a></li>';
					menuOutput += '<li><span class="elgg-icon elgg-icon-retweet gwf" title="' + elgg.echo('retweet') + '">^</span></a></li>';
					if (value.message.match(/@\w{1,}/g)) subMenuOutput += '<li><span class="elgg-icon elgg-icon-response-all gwf" title="' + elgg.echo('replyall') + '">&le;</span>' + elgg.echo('replyall') + '</a></li>';
				}
				//if (menuItem == 'comment') {
				if (value.menu && value.menu.indexOf('delete') > -1) {
					subMenuOutput += '<li><a class="elgg-requires-confirmation" rel="' + elgg.echo('deleteconfirm') + '" is_action="is_action" title="' + elgg.echo('delete:this') + '" href="' +
						elgg.security.addToken(elgg.get_site_url() + 'action/river/delete?id=' + value.id) + '"><span class="elgg-icon elgg-icon-delete ">'+ elgg.echo('delete') +'</span></a></li>';
				}
				if (subMenuOutput != '') menuOutput += '<li class="elgg-submenu-river"><span class="elgg-icon elgg-icon-submenu-river gwf">]</span>' +
					'<ul class="elgg-module-popup hidden">' + subMenuOutput + '</ul></li>';
				if (menuOutput != '') output += '<ul class="elgg-menu elgg-menu-river elgg-menu-hz elgg-menu-river-default">' + menuOutput + '</ul>';
							*//*'<li class="elgg-menu-item-comment">',
								'<a rel="toggle" title="Commenter" href="#comments-add-2054"><span class="elgg-icon elgg-icon-speech-bubble "></span></a>',
							'</li>'*/
				/*output += [
				'<div class="elgg-body">',
					'<div class="elgg-river-summary">',
						value.summary, '<br/>',
						'<span class="elgg-river-timestamp">',
							'<span class="elgg-friendlytime">',
								'<acronym title="', value.posted_acronym ,'">', elgg.friendly_time(value.posted) ,'</acronym>',
								'<span class="hidden">', value.posted ,'</span>',
							'</span>',
						'</span>',
					'</div>',
					'<div class="elgg-river-message">', value.message ,'</div>',
				'</div>',
			'</div>',
		'</li>'
		].join('');*/
	});
	return output;
};


// End of js for deck-river plugin
