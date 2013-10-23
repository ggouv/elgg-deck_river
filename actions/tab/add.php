<?php

$tab = sanitise_string(strtolower(get_input('tab_name')));

// Get the settings of the current user
$owner = elgg_get_logged_in_user_guid();
$user_river_options = json_decode(get_private_setting($owner, 'deck_river_settings'), true);

if (!$user_river_options[$tab]) {
	$user_river_options[$tab] = array();
	$json_user_river_options = json_encode($user_river_options);

	set_private_setting($owner, 'deck_river_settings', $json_user_river_options);
	echo $json_user_river_options;

	if (function_exists('ggouv_execute_js')) {
		$script = <<<TEXT
$('body').click();
TEXT;
		ggouv_execute_js($script);
	}

	forward(elgg_get_site_url() . 'activity/' . $tab);
} else {
	register_error('deck_river:add:tab:error');
	forward(REFERER);
}
