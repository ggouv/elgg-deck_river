<?php

$username = get_input('user', 'false');
$user = get_user_by_username($username);
elgg_set_page_owner_guid($user->guid);
?>

<div class="elgg-head">
	<h3><?php echo elgg_echo('deck_river:user-info-header'); ?></h3>
		<?php echo elgg_view('output/url', array(
			'text' => elgg_view_icon('delete-alt'),
		)); ?>
</div>
<?php 
if (!$user) {
	echo elgg_echo('deck_river:user-not-exist');
	return;
}
?>
<ul class="elgg-tabs elgg-htabs">
	<li class=" elgg-state-selected"><a href="#user-info-profile"><?php echo elgg_echo('profil'); ?></a></li>
	<li><a href="#user-info-activity"><?php echo elgg_echo('activity'); ?></a></li>
</ul>
<ul class="elgg-body">
	<li id="user-info-profile">
		<?php echo elgg_view_entity_icon($user, 'large', array('class' => 'float')); ?>
		
			<div class="elgg-body plm">
				<h1><?php echo $user->realname; ?></h1>
				<h2 class="mvm" style="font-weight:normal;"><?php echo '@' . $user->username; ?></h2>
				<?php echo $user->briefdescription; ?>
				
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
							$profile_report = '<ul class="elgg-menu profile-action-menu mtm"><li>' . $action->getContent(array('class' => 'elgg-icon-attention gwfb')) . '</li></ul>';
						} else {
							$profile_actions .= '<li>' . $action->getContent(array('class' => 'elgg-button elgg-button-action')) . '</li>';
						}
					}
					$profile_actions .= '</ul>';
				}
				echo $profile_actions;
				
				?>
			</div>

		<ul class="groups-stats float"></ul>
		<div id="profile-details" class="elgg-body pll">
			<?php echo elgg_view('profile/details'); ?>
		</div>
	</li>
	<li id="user-info-activity" class="hidden">
		<ul class="elgg-river elgg-list" data-user="<?php echo $user->guid; ?>"><div class="elgg-ajax-loader"></div></ul>
	</li>
</ul>
