<?php
/**
 * JSON thewire river view
 *
 * @uses $vars['item']
 */

global $jsonexport;

$subject = $vars['item']->getSubjectEntity();
$subject_link = elgg_view('output/url', array(
	'href' => $subject->getURL(),
	'text' => $subject->name,
	'class' => 'elgg-river-subject',
	'is_trusted' => true,
));
$object_link = elgg_view('output/url', array(
	'href' => "thewire/owner/$subject->username",
	'text' => elgg_echo('thewire:wire'),
	'class' => 'elgg-river-object',
	'is_trusted' => true,
));

$vars['item']->summary = elgg_echo("river:create:object:thewire", array($subject_link, $object_link));

$object = $vars['item']->getObjectEntity();
$excerpt = strip_tags($object->description);
$excerpt = thewire_filter($excerpt);

$vars['item']->message = $excerpt;

$jsonexport['activity'][] = $vars['item'];

