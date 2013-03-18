<?php
/**
 * @uses $vars['column_settings'] Settings of this tab
 **/

$column_settings = elgg_extract('column_settings', $vars);

$params = array(
	'text' => elgg_view_icon('settings-alt'),
	'title' => elgg_echo('deck_river:edit'),
	'href' => "#",
	'class' => "elgg-column-edit-button tooltip s",
);
$buttons = elgg_view('output/url', $params);

$params = array(
	'text' => elgg_view_icon('refresh'),
	'title' => elgg_echo('deck_river:refresh'),
	'href' => "#",
	'class' => "elgg-column-refresh-button tooltip s",
);
$buttons .= elgg_view('output/url', $params);
$buttons .= elgg_view('output/img', array(
	'src' => elgg_get_site_url() . 'mod/elgg-deck_river/graphics/refresh.gif',
	'class' => 'refresh-gif'
));

$title = elgg_echo($column_settings['title']);
$subtitle = elgg_echo($column_settings['subtitle']);

if (isset($column_settings['types_filter']) || isset($column_settings['subtypes_filter'])) {
	$subtitle .= ' | ' . elgg_echo('river:filtred');
}

echo <<<HTML
<div class="message-box"><div class="column-messages"></div></div>
<ul class="column-header gwfb" data-network="{$column_settings['network']}" data-direct="{$column_settings['direct']}" data-river_type="column_river">
	<li>
		$buttons
		<div class="count hidden"></div>
		<h3 class="title">$title</h3><br/>
		<h6 class="subtitle">$subtitle</h6>
	</li>
</ul>
HTML;
