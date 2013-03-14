<?php
/**
 * Main content filter
 *
 * Select between user, friends, and all content
 *
 * @uses $vars['filter_context']      Filter context: all, friends, mine
 * @uses $vars['filter_override']     HTML for overriding the default filter (override)
 * @uses $vars['context']             Page context (override)
 * @uses $vars['user_river_settings'] Settings of the user
 */

if (isset($vars['filter_override'])) {
	echo $vars['filter_override'];
	return true;
}

if (elgg_is_logged_in()) {
	$filter_context = elgg_extract('filter_context', $vars, 'default');

	// generate a list of default tabs
	$tabs = array();

	$tabs['refresh-all'] = array(
		'text' => elgg_view_icon('refresh'),
		'href' => '#',
		'class' => "elgg-refresh-all-button tooltip sw",
		'selected' => 1,
		'priority' => 100,
		'title' => elgg_echo('deck_river:refresh-all')
	);

	$tabs['plus-column'] = array(
		'text' => '+',
		'href' => '#',
		'class' => "elgg-add-new-column tooltip sw",
		'selected' => 1,
		'priority' => 110,
		'title' => elgg_echo('deck_river:add-column')
	);

	$priority = 12;
	foreach ($vars['user_river_settings'] as $name => $tab) {
		$tabs[$name] = array(
			'text' => ucfirst($name),
			'selected' => ($filter_context == $name),
			'priority' => $priority * 10
		);
		if ($name == 'default') {
			$tabs[$name]['href'] = 'activity';
		} else {
			$tabs[$name]['class'] = 'column-deletable';
			if ($filter_context != $name) {
				$tabs[$name]['text'] = $name . '<a class="delete-tab" href="#">' . elgg_view_icon('deck-river-delete') . '</a>';
				$tabs[$name]['href'] = "activity/$name";
			} else {
				$tabs[$name]['href'] = '#rename-deck-river-tab';
				$tabs[$name]['rel'] = 'popup';
			}
		}
		$priority++;
	}

	$tabs['plus'] = array(
		'text' => '+',
		'href' => '#add-deck-river-tab',
		'class' => 'tooltip s',
		'rel' => 'popup',
		'selected' => 0,
		'priority' => $priority * 10,
		'title' => elgg_echo('deck_river:add-tab')
	);

	$tabs['arrow-left'] = array(
		'text' => elgg_view_icon('arrow-left', 'link hidden') . '<div class="count mlm"></div>',
		'href' => '#',
		'class' => 'deck-river-scroll-arrow left',
		'selected' => 0,
		'priority' =>($priority+1) * 10,
	);

	echo "<div id='add-deck-river-tab' class='elgg-module-popup hidden add-deck-river-tab-popup'>" .
			elgg_view_form('deck_river/tab/add') .
		"</div>";
	echo "<div id='rename-deck-river-tab' class='elgg-module-popup hidden rename-deck-river-tab-popup'>" .
			elgg_view_form('deck_river/tab/rename', '', $vars) .
		"</div>";

	foreach ($tabs as $name => $tab) {
		$tab['name'] = $name;

		elgg_register_menu_item('filter', $tab);
	}

	echo elgg_view_menu('filter', array('sort_by' => 'priority', 'class' => 'elgg-menu-deck-river'));

	echo '<div class="deck-river-scroll-arrow right"><div class="count"></div>' . elgg_view_icon('arrow-right', 'link') . '</div>';
}
