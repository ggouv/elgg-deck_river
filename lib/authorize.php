<?php
/**
 * Common library of functions used by Twitter Services.
 *
 * @package elgg-deck_river
 */



/**
 * Get networks account for the currently logged in user.
 */
function deck_river_get_networks_account($network, $user_guid = null, $user_id = null) {
	if (!$network) return false;
	if (!$user_guid) $user_guid = elgg_get_logged_in_user_guid();

	$params = array(
		'type' => 'object',
		'subtype' => $network . '_account',
		'owner_guid' => $user_guid,
		'limit' => 0
	);

	if ($network == 'all') $params['subtype'] = array('twitter_account', 'facebook_account', 'fb_group_account');
	if ($network == 'facebook') $params['subtype'] = array('facebook_account', 'fb_group_account');

	if ($user_id) {
		$meta_name = ($network == 'fb_group') ? 'group_id' : 'user_id';

		$params = array_merge($params, array(
			'metadata_name' => $meta_name,
			'metadata_value' => $user_id,
		));
	}

	return elgg_get_entities_from_metadata($params);
}



/**
 * count networks account for the currently logged in user.
 */
function deck_river_count_networks_account($network, $user_guid = null, $user_id = null) {
	if (!$network) return false;
	if (!$user_guid) $user_guid = elgg_get_logged_in_user_guid();

	$params = array(
		'type' => 'object',
		'subtype' => $network . '_account',
		'owner_guid' => $user_guid,
		'count' => true
	);

	if ($network == 'all') $params['subtype'] = array('twitter_account', 'facebook_account', 'fb_group_account');

	if ($user_id) {
		$params = array_merge($params, array(
			'metadata_name' => 'user_id',
			'metadata_value' => $user_id
		));
	}

	return elgg_get_entities_from_metadata($params);
}



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
		register_error(elgg_echo('deck_river:network:authorize:error'));
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
	if (deck_river_get_networks_account('twitter', elgg_get_logged_in_user_guid(), $token->user_id)) {
		echo elgg_view('page/elements/head');
		echo elgg_view('page/elements/foot');
		echo '<script type="text/javascript">$(document).ready(function() {elgg.deck_river.network_authorize(false);});</script>';
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

			$account_output = array(
				'network' => 'twitter',
				'network_box' => elgg_view_entity($twitter_account, array(
										'view_type' => 'in_network_box',
									)),
				'full' => '<li id="elgg-object-' . $twitter_account->getGUID() . '" class="elgg-item">' . elgg_view_entity($twitter_account) . '</li>'
			);

			echo '<script type="text/javascript">$(document).ready(function() {elgg.deck_river.network_authorize(' . json_encode($account_output) . ');});</script>';
		} else {
			register_error(elgg_echo('deck_river:network:authorize:error'));
			echo elgg_echo('deck_river:network:authorize:error');
		}
	}
}

/**
 * Remove twitter access for the currently logged in user.
 */
function deck_river_twitter_api_revoke($user_guid = null, $user_id = null, $echo = true) {
	if (!$user_guid) $user_guid = elgg_get_logged_in_user_guid();

	if ($user_guid && elgg_instanceof(get_entity($user_guid), 'user')) {

		$user_deck_river_pinned_accounts = unserialize(get_private_setting($user_guid, 'deck_river_pinned_accounts'));

		$entities = deck_river_get_networks_account('twitter', $user_guid, $user_id);
		foreach ($entities as $entity) {
			if ($entity->canEdit()) {
				// remove account from pinned accounts
				$arr = array_diff($user_deck_river_pinned_accounts, array($entity->getGUID()));
				set_private_setting($user_guid, 'deck_river_pinned_accounts', serialize($arr));

				// remove account
				$entity->delete();
			}
		}

		if ($echo && $entities) system_message(elgg_echo('deck_river:twitter:revoke:success'));
		return true;
	} else {
		register_error(elgg_echo('deck_river:network:revoke:error'));
		return false;
	}
}



function deck_river_get_facebook_scope() {
	return 'read_friendlists,
			read_insights,
			read_mailbox,
			read_requests,
			read_stream,
			xmpp_login,
			create_event,
			manage_friendlists,
			publish_stream,
			user_about_me,
			user_activities,
			user_events,
			user_groups,
			user_likes,
			user_location,
			user_relationships,
			user_subscriptions,
			user_website';
}



function deck_river_facebook_authorize() {
	$code = get_input('code', false);

	if (!$code) {
		register_error(elgg_echo('deck_river:network:authorize:error'));
		return false;
	}

	elgg_load_library('deck_river:facebook_sdk');
	$facebook = new Facebook(array(
		'appId'  => elgg_get_plugin_setting('facebook_app_id', 'elgg-deck_river'),
		'secret' => elgg_get_plugin_setting('facebook_app_secret', 'elgg-deck_river'),
		'cookie' => true
	));
	$token = $facebook->getAccessToken();

	if ($token) {

		$facebook->setAccessToken($token);
		$fbUserProfile = $facebook->api('/me'); // Récupere l'utilisateur

		// make sure don't register twice this facebook account for this user.
		if (deck_river_get_networks_account('facebook', elgg_get_logged_in_user_guid(), $fbUserProfile['id'])) {
			echo elgg_view('page/elements/head');
			echo elgg_view('page/elements/foot');
			echo '<script type="text/javascript">$(document).ready(function() {elgg.deck_river.network_authorize(false);});</script>';
		} else {

			$facebook_account = new ElggObject;
			$facebook_account->subtype = 'facebook_account';
			$facebook_account->access_id = 0;
			$facebook_account->user_id = $fbUserProfile['id'];
			$facebook_account->name = $fbUserProfile['name'];
			$facebook_account->username = $fbUserProfile['username'];
			$facebook_account->oauth_token = $token;

			echo elgg_view('page/elements/head');
			echo elgg_view('page/elements/foot');

			if ($facebook_account->save()) {
				// trigger authorization hook
				elgg_trigger_plugin_hook('authorize', 'elgg-deck_river', array('token' => $token));

				$facebook_account->time_created = time(); // Don't now why time_created is not filled
				$fb_guid = $facebook_account->getGUID();

				$account_output = json_encode(array(
					'network' => 'facebook',
					'network_box' => elgg_view_entity($facebook_account, array(
											'view_type' => 'in_network_box',
										)),
					'full' => '<li id="elgg-object-' . $fb_guid . '" class="elgg-item">' . elgg_view_entity($facebook_account) . '</li>',
					'code' => "elgg.deck_river.getFBGroups($fb_guid);"
				));
				echo '<script type="text/javascript">$(document).ready(function() {elgg.deck_river.network_authorize(' . $account_output . ');});</script>';

			}

		}

	} else {
		register_error(elgg_echo('deck_river:network:authorize:error'));
		echo elgg_echo('deck_river:network:authorize:error');
	}
}


function deck_river_facebook_revoke($user_guid = null, $user_id = null, $echo = true) {
	if (!$user_guid) $user_guid = elgg_get_logged_in_user_guid();

	if ($user_guid && elgg_instanceof(get_entity($user_guid), 'user')) {

		$user_deck_river_pinned_accounts = unserialize(get_private_setting($user_guid, 'deck_river_pinned_accounts'));

		$entities = deck_river_get_networks_account('facebook', $user_guid, $user_id);
		foreach ($entities as $entity) {
			if ($entity->canEdit()) {
				// remove account from pinned accounts
				$arr = array_diff($user_deck_river_pinned_accounts, array($entity->getGUID()));
				set_private_setting($user_guid, 'deck_river_pinned_accounts', serialize($arr));

				// remove account
				$entity->delete();
			}
		}

		if ($echo && $entities) system_message(elgg_echo('deck_river:facebook:revoke:success'));
		return true;
	} else {
		register_error(elgg_echo('deck_river:network:revoke:error'));
		return false;
	}
}


