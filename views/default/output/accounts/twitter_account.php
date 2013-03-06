<?php
/**
 *	Elgg-deck_river plugin
 *	@package elgg-deck_river
 *	@author Emmanuel Salomon @ManUtopiK
 *	@license GNU Affero General Public License, version 3 or late
 *	@link https://github.com/ggouv/elgg-deck_river
 *
 * @uses $vars['account'] The account to display
 * @uses $vars['pinned'] Whether the account is pinned. Default to false.
 * @uses $vars['link'] Base link to the accont
 **/

$account = elgg_extract('account', $vars);
$pinned = elgg_extract('pinned', $vars, false);

global $fb; $fb->info($account);
if ($pinned) {
	$pinned = ' pinned';
	$input_name = 'networks[]';
} else {
	$input_name = '_networks[]';
}

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

$pin_tooltip = htmlspecialchars(elgg_echo('deck-river:network:pin'));

$avatar = elgg_view('output/img', array(
	'src' => $account->avatar,
	'alt' => $account->screen_name,
	'class' => 'float',
	'width' => '25px',
	'height' => '25px'
));

$output = <<<HTML
<div class="net-profile float mlm twitter$pinned">
	<input type="hidden" value="{$account->getGUID()}" name="$input_name">
	<ul>
		<span class="elgg-icon elgg-icon-delete pas hidden"></span>
		<div class="elgg-module-popup hidden">
			<div class="triangle"></div>
			<div class="pin float-alt">
			<span class="elgg-icon elgg-icon-push-pin tooltip w" title="$pin_tooltip"></span>
			</div>
			$info
		</div>
	</ul>
	$avatar
	<span class="network gwf">1</span>
</div>
HTML;

echo $output;