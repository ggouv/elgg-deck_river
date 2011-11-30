<?php

$tab = get_input('tab');
$column = get_input('column');
$type = get_input('type');
$column_title = get_input('title');
$search = get_input('search');
$types_filter = get_input('filters_types');
$subtypes_filter = get_input('filters_subtypes');

// save or delete
$delete = get_input('submit');

// Get the settings of the current column of the current user
$owner = elgg_get_logged_in_user_guid();
$user_river_options = unserialize(get_private_setting($owner, 'deck_river_settings'));

if ($delete === 'delete') {
	unset($user_river_options[$tab][$column]);
	echo "delete,$column,";
} else {
	if (!array_key_exists($column, $user_river_options[$tab])) {
		$column_container_change = 'new';
	} else {
		$column_container_change = 'no';
	}
	$user_river_column_options = $user_river_options[$tab][$column];

	if ($user_river_column_options['type'] != $type) {
		if ($column_container_change == 'no') $column_container_change = 'change';
		$user_river_options[$tab][$column]['type'] = $type;
		switch ($type) {
			case 'all':
				$column_title = elgg_echo('river:all');
				break;
			case 'friends':
				$column_title = elgg_echo('river:friends');
				break;
			case 'mine':
				$column_title = elgg_echo('river:mine');
				break;
			case 'mention':
				$column_title = '@' . get_entity($owner)->name;
				break;
		}
	}

	if ($user_river_column_options['title'] != $column_title) {
		$column_title_change = true;
		$user_river_options[$tab][$column]['title'] = $column_title;
	}

	if ($type == 'search' && $user_river_column_options['search'] != $search) {
		if ($column_container_change == 'no') $column_container_change = 'change';
		$user_river_options[$tab][$column]['search'] = explode(' ', $search);
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

	if ($user_river_column_options['subtypes_filter'] != $subtypes_filter || $user_river_column_options['types_filter'] != $types_filter) {
		if ($column_container_change == 'no') $column_container_change = 'change';
		if (in_array('All', $types_filter)) {
			unset($user_river_options[$tab][$column]['types_filter']);
			unset($user_river_options[$tab][$column]['subtypes_filter']);
		} elseif ($types_filter == 0) {
			unset($user_river_options[$tab][$column]['types_filter']);
			$user_river_options[$tab][$column]['subtypes_filter'] = $subtypes_filter;
		} elseif ($subtypes_filter == 0) {
			unset($user_river_options[$tab][$column]['subtypes_filter']);
			$user_river_options[$tab][$column]['types_filter'] = $types_filter;
		} else {
			$user_river_options[$tab][$column]['types_filter'] = $types_filter;
			$user_river_options[$tab][$column]['subtypes_filter'] = $subtypes_filter;
		}

	}

	echo "$column_container_change,$column,";
	// Send this to change title, last item is true if nothing change except title
	if ($column_title_change) echo $user_river_options[$tab][$column]['title'];
}

set_private_setting($owner, 'deck_river_settings', serialize($user_river_options));
