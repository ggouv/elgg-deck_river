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

$content = '';


// twitter

$twitter_consumer_key = elgg_get_plugin_setting('twitter_consumer_key', 'elgg-deck_river');
$twitter_consumer_secret = elgg_get_plugin_setting('twitter_consumer_secret', 'elgg-deck_river');
if ($twitter_consumer_key && $twitter_consumer_secret) {
	elgg_load_library('deck_river:twitter_async');
	$twitterObjUnAuth = new EpiTwitter($twitter_consumer_key, $twitter_consumer_secret);
	$twitterRequestUrl = $twitterObjUnAuth->getAuthenticateUrl();
	$add_button = elgg_view('output/url', array(
		'href' => $twitterRequestUrl,
		'text' => elgg_echo('deck_river:twitter:authorize:request:button'),
		'class' => 'elgg-button elgg-button-action float-alt',
		'id' => 'authorize-twitter'
	));

	$twitter_accounts = deck_river_get_networks_account('twitter_account', $user->getGUID());

	$name = 'twitter elgg-module-network elgg-module-aside mvl float';
	$title = '<span class="network-icon twitter-icon gwfb pbm float"></span><span class="pls">' . elgg_echo('Twitter') . '</span>';

	if (!empty($twitter_accounts)) {
		$content .= elgg_view_module(
			$name,
			$title . $add_button,
			elgg_view_entity_list($twitter_accounts), array(
				'class' => 'mtl',
			)
		);
	} else {
		$site_name = elgg_get_site_entity()->name;
		$content .= elgg_view_module(
			$name,
			$title,
			elgg_view_module(
				'featured',
				elgg_echo('deck_river:twitter:authorize:request:title', array($site_name)),
				elgg_echo('deck_river:twitter:add_network:request', array($site_name)) . $add_button,
				array('class' => 'mts float')
			)
		);
	}
}



// facebook

$facebook_app_id = elgg_get_plugin_setting('facebook_app_id', 'elgg-deck_river');
$facebook_app_secret = elgg_get_plugin_setting('facebook_app_secret', 'elgg-deck_river');
if ($facebook_app_id && $facebook_app_secret) {
	elgg_load_library('deck_river:facebook_sdk');
	$facebook = new Facebook(array(
		'appId'  => $facebook_app_id,
		'secret' => $facebook_app_secret,
		'cookie' => true
	));
	$loginUrl = $facebook->getLoginUrl(array(
		'redirect_uri' => (elgg_get_site_url() . 'authorize/facebook'),
		'scope' => deck_river_get_facebook_scope(),
	));
	$add_button = elgg_view('output/url', array(
		'href' => $loginUrl,
		'text' => elgg_echo('deck_river:facebook:authorize:request:button'),
		'class' => 'elgg-button elgg-button-action float-alt',
		'id' => 'authorize-facebook'
	));

	$facebook_accounts = deck_river_get_networks_account('facebook_account', $user->getGUID());

	$name = 'facebook elgg-module-network elgg-module-aside mvl float';
	$title = '<span class="network-icon facebook-icon gwfb pbm float"></span><span class="pls">' . elgg_echo('Facebook') . '</span>';

	if (!empty($facebook_accounts)) {
		$content .= elgg_view_module(
			$name,
			$title . $add_button,
			elgg_view_entity_list($facebook_accounts), array(
				'class' => 'mtl',
			)
		);
	} else {
		$site_name = elgg_get_site_entity()->name;
		$content .= elgg_view_module(
			$name,
			$title . '</span>',
			elgg_view_module(
				'featured',
				elgg_echo('deck_river:facebook:authorize:request:title', array($site_name)),
				elgg_echo('deck_river:facebook:add_network:request', array($site_name)) . $add_button,
				array('class' => 'mts float')
			)
		);
	}
}

$title = elgg_echo('usersettings:authorize:applications');

elgg_push_breadcrumb(elgg_echo('settings'), 'settings/user/' . $user->username);
elgg_push_breadcrumb($title);

$params = array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
);
$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);