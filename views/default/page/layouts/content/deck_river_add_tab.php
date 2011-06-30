<?php
?>

<div id='add-deck-river-tab' class='elgg-module elgg-module-popup elgg-likes-list hidden clearfixelgg-module elgg-module-popup elgg-likes-list hidden clearfix'>
	<div>
		<label><?php echo elgg_echo('title'); ?></label><br />
		<?php echo elgg_view('input/text', array('name' => 'title', 'value' => $title)); ?>
	</div>
	<?php echo elgg_view('input/submit', array('value' => elgg_echo("save"))); ?>
</div>
