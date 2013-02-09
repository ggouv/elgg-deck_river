<?php
/**
 * Common library of functions used by Twitter Services.
 *
 * @package elgg-deck_river
 */

/**
 * User-initiated Twitter authorization
 *
 * Callback action from Twitter registration. Registers a single Elgg user with
 * the authorization tokens. Will revoke access from previous users when a
 * conflict exists.
 *
 */
function deck_river_twitter_authorize() {
	$oauth_token = get_input('oauth_token', false);
	
	if (!$oauth_token) {
		register_error(elgg_echo('deck_river:twitter:authorize:error'));
		return false;
	}
	
	// get token
	elgg_load_library('deck_river:twitter_async');
	$twitter_consumer_key = elgg_get_plugin_setting('twitter_consumer_key', 'elgg-deck_river');
	$twitter_consumer_secret = elgg_get_plugin_setting('twitter_consumer_secret', 'elgg-deck_river');
	$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);  
	$twitterObj->setToken($oauth_token);
	$token = $twitterObj->getAccessToken();

	// make sure no other users are registered to this twitter account.
	$options = array(
		'type' => 'user',
		'plugin_id' => 'elgg-deck_river',
		'plugin_user_setting_name_value_pairs' => array(
			'twitter_access_key' => $token->oauth_token,
			'twitter_access_secret' => $token->oauth_token_secret,
		),
		'limit' => 0
	);
	$users = elgg_get_entities_from_plugin_user_settings($options);

	if ($users) {
		foreach ($users as $user) { // revoke access
			deck_river_twitter_api_revoke($user->getGUID());
		}
	}

	// register user's access tokens
	elgg_set_plugin_user_setting('twitter_name', $token->screen_name, null, 'elgg-deck_river');
	elgg_set_plugin_user_setting('twitter_access_key', $token->oauth_token, null, 'elgg-deck_river');
	elgg_set_plugin_user_setting('twitter_access_secret', $token->oauth_token_secret, null, 'elgg-deck_river');
	
	// save avatar
	$twitterObj = new EpiTwitter($twitter_consumer_key, $twitter_consumer_secret, $token->oauth_token, $token->oauth_token_secret);
	$userInfo = $twitterObj->get('/account/verify_credentials.json');
	elgg_set_plugin_user_setting('twitter_avatar', $userInfo->response['profile_image_url_https'], null, 'elgg-deck_river');
	
	// trigger authorization hook
	elgg_trigger_plugin_hook('authorize', 'elgg-deck_river', array('token' => $token));
	
	echo elgg_view('page/elements/head');
	echo elgg_view('page/elements/foot');
	echo '<script type="text/javascript">$(document).ready(function() {elgg.deck_river.authorize();});</script>';
}

/**
 * Remove twitter access for the currently logged in user.
 */
function deck_river_twitter_api_revoke($user_guid = null) {
	elgg_unset_plugin_user_setting('twitter_name', $user_guid, 'elgg-deck_river');
	elgg_unset_plugin_user_setting('twitter_avatar', $user_guid, 'elgg-deck_river');
	elgg_unset_plugin_user_setting('twitter_access_key', $user_guid, 'elgg-deck_river');
	elgg_unset_plugin_user_setting('twitter_access_secret', $user_guid, 'elgg-deck_river');
	
	system_message(elgg_echo('deck_river:twitter:revoke:success'));
}