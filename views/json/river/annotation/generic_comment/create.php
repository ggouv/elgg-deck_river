<?php
/**
 * JSON comment river view
 *
 * @uses $vars['item']
 */

global $jsonexport;

$comment = $vars['item']->getAnnotation();

switch ($vars['item']->subtype) {
	case 'markdown_wiki':
		$vars['item']->summary = elgg_view('river/elements/markdown_wiki_comment_summary', array(
				'item' => $vars['item'],
				'hash' => '#item-annotation-' . $comment->id
		), FALSE, FALSE, 'default');
		break;
	case 'workflow_card':
		$vars['item']->summary = elgg_view('river/elements/workflow_card_comment_summary', array(
				'item' => $vars['item'],
				'hash' => '#item-annotation-' . $comment->id
		), FALSE, FALSE, 'default');
		break;
	default:
		$vars['item']->summary = elgg_view('river/elements/summary', array(
				'item' => $vars['item'],
				'hash' => '#item-annotation-' . $comment->id
		), FALSE, FALSE, 'default');
		break;
}


$vars['item']->message = deck_river_wire_filter(elgg_get_excerpt($comment->value, 140));

$jsonexport['results'][] = $vars['item'];