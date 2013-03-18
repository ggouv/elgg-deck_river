<?php
/**
 * Json river menu
 * Elgg-deck_river
 *
 * @uses $vars['menu']                 Array of menu items
 * @uses $vars['class']                Additional CSS class for the menu
 * @uses $vars['item_class']           Additional CSS class for each menu item
 */

//global $fb; $fb->info($vars['menu']);

foreach ($vars['menu'] as $section => $menu_items) {
	foreach ($menu_items as $key => $item) {
		//global $fb; $fb->info($item->getData());
		$item_class = $item->getItemClass();
		if ($item->getSelected()) {
			$item_class = "$item_class elgg-state-selected";
		}

		$return[$section][$key]['name'] = $item->getName();
		$return[$section][$key]['content'] = $item->getContent();
		if ($item_class) $return[$section][$key]['class'] = $item_class;

	}

	/*echo elgg_view('navigation/menu/elements/section', array(
		'items' => $menu_items,
		'class' => "$class elgg-menu-river-$section",
		'section' => $section,
		'name' => 'river',
		'item_class' => $item_class,
	));*/
}

echo $return;

/*
$class = elgg_extract('class', $vars, '');
$item_class = elgg_extract('item_class', $vars, '');

echo "<ul class=\"$class\">";
foreach ($vars['items'] as $menu_item) {
	echo elgg_view('navigation/menu/elements/item', array(
		'item' => $menu_item,
		'item_class' => $item_class,
	));
}
echo '</ul>';



$item = $vars['item'];

$link_class = 'elgg-menu-closed';
if ($item->getSelected()) {
	// @todo switch to addItemClass when that is implemented
	//$item->setItemClass('elgg-state-selected');
	$link_class = 'elgg-menu-opened';
}

$children = $item->getChildren();
if ($children) {
	$item->addLinkClass($link_class);
	$item->addLinkClass('elgg-menu-parent');
}

$item_class = $item->getItemClass();
if ($item->getSelected()) {
	$item_class = "$item_class elgg-state-selected";
}
if (isset($vars['item_class']) && $vars['item_class']) {
	$item_class .= ' ' . $vars['item_class'];
}

echo "<li class=\"$item_class\">";
echo $item->getContent();
if ($children) {
	echo elgg_view('navigation/menu/elements/section', array(
		'items' => $children,
		'class' => 'elgg-menu elgg-child-menu',
	));
}
echo '</li>';*/