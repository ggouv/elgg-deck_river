<?php
global $fb;
$tab = get_input('tab');
$column = get_input('column');
$column_title = get_input('title');
$types_filter = get_input('filters_types');
$subtypes_filter = get_input('filters_subtypes');

// Get the settings of the current column of the current user
$owner = elgg_get_logged_in_user_guid();
$user_river_options = unserialize(get_private_setting($owner, 'deck_river_settings'));
$user_river_column_options = $user_river_options[$tab][$column];
$column_container_change = 'false';

if ($user_river_column_options['title'] != $column_title) {
	$column_title_change = true;
	$user_river_options[$tab][$column]['title'] = $column_title;
}

if ($user_river_column_options['subtypes_filter'] != $subtypes_filter || $user_river_column_options['types_filter'] != $types_filter) {
	$column_container_change = 'true';
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

set_private_setting($owner, 'deck_river_settings', serialize($user_river_options));

echo "$column_container_change,$column,";
// Send this to change title, last item is true if nothing change except title
if ($column_title_change) echo $user_river_options[$tab][$column]['title'];
