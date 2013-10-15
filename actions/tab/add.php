<?php

$tab = sanitise_string(strtolower(get_input('tab_name')));

// Get the settings of the current user
$owner = elgg_get_logged_in_user_guid();
$user_river_options = json_decode(get_private_setting($owner, 'deck_river_settings'), true);

if (!$user_river_options[$tab]) {
	$user_river_options[$tab] = array();
	set_private_setting($owner, 'deck_river_settings', json_encode($user_river_options));
	forward(elgg_get_site_url() . 'activity/' . $tab);
} else {
	register_error('deck_river:add:tab:error');
	forward(REFERER);
}
