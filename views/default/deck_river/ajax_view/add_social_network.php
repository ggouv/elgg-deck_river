<?php
$site_name = elgg_get_site_entity()->name;

// twitter
$twitter_consumer_key = elgg_get_plugin_setting('twitter_consumer_key', 'elgg-deck_river');
$twitter_consumer_secret = elgg_get_plugin_setting('twitter_consumer_secret', 'elgg-deck_river');
if ($twitter_consumer_key && $twitter_consumer_secret) {
	elgg_load_library('deck_river:twitter_async');
	$twitterObjUnAuth = new EpiTwitter($twitter_consumer_key, $twitter_consumer_secret);
	$twitterRequestUrl = $twitterObjUnAuth->getAuthenticateUrl();

	$body = elgg_echo('deck_river:twitter:usersettings:request:title', array($site_name)) . '.<br/>';
	$body .= elgg_echo('deck_river:twitter:usersettings:request', array($twitterRequestUrl));

	echo elgg_view_image_block('<div class="twitter-icon gwfb t"></div>', $body, array(
		'class' => 'pam'
	));
}
