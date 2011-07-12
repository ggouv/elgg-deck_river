

// js for deck-river plugin
$(document).ready(function() {
	if ( $('.deck-river').length ) {
		$('body').css('position','fixed');
		$('.elgg-page-default .elgg-page-body > .elgg-inner').css('width','100%');
		SetColumnsHeight();
		SetColumnsWidth();

		// load columns
		$('.column-river > .elgg-list').each(function() {
			$(this).load(elgg.config.wwwroot + 'mod/elgg-deck_river/views/default/page/components/ajax_list.php?column=' + $(this).parent().attr('rel'), {}, function() {
				if ( $(this).find('.elgg-list-item').length >= 20 ) {
					$(this).append('<li class="moreItem">More...</li>');

					// load more items
					$('.moreItem').click(function() {
						$(this).parents('.column-river').find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + 'mod/elgg-deck_river/graphics/elgg_refresh.gif") no-repeat scroll -1px -1px transparent');
						var column = $(this).parents('.column-river').attr('rel');
						var posted = $(this).parents('.column-river').find('.elgg-river .elgg-list-item:last').attr('datetime');
						$(this).append('<div id="ajax_list" style="display:none;"><div>');
						$('#ajax_list').load(elgg.config.wwwroot + 'mod/elgg-deck_river/views/default/page/components/ajax_list.php?column=' + column + '&time_method=upper&time_posted=' + posted, {}, function(){
							$(this).parents('.column-river').find('.elgg-river').append($('#ajax_list').html()).append($(this).parents('.column-river').find('.moreItem'));
							$(this).parents('.column-river').find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -576px transparent');
							$('#ajax_list').remove();
						});
					});

				}
			});
		});

		// refresh column
		$('.elgg-column-refresh-button').click(function() {
			RefreshColumn($(this).parents('.column-river'));
		});

		// refresh all columns
		$('.elgg-refresh-all-button').click(function() {
			$('.elgg-column-refresh-button').each(function() {
				RefreshColumn($(this).parents('.column-river'));
			});
		});

//		var elem = $('#box');
//		var inner = $('#box > .inner');
//		if ( Math.abs(inner.offset().top) + elem.height() + elem.offset().top >= inner.outerHeight() ) {
//			// We're at the bottom!
//		}

	}
});

$(window).bind("resize", function() {
	if ( $('.deck-river').length ) {
		SetColumnsHeight();
		SetColumnsWidth();
	}
});

function RefreshColumn(TheColumn) {
	TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + 'mod/elgg-deck_river/graphics/elgg_refresh.gif") no-repeat scroll -1px -1px transparent');
	TheColumn.find('.elgg-list-item').removeClass('newRiverItem');
	TheColumn.append('<div id="ajax_list" style="display:none;"><div>');
	var column = TheColumn.attr('rel');
	var posted = TheColumn.find('.elgg-river .elgg-list-item:first').attr('datetime');
	$('#ajax_list').load(elgg.config.wwwroot + 'mod/elgg-deck_river/views/default/page/components/ajax_list.php?column=' + column + '&time_method=lower&time_posted=' + posted, {}, function(){
		$('#ajax_list .elgg-list-item').addClass('newRiverItem');
		TheColumn.find('.elgg-river').prepend($('#ajax_list').html());
		TheColumn.find('.newRiverItem').fadeIn('slow');
		TheColumn.find('.elgg-icon-refresh').css('background', 'url("' + elgg.config.wwwroot + '_graphics/elgg_sprites.png") no-repeat scroll 0 -576px transparent');
		$('#ajax_list').remove();
	});
}

function SetColumnsHeight() {
	var offset = $('.deck-river-lists').offset();
	$('.elgg-river').height($(window).height() - offset.top - $('.column-header').height() - scrollbarWidth());
	$('.deck-river-lists').height($(window).height() - offset.top);
}

function SetColumnsWidth() {
	var WindowWidth = $('.deck-river-lists').width();
	var CountLists = $('.column-river').length;
	var ListWidth = 0; var i = 0;
	while ( ListWidth < 300 ) {
		ListWidth = (WindowWidth) / ( CountLists - i );
		i++;
	}
	$('.elgg-river, .column-header').width(ListWidth);
	$('.deck-river-lists-container').width(ListWidth * CountLists);
}

function scrollbarWidth() {
	if (!$._scrollbarWidth) {
		var $body = $('body');
		var w = $body.css('overflow', 'hidden').width();
		$body.css('overflow','scroll');
		w -= $body.width();
		if (!w) w=$body.width()-$body[0].clientWidth; // IE in standards mode
		$body.css('overflow','');
		$._scrollbarWidth = w;
	}
	return $._scrollbarWidth;
}

// End of js for deck-river plugin
