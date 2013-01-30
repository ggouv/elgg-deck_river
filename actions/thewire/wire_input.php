<?php
/**
 * Action for adding a wire post
 * 
 */

// don't filter since we strip and filter escapes some characters
$body = get_input('body', '', false);
$network_ggouv = get_input('network_ggouv');
$network_twitter = get_input('network_twitter');

// make sure the post isn't blank
if (empty($body)) {
	register_error(elgg_echo("thewire:blank"));
} else if (!$network_ggouv && !$network_twitter && !$network_facebook) {
	register_error(elgg_echo("thewire:nonetwork"));
} else {

	if ($network_ggouv == 'true') {
		$parent_guid = (int) get_input('parent_guid');
		$access_id = ACCESS_PUBLIC;
		$method = 'site';
	
		$guid = thewire_save_post($body, elgg_get_logged_in_user_guid(), $access_id, $parent_guid, $method);
		if (!$guid) {
			register_error(elgg_echo("thewire:error"));
		}
		
		// Send response to original poster if not already registered to receive notification
		if ($parent_guid) {
			thewire_send_response_notification($guid, $parent_guid, $user);
			$parent = get_entity($parent_guid);
		}
		
		system_message(elgg_echo("thewire:posted"));
	}
	
	if ($network_twitter == 'true') {
		system_message(elgg_echo("thewire:twitter:posted"));
	}

}