<?php
/**
 * add_deck_river_tab popup
 *
 * @package elgg-deck_river
 */

echo elgg_echo('deck_river:add_tab_title');

echo elgg_view('input/text', array(
	'name' => 'tab_name',
	'value' => '',
	'class' => 'mts'
));

echo elgg_view('input/submit', array(
		'value' => 'save',
		'name' => elgg_echo('save'),
		'class' => 'elgg-button-submit mtm mlm'
));