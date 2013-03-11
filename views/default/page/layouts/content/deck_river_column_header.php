<?php
/**
 * @uses $vars['column_settings'] Settings of this tab
 **/

$column_settings = elgg_extract('column_settings', $vars);

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

$title = elgg_echo($column_settings['title']);
$subtitle = elgg_echo($column_settings['subtitle']);

echo <<<HTML
<ul class="column-header" data-network="{$column_settings['network']}" data-direct="{$column_settings['direct']}" data-view_type="column_river">
	<li>
		<h3 class="title">$title</h3>
		<h6 class="subtitle">$subtitle</h6>
		$buttons
	</li>
</ul>
HTML;
