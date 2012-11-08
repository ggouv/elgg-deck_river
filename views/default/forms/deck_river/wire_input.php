<?php
/**
 * Wire add form body
 *
 * @uses $vars['post']
 */

$user = elgg_get_logged_in_user_entity();

$post = elgg_extract('post', $vars);

$text = elgg_echo('post');
if ($post) {
	$text = elgg_echo('thewire:reply');
}

if ($post) {
	echo elgg_view('input/hidden', array(
		'name' => 'parent_guid',
		'value' => $post->guid,
	));
}
?>

<div id="thewire-header">
	<div id="thewire-textarea-border"></div>
	<textarea id="thewire-textarea" name="body"></textarea>
	<div class="url-shortener hidden">
		<?php
			echo elgg_view('input/text', array(
				'value' => elgg_echo('deck-river:reduce_url:string'),
			));
			echo elgg_view('input/button', array(
				'value' => elgg_echo('deck-river:reduce_url'),
				'class' => 'elgg-button-submit'
			));
		?>
	</div>
	<div id="thewire-characters-remaining">
		<span>0</span>
	</div>
	<div id="thewire-textarea-bottom"></div>
	<div class="thewire-button gwfb">
	<?php
		echo elgg_view('input/submit', array(
			'value' => elgg_echo('send'),
			'id' => 'thewire-submit-button',
		));
	?>
	</div>
</div>

<div id="thewire-network">
	<div class="selected-profile pvs">
		<div class="net-profile float mls ggouv">
			<span class="elgg-icon elgg-icon-delete pas hidden"></span>
			<input type="hidden" value="true" name="network_ggouv">
			<?php
				echo elgg_view('output/img', array(
					'src' => elgg_format_url($user->getIconURL('tiny')),
					'alt' => $user->username,
					'title' => $user->username,
					'class' => 'float',
				));
				echo '<span class="network"></span>';
			?>
		</div><!--
		<div class="net-profile float mlm twitter">
			<span class="elgg-icon elgg-icon-delete pas hidden"></span>
			<input type="hidden" value="true" name="network_twitter">
			<?php
				echo elgg_view('output/img', array(
					'src' => elgg_format_url($user->getIconURL('tiny')),
					'alt' => $user->username,
					'title' => $user->username,
					'class' => 'float',
				));
				echo '<span class="network gwf">1</span>';
			?>
		</div> -->
	</div>
</div>