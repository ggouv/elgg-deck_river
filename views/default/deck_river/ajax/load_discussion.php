<?php

$thread_id = get_input('discussion', 'false');

if (!$thread_id || !get_metastring_id($thread_id)) {
	echo elgg_echo('deck_river:thread-not-exist');
} else {
	elgg_load_library('deck_river:river_loader');
	echo load_wire_discussion($thread_id);
}
