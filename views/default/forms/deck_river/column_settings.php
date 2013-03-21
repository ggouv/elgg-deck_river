<?php

// Get tab and column
$tab = elgg_extract('tab', $vars, null);
$column = elgg_extract('column', $vars, null);

if (!$tab || !$column) {
	return;
}

// Get the settings of the current user
$user_guid = elgg_get_logged_in_user_guid();
$user_river_options = unserialize(get_private_setting($user_guid, 'deck_river_settings'));

$site_name = elgg_get_site_entity()->name;

if ($column == 'new') {
	foreach ($user_river_options[$tab] as $key => $item) {
		$n[] = preg_replace('/[^0-9]+/', '', $key);
	}
	$column = 'column-' . (max($n)+1);
	$new = true;
}
$user_river_column_options = $user_river_options[$tab][$column];
$column_title = $user_river_column_options['title'];
?>

<?php echo elgg_view('input/hidden', array('name' => 'column', 'value' => $column)); ?>
<?php echo elgg_view('input/hidden', array('name' => 'tab', 'value' => $tab)); ?>

<div id='deck-column-settings' class='pas'>
	<?php
		$selected = $new ? 'elgg' : $user_river_column_options['network'];
		if (!$selected) $selected = 'elgg';
		$params = array(
			'type' => 'vertical',
			'class' => 'networks float',
			'tabs' => array(
				array('title' => $site_name, 'link_class' => 'elgg', 'url' => "#", 'selected' => $selected == 'elgg' ? true : false),
				array('title' => elgg_echo('Twitter'), 'link_class' => 'twitter', 'url' => "#", 'selected' => $selected == 'twitter' ? true : false),
				array('title' => elgg_echo('Facebook'), 'link_class' => 'facebook', 'url' => "#", 'selected' => $selected == 'facebook' ? true : false),
			)
		);
		echo elgg_view('navigation/tabs', $params);
	?>

	<div class="tab elgg<?php if ($selected != 'elgg') echo ' hidden'; ?>">
		<ul class='box-settings phm'>
			<li>
				<label><?php echo elgg_echo('deck_river:type'); ?></label><br />
				<?php
					$set = str_replace("&gt;", ">", elgg_get_plugin_setting('column_type', 'elgg-deck_river'));
					if (!$set) $set = elgg_echo('deck_river:settings:column_type:default');
					eval("\$options_values = $set;");
					echo elgg_view('input/dropdown', array(
						'name' => 'type',
						'value' => $user_river_column_options['type'],
						'class' => 'column-type mts',
						'options_values' => $options_values
					)); ?>
			</li>
			<li class='search-options hidden pts'>
				<label><?php echo elgg_echo('deck_river:search'); ?></label><br />
				<?php echo elgg_view('input/text', array(
					'name' => 'search',
					'value' => $user_river_column_options['search']
				)); ?>
			</li>
			<li class='group-options hidden pts'>
				<label><?php echo elgg_echo('group'); ?></label><br />
				<?php
					echo elgg_view('input/autocomplete', array(
						'name' => 'group',
						'value' => $user_river_column_options['group'],
						'match_on' => 'groups'
					));
				?>
			</li>
		</ul>

		<div class='filter plm'>
			<label><?php echo elgg_echo('deck_river:filter'); ?></label><br />
			<?php
			// create checkboxes array
			$types_value = array();
			$registered_entities = elgg_get_config('registered_entities');
			$types_label[elgg_echo('deck_river:filter:all')] = 'All';
			if (!array_key_exists('types_filter', $user_river_column_options) && !array_key_exists('subtypes_filter', $user_river_column_options) || $user_river_column_options['types_filter'] == 'All' ) $types_value[] = 'All';
			if (!empty($registered_entities)) {
				foreach ($registered_entities as $type => $subtypes) {
					// subtype will always be an array.
					if (!count($subtypes)) {
						$label = elgg_echo("item:$type");
						$types_label[$label] .= $type;
						if (in_array($type, $user_river_column_options['types_filter'])) $types_value[] = $type;
					} else {
						foreach ($subtypes as $subtype) {
							$label = elgg_echo("item:$type:$subtype");
							$subtypes_label[$label] .= $subtype;
							if (in_array($subtype, $user_river_column_options['subtypes_filter'])) $subtypes_value[] = $subtype;
						}
					}
				}

				// merge keys defined by admin
				$keys_to_merge = explode(',', elgg_get_plugin_setting('keys_to_merge', 'elgg-deck_river'));
				foreach ($keys_to_merge as $key => $value ) {
					$key_master = explode('=', $value);
					foreach ($types_label as $k => $v) {
						if ($v == $key_master[1]) unset($types_label[$k]);
					}
					foreach ($subtypes_label as $k => $v) {
						if ($v == $key_master[1]) unset($subtypes_label[$k]);
					}
				}

				// show filters
				echo elgg_view('input/checkboxes', array(
									'name' => 'filters_types',
									'value' => $types_value,
									'options' => $types_label,
									'class' => 'mts'
									));
				echo elgg_view('input/checkboxes', array(
									'name' => 'filters_subtypes',
									'value' => $subtypes_value,
									'options' => $subtypes_label,
									));
			} ?>
		</div>
	</div>


	<?php // TWITTER
		$twitter_consumer_key = elgg_get_plugin_setting('twitter_consumer_key', 'elgg-deck_river');
		$twitter_consumer_secret = elgg_get_plugin_setting('twitter_consumer_secret', 'elgg-deck_river');
		if ($twitter_consumer_key && $twitter_consumer_secret) {
			$class = ($selected != 'twitter')  ? ' hidden': '';
			echo '<div class="tab twitter' .  $class . '"><ul class="box-settings phm"><li>';

			// get twitter account
			elgg_load_library('deck_river:authorize');
			$twitter_account = deck_river_twitter_get_account();

			function displayTwitterAccount($account, $phrase, $class = null) {
				$site_name = elgg_get_site_entity()->name;
				$twitter_user = $account->screen_name;
				$twitter_avatar = $account->avatar;

				// User twitter block
				$img = elgg_view('output/img', array(
					'src' => $twitter_avatar,
					'alt' => $twitter_user,
					'class' => 'twitter-user-info-popup',
					'title' => $twitter_user,
					'width' => '25',
					'height' => '25',
				));
				$twitter_name = '<div class="elgg-river-summary"><span class="twitter-user-info-popup" title="' . $twitter_user . '">' . $twitter_user . '</span>';
				$twitter_name .= '<br/><span class="elgg-river-timestamp">';
				$twitter_name .= elgg_view('output/url', array(
					'href' => 'http://twitter.com/' . $twitter_user,
					'text' => 'http://twitter.com/' . $twitter_user,
					'target' => '_blank',
					'rel' => 'nofollow'
				));
				$twitter_name .= '</span></div>';
				$twitter_name = elgg_view_image_block($img, $twitter_name);

				$add_account = elgg_view('output/url', array(
					'href' => '#',
					'text' => '+',
					'class' => 'add_social_network tooltip s t',
					'title' => elgg_echo('deck_river:network:add:account')
				));

				return elgg_view_module(
					'info',
					'<span class="elgg-river-timestamp">' . $phrase . '</span>',
					$twitter_name . $add_account,
					array(
						'class' => $class
					)
				);
			}

			$options_values = array(
				'searchTweets' => elgg_echo('deck_river:twitter:feed:search:tweets'),
				'searchTweets-popular' => elgg_echo('deck_river:twitter:feed:search:popular'),
				'get_statusesHome_timeline' => elgg_echo('deck_river:twitter:feed:home'),
				'get_statusesMentions_timeline' => elgg_echo('river:mentions'),
				'get_statusesUser_timeline' => elgg_echo('deck_river:twitter:feed:user'),
				'get_direct_messages' => elgg_echo('deck_river:twitter:feed:dm:recept'),
				'get_direct_messagesSent' => elgg_echo('deck_river:twitter:feed:dm:sent'),
				'get_favoritesList' => elgg_echo('deck_river:twitter:feed:favorites'),
			);

			if (!$twitter_account || count($twitter_account) == 0) { // No account registred, send user off to validate account

				$output = elgg_view_module(
					'featured',
					elgg_echo('deck_river:twitter:usersettings:request:title', array($site_name)),
					elgg_echo('deck_river:twitter:usersettings:request'),
					array('class' => 'mtl float')
				);
				$options_values = array( // override values
					'searchTweets' => elgg_echo('deck_river:twitter:feed:search:tweets'),
					'searchTweets-popular' => elgg_echo('deck_river:twitter:feed:search:popular'),
				);

			} else if (count($twitter_account) == 1) { // One account registred

				$output = displayTwitterAccount($twitter_account[0], elgg_echo('deck_river:twitter:your_account', array($site_name)), 'mbn');
				$output .= elgg_view('input/hidden', array(
					'name' => 'twitter-account',
					'value' => $twitter_account[0]->getGUID(),
				));

			} else { // more than one account

				if (!isset($user_river_column_options['account'])) $user_river_column_options['account'] = $twitter_account[0]->getGUID();
				echo '<label>' . elgg_echo('deck_river:twitter:choose:account') . '</label><br />';
				foreach ($twitter_account as $account) {
					$hidden = ($account->getGUID() == $user_river_column_options['account']) ? '' : 'hidden ';
					echo displayTwitterAccount($account, '', $hidden . 'mtm mbs multi ' . $account->getGUID());
					$accounts_name[$account->getGUID()] = $account->screen_name;
				}
				echo elgg_view('input/dropdown', array(
					'name' => 'twitter-account',
					'value' => $user_river_column_options['account'],
					'class' => '',
					'options_values' => $accounts_name
				));

			}

			// select feed
			echo '<label>' . elgg_echo('deck_river:type') . '</label><br />';
			echo elgg_view('input/dropdown', array(
				'name' => 'twitter-type',
				'value' => $selected == 'twitter' ? $user_river_column_options['type'] : 'twitter:search/tweets',
				'class' => 'column-type mts',
				'options_values' => $options_values
			));

			echo '<li class="searchTweets-options searchTweets-popular-options hidden pts"><label>' . elgg_echo('deck_river:search') . '</label><br />';
			echo elgg_view('input/text', array(
				'name' => 'twitter-search',
				'value' => $user_river_column_options['search']
			));
			echo '</li>';

			echo $output;

			echo '</li></ul></div>';
		}

		// FACEBOOK
		$facebook_consumer_key = elgg_get_plugin_setting('facebook_consumer_key', 'elgg-deck_river');
		$facebook_consumer_secret = elgg_get_plugin_setting('facebook_consumer_secret', 'elgg-deck_river');
		//if ($facebook_consumer_key && $facebook_consumer_secret) {
			$class = ($selected != 'facebook')  ? ' hidden': '';
			echo '<div class="tab facebook' .  $class . '"><ul class="box-settings phm">En construction...<li>';
			echo '</li></ul></div>';
		//}

	?>

	<div class="elgg-foot ptm">
	<?php
		echo elgg_view('input/submit', array(
			'name' => 'elgg',
			'value' => elgg_echo('save'),
			'class' => $selected == 'elgg' ? 'elgg-button-submit elgg' : 'elgg-button-submit elgg hidden'
		));

		if ($twitter_consumer_key && $twitter_consumer_secret) {
			echo elgg_view('input/submit', array(
				'name' => 'twitter',
				'value' => elgg_echo('save'),
				'class' => $selected == 'twitter' ? 'elgg-button-submit twitter' : 'elgg-button-submit twitter hidden'
			));
		}

		if (!$new) {
			echo elgg_view('input/submit', array(
					'name' => 'delete',
					'value' => elgg_echo('delete'),
					'class' => 'elgg-button-delete float-alt',
			));
		}
	?>
	</div>

</div>