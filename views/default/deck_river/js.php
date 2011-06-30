

// js for deck-river plugin
$(document).ready(function() {
	if ( $('.deck-river').length ) {
		$('body').css('position','fixed');
		$('.elgg-page-default .elgg-page-body > .elgg-inner').css('width','100%');
		SetColumnsHeight();
		SetColumnsWidth();
		$('.elgg-avatar-small a > img').css('background-size','32px');
	}
});
$(window).bind("resize", function() {
	if ( $('.deck-river').length ) {
		SetColumnsHeight();
		SetColumnsWidth();
	}
});
function SetColumnsHeight() {
	var offset = $('.deck-river-lists').offset();
	$('.elgg-river').height($(window).height() - offset.top - $('.column-header').height() - 14);
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
}
