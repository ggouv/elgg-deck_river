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

	// make sure don't register twice this twitter account for this user.
	if (count(deck_river_twitter_get_account(elgg_get_logged_in_user_guid(), $token->user_id)) > 0) {
		echo elgg_view('page/elements/head');
		echo elgg_view('page/elements/foot');
		echo '<script type="text/javascript">$(document).ready(function() {elgg.deck_river.twitter_authorize(false);});</script>';
	} else {

		// get avatar
		$twitterObj = new EpiTwitter($twitter_consumer_key, $twitter_consumer_secret, $token->oauth_token, $token->oauth_token_secret);
		$userInfo = $twitterObj->get('/account/verify_credentials.json');

		$twitter_account = new ElggObject;
		$twitter_account->subtype = 'twitter_account';
		$twitter_account->access_id = 0;
		$twitter_account->user_id = $token->user_id;
		$twitter_account->screen_name = $token->screen_name;
		$twitter_account->oauth_token = $token->oauth_token;
		$twitter_account->oauth_token_secret = $token->oauth_token_secret;
		$twitter_account->avatar = $userInfo->response['profile_image_url_https'];

		echo elgg_view('page/elements/head');
		echo elgg_view('page/elements/foot');

		if ($twitter_account->save()) {
			// trigger authorization hook
			elgg_trigger_plugin_hook('authorize', 'elgg-deck_river', array('token' => $token));

			$account_output = elgg_view('output/accounts/twitter_account', array(
				'account' => $twitter_account,
				'pinned' => false,
			));
			echo '<script type="text/javascript">$(document).ready(function() {elgg.deck_river.twitter_authorize(' . json_encode($account_output) . ');});</script>';
		} else {
			register_error(elgg_echo('deck_river:twitter:authorize:error'));
			echo elgg_echo('deck_river:twitter:authorize:error');
		}
	}
}

/**
 * Remove twitter access for the currently logged in user.
 */
function deck_river_twitter_api_revoke($user_guid, $screen_name = null, $echo = true) {
	if ($user_guid && elgg_instanceof(get_entity($user_guid), 'user')) {

		$user_deck_river_pinned_accounts = unserialize(get_private_setting($user_guid, 'deck_river_pinned_accounts'));

		$entities = deck_river_twitter_get_account($user_guid, $screen_name);
		foreach ($entities as $entity) {
			// remove account from pinned accounts
			$arr = array_diff($user_deck_river_pinned_accounts, array($entity->getGUID()));
			set_private_setting($user_guid, 'deck_river_pinned_accounts', serialize($arr));

			// remove account
			$entity->delete();
		}

		if ($echo && $entities) system_message(elgg_echo('deck_river:twitter:revoke:success'));
		return true;
	} else {
		register_error(elgg_echo('deck_river:twitter:revoke:error'));
		return false;
	}
}



/**
 * Get twitter account for the currently logged in user.
 */
function deck_river_twitter_get_account($user_guid = null, $user_id = null) {
	if (!$user_guid) $user_guid = elgg_get_logged_in_user_guid();

	return elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'twitter_account',
		'metadata_name' => 'user_id',
		'metadata_value' => $user_id,
		'owner_guid' => $user_guid,
	));
}