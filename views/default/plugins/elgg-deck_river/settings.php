<?php
/**
 * Elgg-deck_river plugin settings
 */

// set default value

if (!isset($vars['entity']->min_width_column)) {
	$vars['entity']->min_width_column = '300';
}

if (!isset($vars['entity']->max_nbr_column)) {
	$vars['entity']->max_nbr_column = '10';
}

$default = elgg_echo('deck_river:settings:default_column:default');
if (!isset($vars['entity']->default_columns) || empty($vars['entity']->default_columns)) {
	$vars['entity']->default_columns = $default;
}

$default = elgg_echo('deck_river:settings:column_type:default');
if (!isset($vars['entity']->column_type) || empty($vars['entity']->column_type)) {
	$vars['entity']->column_type = $default;
}


$min_width_column_string = elgg_echo('deck_river:settings:min_width_column');
$min_width_column_view = elgg_view('input/text', array(
	'name' => 'params[min_width_column]',
	'value' => $vars['entity']->min_width_column,
	'class' => 'elgg-input-thin',
));

$max_nbr_column_string = elgg_echo('deck_river:settings:max_nbr_column');
$max_nbr_column_view = elgg_view('input/text', array(
	'name' => 'params[max_nbr_column]',
	'value' => $vars['entity']->max_nbr_column,
	'class' => 'elgg-input-thin',
));

$default_columns_string = elgg_echo('deck_river:settings:default_column');
$default_columns_view = elgg_view('input/plaintext', array(
	'name' => 'params[default_columns]',
	'value' => $vars['entity']->default_columns
));
$default_columns_string_default_params = '<strong>' . elgg_echo('deck_river:settings:default_column_default_params') . '</strong><br />' . $default;

$column_type_string = elgg_echo('deck_river:settings:column_type');
$column_type_view = elgg_view('input/plaintext', array(
	'name' => 'params[column_type]',
	'value' => $vars['entity']->column_type
));

$keys_to_merge_string = elgg_echo('deck_river:settings:keys_to_merge');
$keys_to_merge_view = elgg_view('input/text', array(
	'name' => 'params[keys_to_merge]',
	'value' => $vars['entity']->keys_to_merge
));
$registered_entities = elgg_get_config('registered_entities');
foreach ($registered_entities as $type => $subtypes) {
	if (!count($subtypes)) {
		$label[] = $type;
	} else {
		foreach ($subtypes as $subtype) {
			$label[] = $subtype;
		}
	}
}
$keys_to_merge_string_register_entity = elgg_echo('deck_river:settings:keys_to_merge_string_register_entity') . '</strong><br />' . implode(' - ', $label);



$reset_user_string = elgg_echo('deck_river:settings:reset_user');
$reset_user_view = elgg_view('input/text', array(
	'name' => 'reset_user',
	'value' => '',
	'class' => 'elgg-input-thin',
));


/* short url */
if (!isset($vars['entity']->site_shorturl) || empty($vars['entity']->site_shorturl)) {
	$vars['entity']->site_shorturl = '';
}

$site_shorturl_string = elgg_echo('deck_river:settings:site_shorturl');
$site_shorturl_view = elgg_view('input/text', array(
	'name' => 'params[site_shorturl]',
	'value' => $vars['entity']->site_shorturl
));

/* google shortener */
if (!isset($vars['entity']->googleApiKey) || empty($vars['entity']->googleApiKey)) {
	$vars['entity']->googleApiKey = '';
}

$googleApiKey_string = elgg_echo('deck_river:settings:googleApiKey');
$googleApiKey_view = elgg_view('input/text', array(
	'name' => 'params[googleApiKey]',
	'value' => $vars['entity']->googleApiKey
));


/* twitter */
if (!isset($vars['entity']->twitter_consumer_key) || empty($vars['entity']->twitter_consumer_key)) {
	$vars['entity']->twitter_consumer_key = '';
}

if (!isset($vars['entity']->twitter_consumer_secret) || empty($vars['entity']->twitter_consumer_secret)) {
	$vars['entity']->twitter_consumer_secret = '';
}

$twitter_consumer_key_string = elgg_echo('deck_river:settings:twitter_consumer_key');
$twitter_consumer_key_view = elgg_view('input/text', array(
	'name' => 'params[twitter_consumer_key]',
	'value' => $vars['entity']->twitter_consumer_key
));

$twitter_consumer_secret_string = elgg_echo('deck_river:settings:twitter_consumer_secret');
$twitter_consumer_secret_view = elgg_view('input/text', array(
	'name' => 'params[twitter_consumer_secret]',
	'value' => $vars['entity']->twitter_consumer_secret
));


/* facebook */
if (!isset($vars['entity']->facebook_app_id) || empty($vars['entity']->facebook_app_id)) {
	$vars['entity']->facebook_app_id = '';
}

if (!isset($vars['entity']->facebook_app_secret) || empty($vars['entity']->facebook_app_secret)) {
	$vars['entity']->facebook_app_secret = '';
}

$facebook_app_id_string = elgg_echo('deck_river:settings:facebook_app_id');
$facebook_app_id_view = elgg_view('input/text', array(
	'name' => 'params[facebook_app_id]',
	'value' => $vars['entity']->facebook_app_id
));

$facebook_app_secret_string = elgg_echo('deck_river:settings:facebook_app_secret');
$facebook_app_secret_view = elgg_view('input/text', array(
	'name' => 'params[facebook_app_secret]',
	'value' => $vars['entity']->facebook_app_secret
));



echo <<<__HTML
<br/>
<div><label>$min_width_column_string</label><br />$min_width_column_view</div>
<div><label>$max_nbr_column_string</label><br />$max_nbr_column_view</div>
<div><label>$default_columns_string</label><br />$default_columns_view<br /><span style='font-size:0.85em;color:#999;'>$default_columns_string_default_params</span></div>
<div><label>$column_type_string</label><br />$column_type_view</div>
<div><label>$keys_to_merge_string</label><br />$keys_to_merge_view<br /><span style='font-size:0.85em;color:#999;'>$keys_to_merge_string_register_entity</span></div>
<div><label>$reset_user_string</label><br />$reset_user_view</div>

<br/><hr/><br/>
<h3>The short url of your site</h3><br/>
<div><label>$site_shorturl_string</label><br />$site_shorturl_view</div>

<br/><hr/><br/>
<h3>Google shortener</h3><br/>
<div><label>$googleApiKey_string</label><br />$googleApiKey_view</div>

<br/><hr/><br/>
<h3>Twitter</h3><br/>
<div><label>$twitter_consumer_key_string</label><br />$twitter_consumer_key_view</div>
<div><label>$twitter_consumer_secret_string</label><br />$twitter_consumer_secret_view</div>

<br/><hr/><br/>
<h3>Facebook</h3><br/>
<div><label>$facebook_app_id_string</label><br />$facebook_app_id_view</div>
<div><label>$facebook_app_secret_string</label><br />$facebook_app_secret_view</div>
__HTML;
