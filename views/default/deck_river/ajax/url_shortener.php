<?php

$longUrl = get_input('url', 'false');

if (!$longUrl) {
	echo elgg_echo('deck_river:url-not-exist');
	return;
}

if (filter_var($longUrl, FILTER_VALIDATE_URL)) {
	echo goo_gl_short_url($longUrl);
} else {
	echo 'badurl';
}