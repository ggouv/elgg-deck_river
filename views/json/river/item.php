<?php
/**
 * JSON river item view
 *
 * @uses $vars['item']
 */

global $jsonexport;

if (elgg_view_exists($vars['item']->view, 'default')) {
	$vars['item']->summary = elgg_view('river/elements/summary', array('item' => $vars['item']), FALSE, FALSE, 'default');
	$object = $vars['item']->getObjectEntity();
	$vars['item']->message = elgg_get_excerpt($object->description, '140');
}

$jsonexport['activity'][] = $vars['item'];
