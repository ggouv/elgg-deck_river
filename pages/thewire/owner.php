<?php
/**
 * User's wire posts
 * 
 */

$owner = elgg_get_page_owner_entity();
if (!$owner) {
	forward('thewire/all');
}

$title = elgg_echo('thewire:user', array($owner->name));

elgg_push_breadcrumb(elgg_echo('thewire'), "thewire/all");
elgg_push_breadcrumb($owner->name);

$context = '';
if (elgg_get_logged_in_user_guid() == $owner->guid) {
	$context = 'mine';
}

$content = '<div id="column"><ul class="elgg-river elgg-list">' . elgg_view('graphics/ajax_loader', array('hidden' => false)) . '</ul></div>';
$content .= "<input id=\"json-river-owner\" class=\"hidden\" value=\"{$owner->guid}\">";

$body = elgg_view_layout('content', array(
	'filter_context' => $context,
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('thewire/sidebar'),
));

echo elgg_view_page($title, $body);
