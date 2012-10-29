<?php

$thread_id = get_input('discussion', 'false');

if (!$thread_id) {
	echo elgg_echo('deck_river:thread-not-exist');
	return;
}

global $CONFIG;
$dbprefix = $CONFIG->dbprefix;

$metastring = get_metastring_id('wire_thread');
$thread_string = get_metastring_id($thread_id);

$options['joins'][] = "JOIN {$dbprefix}entities e ON e.guid = rv.object_guid";
$options['joins'][] = "LEFT JOIN {$dbprefix}metadata d ON d.entity_guid = e.guid";
$options['joins'][] = "LEFT JOIN {$dbprefix}metastrings m ON m.id = d.value_id";
$options['wheres'][] = "d.name_id = {$metastring} AND d.value_id = {$thread_string}";

$defaults = array(
	'pagination' => FALSE,
	'count' => FALSE,
	'order_by' => 'rv.posted asc'
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