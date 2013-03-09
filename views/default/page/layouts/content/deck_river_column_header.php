<?php
/**
 * @uses $vars['tab_settings'] Settings of this tab
 **/

$tab_settings = elgg_extract('tab_settings', $vars, 'default');

$params = array(
	'text' => elgg_view_icon('refresh'),
	'title' => elgg_echo('deck_river:refresh'),
	'href' => "#",
	'class' => "elgg-column-refresh-button tooltip s",
);
$buttons = elgg_view('output/url', $params);

$params = array(
	'text' => elgg_view_icon('settings-alt'),
	'title' => elgg_echo('deck_river:edit'),
	'href' => "#",
	'class' => "elgg-column-edit-button tooltip s",
);
$buttons .= elgg_view('output/url', $params);

echo <<<HTML
<h3 class="title">{$tab_settings['title']}</h3>
<h6 class="subtitle">{$tab_settings['subtitle']}</h6>
$buttons
HTML;
