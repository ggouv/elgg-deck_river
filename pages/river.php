<?php
/**
 * Main activity stream list page
 */


// Get the settings of the current user. If not, set it to defaults.
$user_guid = elgg_get_logged_in_user_guid();
$user_river_options = unserialize(get_private_setting($user_guid, 'deck_river_settings'));
if ( !$user_river_options || !is_array($user_river_options) ) {
	$set = str_replace("&gt;", ">", elgg_get_plugin_setting('default_columns', 'elgg-deck_river'));
	eval("\$defaults = $set;");
	set_private_setting($user_guid, 'deck_river_settings', serialize($defaults));
	$user_river_options = $defaults;
}

//get page for tabs
$page_filter = elgg_get_context();

$activity = "<div class='deck-river-lists' id='{$page_filter}'><ul class='deck-river-lists-container'>";

foreach ($user_river_options[$page_filter] as $key => $tab_options) {
	$options['title'] = $tab_options['title'];
	$direct = $tab_options['direct'] == true ? ' data-direct="true"' : '';
	$activity .= "<li class='column-river' id='{$key}'>" .
				'<ul class="column-header"><li>' .
				'<h3 class="title"' . $direct . '>' . $tab_options['title'] . '</h3>' .
				'<h6 class="subtitle">' . $tab_options['subtitle'] . '</h6>' . elgg_view('deck_river/deck_river_column_menu', $options) .
				'</li></ul>' .
				'<ul class="elgg-river elgg-list">' .
					elgg_view('graphics/ajax_loader', array('hidden' => false)) .
				'</ul>' .
			'</li>';
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
