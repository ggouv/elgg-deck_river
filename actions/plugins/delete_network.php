<?php
/**
 * Action to delete a network
 */

$network_guid = (int) get_input('guid');

if (!$network_guid) {
	register_error(elgg_echo('deck_river:twitter:revoke:error'));
} else {

	$network = get_entity($network_guid);

	if ($network->canEdit()) {
		elgg_load_library('deck_river:authorize');
		deck_river_twitter_api_revoke(null, $network->user_id);
	} else {
		register_error(elgg_echo('deck_river:twitter:revoke:error'));
	}
}

forward('/authorize/applications/' . elgg_get_logged_in_user_entity()->username);