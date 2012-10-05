<?php

$tab = get_input('tab');
$column = get_input('column');
$type = get_input('type');

if (!$tab || !$column || !$type) {
	return;
}

$search = get_input('search');
$group = get_input('group');
$types_filter = get_input('filters_types');
$subtypes_filter = get_input('filters_subtypes');
$submit = get_input('submit');

// Get the settings of the current column of the current user
$owner = elgg_get_logged_in_user_guid();
$user_river_options = unserialize(get_private_setting($owner, 'deck_river_settings'));

$return = array();
if ($submit == 'delete') {
	unset($user_river_options[$tab][$column]);
	$return['action'] = 'delete';
	$return['column'] = $column;
} else if ($submit == 'ggouv') {
	if (!array_key_exists($column, $user_river_options[$tab])) {
		$return['action'] = 'new';
	} else if ($user_river_options[$tab][$column]['type'] != $type) {
		$return['action'] = 'change';
	}

	switch ($type) {
		case 'all':
			$return['column_title'] = elgg_echo('river:all');
			$return['column_subtitle'] = '';
			break;
		case 'friends':
			$return['column_title'] = elgg_echo('river:timeline');
			$return['column_subtitle'] = elgg_echo('river:timeline:definition');
			break;
		case 'mine':
			$return['column_title'] = elgg_echo('river:mine');
			$return['column_subtitle'] = get_entity($owner)->name;
			break;
		case 'mention':
			$return['column_title'] = '@' . get_entity($owner)->name;
			$return['column_subtitle'] = elgg_echo('river:mentions');
			break;
		case 'group':
			if ($return['action'] != 'new' && $user_river_options[$tab][$column]['group'] != $group) $return['action'] = 'change';
			$user_river_options[$tab][$column]['group'] = $group;
			$return['column_title'] = '!' . get_entity($group)->name;
			$return['column_subtitle'] = elgg_echo('river:group');
			break;
		case 'search':
			if ($return['action'] != 'new' && $user_river_options[$tab][$column]['search'] != explode(' ', $search)) $return['action'] = 'change';
			$user_river_options[$tab][$column]['search'] = explode(' ', $search);
			$return['column_title'] = $search;
			$return['column_subtitle'] = elgg_echo('search');
			break;
		default:
			$params = array('owner' => $owner, 'query' => 'title');
			$hook = elgg_trigger_plugin_hook('deck-river', "column:$type", $params);
			$return = array_merge($return, $hook);
			break;
	}
	$user_river_options[$tab][$column]['title'] = $return['column_title'];
	$user_river_options[$tab][$column]['subtitle'] = $return['column_subtitle'];

	// allow plugin break here
	if ($return['break']) {
		$user_river_options[$tab][$column]['type'] = $type;
		set_private_setting($owner, 'deck_river_settings', serialize($user_river_options));
		$return['column'] = $column;
		echo json_encode($return);
		return true;
	}

	// merge keys defined by admin
	$keys_to_merge = explode(',', elgg_get_plugin_setting('keys_to_merge', 'elgg-deck_river'));
	foreach ($keys_to_merge as $key => $value ) {
		$key_master = explode('=', $value);
		foreach ($types_filter as $k => $v) {
			if ($v == $key_master[0]) $types_filter[] = $key_master[1];
		}
		foreach ($subtypes_filter as $k => $v) {
			if ($v == $key_master[0]) $subtypes_filter[] = $key_master[1];
		}
	}

	// filter changed ?
	if ($types_filter == '0') $types_filter = ''; // in case no checkbox checked or All
	if ($subtypes_filter == '0') $subtypes_filter = ''; // in case no checkbox checked
	if ($user_river_options[$tab][$column]['types_filter'] != $types_filter || $user_river_options[$tab][$column]['subtypes_filter'] != $subtypes_filter) {
		if (in_array('All', $types_filter)) {
			if (isset($user_river_options[$tab][$column]['types_filter'])) $return['action'] = 'change';
			unset($user_river_options[$tab][$column]['types_filter']);
			unset($user_river_options[$tab][$column]['subtypes_filter']);
		} elseif ($types_filter == 0) {
			$return['action'] = 'change';
			unset($user_river_options[$tab][$column]['types_filter']);
			$user_river_options[$tab][$column]['subtypes_filter'] = $subtypes_filter;
		} elseif ($subtypes_filter == 0) {
			$return['action'] = 'change';
			unset($user_river_options[$tab][$column]['subtypes_filter']);
			$user_river_options[$tab][$column]['types_filter'] = $types_filter;
		} else {
			$return['action'] = 'change';
			$user_river_options[$tab][$column]['types_filter'] = $types_filter;
			$user_river_options[$tab][$column]['subtypes_filter'] = $subtypes_filter;
		}
	}

	if (isset($user_river_options[$tab][$column]['types_filter']) || isset($user_river_options[$tab][$column]['subtypes_filter'])) {
		$return['column_subtitle'] .= ' | ' . elgg_echo('river:filtred');
	}
	
	$user_river_options[$tab][$column]['type'] = $type;

} else if ($submit == 'twitter') {
	$type = 'twitter';
	if (!array_key_exists($column, $user_river_options[$tab])) {
		$return['action'] = 'new';
	} else if ($user_river_options[$tab][$column]['type'] != $type) {
		$return['action'] = 'change';
	}
	$return['column_title'] = 'Twitter';
	$return['column_subtitle'] = 'Flux';
	$return['direct'] = 'true';

	$user_river_options[$tab][$column]['type'] = 'twitter';
	$user_river_options[$tab][$column]['title'] = $return['column_title'];
	$user_river_options[$tab][$column]['subtitle'] = $return['column_subtitle'];
	$user_river_options[$tab][$column]['direct'] = true;
}

set_private_setting($owner, 'deck_river_settings', serialize($user_river_options));
$return['column'] = $column;
echo json_encode($return);