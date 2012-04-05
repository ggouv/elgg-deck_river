
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
			$('.elgg-refresh-all-button').click(function() {
				$('.elgg-column-refresh-button').each(function() {
					elgg.deck_river.RefreshColumn($(this).parents('.column-river'));
				});
			});

			// Column settings, use 'live' for new column
			$('.elgg-column-edit-button').live('click', function() {
				if (!$('#column-settings').length) { $(this).parent().append('<div id="column-settings" class="elgg-module-popup"></div>'); }
				elgg.deck_river.ColumnSettings($(this).parents('.column-river').attr('id'));
			});

			// Delete tabs
			$('.delete-tab').click(function() {
				var tab = $(this).closest('li').attr('class');
				tab = tab.substr(tab.indexOf('elgg-menu-item-') + "elgg-menu-item-".length);
				if (confirm(elgg.echo('deck_river:delete:tab:confirm', [tab]))) {
					// delete tab through ajax
					elgg.action('deck_river/tab/delete', {
						data: {
							tab: tab
						},
						success: function() {
							$('li.elgg-menu-item-'+tab).remove();
						}
					});
				}
				return false;
			});

			// rename column button 
			$('.elgg-form-deck-river-tab-rename .elgg-button-submit').live('click', elgg.deck_river.tabRename);

			// Add new column
			$('.elgg-add-new-column').click(function() {
				var NbrColumn = $('.column-river').length;
				if (NbrColumn == deck_river_max_nbr_column) {
					elgg.system_message(elgg.echo('deck_river:limitColumnReached'));
				} else {
					if (!$('#column-settings').length) { $('.deck-river-lists-container').append('<div id="column-settings" class="elgg-module-popup"></div>'); }
					NumColumn = [];
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
				stop:                 elgg.deck_river.MoveColumn
			});

		}
	});
	$(window).bind("resize", function() {
		if ( $('.deck-river').length ) {
			elgg.deck_river.SetColumnsHeight();
			elgg.deck_river.SetColumnsWidth();
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
	elgg.get(elgg.config.wwwroot + 'activity/ajax/river', {
		data: {
			tab: $('.deck-river-lists').attr('id'),
			column: TheColumn.attr('id'),
		},
		success: function(response) {
			TheColumn.find('.elgg-river').html(response);
			if ( TheColumn.find('.elgg-list-item').length >= 20 ) {
				TheColumn.find('.elgg-river').append('<li class="moreItem">More...</li>');

				// load more items
				TheColumn.find('.moreItem').click(function() {
					TheColumn = $(this).closest('.column-river');
					TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + 'mod/elgg-deck_river/graphics/elgg_refresh.gif") no-repeat scroll -1px -1px transparent');
					elgg.get(elgg.config.wwwroot + 'activity/ajax/river', {
							data: {
								tab: $('.deck-river-lists').attr('id'),
								column: TheColumn.attr('id'),
								time_method: 'upper',
								time_posted: TheColumn.find('.elgg-river .elgg-list-item:last').attr('datetime')
							},
							success: function(response) {
								TheColumn.find('.elgg-river').append(response);
								TheColumn.find('.moreItem').appendTo(TheColumn.find('.elgg-river'));
							}
					});
					TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
				});
			}
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
	elgg.get(elgg.config.wwwroot + 'activity/ajax/river', {
		dataType: "html",
		data: {
			tab: $('.deck-river-lists').attr('id'),
			column: TheColumn.attr('id'),
			time_method: 'lower',
			time_posted: TheColumn.find('.elgg-river .elgg-list-item:first').attr('datetime') || 0,
		},
		success: function(response) {
		var $response = $('<div>').html(response);
		$response.find('.elgg-list-item').addClass('newRiverItem');
		TheColumn.find('.elgg-river').prepend($response.html());
		TheColumn.find('.newRiverItem').fadeIn('slow');
		}
	});
	TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -792px transparent');
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
	$('#column-settings').draggable().load(elgg.config.wwwroot + 'mod/elgg-deck_river/views/default/deck_river/column_settings.php?tab='+ $('.deck-river-lists').attr('id') + '&column=' + TheColumn, {}, function() {
		$('#column-settings .elgg-head a').click(function() {
			$('#column-settings').remove();
		});
		if ($('.column-type option:selected').val() == 'search') {
			$('#column-settings .search-type').show('slow');
		}
		$('.column-type').change(function() {
			if ($('.column-type option:selected').val() == 'search') {
				$('#column-settings .search-type').show('slow');
			} else {
				$('#column-settings .search-type').hide('slow');
			}
		});
		$('.deck-river-form-column-settings').submit(function() { return false; });
		$(".elgg-button").click(function(event) {
			if ($(this).parent("form").beenSubmitted) // Prevent double-click
				return false;
			else {
				$(this).parent("form").beenSubmitted = true;
				dataString = $('.deck-river-form-column-settings').serialize() + "&submit=" + $(this).attr("value");
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
							$('li.column-river[id="'+TheResponse[1]+'"]').fadeOut().animate({'width':0},'', function() {
								$(this).remove();
								elgg.deck_river.SetColumnsWidth();
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
console.log($('#column-settings').length);
						$('#column-settings').remove();
console.log($('#column-settings').length);
						return false;
					}
				});
				return false;
			}
		});
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

// End of js for deck-river plugin
