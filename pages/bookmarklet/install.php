<?php
/* install bookmarklet */

$content = elgg_echo("bookmarks:bookmarklet:description");

$content .= "<a href=\"javascript:(function(){var%20w=795;var%20h=209;var%20x=Number((window.screen.width-w)/2);var%20y=Number((window.screen.height-h)/2);window.open('" . elgg_get_site_url() . "bookmarklet/popup?url='+encodeURIComponent(location.href)+'&title='+encodeURIComponent(document.title),'','width='+w+',height='+h+',left='+x+',top='+y+',scrollbars=no');})();
\">" . elgg_echo('bookmarklet') . '</a>';

$title = elgg_echo('bookmarklet');

$body = elgg_view_layout('content', array(
	'filter' => false,
	'content' => $content,
	'title' => $title,
));

echo elgg_view_page($title, $body);