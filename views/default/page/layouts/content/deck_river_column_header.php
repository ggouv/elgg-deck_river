<?php
/**
 * @uses $vars['column_settings'] Settings of this tab
 **/

$column_settings = elgg_extract('column_settings', $vars);
$column_id = elgg_extract('column_id', $vars);
$has_filter = elgg_extract('has_filter', $vars, false);

if (!$column_settings['network']) $column_settings['network'] = 'elgg';

// check if this column can filter content
if ((!$column_settings['network'] || $column_settings['network'] == 'elgg')
	&& in_array($column_settings['type'], array('all', 'friends', 'mine', 'mention', 'group', 'group_mention', 'search'))) {
		$has_filter = true;
	} else {
		$has_filter = false;
	}

// set filter
if ($has_filter) {
	$filter = elgg_view('page/layouts/content/deck_river_column_filter', array(
		'column_settings' => $column_settings
	));
} else {
	$filter = '';
}


$params = array(
	'text' => elgg_view_icon('refresh'),
	'title' => elgg_echo('deck_river:refresh'),
	'href' => "#",
	'class' => "elgg-column-refresh-button tooltip s prs",
);
$buttons = elgg_view('output/url', $params);

$buttons .= elgg_view('output/img', array(
	'src' => elgg_get_site_url() . 'mod/elgg-deck_river/graphics/refresh.gif',
	'class' => 'refresh-gif'
));

$params = array(
	'text' => elgg_view_icon('settings-alt'),
	'title' => elgg_echo('deck_river:edit'),
	'href' => "#",
	'class' => "elgg-column-edit-button tooltip s",
);
$buttons .= elgg_view('output/url', $params);

if ($has_filter) {
	$params = array(
		'text' => elgg_view_icon('search'),
		'title' => elgg_echo('deck_river:filter'),
		'href' => "#",
		'class' => "elgg-column-filter-button tooltip s",
	);
	$buttons .= elgg_view('output/url', $params);
}


$title = elgg_echo($column_settings['title']);
$subtitle = is_array($column_settings['subtitle']) ? elgg_echo($column_settings['subtitle'][0], array($column_settings['subtitle'][1])) : elgg_echo($column_settings['subtitle'], array());

if (isset($column_settings['types_filter']) || isset($column_settings['subtypes_filter'])) {
	$hidden = '';
} else {
	$hidden = 'hidden';
}
$subtitle .= '<span class="filtered pls mls link '.$hidden.'">' . elgg_echo('river:filtred'). '</span>';

echo <<<HTML
<div class="message-box"><div class="column-messages"></div></div>
<ul class="column-header gwfb {$column_settings['network']}">
	<li>
		$buttons
		<div class="count hidden"></div>
		<div class="column-handle">
			<h3 class="title">$title</h3><br/>
			<h6 class="subtitle">$subtitle</h6>
		</div>
	</li>
</ul>
$filter
HTML;
