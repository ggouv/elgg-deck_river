<?php

$tab = sanitise_string(get_input('tab'));

// Get settings of the current user
$owner = elgg_get_logged_in_user_guid();
$user_river_options = unserialize(get_private_setting($owner, 'deck_river_settings'));

if ($tab && $user_river_options && $tab != 'default') {
	unset($user_river_options[$tab]);
	set_private_setting($owner, 'deck_river_settings', serialize($user_river_options));
} else {
	register_error('deck_river:delete:tab:error');
}
forward(REFERER);
