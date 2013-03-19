<?php

$group_name = get_input('group', 'false');
$group_id = search_group_by_title($group_name);
elgg_set_page_owner_guid($group_id);
$group = get_entity($group_id);
$user = elgg_get_logged_in_user_entity();

if (!$group) {
	echo elgg_echo('deck_river:group-not-exist');
	return;
}
?>
<ul class="elgg-tabs elgg-htabs">
	<li class="elgg-state-selected"><a href="#<?php echo $group_id; ?>-info-profile"><?php echo elgg_echo('profile'); ?></a></li>
	<li><a href="#<?php echo $group_id; ?>-info-activity"><?php echo elgg_echo('activity'); ?></a></li>
	<li><a href="#<?php echo $group_id; ?>-info-mentions"><?php echo elgg_echo('river:mentions'); ?></a></li>
</ul>
<ul class="elgg-body">
	<li id="<?php echo $group_id; ?>-info-profile">
		<?php echo elgg_view_entity_icon($group, 'medium', array('img_class' => 'float')); ?>

			<div class="elgg-body plm">
				<h1 class="mbm"><?php echo $group->name; ?></h1>
				<div><?php echo deck_river_wire_filter($group->briefdescription); ?></div>

				<?php
					$profile_actions = '<ul class="elgg-menu profile-action-menu mvm float">';
					// group members
					if ($group->isMember($user)) {
						if ($group->getOwnerGUID() != $user->guid && !is_user_group_admin($user, $group)) {
							$profile_actions .= '<li class="elgg-menu-item-groups-leave">' .
								'<a href="'. elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/groups/leave?group_id}") . '" class="elgg-button elgg-button-action leave-button gwfb">' .
									elgg_echo('groups:leave') .
								'</a></li>';
						}
					} elseif (elgg_is_logged_in() && !in_array($group->getSubtype(), array('metagroup', 'typogroup'))) {
						if ($group->isPublicMembership() || $group->canEdit()) {
							$profile_actions .= '<li class="elgg-menu-item-groups-join">' .
								'<a href="'. elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/groups/join?group_id}") . '" class="elgg-button elgg-button-action join-button gwfb">' .
									elgg_echo('groups:join') .
								'</a></li>';
						} else {
							// request membership
							$profile_actions .= '<li class="elgg-menu-item-groups-join">' .
								'<a href="'. elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/groups/join?group_id}") . '" class="elgg-button elgg-button-action join-button gwfb">' .
									elgg_echo('groups:joinrequest') .
								'</a></li>';
						}
					}

				echo $profile_actions . '</ul>';

				?>
			</div>

		<div id="<?php echo $group_id; ?>-details" class="elgg-body pll">
			<?php //echo elgg_view('profile/details'); ?>
		</div>
	</li>
	<li id="<?php echo $group_id; ?>-info-activity" class="column-river hidden">
		<ul class="column-header hidden" data-network="elgg" data-river_type="entity_river" data-entity="<?php echo $group_id; ?>"></ul>
		<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
	</li>
		<li id="<?php echo $group_id; ?>-info-mentions" class="column-river hidden">
		<ul class="column-header hidden" data-network="elgg" data-river_type="entity_mention" data-entity="<?php echo $group_id; ?>"></ul>
		<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
	</li>
</ul>
