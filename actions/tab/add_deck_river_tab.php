<?php

$tab = sanitise_string(get_input('tab_name'));

// Get the settings of the current user
$owner = elgg_get_logged_in_user_guid();
$user_river_options = unserialize(get_private_setting($owner, 'deck_river_settings'));

if (!array_key_exists($tab, $user_river_options)) {
	//array_push($user_river_options[$tab], array());
	$user_river_options[$tab] = array('column-1' => array(
					'title' => elgg_echo('river:friends'),
					'type' => 'friends'
				));
	set_private_setting($owner, 'deck_river_settings', serialize($user_river_options));
	forward(elgg_get_site_url() . 'activity/' . $tab);
} else {
	register_error('deck_river:add:tab:error');
	forward(REFERER);
}
