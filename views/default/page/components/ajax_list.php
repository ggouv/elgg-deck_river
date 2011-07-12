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

$site_url = elgg_get_site_url();

// Get callback type (list or picker)
$column = get_input('settings', 'false');

// Get the settings of the current user. If not, set it to defaults.
$owner = elgg_get_logged_in_user_guid();
$defaults = array('default' => array(
	'column-1' => array(
		'title' => elgg_echo('river:all'),
		'page_filter' => 'all',
	),
	'column-2' => array(
		'title' => elgg_echo('river:friends'),
		'page_filter' => 'friends',
		'relationship_guid' => $owner,
		'relationship' => 'friend',
	),
	'column-3' => array(
		'title' => elgg_echo('river:mine'),
		'page_filter' => 'mine',
		'subject_guid' => $owner,
	),
	'column-4' => array(
		'title' => '@' . get_entity($owner)->name,
		'page_filter' => 'mention',
		'including' => array('@' . get_entity($owner)->name),
	),
	'column-5' => array(
		'title' => '#nrst + !test + #rnst',
		'page_filter' => 'mention',
		'including' => array('#nrst', '!test', '#rnst'),
	),
	'column-6' => array(
		'title' => '#nrstauie',
		'page_filter' => 'mention',
		'including' => array('#nrstauie'),
	),
));

//set_private_setting($owner, 'deck_river_settings', 'a:1:{s:7:"default";a:5:{s:8:"column-1";a:2:{s:5:"title";s:17:"All Site Activity";s:11:"page_filter";s:3:"all";}s:8:"column-2";a:4:{s:5:"title";s:15:"Friends Activty";s:11:"page_filter";s:7:"friends";s:17:"relationship_guid";s:2:"34";s:12:"relationship";s:6:"friend";}s:8:"column-3";a:3:{s:5:"title";s:11:"My Activity";s:11:"page_filter";s:4:"mine";s:12:"subject_guid";s:2:"34";}s:8:"column-4";a:3:{s:5:"title";s:5:"@manu";s:11:"page_filter";s:7:"mention";s:9:"including";a:1:{i:0;s:5:"@manu";}}s:8:"column-5";a:3:{s:5:"title";s:21:"#nrst + !test + #rnst";s:11:"page_filter";s:7:"mention";s:9:"including";a:1:{i:0;s:5:"#nrst";}}}}');
//$fb->info(get_private_setting($owner, 'deck_river_settings'),'deck_river_settings1');
//$user_river_options = $defaults; //unserialize(get_private_setting($owner, 'deck_river_settings'));
//if ( !$user_river_options || !is_array($user_river_options) ) set_private_setting($owner, 'deck_river_settings', serialize($defaults));
$user_river_options = $defaults;
//$fb->info($user_river_options,'$user_river_options');

$options['title'] = $user_river_options['default'][$column]['title'];
$options['subject_guid'] = $user_river_options['default'][$column]['subject_guid'];
$options['relationship_guid'] = $user_river_options['default'][$column]['relationship_guid'];
$options['relationship'] = $user_river_options['default'][$column]['relationship'];

$options['including'] = $user_river_options['default'][$column]['including'];

if (	$options['including'] ) {
	$options['joins'] = array(',entities e',',objects_entity o');
	$wheres = array();
	$i = 0;
	foreach ( $user_river_options['default'][$column]['including'] as $include) {
		$include = sanitise_string($include);
		if ($i > 0 ) $or = '( (1 = 1) ) OR';
		$wheres[] = $or . " e.guid=o.guid AND rv.object_guid=o.guid AND o.description LIKE '%" . $include . "%'";
		$i++;
	}
	$options['wheres'] = $wheres;
}

#	global $autofeed;
#	$autofeed = true;

	$defaults = array(
		'offset'     => (int) max(get_input('offset', 0), 0),
		'limit'      => (int) max(get_input('limit', 20), 0),
		'pagination' => FALSE,
		'list_class' => 'elgg-river',
	);

	$options = array_merge($defaults, $options);

	//$options['count'] = TRUE;
	//$count = elgg_get_river($options);

	$options['count'] = FALSE;
	$items = elgg_get_river($options);
//global $fb;
//$fb->warn($items);
//print_r($options);

	//$options['count'] = $count;
	//$options['items'] = $items;
	//return elgg_view('page/components/list', $options);


//$items = $vars['items'];
//$offset = $vars['offset'];
//$limit = $vars['limit'];
//$count = $vars['count'];
//$base_url = $vars['base_url'];
//$pagination = elgg_extract('pagination', $vars, true);
//$offset_key = elgg_extract('offset_key', $vars, 'offset');
//$position = elgg_extract('position', $vars, 'after');

$list_class = 'elgg-list';
if (isset($vars['list_class'])) {
	$list_class = "{$vars['list_class']} $list_class";
}

$item_class = 'elgg-list-item';
if (isset($vars['item_class'])) {
	$item_class = "{$vars['item_class']} $item_class";
}

$html = "";
#$nav = "";

#if ($pagination && $count && false) {
#	$nav .= elgg_view('navigation/pagination', array(
#		'baseurl' => $base_url,
#		'offset' => $offset,
#		'count' => $count,
#		'limit' => $limit,
#		'offset_key' => $offset_key,
#	));
#}

if (is_array($items)) {
	//$html .= "<ul class=\"$list_class\">";
	foreach ($items as $item) {
		if (elgg_instanceof($item)) {
			$id = "elgg-{$item->getType()}-{$item->getGUID()}";
		} else {
			$id = "item-{$item->getType()}-{$item->id}";
		}
		$html .= "<li id=\"$id\" class=\"$item_class\">";
		$html .= elgg_view_list_item($item, $vars);
		$html .= '</li>';
	}
	//$html .= '</ul>';
}

#if ($position == 'before' || $position == 'both') {
#	$html = $nav . $html;
#}

#if ($position == 'after' || $position == 'both') {
#	$html .= $nav;
#}

echo $html;
