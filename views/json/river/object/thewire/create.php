<?php
/**
 * JSON thewire river view
 *
 * @uses $vars['item']
 */

global $jsonexport;

$subject = $vars['item']->getSubjectEntity();
$object = $vars['item']->getObjectEntity();

$subject_link = elgg_view('output/url', array(
	'href' => $subject->getURL(),
	'text' => $subject->name,
	'class' => 'elgg-river-subject',
	'is_trusted' => true,
));
$object_link = elgg_view('output/url', array(
	'href' => $object->getURL(),
	'text' => elgg_echo('thewire:wire'),
	'class' => 'elgg-river-object',
	'is_trusted' => true,
));

$vars['item']->summary = elgg_echo("river:create:object:thewire", array($subject_link, $object_link));


$excerpt = strip_tags($object->description);
$excerpt = deck_river_wire_filter($excerpt);

if ($object->reply) $vars['item']->responses = $object->wire_thread;

$vars['item']->message = $excerpt;

$jsonexport['results'][] = $vars['item'];

