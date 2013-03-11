/**
 *	Elgg-deck_river plugin
 *	@package elgg-deck_river
 *	@author Emmanuel Salomon @ManUtopiK
 *	@license GNU Affero General Public License, version 3 or late
 *	@link https://github.com/ManUtopiK/elgg-deck_river
 *
 *	Elgg-deck_river shortener url js
 *
 */


/**
 * Elgg-deck_river shortener url init
 *
 * @return void
 */
elgg.deck_river.ShortenerUrlInit = function() {

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

}
elgg.register_hook_handler('init', 'system', elgg.deck_river.ShortenerUrlInit);


/**
 * Shortener url
 */
elgg.deck_river.ShortenerUrl = function(url, input) {
	elgg.post('ajax/view/deck_river/ajax_json/url_shortener', {
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