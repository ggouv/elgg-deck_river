<?php
/**
 * Wire add form body
 */

$user = elgg_get_logged_in_user_entity();

if (!$user) {
	return false;
}

$accounts = array();
// get and format twitter accounts
elgg_load_library('deck_river:authorize');
$twitter_accounts = deck_river_twitter_get_account();

foreach ($twitter_accounts as $account) {
	$avatar = elgg_view('output/img', array(
		'src' => $account->avatar,
		'alt' => $account->screen_name,
		'class' => 'float',
		'width' => '25px',
		'height' => '25px'
	));
	$info = elgg_echo('deck-river:twitter:account');
	$info .= '<div class="elgg-river-summary"><span class="twitter-user-info-popup" title="' . $account->screen_name . '">' . $account->screen_name . '</span>';
	$info .= '<br/><span class="elgg-river-timestamp">';
	$info .= elgg_view('output/url', array(
		'href' => 'http://twitter.com/' . $account->screen_name,
		'text' => 'http://twitter.com/' . $account->screen_name,
		'target' => '_blank',
		'rel' => 'nofollow'
	));
	$info .= '</span></div>';
	if (true) {
		$pin = '<span class="elgg-icon elgg-icon-push-pin tooltip w" title="' . htmlspecialchars(elgg_echo('deck-river:network:pin')) . '"></span>';
	}
	$accounts[$account->getGUID()] = <<<HTML
<div class="net-profile float mlm twitter">
	<input type="hidden" value="{$account->getGUID()}" name="_networks[]">
	<ul>
		<span class="elgg-icon elgg-icon-delete pas hidden"></span>
		<div class="elgg-module-popup hidden">
			<div class="triangle"></div>
			<div class="pin float-alt">$pin</div>
			$info
		</div>
	</ul>
	$avatar
	<span class="network gwf">1</span>
</div>
HTML;
}

?>

<div id="thewire-header">
	<div id="thewire-textarea-border"></div>
	<textarea id="thewire-textarea" name="body"></textarea>
	<div class="options hidden">
		<div class="url-shortener">
			<?php
				echo elgg_view('input/text', array(
					'value' => elgg_echo('deck-river:reduce_url:string'),
				));
				echo '<span class="elgg-icon elgg-icon-delete hidden tooltip s" title="' . elgg_echo('deck-river:clean_url') . '"></span>';
				echo elgg_view('input/button', array(
					'value' => elgg_echo('deck-river:copy_url'),
					'class' => 'elgg-button-action hidden'
				));
				echo elgg_view('input/button', array(
					'value' => elgg_echo('deck-river:reduce_url'),
					'class' => 'elgg-button-submit'
				));
			?>
		</div>
	</div>
	<div id="thewire-characters-remaining">
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
	<div class="selected-profile pvs">
		<div class="net-profile float mls ggouv">
			<input type="hidden" value="<?php echo $user->getGUID(); ?>" name="networks[]">
			<ul>
				<span class="elgg-icon elgg-icon-delete pas hidden"></span>
				<div class="elgg-module-popup hidden">
					<div class="triangle"></div>
					<?php
						echo elgg_echo('deck-river:ggouv:account');
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
			$user_deck_river_pinned_accounts = unserialize(get_private_setting($user->getGUID(), 'deck_river_pinned_accounts'));
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
		<div class="helper"><div><?php echo elgg_echo('deck-river:add:network:slide'); ?></div></div>
		<div class="content">
			<?php
				foreach ($accounts as $account_output) {
					echo $account_output;
				}
			?>
		</div>
	</div>
</div>


