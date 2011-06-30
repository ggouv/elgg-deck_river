<?php
?>

<ul class='column-header'>
	<li>
		<h3><?php echo $vars['title']; ?></h3>
		<?php
			$params = array(
				'text' => elgg_view_icon('refresh'),
				'title' => elgg_echo('widget:edit'),
				'href' => "#widget-refresh-$widget->guid",
				'class' => "elgg-column-refresh-button",
			);
			echo elgg_view('output/url', $params);
		?>
		<?php
			$params = array(
				'text' => elgg_view_icon('settings-alt'),
				'title' => elgg_echo('widget:edit'),
				'href' => "#widget-edit-$widget->guid",
				'rel' => 'popup',
				'class' => "elgg-toggler elgg-column-edit-button",
			);
			echo elgg_view('output/url', $params);
		?>
	</li>
</ul>
