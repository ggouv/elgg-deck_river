<?php
/**
 * User's applications page
 */

// Only logged in users
gatekeeper();

$user = elgg_get_logged_in_user_entity();

// Make sure we don't open a security hole ...
if ((!elgg_get_page_owner_entity()) || (!elgg_get_page_owner_entity()->canEdit())) {
	elgg_set_page_owner_guid($user->getGUID());
}

elgg_set_context('settings');

$title = elgg_echo('usersettings:authorize:applications');

elgg_push_breadcrumb(elgg_echo('settings'), 'settings/user/' . $user->username);
elgg_push_breadcrumb($title);

$content = '';

$twitter_accounts = deck_river_twitter_get_account($user->getGUID());

if (!empty($twitter_accounts)) {
	$content .= elgg_view_module(
		'twitter',
		'<span class="twitter-icon gwfb"></span><span class="pls">' . elgg_echo('Twitter') . '</span>',
		elgg_view_entity_list($twitter_accounts), array(
			'class' => 'mtl',
		)
	);
} else {
	$twitter_consumer_key = elgg_get_plugin_setting('twitter_consumer_key', 'elgg-deck_river');
	$twitter_consumer_secret = elgg_get_plugin_setting('twitter_consumer_secret', 'elgg-deck_river');
	if ($twitter_consumer_key && $twitter_consumer_secret) {
		elgg_load_library('deck_river:twitter_async');
		$twitterObjUnAuth = new EpiTwitter($twitter_consumer_key, $twitter_consumer_secret);
		$twitterRequestUrl = $twitterObjUnAuth->getAuthenticateUrl();

		$site_name = elgg_get_site_entity()->name;
		$content .= elgg_view_module(
			'twitter',
			'<span class="twitter-icon gwfb"></span><span class="pls">' . elgg_echo('Twitter') . '</span>',
			elgg_view_module(
				'featured',
				elgg_echo('deck_river:twitter:authorize:request:title', array($site_name)),
				elgg_echo('deck_river:twitter:add_network:request', array($site_name)) . elgg_view('output/url', array(
					'href' => $twitterRequestUrl,
					'text' => elgg_echo('deck_river:twitter:authorize:request:button'),
					'class' => 'elgg-button elgg-button-action mtm',
					'id' => 'authorize-twitter'
				)),
				array('class' => 'mts float')
			)
		);
	}
}

$params = array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
);
$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);