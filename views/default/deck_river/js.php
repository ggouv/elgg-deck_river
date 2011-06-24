

// js for deck-river plugin
$(document).ready(function() {
	if ( $('.deck-river').length ) {
		$('body').css('position','fixed');
		$('.elgg-page-default .elgg-page-body > .elgg-inner').css('width','100%');
		var offset = $('.elgg-river').offset();
		$('.elgg-river').height($(window).height() - offset.top - 14);
		$('.deck-river-lists').height($('.elgg-river').height() + $('.column-header').height() + 14);
		SetListWidth();
		$('.elgg-avatar-small a > img').css('background-size','32px');
	}
});
$(window).bind("resize", function() {
	if ( $('.deck-river').length ) {
		var offset = $('.elgg-river').offset();
		$('.elgg-river').height($(window).height() - offset.top - 14);
		$('.deck-river-lists').height($('.elgg-river').height() + $('.column-header').height() + 14);
		$('.elgg-menu-deck-river').width(WindowWidth)
		SetListWidth();
	}
});
function SetListWidth() {
	var WindowWidth = $(window).width();
	var CountLists = $('.column-river').length;
	var ListWidth = 0; var i = 0;
	while ( ListWidth < 300 ) {
		ListWidth = (WindowWidth) / ( CountLists - i );
		i++;
	}
	$('.elgg-river').width(ListWidth);
}
