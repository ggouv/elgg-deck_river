<?php

global $CONFIG;

// Get callbacks
$page_filter = get_input('tab', 'default');
$column = get_input('column', 'false');
$time_method = get_input('time_method', 'false');
$time_posted = get_input('time_posted', 'false');

// Get the settings of the current user.
$owner = elgg_get_logged_in_user_guid();
$user_river_options = unserialize(get_private_setting($owner, 'deck_river_settings'));

// Set column user settings
switch ($user_river_options[$page_filter][$column]['type']) {
	case 'friends':
		$options['relationship_guid'] = $owner;
		$options['relationship'] = 'friend';
		break;
	case 'mine':
		$options['subject_guid'] = $owner;
		break;
	case 'mention':
		$options['search'] = array('@' . get_entity($owner)->name);
		break;
	case 'search':
		$options['search'] = $user_river_options[$page_filter][$column]['search'];
		break;
}
$options['title'] = $user_river_options[$page_filter][$column]['title'];
$options['types_filter'] = $user_river_options[$page_filter][$column]['types_filter'];
$options['subtypes_filter'] = $user_river_options[$page_filter][$column]['subtypes_filter'];


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


// Prepare joins and wheres clause for multiple query
if ($options['search']) {
	$options['joins'] = array(','.$CONFIG->dbprefix.'entities e',','.$CONFIG->dbprefix.'objects_entity o');
	$options['wheres'][] = "e.guid=o.guid AND rv.object_guid=o.guid AND (o.description REGEXP '(" . implode('|',$options['search']) . ")')";
}

$defaults = array(
	'offset'     => (int) max(get_input('offset', 0), 0),
	'limit'      => (int) max(get_input('limit', 20), 0),
	'pagination' => FALSE,
	'count' => FALSE,
);
$options = array_merge($defaults, $options);

$items = elgg_get_river($options);

$html = "";
if (is_array($items)) {
	foreach ($items as $item) {
		if (elgg_instanceof($item)) {
			$id = "elgg-{$item->getType()}-{$item->getGUID()}";
		} else {
			$id = "item-{$item->getType()}-{$item->id}";
		}
		$html .= "<li id=\"$id\" class='elgg-list-item' datetime=\"{$item->posted}\">";
		$html .= elgg_view_list_item($item);
		$html .= '</li>';
	}
}

echo $html;
