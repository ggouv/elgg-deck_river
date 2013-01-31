<?php
/**
 * View conversation thread
 */

$wire_guid = get_input('guid');
$wire = get_entity($wire_guid);

if (!$wire) {
	register_error(elgg_echo('noaccess'));
	forward(REFERER);
}

$owner = $wire->getOwnerEntity();
if (!$owner) {
	forward(REFERER);
}

$title = elgg_echo('thewire:by', array($owner->name));

elgg_push_breadcrumb(elgg_echo('thewire'), 'thewire/all');
elgg_push_breadcrumb($title);

$content = '<ul class="elgg-river elgg-list single-view">' . elgg_view('graphics/ajax_loader', array('hidden' => false)) . '</ul>';

elgg_load_library('deck_river:river_loader');
$thread = htmlspecialchars(load_wire_discussion($wire->wire_thread));
$river = elgg_get_river(array('object_guid' => $wire_guid));
$river_id = $river[0]->id;
$content .= "<input id=\"json-river-thread\" class=\"hidden\" value=\"$thread\" data-message-id=\"$river_id\">";

$body = elgg_view_layout('content', array(
	'filter' => false,
	'content' => $content,
	'title' => $title,
));

echo elgg_view_page($title, $body);
