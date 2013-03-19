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
		<?php echo elgg_view_entity_icon($user, 'large', array('class' => 'float', 'href' => $user->getURL())); ?>

			<div class="elgg-body plm">
				<h1 class="pts mbm"><?php echo $user->realname; ?></h1>
				<h2 class="mbs" style="font-weight:normal;"><?php echo '@' . $user->username; ?></h2>
				<div><?php echo deck_river_wire_filter($user->briefdescription); ?></div>

				<?php
				// grab the actions and admin menu items from user hover
				$menu = elgg_trigger_plugin_hook('register', "menu:user_hover", array('entity' => $user), array());
				$builder = new ElggMenuBuilder($menu);
				$menu = $builder->getMenu('priority');
				$actions = elgg_extract('action', $menu, array());
				$admin = elgg_extract('admin', $menu, array());
				$profile_actions = '';
				if (elgg_is_logged_in() && $actions) {
					$profile_actions = '<ul class="elgg-menu profile-action-menu mvm float">';
					foreach ($actions as $action) {
						if ($action->getName() == 'reportuser') {
							//$profile_report = '<ul class="elgg-menu profile-action-menu mtm"><li>' . $action->getContent(array('class' => 'elgg-icon-attention gwfb')) . '</li></ul>';
						} else {
							$action_name = $action->getName();
							$profile_actions .= '<li>' . $action->getContent(array('class' => "elgg-button elgg-button-action gwfb $action_name")) . '</li>';
						}
					}
					$profile_actions .= '</ul>';
				}
				echo $profile_actions;

				?>
			</div>

		<div id="profile-details" class="elgg-body pll">
			<?php echo elgg_view('profile/details'); ?>
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
