<?php
/**
 * JSON comment river view
 *
 * @uses $vars['item']
 */

global $jsonexport;

$comment = $vars['item']->getAnnotation();

$vars['item']->summary = elgg_view('river/elements/summary', array(
																'item' => $vars['item'],
																'hash' => '#item-annotation-' . $comment->id
								), FALSE, FALSE, 'default');
$vars['item']->message = deck_river_wire_filter(elgg_get_excerpt($comment->value), 140);

$jsonexport['activity'][] = $vars['item'];