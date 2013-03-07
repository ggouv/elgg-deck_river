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

$twitter_accounts = deck_river_twitter_get_account($user->getGUID());

$content = elgg_view_module('twitter', '<span class="twitter-icon gwfb prs"></span>' . elgg_echo('Twitter'), elgg_view_entity_list($twitter_accounts), array(
	'class' => 'mtl',
));

$params = array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
);
$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);