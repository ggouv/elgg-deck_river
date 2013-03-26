<?php
/**
 * Action for twitter
 */

$account_guid = (int) get_input('twitter_account');
$method = (string) get_input('method');

$account = get_entity($account_guid);
$user = elgg_get_logged_in_user_entity();

// @todo need to check authorized actions

if ($method && $account->getSubtype() == 'twitter_account' && $account->getOwnerGUID() == $user->getGUID()) {
	elgg_load_library('deck_river:twitter_async');
	$twitter_consumer_key = elgg_get_plugin_setting('twitter_consumer_key', 'elgg-deck_river');
	$twitter_consumer_secret = elgg_get_plugin_setting('twitter_consumer_secret', 'elgg-deck_river');
	$twitterObj = new EpiTwitter($twitter_consumer_key, $twitter_consumer_secret, $account->oauth_token, $account->oauth_token_secret);

	try {
		$result = call_user_func(array($twitterObj, $method), array('screen_name' => $account->screen_name));
	} catch(Exception $e) {
		$result = json_decode($e->getMessage())->errors[0];
	}

	// check result
	if ($result->code == 200) {
		$jsonexport['result'] = $result->response;
	} else {
		$key = 'deck_river:twitter:error:' . $result->code;
		if (elgg_echo($key) == $key) { // check if language string exist
			$jsonexport['column_error'] = elgg_echo('deck_river:twitter:error', array($result->code, $result->message));
		} else {
			$jsonexport['column_error'] = elgg_echo($key);
		}
		$jsonexport['results'] = '';
	}

	echo json_encode($jsonexport);

} else {
	register_error(elgg_echo('deck_river:twitter:error'));
}
