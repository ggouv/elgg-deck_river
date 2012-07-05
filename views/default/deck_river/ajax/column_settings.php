<?php

// Get callbacks
$vars['deck-river'] = array(
					'tab' => get_input('tab', 'false'),
					'column' => get_input('column', 'false'),
					'new' => get_input('new', 'false')
				);

echo elgg_view_form('deck_river/column_settings', array('class' => 'deck-river-form-column-settings'), $vars);
