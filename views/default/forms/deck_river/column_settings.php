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
	$selected = explode(':', $user_river_column_options['type']);
	$selected = $selected[0];
	if (!in_array($selected, array('twitter', 'facebook'))) $selected = 'elgg';
		$params = array(
			'type' => 'vertical',
			'class' => 'networks',
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
						'class' => 'column-type mtm',
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
				<?php // once autocomplete is working use that
					/*$groups = elgg_get_logged_in_user_entity()->getGroups("", 0);
					$mygroups = array();
					if (!$user_river_column_options['group']) {
						$mygroups[0] = '';
					}
					foreach ($groups as $group) {
						$mygroups[$group->guid] = $group->name;
					}
					$params = array(
						'name' => 'group',
						'value' => $user_river_column_options['group'],
						'options_values' => $mygroups,
					);
					echo elgg_view('input/dropdown', $params);*/
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

			$access_key = elgg_get_plugin_user_setting('twitter_access_key', $user_guid, 'elgg-deck_river');
			$access_secret = elgg_get_plugin_user_setting('twitter_access_secret', $user_guid, 'elgg-deck_river');

			if (!$access_key || !$access_secret) { // send user off to validate account
				elgg_load_library('deck_river:twitter_async');
				$twitterObjUnAuth = new EpiTwitter($twitter_consumer_key, $twitter_consumer_secret);
				$output = elgg_view_module(
					'featured',
					elgg_echo('deck_river:twitter:usersettings:request:title', array($site_name)),
					elgg_echo('deck_river:twitter:usersettings:request', array($twitterObjUnAuth->getAuthenticateUrl())),
					array('class' => 'mtl float')
				);
				
				$options_values = array(
					'twitter:search/tweets' => elgg_echo('deck_river:twitter:feed:search:tweets'),
					'twitter:users/search' => elgg_echo('deck_river:twitter:feed:users:search')
				);
			} else {
				$twitter_user = elgg_get_plugin_user_setting('twitter_name', $user_guid, 'elgg-deck_river');
				$twitter_avatar = elgg_get_plugin_user_setting('twitter_avatar', $user_guid, 'elgg-deck_river');
				
				// User twitter block
				$img = elgg_view('output/img', array(
					'src' => $twitter_avatar,
					'alt' => $twitter_user,
					'class' => 'twitter-user-info-popup',
					'title' => $twitter_user,
					'width' => '25',
					'height' => '25',
				));
				$twitter_name = '<div class="elgg-river-summary"><span class="twitter-user-info-popup elgg-river-subject">' . $twitter_user . '</span>';
				$twitter_name .= '<br/><span class="elgg-river-timestamp">';
				$twitter_name .= elgg_view('output/url', array(
					'href' => 'http://twitter.com/' . $twitter_user,
					'text' => 'http://twitter.com/' . $twitter_user,
					'target' => '_blank'
				));
				$twitter_name .= '</span></div>';
				$twitter_name = elgg_view_image_block($img, $twitter_name);
				
				$output = elgg_view_module(
					'info',
					'<span class="elgg-river-timestamp">' . elgg_echo('deck_river:twitter:your_account', array($site_name)) . '</span>',
					$twitter_name
				);
				
				$options_values = array(
					'twitter:search/tweets' => elgg_echo('deck_river:twitter:feed:search:tweets'),
					'twitter:users/search' => elgg_echo('deck_river:twitter:feed:users:search')
				);
				
			}
			
			// select feed
			echo '<label>' . elgg_echo('deck_river:type') . '</label><br />';
			echo elgg_view('input/dropdown', array(
				'name' => 'twitter-type',
				'value' => $selected == 'twitter' ? $user_river_column_options['type'] : 'twitter:search/tweets',
				'class' => 'column-type mtm',
				'options_values' => $options_values
			));
			
			echo '<li class="twitter-search-tweets-options hidden pts"><label>' . elgg_echo('deck_river:search') . '</label><br />';
			echo elgg_view('input/text', array(
				'name' => 'search',
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
	
</div>

<div class="elgg-foot phs">
<?php
	echo elgg_view('input/submit', array(
		'name' => 'elgg',
		'value' => elgg_echo('save'),
		'class' => $selected == 'elgg' ? 'elgg-button elgg-button-submit elgg' : 'elgg-button elgg-button-submit elgg hidden'
	));
	
	if ($twitter_consumer_key && $twitter_consumer_secret) {
		echo elgg_view('input/submit', array(
			'name' => 'twitter',
			'value' => elgg_echo('save'),
			'class' => $selected == 'twitter' ? 'elgg-button elgg-button-submit twitter' : 'elgg-button elgg-button-submit twitter hidden'
		));
	}

	if (!$new) {
		echo elgg_view('input/submit', array(
				'name' => 'delete',
				'value' => elgg_echo('delete'),
				'class' => 'elgg-button-delete float-alt mls',
		));
	}
?>
</div>
