<?php
/**
 *	Elgg-deck_river plugin
 *	@package elgg-deck_river
 *	@author Emmanuel Salomon @ManUtopiK
 *	@license GNU Affero General Public License, version 3 or late
 *	@link https://github.com/ggouv/elgg-deck_river
 *
 * View for facebook_account object
 *
 * @uses $vars['entity'] The account to display
 * @uses $vars['view_type'] View type. Default full. in_network_box or in_column_settings.
 * @uses $vars['pinned'] Whether the account is pinned. Default to false.
 **/


$account = elgg_extract('entity', $vars);
$view = elgg_extract('view_type', $vars, false);
$pinned = elgg_extract('pinned', $vars, false);

$avatar = elgg_view('output/img', array(
	'src' => $account->icon,
	'alt' => $account->username,
	'class' => 'float',
));

if ($view === 'in_network_box') {
	if ($pinned) {
		$pinned = ' pinned';
		$input_name = 'networks[]';
	} else {
		$input_name = '_networks[]';
	}

	$info = '<div class="elgg-river-summary"><span class="facebook-user-info-popup" title="' . $account->user_id . '">' . $account->name . '</span>';
	$info .= '<br/><span class="elgg-river-timestamp">';
	$info .= elgg_view('output/url', array(
		'href' => 'http://facebook.com/groups/' . $account->name,
		'text' => 'http://facebook.com/groups/' . $account->name,
		'target' => '_blank',
		'rel' => 'nofollow'
	));
	$info .= '</span></div>';

	$pin_tooltip = htmlspecialchars(elgg_echo('deck-river:network:pin'));

	$output = <<<HTML
<div class="net-profile float mlm facebook$pinned">
	<input type="hidden" value="{$account->getGUID()}" name="$input_name" data-network="facebook" data-scrap>
	<ul>
		<span class="elgg-icon elgg-icon-delete pas hidden"></span>
		<div class="elgg-module-popup hidden">
			<div class="triangle"></div>
			<div class="pin float-alt">
			<span class="elgg-icon elgg-icon-push-pin tooltip w link" title="$pin_tooltip"></span>
			</div>
			$info
		</div>
	</ul>
	$avatar
	<span class="network gwf link">F</span>
</div>
HTML;

	echo $output;

} else if ($view === 'in_column_settings') {

} else { // full view in applications user settings page

	$owner = $account->getOwnerEntity();

	$owner_link = elgg_view('output/url', array(
		'href' => "profile/$owner->username",
		'text' => $owner->name,
		'is_trusted' => true,
	));
	$author_text = elgg_echo('deck_river:account:createdby', array('Facebook', elgg_get_site_entity()->name, $owner_link));
	$date = elgg_view_friendly_time($account->time_created);

	$access = elgg_view('output/access', array('entity' => $account));
	$delete = elgg_view('output/url', array(
		'href' => "action/deck_river/network/delete?guid={$account->getGUID()}",
		'text' => elgg_view_icon('delete'),
		'title' => elgg_echo('delete:this'),
		'class' => 'tooltip s t elgg-requires-confirmation',
		'rel' => elgg_echo('deck_river:account:deleteconfirm'),
		'is_action' => true,
	));

	$subtitle = "$author_text $date";

	$link = elgg_view('output/url', array(
		'href' => 'http://facebook.com/' . $account->name,
		'text' => 'http://facebook.com/' . $account->name,
		'class' => 'external',
		'target' => '_blank'
	));

	echo <<<HTML
<div class="elgg-content">
	<div class="elgg-image-block clearfix">
		<div class="elgg-image">
			<span title="{$account->user_id}" class="facebook-user-info-popup">$avatar</span>
		</div>
		<div class="elgg-body">
			<ul class="elgg-menu elgg-menu-entity elgg-menu-hz elgg-menu-entity-default">
				<li class="elgg-menu-item-access">$access</li>
				<li class="elgg-menu-item-delete">$delete</li>
			</ul>
			<h3><span class="facebook-user-info-popup" title="{$account->user_id}">{$account->name}</span></h3>
			$link
			<div class="elgg-subtext">$subtitle</div>
		</div>
	</div>
</div>
HTML;

}
