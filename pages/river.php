<?php
/**
 * Main activity stream list page
 */

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
	),
	'column-2' => array(
		'title' => elgg_echo('river:friends'),
		'relationship_guid' => $owner,
		'relationship' => 'friend',
	),
	'column-3' => array(
		'title' => elgg_echo('river:mine'),
		'subject_guid' => $owner,
	),
	'column-4' => array(
		'title' => '@' . get_entity($owner)->name,
		'including' => array('@' . get_entity($owner)->name),
	),
	'column-5' => array(
		'title' => '#test + !test',
		'including' => array('#test', '!test'),
	)
));

$user_river_options = unserialize(get_private_setting($owner, 'deck_river_settings'));
if ( !$user_river_options || !is_array($user_river_options) ) set_private_setting($owner, 'deck_river_settings', serialize($defaults));
$user_river_options = array_merge($defaults, (array)$user_river_options);

// @todo : get page to make many tabs
//$fb->info($page_type,'page_type');
$page_filter = 'default';

$column_number = 1;
$activity = "<div class='deck-river-lists' rel='{$page_filter}'><ul class='deck-river-lists-container'>";
foreach ($user_river_options[$page_filter] as $tab_options) {
	$options['title'] = $tab_options['title'];

	$activity .= "<li class='column-river' rel='column-{$column_number}'>" .
				elgg_view('river/elements/deck_river_column_header', $options) .
				'<ul class="elgg-river elgg-list">' .
					elgg_view('graphics/ajax_loader', array('hidden' => false)) .
				'</ul>' .
			'</li>';

	$column_number++;
}
$activity .= '</ul></div>';

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
