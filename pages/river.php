<?php
/**
 * Main activity stream list page
 */


// Get the settings of the current user. If not, set it to defaults.
$user_guid = elgg_get_logged_in_user_guid();
$user_river_settings = json_decode(get_private_setting($user_guid, 'deck_river_settings'), true);

//get page for tabs
$page_filter = elgg_get_context();

$content = "<div id=\"deck-river-lists\" data-tab=\"{$page_filter}\"><ul class=\"deck-river-lists-container hidden\">";

$loader = elgg_view('graphics/ajax_loader', array('hidden' => false));
foreach ($user_river_settings[$page_filter] as $key => $column_settings) {
	// check if this column can filter content
	if ((!$column_settings['network'] || $column_settings['network'] == 'elgg')
		&& in_array($column_settings['type'], array('all', 'friends', 'mine', 'mention', 'group', 'group_mention', 'search'))) {
			$has_filter = true;
		} else {
			$has_filter = false;
		}

	// set header
	$header = elgg_view('page/layouts/content/deck_river_column_header', array(
			'column_id' => $key,
			'column_settings' => $column_settings,
			'has_filter' => $has_filter
	));

	// set filter
	if ($has_filter) {
		$filter = elgg_view('page/layouts/content/deck_river_column_filter', array(
			'column_settings' => $column_settings
		));
	} else {
		$filter = '';
	}

	$content .= <<<HTML
<li class="column-river" id="$key">
	$header
	$filter
	<ul class="elgg-river elgg-list">
		$loader
	</ul>
	<div class="river-to-top hidden link t25 gwfb pas"></div>
</li>
HTML;
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
