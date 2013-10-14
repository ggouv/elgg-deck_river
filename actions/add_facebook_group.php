<?php
/**
 * Action add group facebook
 */

action_gatekeeper();
elgg_load_library('deck_river:authorize');

if (deck_river_count_networks_account('all') >= elgg_get_plugin_setting('max_accounts', 'elgg-deck_river')) {
	register_error('deck_river:network:too_many_accounts', array(elgg_get_site_entity()->name));
	return true;
}

$account_guid = (int) get_input('facebook_account');
$group_id = (string) get_input('group_id');

$account = get_entity($account_guid);
$user_guid = elgg_get_logged_in_user_guid();

if (deck_river_get_networks_account('fb_group', $user_guid, $group_id)) {
	return true;
}

if ($account && $account->getSubtype() == 'facebook_account' && $account->getOwnerGUID() == $user_guid) {
	elgg_load_library('deck_river:facebook_sdk');
	$facebook = new Facebook(array(
		'appId'  => elgg_get_plugin_setting('facebook_app_id', 'elgg-deck_river'),
		'secret' => elgg_get_plugin_setting('facebook_app_secret', 'elgg-deck_river')
	));
	$facebook->setAccessToken($account->oauth_token);

	try {
		$result = $facebook->api($group_id, 'get');
	} catch(FacebookApiException $e) {
		$result = json_decode($e);
	}

	if ($result) {

		$fb_group_account = new ElggObject;
		$fb_group_account->subtype = 'fb_group_account';
		$fb_group_account->access_id = 0;
		$fb_group_account->group_id = $result['id'];
		$fb_group_account->name = $result['name'];
		$fb_group_account->icon = $result['icon'];
		$fb_group_account->oauth_token = $account->oauth_token;

		if ($fb_group_account->save()) {
			// trigger authorization hook
			elgg_trigger_plugin_hook('authorize', 'elgg-deck_river', array('token' => $token));

			$fb_group_account->time_created = time(); // Don't now why time_created is not filled
			$fb_guid = $fb_group_account->getGUID();

			echo json_encode(array(
				'network' => 'facebook',
				'network_box' => elgg_view_entity($fb_group_account, array(
										'view_type' => 'in_network_box',
									)),
				'full' => '<li id="elgg-object-' . $fb_guid . '" class="elgg-item">' . elgg_view_entity($fb_group_account) . '</li>'
			));

		}

	} else {
		register_error(elgg_echo('deck_river:facebook:error'));
	}


} else {
	register_error(elgg_echo('deck_river:facebook:error'));
}
