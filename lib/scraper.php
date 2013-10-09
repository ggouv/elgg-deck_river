<?php

include '../vendors/url-scraper-php/website_parser.php';

$url = $_GET['url'];

//Instance of WebsiteParser
$parser = new WebsiteParser($url);

//Get all metadatas
$metatags = $parser->getMetaTags();

//Get all hyper links
$links = $parser->getHrefLinks();

//Get all image sources
$images = $parser->getImageSources();

echo json_encode(array(
	'url' => $url,
	'links' => $links,
	'metatags' => $metatags,
	'images' => $images
));