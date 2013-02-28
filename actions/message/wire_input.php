<?php
/**
 * Action for adding a wire post
 */

// don't filter since we strip and filter escapes some characters
$body = get_input('body', '', false);
$networks = (array) get_input('networks');

$user = elgg_get_logged_in_user_entity();

// make sure the post isn't blank
if (empty($body)) {
	register_error(elgg_echo("thewire:blank"));
} else if (!$networks) {
	register_error(elgg_echo("thewire:nonetwork"));
} else {
	// no html tags allowed so we escape
	$body = htmlspecialchars($body, ENT_NOQUOTES, 'UTF-8');
	// only 140 characters allowed
	$body = elgg_substr($body, 0, 140);

	foreach ($networks as $network) {
		if ($network == $user->getGUID()) { // network is ggouv
			$parent_guid = (int) get_input('parent_guid');

			$guid = deck_river_thewire_save_post($body, $user->guid, ACCESS_PUBLIC, $parent_guid, 'site');
			if (!$guid) {
				register_error(elgg_echo("thewire:error"));
			} else {
				// Send response to original poster if not already registered to receive notification
				if ($parent_guid) {
					deck_river_thewire_send_response_notification($guid, $parent_guid, $user);
					$parent_owner_guid = get_entity($parent_guid)->getOwnerGUID();
				}
				// send @mention
				foreach (deck_river_thewire_get_users($body) as $user_mentioned) {
					if ($user_mentioned->guid != $user->guid // don't send mention to owner of the message
						&& $user_mentioned->guid != $parent_owner_guid) // already send mail with send response notification
					deck_river_thewire_send_mention_notification($guid, $user_mentioned);
				}
				system_message(elgg_echo("thewire:posted"));
			}
		} else {
			$network_entity = get_entity($network);
			if ($network_entity->getSubtype() == 'twitter_account') {
				system_message(elgg_echo("thewire:twitter:posted"));
			}
		}
	}

}
