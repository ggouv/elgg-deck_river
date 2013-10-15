<?php
/**
 * Action for pin a network
 */

// don't filter since we strip and filter escapes some characters
$network = (int) get_input('network');

if (!$network) {
	register_error(elgg_echo('deck_river:error:pin'));
} else {

	$network_entity = get_entity($network);
	$user_guid = elgg_get_logged_in_user_guid();

	if ($network_entity->getOwnerGUID() != $user_guid) {
		register_error(elgg_echo('deck_river:error:pin'));
	} else {

		$user_deck_river_pinned_accounts = unserialize(get_private_setting($user_guid, 'deck_river_pinned_accounts'));

		if ($user_deck_river_pinned_accounts && in_array($network, $user_deck_river_pinned_accounts)) {
			foreach ($user_deck_river_pinned_accounts as $key => $value) {
				if ($value == $network) unset($user_deck_river_pinned_accounts[$key]);
			}
			set_private_setting($user_guid, 'deck_river_pinned_accounts', serialize($user_deck_river_pinned_accounts));
			system_message(elgg_echo('deck_river:ok:unpin'));
			echo true;
		} else if (count($user_deck_river_pinned_accounts) < 5) {
			$user_deck_river_pinned_accounts[] = $network;
			set_private_setting($user_guid, 'deck_river_pinned_accounts', serialize($user_deck_river_pinned_accounts));
			system_message(elgg_echo('deck_river:ok:pin'));
			echo true;
		} else {
			register_error(elgg_echo('deck_river:error:pin:too_much'));
		}

	}
}