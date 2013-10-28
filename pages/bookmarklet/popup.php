<?php
/* bookmarklet */

if (!elgg_is_logged_in()) {

$body = elgg_view('core/account/login_box');
$nolog = true;
} else {

	$url = get_input('url', 'false');
	$title = get_input('title', 'false');

	if (filter_var($url, FILTER_VALIDATE_URL)) {
		$url_tiny = goo_gl_short_url($url);
	} else {
		$url_tiny = 'badurl';
	}

	$content = $title . ' ' . $url_tiny;
	$body = elgg_view_form('deck_river/wire_input', '', array('bookmarklet' => $content));

}

$css = elgg_view('css/elements/reset');
$css .= elgg_view('css/elements/core');
$css .= elgg_view('css/elements/typography', $vars);
$css .= elgg_view('css/elements/forms', $vars);
$css .= elgg_view('css/elements/buttons', $vars);
$css .= elgg_view('css/elements/icons', $vars);
//$css .= elgg_view('css/elements/navigation', $vars);
//$css .= elgg_view('css/elements/modules', $vars);
$css .= elgg_view('css/elements/components', $vars);
$css .= elgg_view('css/elements/layout', $vars);
//$css .= elgg_view('css/elements/misc', $vars);
$css .= elgg_view('css/elements/helpers');
$css .= elgg_view('deck_river/css');
$css .= elgg_view('ggouv_template/css');


// clear loaded external javascript
global $CONFIG;
//var_dump($CONFIG->externals_map['js']);
foreach($CONFIG->externals_map['js'] as $js) {
	$js->loaded = false;
}

//var_dump($CONFIG->views->extensions['js/elgg']);

elgg_extend_view('bookmarklet_js', 'js/elgg');
elgg_register_js('jquery.tipsy', "/mod/elgg-ggouv_template/vendors/jquery.tipsy.min.js");

elgg_load_js('jquery');
elgg_load_js('jquery-ui');
elgg_load_js('jquery.caretposition');
elgg_load_js('jquery.tipsy');

header("Content-type: text/html; charset=UTF-8");
$lang = get_current_language();
$window_title = elgg_get_config('sitename');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $lang; ?>" lang="<?php echo $lang; ?>" class="bookmarklet">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">

	<title><?php echo $window_title; ?></title>

	<style type="text/css">
		<?php echo $css; ?>
		body {
			position: fixed;
			overflow: hidden;
		}
		#elgg-page-header-container {
			margin-left: -1px;
		}
		.elgg-page-header {
			height: 207px;
		}
		#elgg-page-header-container .elgg-inner {
			width: 580px;
		}
		#thewire-header {
			height: 163px;
		}
		#thewire-network .non-pinned {
			background: none;
			-webkit-box-shadow: none;
			box-shadow: none;
		}
		#thewire-network .net-profiles-wrapper {
			border-radius: 0 0 6px 6px;
		}
		#thewire-network .net-profiles {
			height: 135px;
		}
		.elgg-module-popup {
			z-index: 9999;
			margin-bottom: 0;
			padding: 5px;
		}
	</style>

</head>
<body>
	<div id="elgg-page-header-container">
		<div class="elgg-page-header">
			<div class="elgg-inner">
				<?php echo $body; ?>
			</div>
		</div>
	</div>

	<?php
		echo elgg_view('deck_river/mustaches/linkbox');
		foreach (elgg_get_loaded_js('head') as $script) {
			echo '<script type="text/javascript" src="' . $script . '"></script>';
		}
	?>

	<script type="text/javascript">
		<?php echo elgg_view('bookmarklet_js');
			if (!$nolog) {
		?>
		var FBappID = <?php echo elgg_get_plugin_setting('facebook_app_id', 'elgg-deck_river') ?>;
		var site_shorturl = <?php $site_shorturl = elgg_get_plugin_setting('site_shorturl', 'elgg-deck_river'); echo json_encode($site_shorturl ? $site_shorturl : false); ?>;
		elgg.provide('elgg.deck_river');
		<?php
			echo elgg_view('deck_river/js/thewire');
			echo elgg_view('deck_river/js/shortener_url');
			echo elgg_view('deck_river/js/tools');
		?>
		elgg.thewire.init();
		elgg.thewire.resize();
		elgg.deck_river.ShortenerUrlInit();
		linkParsed = '<?php echo $url; ?>';
		elgg.thewire.scrapToLinkBox(linkParsed);
		// Inintialize tooltips
		$(window).load(function() {
			$('.tooltip').tipsy({
				live: true,
				offset: function() {
					if ($(this).hasClass('o8')) return 8;
					return 5;
				},
				fade: true,
				html: true,
				delayIn: 500,
				gravity: function() {
					var t = $(this);

					if (t.hasClass('nw')) return 'nw';
					if (t.hasClass('n')) return 'n';
					if (t.hasClass('ne')) return 'ne';
					if (t.hasClass('w')) return 'e';
					if (t.hasClass('e')) return 'e';
					if (t.hasClass('sw')) return 'sw';
					if (t.hasClass('s')) return 's';
					if (t.hasClass('se')) return 'se';
					return 'n';
				}
			});
		});
		<?php } ?>
	</script>

	<?php global $dbcalls; echo '<script type="text/javascript">console.log("'.$dbcalls.'", "dbcalls");</script>'; // uncomment to see number of SQL calls ?>
</body>
</html>

