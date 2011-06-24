<?php
/**
 * Main activity stream list page
 */

// $page_type comes from the page handler function
global $fb;

// type filter
/*
$type = get_input('type', 'all');

$subtype = get_input('subtype', '');
if ($subtype) {
	$selector = "type=$type&subtype=$subtype";
} else {
	$selector = "type=$type";
}

if ($type != 'all') {
	$options['type'] = $type;
	if ($subtype) {
		$options['subtype'] = $subtype;
	}
}
global $fb; $fb->info($options['type'],'type'); $fb->info($options['subtype'],'subtype');
//$options['type'] = 'object';
//$options['type_subtype_pairs'] = array('object'=> array('page','page_top','file','thewire','bookmarks'));
//$options = array('type_subtype_pairs'=>array('object'=> array('page','thewire','bookmarks')));
*/
//$options['type'] = 'all';
//$selector = "type='all'";
//$options['type'] = 'object';


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
		'title' => '#nrst + !test + #rnst',
		'page_filter' => 'mention',
		'including' => array('#nrst', '!test', '#rnst'),
	),
));

//set_private_setting($owner, 'deck_river_settings', 'a:1:{s:7:"default";a:5:{s:8:"column-1";a:2:{s:5:"title";s:17:"All Site Activity";s:11:"page_filter";s:3:"all";}s:8:"column-2";a:4:{s:5:"title";s:15:"Friends Activty";s:11:"page_filter";s:7:"friends";s:17:"relationship_guid";s:2:"34";s:12:"relationship";s:6:"friend";}s:8:"column-3";a:3:{s:5:"title";s:11:"My Activity";s:11:"page_filter";s:4:"mine";s:12:"subject_guid";s:2:"34";}s:8:"column-4";a:3:{s:5:"title";s:5:"@manu";s:11:"page_filter";s:7:"mention";s:9:"including";a:1:{i:0;s:5:"@manu";}}s:8:"column-5";a:3:{s:5:"title";s:21:"#nrst + !test + #rnst";s:11:"page_filter";s:7:"mention";s:9:"including";a:1:{i:0;s:5:"#nrst";}}}}');
//$fb->info(get_private_setting($owner, 'deck_river_settings'),'deck_river_settings1');
$user_river_options = $defaults; //unserialize(get_private_setting($owner, 'deck_river_settings'));
if ( !$user_river_options || !is_array($user_river_options) ) set_private_setting($owner, 'deck_river_settings', serialize($defaults));
$user_river_options = array_merge($defaults, (array)$user_river_options);
//$fb->info($user_river_options,'$user_river_options');




//$fb->info($page_type,'page_type');
$page_filter = 'default';

$activity = '<ul class="deck-river-lists">'; $options = array();
//$options['type_subtype_pairs'] = array('object'=> array('thewire','bookmarks'));
foreach ($user_river_options['default'] as $tab_options) {
	$options['title'] = $tab_options['title'];
	$options['subject_guid'] = $tab_options['subject_guid'];
	$options['relationship_guid'] = $tab_options['relationship_guid'];
	$options['relationship'] = $tab_options['relationship'];
	$options['including'] = $tab_options['including'];
	$options['pagination'] = FALSE;

	$activity .= '<li class="column-river">' . elgg_view('river/elements/deck_river_column_header', $options) . elgg_list_river_including($options) . '</li>';
}
$activity .= '</ul>';
/*
switch ($page_type) {
	case 'mine':
		$title = elgg_echo('river:mine');
		$page_filter = 'mine';
		$options['subject_guid'] = elgg_get_logged_in_user_guid();
		break;
	case 'friends':
		$title = elgg_echo('river:friends');
		$page_filter = 'friends';
		$options['relationship_guid'] = elgg_get_logged_in_user_guid();
		$options['relationship'] = 'friend';
		break;
	default:
		$title = elgg_echo('river:all');
		$page_filter = 'all';
		break;
}

$options['pagination'] = FALSE;
$activity = '<li class="column-river">' . elgg_list_river($options) . '</li>';
$activity .= '<li class="column-river">' . elgg_list_river($options) . '</li>';
$activity .= '<li class="column-river">' . elgg_list_river($options) . '</li>';
*/
//$content = elgg_view('core/river/filter', array('selector' => $selector));

$sidebar = elgg_view('core/river/sidebar');

$params = array(
	'content' =>  $content . $activity,
	'sidebar' => $sidebar,
	'buttons' => '',
	'filter_context' => $page_filter,
	'class' => 'elgg-river-layout',
	'user_river_options' => $user_river_options,
);

//$body = 'aaa'.elgg_view_layout('one_column', $params);
$body = elgg_view_layout('deck-river', $params);

echo elgg_view_page($title, $body);
