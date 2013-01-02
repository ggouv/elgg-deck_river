<?php
/**
 * Deck-river English language file.
 *
 */

$english = array(
	'deck_river:activity:none' => "There is no activity to display.",
	'deck_river:edit' => 'Edit column settings',
	'deck_river:refresh' => 'Refresh column',
	'deck_river:refresh-all' => 'Refresh all columns',
	'deck_river:add-column' => 'Add a new column',
	'deck_river:add-tab' => 'Add a new tab',
	'deck_river:limitColumnReached' => 'The maximum number of columns is reached.',
	'river:mentions' => "Mentions",
	'deck_river:more' => "More...",
	'deck-river:reduce_url' => "Reduce",
	'deck-river:copy_url' => "Insert",
	'deck-river:clean_url' => "Effacer",
	'responseToHelper:text' => "Reply to %s : <span>%s</span>",
	'responseToHelper:delete' => "Stop reply to %s",
	
	// river menu
	'replyall' => "Répondre à tous",
	'river:timeline' => "Time line",
	'river:timeline:definition' => "Following activity",
	'river:group' => "Group",
	'river:filtred' => "filtred",
	'retweet' => "Retweet",
	'retweet:one' => "%s retweet",
	'retweet:twoandmore' => "%s retweets",
	'deck_river:show_discussion' => "Show discussion",
	'deck_river:toggle_discussion' => "Toggle discussion",
	
	// add tab form
	'deck_river:add_tab_title' => 'Add a new tab:',
	'deck_river:add:tab:error' => 'Cannot add a new tab.',
	'deck_river:rename_tab_title' => 'Rename tab:',

	// delete
	'deck_river:delete:tab:confirm' => "Are you sure to delete tab '%s'?",
	'deck_river:delete:tab:error' => "Cannot delete tab.",
	'deck-river:delete:column:confirm' => "Are you sure to delete this column?",
	
	// column-settings form
	'deck_river:settings' => 'Settings of column "%s"',
	'deck_river:type' => 'Type:',
	'deck_river:filter' => 'Filter:',
	'deck_river:title' => 'Title:',
	'deck_river:search' => 'Search:',
	
	// user info popup
	'deck_river:user-not-exist' => "This user doesn't exits.",
	'deck_river:user-info-header' => "Informations on this person",
	'deck_river:hashtag-info-header' => "Search: %s",
	
	// plugin settings
	'deck_river:settings:min_width_column' => 'Minimum width of columns',
	'deck_river:settings:max_nbr_column' => 'Maximum number of columns',
	'deck_river:settings:default_column' => 'Columns by default for new user',
	'deck_river:settings:default_column_default_params' => 'Standards columns :',
	'deck_river:settings:column_type' => "Type of columns",
	'deck_river:settings:keys_to_merge' => 'Entities to merge on the column settings',
	'deck_river:settings:keys_to_merge_string_register_entity' => '<strong>Example:</strong> page=page_top (first element will be displayed. Comma separated)<br /><strong>Entities registered on this site :</strong>',
	'deck_river:settings:reset_user' => "Reset column's settings of a user. Enter his ID",
	'deck_river:settings:reset_user:ok' => "Column's settings of user %s reseted.",
	'deck_river:settings:reset_user:nok' => "Impossible to reset column's settings of user %s.",
	'deck_river:settings:twitter_consumer_key' => "Consumer key :",
	'deck_river:settings:twitter_consumer_secret' => "Consumer secret :",
	'deck_river:settings:default_column:default' => "array(
	'default' => array(
		'column-1' => array(
			'title' => elgg_echo('river:all'),
			'subtitle' => '',
			'type' => 'all'
		),
		'column-2' => array(
			'title' => elgg_echo('river:timeline'),
			'subtitle' => elgg_echo('river:timeline:definition'),
			'type' => 'friends'
		),
		'column-3' => array(
			'title' => elgg_echo('river:mine'),
			'subtitle' => get_entity(\$user_guid)->name,
			'type' => 'mine'
		),
		'column-4' => array(
			'title' => '@' . get_entity(\$user_guid)->name,
			'subtitle' => elgg_echo('river:mentions'),
			'type' => 'mention'
		)
	)
)",
	'deck_river:settings:column_type:default' => "array('all' => elgg_echo('river:all'),
	'friends' => elgg_echo('river:friends'),
	'mine' => elgg_echo('river:mine'),
	'mention' => elgg_echo('river:mentions') . ' @' . get_entity(\$user_guid)->name,
	'group' => elgg_echo('group'),
	'search' => elgg_echo('search'),
)",
	
	// messages
	'deck_river:url-not-exist' => "There is no url to reduce.",
	'deck_river:url-bad-format' => "Url format is not good.",

);

add_translation('en', $english);
