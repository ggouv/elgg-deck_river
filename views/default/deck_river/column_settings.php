<?php

// Load Elgg engine
require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/engine/start.php");
global $fb;
$site_url = elgg_get_site_url();



// Get callbacks
$vars['deck-river'] = array(
					'page_filter' => get_input('tab', 'false'),
					'column' => get_input('column', 'false')
				);

echo elgg_view_form('deck_river/column_settings', array('class' => 'deck-river-form-column-settings'), $vars);
