<?php
/**
 * Main activity stream list page
 */


// Get the settings of the current user. If not, set it to defaults.
$user_guid = elgg_get_logged_in_user_guid();
$user_river_settings = unserialize(get_private_setting($user_guid, 'deck_river_settings'));
if ( !$user_river_settings || !is_array($user_river_settings) ) {
	$set = str_replace("&gt;", ">", elgg_get_plugin_setting('default_columns', 'elgg-deck_river'));
	if (!$set) $set = elgg_echo('deck_river:settings:default_column:default');
	eval("\$defaults = $set;");
	set_private_setting($user_guid, 'deck_river_settings', serialize($defaults));
	$user_river_settings = $defaults;
}

//get page for tabs
$page_filter = elgg_get_context();

$content = "<div class=\"deck-river-lists\" id=\"{$page_filter}\"><ul class=\"deck-river-lists-container\">";

foreach ($user_river_settings[$page_filter] as $key => $tab_settings) {
	$content .= "<li class=\"column-river\" id=\"{$key}\" data-network=\"{$tab_settings['network']}\">" .
				'<ul class="column-header"><li>' .
					elgg_view('page/layouts/content/deck_river_column_header', array('tab_settings' => $tab_settings)) .
				'</li></ul>' .
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
