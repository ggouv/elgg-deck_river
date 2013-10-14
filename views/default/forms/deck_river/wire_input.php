<?php
/**
 * Wire add form body
 */

$user = elgg_get_logged_in_user_entity();

if (!$user) {
	return false;
}

$user_deck_river_pinned_accounts = unserialize(get_private_setting($user->getGUID(), 'deck_river_pinned_accounts'));

$accounts = array();
// get and format twitter accounts
elgg_load_library('deck_river:authorize');
$all_accounts = deck_river_get_networks_account('all');

foreach ($all_accounts as $account) {
	$accounts[$account->getGUID()] = elgg_view_entity($account, array(
		'view_type' => 'in_network_box',
		'pinned' => in_array($account->getGUID(), $user_deck_river_pinned_accounts) ? true : false,
	));
}

?>

<div id="thewire-header">
	<div id="thewire-textarea-border"></div>
	<textarea id="thewire-textarea" name="body"></textarea>
	<div class="options hidden">
		<div class="responseTo hidden tooltip s" title="<?php echo elgg_echo('responseToHelper:delete');?>"></div>
		<input class="parent" type="hidden" name="">
		<div id="linkbox" class="hidden phm pvs">
			<?php echo elgg_view('graphics/ajax_loader', array('hidden' => false)); ?>
		</div>
		<div class="url-shortener">
			<?php
				echo elgg_view('output/group', array(
						'group' => array(elgg_view('input/text', array(
								'value' => elgg_echo('deck-river:reduce_url:string')
							)),
							'<span class="elgg-icon elgg-icon-delete hidden tooltip s link" title="' . elgg_echo('deck-river:clean_url') . '"></span>',
							elgg_view('input/button', array(
								'value' => elgg_echo('deck-river:copy_url'),
								'class' => 'elgg-button-action hidden'
							)),
							elgg_view('input/button', array(
								'value' => elgg_echo('deck-river:reduce_url'),
								'class' => 'elgg-button-submit'
							))
						)
					));
			?>
		</div>
	</div>
	<div id="thewire-characters-remaining" class="reverse-border">
		<span>0</span>
	</div>
	<div id="thewire-textarea-bottom"></div>
	<div id="submit-loader" class="hidden response-loader"></div>
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
	<div class="selected-profile">
		<div class="net-profile float mls elgg ggouv">
			<input type="hidden" value="<?php echo $user->getGUID(); ?>" name="networks[]" data-network="elgg">
			<ul>
				<span class="elgg-icon elgg-icon-delete pas hidden"></span>
				<div class="elgg-module-popup hidden">
					<div class="triangle"></div>
					<?php
						echo '<br/><a title="' . $user->username . '" href="#" class="user-info-popup">@' . $user->username . '</a>';
					?>
				</div>
			</ul>
			<?php
				echo elgg_view('output/img', array(
					'src' => elgg_format_url($user->getIconURL('tiny')),
					'alt' => $user->username,
					'title' => $user->username,
					'class' => 'float',
				));
			?>
			<span class="network"></span>
		</div>
		<?php
			foreach ($accounts as $account_guid => $account_output) {
				if (in_array($account_guid, $user_deck_river_pinned_accounts)) {
					echo $account_output;
					unset($accounts[$account_guid]);
				}
			}
		?>
	</div>
	<div class="more_networks gwf tooltip w t5 phs" title="<?php echo elgg_echo('deck-river:add:network'); ?>">+</div>
	<div class="non-pinned clearfloat hidden">
		<div class="helper tooltip w" title="<?php echo htmlspecialchars(elgg_echo('deck-river:add:network:helper')); ?>"><div><?php echo elgg_echo('deck-river:add:network:slide'); ?></div></div>
		<div class="content">
			<div class="net-profiles">
			<?php
				foreach ($accounts as $account_output) {
					echo $account_output;
				}
			?>
			</div>
			<div class="footer">
				<ul>
					<li>
					<?php
						echo elgg_view('output/url', array(
							'href' => '#',
							'text' => elgg_echo('deck_river:network:add:account'),
							'class' => 'add_social_network'
						));
					?>
					</li>
					<li>
					<?php
						echo elgg_view('output/url', array(
							'href' => '/authorize/applications/' . $user->username,
							'text' => elgg_echo('deck_river:network:manage_account')
						));
					?>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>


