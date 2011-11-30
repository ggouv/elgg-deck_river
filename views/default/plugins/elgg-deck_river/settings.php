<?php
/**
 * Elgg-deck_river plugin settings
 */

// set default value

if (!isset($vars['entity']->min_width_column)) {
	$vars['entity']->min_width_column = '300';
}

if (!isset($vars['entity']->max_nbr_column)) {
	$vars['entity']->max_nbr_column = '10';
}

$default = "array('default' => array(
				'column-1' => array(
					'title' => elgg_echo('river:all'),
					'type' => 'all'
				),
				'column-2' => array(
					'title' => elgg_echo('river:friends'),
					'type' => 'friends'
				),
				'column-3' => array(
					'title' => elgg_echo('river:mine'),
					'type' => 'mine'
				),
				'column-4' => array(
					'title' => '@' . get_entity(\$owner)->name,
					'type' => 'mention'
				)
			))";
if (!isset($vars['entity']->default_columns)) {
	$vars['entity']->default_columns = $default;
}



$min_width_column_string = elgg_echo('deck_river:settings:min_width_column');
$min_width_column_view = elgg_view('input/text', array(
	'name' => 'params[min_width_column]',
	'value' => $vars['entity']->min_width_column,
	'class' => 'elgg-input-thin',
));

$max_nbr_column_string = elgg_echo('deck_river:settings:max_nbr_column');
$max_nbr_column_view = elgg_view('input/text', array(
	'name' => 'params[max_nbr_column]',
	'value' => $vars['entity']->max_nbr_column,
	'class' => 'elgg-input-thin',
));

$default_columns_string = elgg_echo('deck_river:settings:default_column');
$default_columns_view = elgg_view('input/plaintext', array(
	'name' => 'params[default_columns]',
	'value' => $vars['entity']->default_columns
));
$default_columns_string_default_params = '<strong>' . elgg_echo('deck_river:settings:default_column_default_params') . '</strong><br />' . $default;

$keys_to_merge_string = elgg_echo('deck_river:settings:keys_to_merge');
$keys_to_merge_view = elgg_view('input/text', array(
	'name' => 'params[keys_to_merge]',
	'value' => $vars['entity']->keys_to_merge
));
$registered_entities = elgg_get_config('registered_entities');
foreach ($registered_entities as $type => $subtypes) {
	if (!count($subtypes)) {
		$label[] = $type;
	} else {
		foreach ($subtypes as $subtype) {
			$label[] = $subtype;
		}
	}
}
$keys_to_merge_string_register_entity = elgg_echo('deck_river:settings:keys_to_merge_string_register_entity') . '</strong><br />' . implode(' - ', $label);

echo <<<__HTML
<br />
<div><label>$min_width_column_string</label><br />$min_width_column_view</div>
<div><label>$max_nbr_column_string</label><br />$max_nbr_column_view</div>
<div><label>$default_columns_string</label><br />$default_columns_view<br /><span style='font-size:0.85em;color:#999;'>$default_columns_string_default_params</span></div>
<div><label>$keys_to_merge_string</label><br />$keys_to_merge_view<br /><span style='font-size:0.85em;color:#999;'>$keys_to_merge_string_register_entity</span></div>
__HTML;
