<?php
/**
 * View a list of items
 *
 * @package Elgg
 *
 * @uses $vars['items']       Array of ElggEntity or ElggAnnotation objects
 * @uses $vars['offset']      Index of the first list item in complete list
 * @uses $vars['limit']       Number of items per page
 * @uses $vars['count']       Number of items in the complete list
 * @uses $vars['base_url']    Base URL of list (optional)
 * @uses $vars['pagination']  Show pagination? (default: true)
 * @uses $vars['position']    Position of the pagination: before, after, or both
 * @uses $vars['full_view']   Show the full view of the items (default: false)
 * @uses $vars['list_class']  Additional CSS class for the <ul> element
 * @uses $vars['item_class']  Additional CSS class for the <li> elements
 */

// Load Elgg engine
require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__))))))) . "/engine/start.php");

// Get callbacks
$page_filter = get_input('tab', 'default');
$column = get_input('column', 'false');
$time_method = get_input('time_method', 'false');
$time_posted = get_input('time_posted', 'false');
$refresh_title = get_input('refreshTitle', 'false');

// Get the settings of the current user.
$owner = elgg_get_logged_in_user_guid();
$user_river_options = unserialize(get_private_setting($owner, 'deck_river_settings'));
$fb->info($user_river_options);
// Set column user settings
$options['title'] = $user_river_options[$page_filter][$column]['title'];
$options['subject_guid'] = $user_river_options[$page_filter][$column]['subject_guid'];
$options['relationship_guid'] = $user_river_options[$page_filter][$column]['relationship_guid'];
$options['relationship'] = $user_river_options[$page_filter][$column]['relationship'];
$options['including'] = $user_river_options[$page_filter][$column]['including'];
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
if ($options['including']) {
	$options['joins'] = array(',entities e',',objects_entity o');
	$options['wheres'][] = "e.guid=o.guid AND rv.object_guid=o.guid AND (o.description REGEXP '(" . implode('|',$options['including']) . ")')";
}

$defaults = array(
	'offset'     => (int) max(get_input('offset', 0), 0),
	'limit'      => (int) max(get_input('limit', 20), 0),
	'pagination' => FALSE,
	'count' => FALSE,
);
$options = array_merge($defaults, $options);

$options2['pagination'] = FALSE;
//$options2['subtypes'] = array('page','page_top','file','bookmarks');
//$options2['types'] = array('user','object');
$options2['joins'] = array(',entities e',',objects_entity o');
$options2['wheres'] = array("((rv.type = 'group') OR ((rv.type = 'object') AND (rv.subtype IN ('page_top','bookmarks'))))");
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
		$html .= elgg_view_list_item($item, $vars);
		$html .= '</li>';
	}
}

echo $html;
