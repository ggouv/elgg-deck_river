<?php

elgg_register_event_handler('init','system','deck_river_init');

function deck_river_init() {

	elgg_register_library('deck_river:api', elgg_get_plugins_path() . 'elgg-deck_river/lib/api.php');
	elgg_register_library('deck_river:river_loader', elgg_get_plugins_path() . 'elgg-deck_river/lib/river_loader.php');

	elgg_register_class('EpiCurl', elgg_get_plugins_path() . 'elgg-deck_river/vendors/twitter-async/EpiCurl.php');
	elgg_register_class('EpiOAuth', elgg_get_plugins_path() . 'elgg-deck_river/vendors/twitter-async/EpiOAuth.php');
	elgg_register_class('EpiTwitter', elgg_get_plugins_path() . 'elgg-deck_river/vendors/twitter-async/EpiTwitter.php');
	
	//elgg_load_library('deck_river:api');

	elgg_extend_view('css/elgg','deck_river/css');
	elgg_extend_view('js/elgg', 'deck_river/js/init');
	elgg_extend_view('js/elgg', 'deck_river/js/popups');
	elgg_extend_view('js/elgg', 'deck_river/js/loaders');
	elgg_extend_view('js/elgg', 'deck_river/js/shortener_url');

	elgg_register_ajax_view('deck_river/ajax/column_river');
	elgg_register_ajax_view('deck_river/ajax/column_settings');
	elgg_register_ajax_view('deck_river/ajax/entity_river');
	elgg_register_ajax_view('deck_river/ajax/entity_mention');
	elgg_register_ajax_view('deck_river/ajax/user_info');
	elgg_register_ajax_view('deck_river/ajax/group_info');
	elgg_register_ajax_view('deck_river/ajax/url_shortener');
	elgg_register_ajax_view('deck_river/ajax/load_discussion');

	elgg_register_page_handler('activity', 'deck_river_page_handler');
	elgg_register_page_handler('thewire', 'deck_river_wire_page_handler');

	// register actions
	$action_path = elgg_get_plugins_path() . 'elgg-deck_river/actions';
	elgg_register_action('deck_river/wire_input', "$action_path/thewire/wire_input.php");
	elgg_register_action('thewire/delete', "$action_path/thewire/delete.php");
	elgg_register_action('deck_river/column/settings', "$action_path/column/settings.php");
	elgg_register_action('deck_river/column/move', "$action_path/column/move.php");
	elgg_register_action('deck_river/tab/add', "$action_path/tab/add.php");
	elgg_register_action('deck_river/tab/delete', "$action_path/tab/delete.php");
	elgg_register_action('deck_river/tab/rename', "$action_path/tab/rename.php");
	elgg_register_action('elgg-deck_river/settings/save', "$action_path/plugins/save.php");

	// Register a URL handler for thewire posts
	elgg_register_entity_url_handler('object', 'thewire', 'deck_river_thewire_url');

	// Register for search
	elgg_register_entity_type('object', 'thewire');

	// Register granular notification for this type
	register_notification_object('object', 'thewire', elgg_echo('thewire:notify:subject'));

	// Listen to notification events and supply a more useful message
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'deck_river_thewire_notify_message');
}

function deck_river_page_handler($page) {

	if (elgg_is_logged_in()) {
	
		if (!isset($page[0])) {
			$page[0] = 'default';
		}
	
		switch ($page[0]) {
			default:
				elgg_set_context(elgg_extract(0, $page, 'default'));
				include_once dirname(__FILE__) . '/pages/river.php';
				break;
		}
	
	} else {
		forward('');
	}

	return true;
}

/**
 * The wire page handler
 *
 * Supports:
 * thewire/owner/<username>     View this user's wire posts
 * thewire/following/<username> View the posts of those this user follows
 * thewire/reply/<guid>         Reply to a post
 * thewire/view/<guid>          View a post
 * thewire/thread/<id>          View a conversation thread
 * thewire/tag/<tag>            View wire posts tagged with <tag>
 *
 * @param array $page From the page_handler function
 * @return bool
 */
function deck_river_wire_page_handler($page) {

	$base_dir = elgg_get_plugins_path() . 'elgg-deck_river/pages/thewire';

	if (!isset($page[0]) || $page[0] == 'all') {
		forward('activity');
	}

	switch ($page[0]) {
		case "friends":
			include "$base_dir/friends.php";
			break;

		case "owner":
			include "$base_dir/owner.php";
			break;

		case "view":
			if (isset($page[1])) {
				set_input('guid', $page[1]);
			}
			include "$base_dir/view.php";
			break;

		case "thread":
			if (isset($page[1])) {
				set_input('thread_id', $page[1]);
			}
			include "$base_dir/thread.php";
			break;

		case "reply":
			if (isset($page[1])) {
				set_input('guid', $page[1]);
			}
			include "$base_dir/reply.php";
			break;

		case "tag":
			if (isset($page[1])) {
				set_input('tag', $page[1]);
			}
			include "$base_dir/tag.php";
			break;

		case "previous":
			if (isset($page[1])) {
				set_input('guid', $page[1]);
			}
			include "$base_dir/previous.php";
			break;

		default:
			return false;
	}
	return true;
}



/**
 * Override the url for a wire post to return the thread
 * 
 * @param ElggObject $thewirepost Wire post object
 */
function deck_river_thewire_url($thewirepost) {
	return "thewire/view/" . $thewirepost->guid;
}



/**
 * Replace urls, hashtags,  ! and @ by links
 *
 * @param string $text The text of a post
 * @return string
 */
function deck_river_wire_filter($text) {
	global $CONFIG;

	$text = ' ' . $text;

	// email addresses
	$text = preg_replace(
				'/(^|[^\w])([\w\-\.]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})/i',
				'$1<a href="mailto:$2@$3">$2@$3</a>',
				$text);

	// links
	$text = parse_urls($text);

	// usernames
	$text = preg_replace(
				'/(^|[^\w])@([\p{L}\p{Nd}._]+)/u',
				'$1<a class="user-info-popup" href="#" title="$2">@$2</a>',
				$text);
				
	// groups
	$text = preg_replace(
				'/(^|[^\w])!([\p{L}\p{Nd}._]+)/u',
				'$1<a class="group-info-popup" href="#" title="$2">!$2</a>',
				$text);

	// hashtags
	$text = preg_replace(
				'/(^|[^\w])#(\w*[^\s\d!-\/:-@]+\w*)/',
				'$1<a class="hashtag-info-popup" href="#" title="#$2">#$2</a>',
				$text);

	$text = trim($text);

	return $text;
}


/**
 * Get group by title
 *
 * @param string $group The title's group
 *
 * @return GUID|false Depending on success
 */
function search_group_by_title($group) {
	global $CONFIG, $GROUP_TITLE_TO_GUID_MAP_CACHE;

	$group = sanitise_string($group);

	// Caching
	if ((isset($GROUP_TITLE_TO_GUID_MAP_CACHE[$group]))
	&& (retrieve_cached_entity($GROUP_TITLE_TO_GUID_MAP_CACHE[$group]))) {
		return retrieve_cached_entity($GROUP_TITLE_TO_GUID_MAP_CACHE[$group])->guid;
	}

	$guid = get_data("SELECT guid from {$CONFIG->dbprefix}groups_entity where name='$group'");

	if ($guid) {
		$GROUP_TITLE_TO_GUID_MAP_CACHE[$group] = $guid[0]->guid;
	} else {
		$guid = false;
	}

	if ($guid) {
		return $guid[0]->guid;
	} else {
		return false;
	}
}



/**
 * Get an array of hashtags from a text string
 * 
 * @param string $text The text of a post
 * @return array
 */
function deck_river_thewire_get_hashtags($text) {
	// beginning of text or white space followed by hashtag
	// hashtag must begin with # and contain at least one character not digit, space, or punctuation
	$matches = array();
	preg_match_all('/(^|[^\w])#(\w*[^\s\d!-\/:-@]+\w*)/', $text, $matches);
	return $matches[2];
}



/**
 * Create a new wire post.
 *
 * @param string $text        The post text
 * @param int    $userid      The user's guid
 * @param int    $access_id   Public/private etc
 * @param int    $parent_guid Parent post guid (if any)
 * @param string $method      The method (default: 'site')
 * @return guid or false if failure
 */
function deck_river_thewire_save_post($text, $userid, $access_id, $parent_guid = 0, $method = "site") {
	$post = new ElggObject();

	$post->subtype = "thewire";
	$post->owner_guid = $userid;
	$post->access_id = $access_id;

	// only 200 characters allowed
	$text = elgg_substr($text, 0, 200);

	// no html tags allowed so we escape
	$post->description = htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');

	$post->method = $method; //method: site, email, api, ...

	$tags = deck_river_thewire_get_hashtags($text);
	if ($tags) {
		$post->tags = $tags;
	}

	// must do this before saving so notifications pick up that this is a reply
	if ($parent_guid) {
		$post->reply = true;
	}

	$guid = $post->save();

	// set thread guid
	if ($parent_guid) {
		$post->addRelationship($parent_guid, 'parent');
		
		// name conversation threads by guid of first post (works even if first post deleted)
		$parent_post = get_entity($parent_guid);
		$post->wire_thread = $parent_post->wire_thread;
	} else {
		// first post in this thread
		$post->wire_thread = $guid;
	}

	if ($guid) {
		$idd = add_to_river('river/object/thewire/create', 'create', $post->owner_guid, $post->guid);

		// let other plugins know we are setting a user status
		$params = array(
			'entity' => $post,
			'user' => $post->getOwnerEntity(),
			'message' => $post->description,
			'url' => $post->getURL(),
			'origin' => 'thewire',
		);
		elgg_trigger_plugin_hook('status', 'user', $params);
	}
	
	return $guid;
}



/**
 * Returns the notification body
 *
 * @return $string
 */
function deck_river_thewire_notify_message($hook, $entity_type, $returnvalue, $params) {
	global $CONFIG;
	
	$entity = $params['entity'];
	if (($entity instanceof ElggEntity) && ($entity->getSubtype() == 'thewire')) {
		$descr = $entity->description;
		$owner = $entity->getOwnerEntity();
		if ($entity->reply) {
			// have to do this because of poor design of Elgg notification system
			$parent_post = get_entity(get_input('parent_guid'));
			if ($parent_post) {
				$parent_owner = $parent_post->getOwnerEntity();
			}
			$body = sprintf(elgg_echo('thewire:notify:reply'), $owner->name, $parent_owner->name);
		} else {
			$body = sprintf(elgg_echo('thewire:notify:post'), $owner->name);
		}
		$body .= "\n\n" . $descr . "\n\n";
		$body .= elgg_echo('thewire') . ": {$CONFIG->url}thewire";
		return $body;
	}
	return $returnvalue;
}




/**
 * Send notification to poster of parent post if not notified already
 *
 * @param int      $guid        The guid of the reply wire post
 * @param int      $parent_guid The guid of the original wire post
 * @param ElggUser $user        The user who posted the reply
 * @return void
 */
function deck_river_thewire_send_response_notification($guid, $parent_guid, $user) {
	$parent_owner = get_entity($parent_guid)->getOwnerEntity();
	$user = elgg_get_logged_in_user_entity();

	// check to make sure user is not responding to self
	if ($parent_owner->guid != $user->guid) {
		// check if parent owner has notification for this user
		$send_response = true;
		global $NOTIFICATION_HANDLERS;
		foreach ($NOTIFICATION_HANDLERS as $method => $foo) {
			if (check_entity_relationship($parent_owner->guid, 'notify' . $method, $user->guid)) {
				$send_response = false;
			}
		}

		// create the notification message
		if ($send_response) {
			// grab same notification message that goes to everyone else
			$params = array(
				'entity' => get_entity($guid),
				'method' => "email",
			);
			$msg = deck_river_thewire_notify_message("", "", "", $params);

			notify_user(
					$parent_owner->guid,
					$user->guid,
					elgg_echo('thewire:notify:subject'),
					$msg);
		}
	}
}




/**
* Google url shortener
* http://www.webgalli.com/blog/easily-create-short-urls-with-php-curl-and-goo-gl-or-bit-ly/
*/
function goo_gl_short_url($longUrl) {
	$GoogleApiKey = elgg_get_plugin_setting('googleApiKey', 'elgg-deck_river');
	$postData = array('longUrl' => $longUrl, 'key' => $GoogleApiKey);
	$jsonData = json_encode($postData);
	$curlObj = curl_init();
	curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url');
	curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($curlObj, CURLOPT_HEADER, 0);
	curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
	curl_setopt($curlObj, CURLOPT_POST, 1);
	curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);
	$response = curl_exec($curlObj);
	$json = json_decode($response);
	curl_close($curlObj);
	return $json->id;
}
