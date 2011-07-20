<?php
global $fb;
//$fb->info($vars);

// Get tab and column
$tab = $vars['deck-river']['page_filter'];
$column = $vars['deck-river']['column'];
// Get the settings of the current user
$owner = elgg_get_logged_in_user_guid();
$user_river_options = unserialize(get_private_setting($owner, 'deck_river_settings'));
$user_river_column_options = $user_river_options[$tab][$column];
?>

<div id='add-deck-column-settings' class='elgg-module elgg-module-popup clearfix'>
	<?php echo elgg_view('input/hidden', array('name' => 'column', 'value' => $column)); ?>
	<?php echo elgg_view('input/hidden', array('name' => 'tab', 'value' => $tab)); ?>
	<div>
		<label><?php echo elgg_echo('title'); ?></label><br />
		<?php echo elgg_view('input/text', array('name' => 'title', 'value' => $user_river_column_options['title'])); ?>
	</div><br />
	<div>
		<label><?php echo elgg_echo('filter'); ?></label><br />
		<?php
		// create checkboxes array
		$types_value = array();
		$registered_entities = elgg_get_config('registered_entities');
		$types_label[] = 'All';
		if (!array_key_exists('types_filter', $user_river_column_options) && !array_key_exists('subtypes_filter', $user_river_column_options) || $user_river_column_options['types_filter'] == 'All' ) $types_value[] = 'All';
		if (!empty($registered_entities)) {
			foreach ($registered_entities as $type => $subtypes) {
				// subtype will always be an array.
				if (!count($subtypes)) {
					$label = str_replace( 'Show ', '', elgg_echo('river:select', array(elgg_echo("item:$type"))) );
					$types_label[$label] .= $type;
					if (in_array($type, $user_river_column_options['types_filter'])) $types_value[] = $type;
				} else {
					foreach ($subtypes as $subtype) {
						$label = str_replace( 'Show ', '', elgg_echo('river:select', array(elgg_echo("item:$type:$subtype"))) );
						$subtypes_label[$label] .= $subtype;
						if (in_array($subtype, $user_river_column_options['subtypes_filter'])) $subtypes_value[] = $subtype;
					}
				}
			}
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
		}
		?>
	</div><br />
	<?php echo elgg_view('input/submit', array(
				'value' => elgg_echo('save'),
				'name' => 'save'
		));
		echo elgg_view('input/submit', array(
				'value' => elgg_echo('delete'),
				'name' => 'delete',
				'class' => 'mls'
		)); ?>
</div>
