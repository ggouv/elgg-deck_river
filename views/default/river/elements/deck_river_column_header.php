<?php
/**
 * @uses $vars['title']       String of the column header
**/
?>

<ul class='column-header'>
	<li>
		<h3><?php echo $vars['title']; ?></h3>
		<?php
			$params = array(
				'text' => elgg_view_icon('refresh'),
				'title' => elgg_echo('deck_river:refresh'),
				'href' => "#",
				'class' => "elgg-column-refresh-button",
			);
			echo elgg_view('output/url', $params);
		?>
		<?php
			$params = array(
				'text' => elgg_view_icon('settings-alt'),
				'title' => elgg_echo('deck_river:edit'),
				'href' => "#",
				'class' => "elgg-column-edit-button",
			);
			echo elgg_view('output/url', $params);
		?>
	</li>
</ul>
