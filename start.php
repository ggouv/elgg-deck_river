<?php

function deck_river_init() {

	//elgg_register_library('elgg:river_extended', dirname(__FILE__). '/lib/river_functions.php');
	//elgg_load_library('elgg:river_extended');

	elgg_extend_view('css/elgg','deck_river/css');
	elgg_extend_view('js/elgg', 'deck_river/js');

	global $CONFIG, $deck_river_original_activity_page_handler;
	$deck_river_original_activity_page_handler = $CONFIG->pagehandler['activity'];
	elgg_register_page_handler('activity', 'deck_river_page_handler');

}

function deck_river_page_handler($page) {
	global $CONFIG, $deck_river_original_activity_page_handler;

	elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());

	$page_type = elgg_extract(0, $page, 'all');
	if ($page_type == 'owner') {
		$page_type = 'mine';
	}

	// content filter code here
	$entity_type = '';
	$entity_subtype = '';

	require_once dirname(__FILE__) . '/pages/river.php';
	return true;
}

register_elgg_event_handler('init','system','deck_river_init');
