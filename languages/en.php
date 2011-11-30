<?php
/**
 * Deck-river English language file.
 *
 */

$english = array(
	'deck_river:edit' => 'Edit column settings',
	'deck_river:refresh' => 'Refresh column',
	'deck_river:refresh-all' => 'Refresh all columns',
	'deck_river:add-column' => 'Add a new column',
	'deck_river:add-tab' => 'Add a new tab',
	'deck_river:limitColumnReached' => 'The maximum number of columns is reached.',
	
	// add tab form
	'deck_river:add_tab_title' => 'Add a new tab:',
	
	// column-settings form
	'deck_river:settings' => 'Settings of column "%s"',
	'deck_river:type' => 'Type:',
	'deck_river:filter' => 'Filter:',
	'deck_river:title' => 'Title:',
	'deck_river:search' => 'Search:',
	
	// plugin settings
	'deck_river:settings:min_width_column' => 'Minimum width of columns',
	'deck_river:settings:max_nbr_column' => 'Maximum number of columns',
	'deck_river:settings:default_column' => 'Columns by default for new user',
	'deck_river:settings:default_column_default_params' => 'Standards columns :',
	'deck_river:settings:keys_to_merge' => 'Entities to merge on the column settings',
	'deck_river:settings:keys_to_merge_string_register_entity' => '<strong>Example:</strong> page=page_top (first element will be displayed. Comma separated)<br /><strong>Entities registered on this site :</strong>',
	'deck_river:settings:reset_user' => "Reset column's settings of a user. Enter his ID",
	'deck_river:settings:reset_user:ok' => "Column's settings of user %s reseted.",
	'deck_river:settings:reset_user:nok' => "Impossible to reset column's settings of user %s.",

);

add_translation('en', $english);
