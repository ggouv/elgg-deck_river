<?php

$username = get_input('user', 'false');
$user = get_user_by_username($username);
elgg_set_page_owner_guid($user->guid);

if (!$user) {
	echo elgg_echo('deck_river:user-not-exist');
	return;
}
?>
<ul class="elgg-tabs elgg-htabs">
	<li class="elgg-state-selected"><a href="#<?php echo $user->guid; ?>-info-profile"><?php echo elgg_echo('profile'); ?></a></li>
	<li><a href="#<?php echo $user->guid; ?>-info-activity"><?php echo elgg_echo('activity'); ?></a></li>
	<li><a href="#<?php echo $user->guid; ?>-info-mentions"><?php echo elgg_echo('river:mentions'); ?></a></li>
</ul>
<ul class="elgg-body">
	<li id="<?php echo $user->guid; ?>-info-profile">
		<div class="elgg-avatar elgg-avatar-large float">
			<a href="<?php echo $user->getURL(); ?>" title="<?php echo $user->username; ?>">
				<span class="gwfb hidden"><br><?php echo elgg_echo('deck_river:go_to_profile'); ?></span>
				<div class="avatar-wrapper center">
					<?php
						echo elgg_view('output/img', array(
							'src' => elgg_format_url($user->getIconURL('large')),
							'alt' => $user->username,
							'title' => $user->username,
							'width' => '200px'
						));
					?>
				</div>
			</a>
		</div>

		<div class="elgg-body plm">
			<h1 class="pts mbm"><?php echo $user->realname; ?></h1>
			<h2 class="mbs" style="font-weight:normal;"><?php echo '@' . $user->username; ?></h2>
			<div><?php echo deck_river_wire_filter($user->briefdescription); ?></div>

<?php 			echo elgg_view('output/url', array(
					'class' => 'elgg-button elgg-button-dropdown',
					'rel' => 'popup',
					'href' => '#twitter-dropdown-menu',
					'text' => 'Twitter'
				));
				$body = '<ul class="elgg-menu elgg-menu-hover"><li></li><ul/>';
				echo elgg_view_module('dropdown', '', $body, array('id' => 'twitter-dropdown-menu'));
?>
			<?php
			if (elgg_is_logged_in() && $user->getGUID() != elgg_get_logged_in_user_guid()) {
				echo '<ul class="elgg-menu profile-action-menu mvm float">';
				if ($user->isFriend()) {
					echo elgg_view('output/url', array(
						'href' => elgg_add_action_tokens_to_url("action/friends/remove?friend={$user->guid}"),
						'text' => elgg_echo('friend:remove'),
						'class' => 'elgg-button elgg-button-action gwfb remove_friend'
					));
				} else {
					echo elgg_view('output/url', array(
						'href' => elgg_add_action_tokens_to_url("action/friends/add?friend={$user->guid}"),
						'text' => elgg_echo('friend:add'),
						'class' => 'elgg-button elgg-button-action gwfb add_friend'
					));
				}
				echo '</ul>';
			}
			?>
		</div>

		<?php
			echo elgg_view_menu('owner_block', array(
				'entity' => $user,
				'class' => 'profile-content-menu tiny',
			));
		?>

		<div id="profile-details" class="elgg-body pll">
			<?php
				echo elgg_view('profile/details');
			?>
		</div>
	</li>
	<li id="<?php echo $user->guid; ?>-info-activity" class="column-river hidden">
		<ul class="column-header hidden" data-network="elgg" data-river_type="entity_river" data-entity="<?php echo $user->guid; ?>"></ul>
		<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
	</li>
	<li id="<?php echo $user->guid; ?>-info-mentions" class="column-river hidden">
		<ul class="column-header hidden" data-network="elgg" data-river_type="entity_mention" data-entity="<?php echo $user->guid; ?>"></ul>
		<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
	</li>
</ul>
