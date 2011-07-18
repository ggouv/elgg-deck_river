<?php
global $fb;
//$fb->info($vars);

// Get the settings of the current user
$owner = elgg_get_logged_in_user_guid();
$user_river_options = unserialize(get_private_setting($owner, 'deck_river_settings'));
// Get tab and column
$tab = $vars['deck-river']['page_filter'];
$column = $vars['deck-river']['column'];
$fb->info($user_river_options);
?>

<div id='add-deck-column-settings' class='elgg-module elgg-module-popup clearfix'>
	<?php echo elgg_view('input/hidden', array('name' => 'column', 'value' => $column)); ?>
	<?php echo elgg_view('input/hidden', array('name' => 'tab', 'value' => $tab)); ?>
	<div>
		<label><?php echo elgg_echo('title'); ?></label><br />
		<?php echo elgg_view('input/text', array('name' => 'title', 'value' => $user_river_options[$tab][$column]['title'])); ?>
	</div><br />
	<div>
		<label><?php echo elgg_echo('filter'); ?></label><br />
		<?php
		// create checkboxes array
		$options = array();
		$registered_entities = elgg_get_config('registered_entities');

		$types_checkboxes = elgg_view('input/checkboxes', array(
									'name' => 'All',
									'value' => 'All',
									'options' => array('All'),
									'checked' => TRUE,
									));
		$types_checkboxes .= elgg_view('input/checkboxes', array(
									'name' => 'None',
									'value' => 'None',
									));
		if (!empty($registered_entities)) {
			foreach ($registered_entities as $type => $subtypes) {
				// subtype will always be an array.
				if (!count($subtypes)) {
					$label = str_replace( 'Show ', '', elgg_echo('river:select', array(elgg_echo("item:$type"))) );
					$types_checkboxes .= elgg_view('input/checkboxes', array(
									'name' => $label,
									'value' => $label,
									));
				} else {
					foreach ($subtypes as $subtype) {
						$subtypes_label[] = str_replace( 'Show ', '', elgg_echo('river:select', array(elgg_echo("item:$type:$subtype"))) );
						if (in_array($user_river_options[$tab][$column]['type_subtype_pairs']['object'])) $value = TRUE;
					}
				}
			}
			echo $types_checkboxes;
			foreach ($types_label as $type) {
$fb->info($type);
				echo elgg_view('input/checkboxes', array(
									'name' => $type,
									'value' => $type,
									));
			}
			echo '<hr>';
			foreach ($subtypes_label as $subtype) { 
				echo elgg_view('input/checkbox', array(
									'name' => 'filters_subtypes',
									'options' => $subtype,
									));
			}
		}
		?>
	</div><br />
	<?php echo elgg_view('input/submit', array('value' => elgg_echo('save'))); ?>
</div>
