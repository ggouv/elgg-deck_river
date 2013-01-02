<?php

global $CONFIG;
$dbprefix = $CONFIG->dbprefix;

// Get callbacks
$entity_guid = get_input('guid', 'false');
$time_method = get_input('time_method', 'false');
$time_posted = get_input('time_posted', 'false');

$entity = get_entity($entity_guid);

$options['joins'][] = "JOIN {$dbprefix}objects_entity o ON o.guid = rv.object_guid";
$options['joins'][] = "LEFT JOIN {$dbprefix}annotations a ON a.id = rv.annotation_id";
$options['joins'][] = "LEFT JOIN {$dbprefix}metastrings m ON m.id = a.value_id";
$options['wheres'][] = "((o.description REGEXP '!" . $entity->name . "([[:blank:]]|$|<)') OR (m.string REGEXP '!" . $entity->name . "([[:blank:]]|$|<)'))";

$options['types_filter'] = get_input('types_filter');
$options['subtypes_filter'] = get_input('subtypes_filter');

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

echo json_encode($jsonexport);