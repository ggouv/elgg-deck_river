<?php

elgg_load_library('deck_river:twitter_async');

$consumer_key = elgg_get_plugin_setting('twitter_consumer_key', 'elgg-deck_river');
$consumer_secret = elgg_get_plugin_setting('twitter_consumer_secret', 'elgg-deck_river');

global $fb; $fb->info($consumer_key);
$fb->info($consumer_secret);

//$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);
//global $fb; $fb->info($twitterObj);


$user = elgg_get_logged_in_user_entity();
global $fb; $fb->info($user);
$twitter_name = elgg_get_plugin_user_setting('twitter_name', $user->getGUID(), 'elgg-deck_river');
$access_key = elgg_get_plugin_user_setting('twitter_access_key', $user->getGUID(), 'elgg-deck_river');
$access_secret = elgg_get_plugin_user_setting('twitter_access_secret', $user->getGUID(), 'elgg-deck_river');

$fb->info($twitter_name, 'twitter_name');
$fb->info($access_key, 'access_key');
$fb->info($access_secret, 'access_secret');

$twitterObjUnAuth = new EpiTwitter($consumer_key, $consumer_secret);
$fb->info($twitterObjUnAuth->getAuthenticateUrl());

$twitterObj = new EpiTwitter($consumer_key, $consumer_secret, $access_key, $access_secret);
$fb->info($twitterObj);
$userInfo = $twitterObj->get('/account/verify_credentials.json');
$fb->info($userInfo);
/*
include_once dirname(__FILE__) . '/../ggouv/engine/start.php';

$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);
$cll = urlencode('http://localhost/~mama/ggouv/sign_in_with_twitter/random.php');

echo '<a href="' . $twitterObj->getAuthenticateUrl() . '&oauth_callback='.$cll.'">Authorize with Twitter</a>';
*/

?>

