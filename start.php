<?php

elgg_register_event_handler('init','system','deck_river_init');

function deck_river_init() {

	elgg_register_library('deck_river:api', elgg_get_plugins_path() . 'elgg-deck_river/lib/api.php');
	elgg_load_library('deck_river:api');

	elgg_extend_view('css/elgg','deck_river/css');
	elgg_extend_view('js/elgg', 'deck_river/js');

	elgg_register_ajax_view('deck_river/ajax/column_river');
	elgg_register_ajax_view('deck_river/ajax/column_settings');
	elgg_register_ajax_view('deck_river/ajax/entity_river');
	elgg_register_ajax_view('deck_river/ajax/user_info');

	elgg_register_page_handler('activity', 'deck_river_page_handler');

	// register actions
	$action_path = elgg_get_plugins_path() . 'elgg-deck_river/actions';
	elgg_register_action('deck_river/column/settings', "$action_path/column/settings.php");
	elgg_register_action('deck_river/column/move', "$action_path/column/move.php");
	elgg_register_action('deck_river/tab/add', "$action_path/tab/add.php");
	elgg_register_action('deck_river/tab/delete', "$action_path/tab/delete.php");
	elgg_register_action('deck_river/tab/rename', "$action_path/tab/rename.php");
	elgg_register_action('elgg-deck_river/settings/save', "$action_path/plugins/save.php");

}

function deck_river_page_handler($page) {

	if (elgg_is_logged_in()) {
	
		switch ($page[0]) {
			default:
				elgg_set_context(elgg_extract(0, $page, 'default'));
				include_once dirname(__FILE__) . '/pages/river.php';
				break;
		}
	
	} else {
		forward('');
	}

	return true;
}


/**
 * Replace urls, hashtags,  ! and @ by links
 *
 * @param string $text The text of a post
 * @return string
 */
function deck_river_wire_filter($text) {
	global $CONFIG;

	$text = ' ' . $text;

	// email addresses
	$text = preg_replace(
				'/(^|[^\w])([\w\-\.]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})/i',
				'$1<a href="mailto:$2@$3">$2@$3</a>',
				$text);

	// links
	$text = parse_urls($text);

	// usernames
	$text = preg_replace(
				'/(^|[^\w])@([\p{L}\p{Nd}._]+)/u',
				'$1<a class="user-info-popup" href="#" title="$2">@$2</a>',
				$text);
				
	// groups
	$text = preg_replace(
				'/(^|[^\w])!([\p{L}\p{Nd}._]+)/u',
				'$1<a class="group-info-popup" href="#" title="$2">!$2</a>',
				$text);

	// hashtags
	$text = preg_replace(
				'/(^|[^\w])#(\w*[^\s\d!-\/:-@]+\w*)/',
				'$1<a href="' . $CONFIG->wwwroot . 'thewire/tag/$2">#$2</a>',
				$text);

	$text = trim($text);

	return $text;
}