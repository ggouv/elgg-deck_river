<?php
/**
 * Main activity stream list page
 */

// Get the settings of the current user. If not, set it to defaults.
$owner = elgg_get_logged_in_user_guid();
$defaults = array('default' => array(
	'column-1' => array(
		'title' => elgg_echo('river:all'),
		'type' => 'all'
	),
	'column-2' => array(
		'title' => elgg_echo('river:friends'),
		'type' => 'friends'
	),
	'column-3' => array(
		'title' => elgg_echo('river:mine'),
		'type' => 'mine'
	),
	'column-4' => array(
		'title' => '@' . get_entity($owner)->name,
		'type' => 'mention'
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
foreach ($user_river_options[$page_filter] as $key => $tab_options) {
	$options['title'] = $tab_options['title'];

	$activity .= "<li class='column-river' rel='{$key}'>" .
				elgg_view('river/elements/deck_river_column_header', $options) .
				'<ul class="elgg-river elgg-list">' .
					elgg_view('graphics/ajax_loader', array('hidden' => false)) .
				'</ul>' .
			'</li>';

	$column_number++;
}
$activity .= '</ul></div>';

$params = array(
	'content' =>  $content . $activity,
	'buttons' => '',
	'filter_context' => $page_filter,
	'class' => 'elgg-river-layout',
	'user_river_options' => $user_river_options,
);

$body = elgg_view_layout('deck-river', $params);

echo elgg_view_page($title, $body);
