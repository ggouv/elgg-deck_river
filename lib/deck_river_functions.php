<?php
function elgg_list_deck_river(array $options = array()) {
	global $autofeed;
	$autofeed = true;

	$defaults = array(
		'offset'     => (int) max(get_input('offset', 0), 0),
		'limit'      => (int) max(get_input('limit', 20), 0),
		'pagination' => FALSE,
		'list_class' => 'elgg-river',
	);

	$options = array_merge($defaults, $options);

	//$options['count'] = TRUE;
	//$count = elgg_get_river($options);

	$options['count'] = FALSE;
	$items = elgg_get_river($options);

	//$options['count'] = $count;
	$options['items'] = $items;
	return elgg_view('page/components/list', $options);
}
