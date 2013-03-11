<?php

global $CONFIG;
$dbprefix = $CONFIG->dbprefix;

// Get callbacks
$tab = get_input('tab', 'default');
$column = get_input('column', 'false');
$time_method = get_input('time_method', 'false');
$time_posted = get_input('time_posted', 'false');

// Get the settings of the current user.
$owner = elgg_get_logged_in_user_entity();
$user_river_options = unserialize(get_private_setting($owner->guid, 'deck_river_settings'));
$column_settings = $user_river_options[$tab][$column];

// detect network
if ($column_settings['network'] == 'twitter') {
	$twitter_consumer_key = elgg_get_plugin_setting('twitter_consumer_key', 'elgg-deck_river');
	$twitter_consumer_secret = elgg_get_plugin_setting('twitter_consumer_secret', 'elgg-deck_river');
	$account = get_entity($column_settings['account']);

	/*elgg_load_library('deck_river:twitter_async');
	$twitterObj = new EpiTwitter($twitter_consumer_key, $twitter_consumer_secret, $account->oauth_token, $account->oauth_token_secret);
	$result = $twitterObj->get_statusesHome_timeline();
	global $fb; $fb->info($result->__get('response'));*/
} else {



// Set column user settings
switch ($column_settings['type']) {
	case 'all':
		break;
	case 'friends':
		$options['joins'][] = "JOIN {$dbprefix}entity_relationships r ON r.guid_two = rv.subject_guid";
		$options['joins'][] = "LEFT JOIN {$dbprefix}objects_entity o ON o.guid = rv.object_guid";
		$options['wheres'][] = "(r.relationship = 'friend' AND r.guid_one = '" . $owner->guid ."')";
		$options['wheres'][] = "(o.description IS NULL OR o.description NOT REGEXP '^@')";
		break;
	case 'mine':
		$options['subject_guid'] = $owner->guid;
		break;
	case 'mention':
		$options['joins'][] = "JOIN {$dbprefix}objects_entity o ON o.guid = rv.object_guid";
		$options['joins'][] = "LEFT JOIN {$dbprefix}annotations a ON a.id = rv.annotation_id";
		$options['joins'][] = "LEFT JOIN {$dbprefix}metastrings m ON m.id = a.value_id";
		$options['wheres'][] = "((o.description REGEXP '@" . $owner->name . "([[:blank:]]|$|<)') OR (m.string REGEXP '@" . $owner->name . "([[:blank:]]|$|<)'))";
		break;
	case 'group':
		$options['joins'][] = "JOIN {$dbprefix}entities e ON e.guid = rv.object_guid";
		$options['wheres'][] = "e.container_guid = " . $column_settings['group'];
		break;
	case 'search':
		$options['joins'][] = "JOIN {$dbprefix}objects_entity o ON o.guid = rv.object_guid";
		$options['wheres'][] = "(o.description REGEXP '(" . implode('|', $column_settings['search']) . ")')";
		break;
	default:
		$params = array('owner' => $owner->guid, 'query' => 'activity');
		$result = elgg_trigger_plugin_hook('deck-river', "column:{$column_settings['type']}", $params);
		$result['column_type'] = $column_settings['type'];
		echo json_encode($result);
		return;
		break;
}
$options['title'] = $column_settings['title'];
$options['types_filter'] = $column_settings['types_filter'];
$options['subtypes_filter'] = $column_settings['subtypes_filter'];


// set time_method and set $where_with_time in case of multiple query
if ($time_method == 'lower') {
	$options['posted_time_lower'] = (int)$time_posted+1; // +1 for not repeat first river item
} elseif ($time_method == 'upper') {
	$options['posted_time_upper'] = (int)$time_posted-1; // -1 for not repeat last river item
}

// Prepare wheres clause for filter
if ($options['subtypes_filter']) {
	$filters = "object' AND (rv.subtype IN ('";
	$filters .= implode("','", $options['subtypes_filter']);
	$options['types_filter'][] = $filters . "'))";
}
if ($options['types_filter']) {
	$filters = "((rv.type = '";
	$filters .= implode("') OR (rv.type = '", $options['types_filter']);
	if (substr($filters, -1) == ')') {
		$filters .= ')) ';
	} else {
		$filters .= "')) ";
	}
	$options['wheres'][] = $filters;
}

$defaults = array(
	'offset' => (int) get_input('offset', 0),
	'limit' => (int) get_input('limit', 20),
	'pagination' => FALSE,
	'count' => FALSE,
);
$options = array_merge($defaults, $options);
$items = elgg_get_river($options);

global $jsonexport;
$jsonexport['activity'] = array();

if (is_array($items)) {
	foreach ($items as $item) {
		if (elgg_view_exists($item->view, 'json')) {
			elgg_view($item->view, array('item' => $item), '', '', 'json');
		} else {
			elgg_view('river/item', array('item' => $item), '', '', 'json');
		}
	}
}


$temp_subjects = array();
foreach ($jsonexport['activity'] as $item) {
	if (!in_array($item->subject_guid, $temp_subjects)) $temp_subjects[] = $item->subject_guid; // store user

	$item->posted_acronym = htmlspecialchars(strftime(elgg_echo('friendlytime:date_format'), $item->posted)); // add date

	$menus = elgg_trigger_plugin_hook('register', "menu:river", array('item' => $item)); // add menus
	foreach ($menus as $menu) {
		$item->menu[] = $menu->getData('name');
	}

	unset($item->view); // delete view
}

$jsonexport['users'] = array();
foreach ($temp_subjects as $item) {
	$entity = get_entity($item);
	$jsonexport['users'][] = array(
		'guid' => $item,
		'type' => $entity->type,
		'username' => $entity->username,
		'icon' => $entity->getIconURL('small'),
	);
}

$jsonexport['column_type'] = $column_settings['type'];
echo json_encode($jsonexport);
}