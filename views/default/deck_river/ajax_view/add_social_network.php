<?php
$site_name = elgg_get_site_entity()->name;

// twitter
$twitter_consumer_key = elgg_get_plugin_setting('twitter_consumer_key', 'elgg-deck_river');
$twitter_consumer_secret = elgg_get_plugin_setting('twitter_consumer_secret', 'elgg-deck_river');
if ($twitter_consumer_key && $twitter_consumer_secret) {
	elgg_load_library('deck_river:twitter_async');
	$twitterObjUnAuth = new EpiTwitter($twitter_consumer_key, $twitter_consumer_secret);
	$twitterRequestUrl = $twitterObjUnAuth->getAuthenticateUrl();

	$body = '<h2>' . elgg_echo('deck_river:twitter:authorize:request:title', array($site_name)) . '</h2>';
	$body .= '<ul style="list-style: disc;" class="pll">' . elgg_echo('deck_river:twitter:add_network:request', array($site_name)) . '</ul><br />';
	$body .= elgg_view('output/url', array(
		'href' => $twitterRequestUrl,
		'text' => elgg_echo('deck_river:twitter:authorize:request:button'),
		'class' => 'elgg-button elgg-button-action mtm',
		'id' => 'authorize-twitter'
	));

	echo elgg_view_image_block('<div class="twitter-icon gwfb"></div>', $body, array(
		'class' => 'pam'
	));
}
