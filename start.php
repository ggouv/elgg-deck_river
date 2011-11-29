<?php

elgg_register_event_handler('init','system','deck_river_init');

function deck_river_init() {
	global $CONFIG, $deck_river_original_activity_page_handler;

	elgg_extend_view('css/elgg','deck_river/css');
	elgg_extend_view('js/elgg', 'deck_river/js');

	$deck_river_original_activity_page_handler = $CONFIG->pagehandler['activity'];
	elgg_register_page_handler('activity', 'deck_river_page_handler');

	// register actions
	$action_path = elgg_get_plugins_path() . 'elgg-deck_river/actions';
	elgg_register_action('deck_river/column_settings', "$action_path/column/settings.php");
	elgg_register_action('elgg-deck_river/settings/save', "$action_path/plugins/save.php");

}

function deck_river_page_handler($page) {
	global $CONFIG, $deck_river_original_activity_page_handler;

	$owner = elgg_get_logged_in_user_guid();

	elgg_set_page_owner_guid($owner);

	$user_river_options = unserialize(get_private_setting($owner, 'deck_river_settings'));

	$page_type = elgg_extract(0, $page, 'all');

	if (array_key_exists($page_type,$user_river_options) || $page_type == 'all') {
		require_once dirname(__FILE__) . '/pages/river.php';
		return true;
	} else {
		return call_user_func($deck_river_original_activity_page_handler, $segments, $handle);
	}
}
