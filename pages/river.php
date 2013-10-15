<?php
/**
 * Main activity stream list page
 */


// Get the settings of the current user. If not, set it to defaults.
$user_guid = elgg_get_logged_in_user_guid();
$user_river_settings = json_decode(get_private_setting($user_guid, 'deck_river_settings'));

if ( !$user_river_settings || !is_object($user_river_settings) ) {
	$set = str_replace("&gt;", ">", elgg_get_plugin_setting('default_columns', 'elgg-deck_river'));
	if (!$set) $set = elgg_echo('deck_river:settings:default_column:default');
	eval("\$defaults = $set;");
	set_private_setting($user_guid, 'deck_river_settings', json_encode($defaults));
	$user_river_settings = $defaults;
}

//get page for tabs
$page_filter = elgg_get_context();

$content = '<script type="text/javascript">var deckRiverSettings = ' . json_encode($user_river_settings) . '</script>';
$content .= "<div id=\"deck-river-lists\" data-tab=\"{$page_filter}\"><ul class=\"deck-river-lists-container hidden\">";

foreach ($user_river_settings->$page_filter as $key => $column_settings) {
	$content .= "<li class=\"column-river\" id=\"{$key}\">" .
				elgg_view('page/layouts/content/deck_river_column_header', array(
					'column_id' => $key,
					'column_settings' => $column_settings
				)) .
				'<ul class="elgg-river elgg-list">' .
					elgg_view('graphics/ajax_loader', array('hidden' => false)) .
				'</ul>' .
			'</li>';
}
$content .= '</ul></div>';

$params = array(
	'content' => $content,
	'filter_context' => $page_filter,
	'class' => 'elgg-river-layout',
	'user_river_settings' => $user_river_settings,
);

$body = elgg_view_layout('deck-river', $params);

echo elgg_view_page($title, $body);
