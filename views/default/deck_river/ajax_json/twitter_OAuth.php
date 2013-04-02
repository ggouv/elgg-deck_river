<?php

global $CONFIG, $jsonexport;
$dbprefix = $CONFIG->dbprefix;

// Get callbacks
$column = get_input('column', 'false');
$time_method = get_input('time_method', 'false');
$time_posted = get_input('time_posted', 'false');

$params = explode('-', $column);

$jsonexport = array();

// detect network
if ($params) {
	$twitter_consumer_key = elgg_get_plugin_setting('twitter_consumer_key', 'elgg-deck_river');
	$twitter_consumer_secret = elgg_get_plugin_setting('twitter_consumer_secret', 'elgg-deck_river');

	elgg_load_library('deck_river:authorize');
	$accounts = deck_river_twitter_get_account();
	$account = $accounts[0]; // @todo why the first ? Check limit rate and take the most free ?

	elgg_load_library('deck_river:twitter_async');
	$twitterObj = new EpiTwitter($twitter_consumer_key, $twitter_consumer_secret, $account->oauth_token, $account->oauth_token_secret);

	try {
		if ($time_method == 'lower') {
			$result = call_user_func(array($twitterObj, $params[1]), array(
				'user_id' => $params[0],
				'count' => 30,
				'since_id' => $time_posted+1 // +1 for not repeat first river item
			));
		} elseif ($time_method == 'upper') {
			$result = call_user_func(array($twitterObj, $params[1]), array(
				'user_id' => $params[0],
				'count' => 30,
				'max_id' => $time_posted-1 // -1 for not repeat last river item
			));
		} else {
			$result = call_user_func(array($twitterObj, $params[1]), array(
				'user_id' => $params[0],
				'count' => 30
			));
		}
	} catch(Exception $e) {
		$result = json_decode($e->getMessage())->errors[0];
	}

	// check result
	if ($result->code == 200) {
		$jsonexport['column_type'] = $params[1];
		foreach ($result->__get('response') as $value) {
			$value['menu'] = array(
				'default' => array(
					array(
						'name' => 'response',
						'content' => '<a href="" title="Répondre" class="gwfb tooltip s"><span class="elgg-icon elgg-icon-response "></span></a>'
					),
					array(
						'name' => 'retweet',
						'content' => '<a href="" title="Retweeter" class="gwfb tooltip s"><span class="elgg-icon elgg-icon-share "></span></a>'
					)
				),
				'submenu' => array()
			);
			$results[] = $value;
		}
		$jsonexport['results'] = $results;
	} else {
		$key = 'deck_river:twitter:error:' . $result->code;
		if (elgg_echo($key) == $key) { // check if language string exist
			$jsonexport['column_error'] = elgg_echo('deck_river:twitter:error', array($result->code, $result->message));
		} else {
			$jsonexport['column_error'] = elgg_echo($key);
		}
		$jsonexport['results'] = '';
	}
}

echo json_encode($jsonexport);
