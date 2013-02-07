<?php

$token = twitter_api_get_access_token();
if (!isset($token['oauth_token']) || !isset($token['oauth_token_secret'])) {
	register_error(elgg_echo('twitter_api:authorize:error'));
	forward('settings/plugins', 'twitter_api');
}

// make sure no other users are registered to this twitter account.
$options = array(
	'type' => 'user',
	'plugin_id' => 'elgg-deck_river',
	'plugin_user_setting_name_value_pairs' => array(
		'twitter_access_key' => $token['oauth_token'],
		'twitter_access_secret' => $token['oauth_token_secret'],
	),
	'limit' => 0
);
$users = elgg_get_entities_from_plugin_user_settings($options);

if ($users) {
	foreach ($users as $user) {
		// revoke access
		elgg_unset_plugin_user_setting('twitter_name', $user->getGUID(), 'elgg-deck_river');
		elgg_unset_plugin_user_setting('twitter_access_key', $user->getGUID(), 'elgg-deck_river');
		elgg_unset_plugin_user_setting('twitter_access_secret', $user->getGUID(), 'elgg-deck_river');
	}
}

// register user's access tokens
elgg_set_plugin_user_setting('twitter_name', $token['screen_name'], null, 'elgg-deck_river');
elgg_set_plugin_user_setting('twitter_access_key', $token['oauth_token'], null, 'elgg-deck_river');
elgg_set_plugin_user_setting('twitter_access_secret', $token['oauth_token_secret'], null, 'elgg-deck_river');

// trigger authorization hook
elgg_trigger_plugin_hook('authorize', 'elgg-deck_river', array('token' => $token));

system_message(elgg_echo('twitter_api:authorize:success'));

