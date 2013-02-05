<?php
/**
 * User's wire posts
 * 
 */

$owner = elgg_get_page_owner_entity();
if (!$owner) {
	forward(REFERER);
}

elgg_push_breadcrumb($owner->name, 'profile/' . $owner->name);
elgg_push_breadcrumb(elgg_echo('thewire:breadcrumb:user'));

$title = elgg_echo('thewire:user', array($owner->name));


$content = '<div id="column"><ul class="elgg-river elgg-list">' . elgg_view('graphics/ajax_loader', array('hidden' => false)) . '</ul></div>';
$content .= "<input id=\"json-river-owner\" class=\"hidden\" value=\"{$owner->guid}\">";

$body = elgg_view_layout('content', array(
	'filter_override' => '',
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('thewire/sidebar'),
));

echo elgg_view_page($title, $body);
