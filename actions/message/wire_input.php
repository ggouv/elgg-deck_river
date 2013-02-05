<?php
/**
 * Action for adding a wire post
 * 
 */

// don't filter since we strip and filter escapes some characters
$body = get_input('body', '', false);
$network_ggouv = get_input('network_ggouv');
$network_twitter = get_input('network_twitter');

$user = elgg_get_logged_in_user_entity();

// make sure the post isn't blank
if (empty($body)) {
	register_error(elgg_echo("thewire:blank"));
} else if (!$network_ggouv && !$network_twitter && !$network_facebook) {
	register_error(elgg_echo("thewire:nonetwork"));
} else {
	// no html tags allowed so we escape
	$body = htmlspecialchars($body, ENT_NOQUOTES, 'UTF-8');
	// only 140 characters allowed
	$body = elgg_substr($body, 0, 140);
	
	if ($network_ggouv == 'true') {
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
	}
	
	if ($network_twitter == 'true') {
		system_message(elgg_echo("thewire:twitter:posted"));
	}

}
