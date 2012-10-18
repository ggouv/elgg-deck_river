<?php

$thread = get_input('discussion', 'false');

if (!$thread) {
	echo elgg_echo('deck_river:thread-not-exist');
	return;
}

global $fb; $fb->info($thread);