<?php
/**
 * Group of output form (button, input, dropdown menu...)
 * Displays a grouped output view
 *
 * @package Elgg-deck_river
 *
 * @uses $vars['group'] An array of group views to dispaly
 *
 */

echo '<div class="output-group">';

foreach ($vars['group'] as $key => $value) {
	echo $value;
}

echo '</div>';