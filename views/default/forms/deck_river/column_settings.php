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

if ($column == 'new') {
	foreach ($user_river_options[$tab] as $key => $item) {
		$n[] = preg_replace('/[^0-9]+/', '', $key);
	}
	$column = 'column-' . (max($n)+1);
}
$user_river_column_options = $user_river_options[$tab][$column];
$column_title = $user_river_column_options['title'];
?>

<?php echo elgg_view('input/hidden', array('name' => 'column', 'value' => $column)); ?>
<?php echo elgg_view('input/hidden', array('name' => 'tab', 'value' => $tab)); ?>

<div class='elgg-head'>
	<?php
		if ( $column_title == '' ) {
			echo '<h3>' . elgg_echo('deck_river:add-column') .'</h3>';
		} else {
			echo '<h3>' . elgg_echo('deck_river:settings', array($column_title)) .'</h3>';
		}

		$params = array(
			'text' => elgg_view_icon('delete-alt'),
		);
		echo elgg_view('output/url', $params);
	?>
</div>

<div id='deck-column-settings' class='pas'>
	<?php
	$selected = $user_river_column_options['type'];
	if (!in_array($selected, array('twitter', 'facebook'))) $selected = 'ggouv';
		$params = array(
			'type' => 'vertical',
			'class' => 'networks',
			'tabs' => array(
				array('title' => elgg_get_site_entity()->name, 'link_class' => 'ggouv', 'url' => "#", 'selected' => $selected == 'ggouv' ? true : false),
				array('title' => elgg_echo('Twitter'), 'link_class' => 'twitter', 'url' => "#", 'selected' => $selected == 'twitter' ? true : false),
				array('title' => elgg_echo('Facebook'), 'link_class' => 'facebook', 'url' => "#", 'selected' => $selected == 'facebook' ? true : false),
			)
		);
		echo elgg_view('navigation/tabs', $params);
	?>
	
	
	<div class="ggouv<?php if ($selected != 'ggouv') echo ' hidden'; ?>">
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
						'class' => 'column-type',
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
					$groups = elgg_get_logged_in_user_entity()->getGroups("", 0);
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
					echo elgg_view('input/dropdown', $params);
					?>
			</li>
		</ul>
		
		<div class='filter plm'>
			<label><?php echo elgg_echo('deck_river:filter'); ?></label><br />
			<?php
			// create checkboxes array
			$types_value = array();
			$registered_entities = elgg_get_config('registered_entities');
			$types_label[elgg_echo('all')] = 'All';
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
									));
				echo elgg_view('input/checkboxes', array(
									'name' => 'filters_subtypes',
									'value' => $subtypes_value,
									'options' => $subtypes_label,
									));
			} ?>
		</div>
	</div>
	
	<div class="twitter<?php if ($selected != 'twitter') echo ' hidden'; ?>">
		nrstaénprs
	</div>
	
</div>

<div>
<?php
	echo elgg_view('input/submit', array(
		'name' => 'ggouv',
		'value' => elgg_echo('save'),
		'class' => $selected == 'ggouv' ? 'elgg-button elgg-button-submit ggouv' : 'elgg-button elgg-button-submit ggouv hidden'
	));
	echo elgg_view('input/submit', array(
		'name' => 'twitter',
		'value' => elgg_echo('save'),
		'class' => $selected == 'twitter' ? 'elgg-button elgg-button-submit twitter' : 'elgg-button elgg-button-submit twitter hidden'
	));
	if ($new != 'true') {
		echo elgg_view('input/submit', array(
				'name' => 'delete',
				'value' => elgg_echo('delete'),
				'class' => 'elgg-button-delete float-alt mls',
		));
	}
?>
</div>
