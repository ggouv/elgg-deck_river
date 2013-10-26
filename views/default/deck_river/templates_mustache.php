<!-- Templates deck_river --><div class="hidden">


<!-- Pass var from php to client -->
<script type="text/javascript">
	var deckRiverSettings = <?php echo elgg_is_logged_in() ? get_private_setting(elgg_get_logged_in_user_guid(), 'deck_river_settings') : 'null'; ?>;
	var FBappID = <?php echo elgg_get_plugin_setting('facebook_app_id', 'elgg-deck_river') ?>;
	var site_shorturl = <?php $site_shorturl = elgg_get_plugin_setting('site_shorturl', 'elgg-deck_river'); echo json_encode($site_shorturl ? $site_shorturl : false); ?>;
	var deck_river_min_width_column = <?php $mwc = elgg_get_plugin_setting('min_width_column', 'elgg-deck_river'); echo $mwc ? $mwc : 300; ?>;
	var deck_river_max_nbr_columns = <?php $mnc = elgg_get_plugin_setting('max_nbr_column', 'elgg-deck_river');  echo $mnc ? $mnc : 10; ?>;
</script>


<!-- Template for column -->
<script id="column-template" type="text/template">
	<li class=​"column-river" id=​"{{column}}">​
		<ul class=​"column-header">​</ul>​
		<ul class=​"column-filter">​</ul>​
		<ul class=​"elgg-river elgg-list">​
			<div class="elgg-ajax-loader"></div>
		</ul>​
		<div class=​"river-to-top hidden link t25 gwfb pas">​</div>​
	</li>​
</script>

<!-- Template for linkbox -->
<script id="linkbox-template" type="text/template">
	<div class="elgg-image-block clearfix">
		{{#mainimage}}
		<ul class="elgg-image">
			<div class="link_picture image-wrapper center tooltip s t25 gwfb" title="<?php echo elgg_echo('deck_river:linkbox:hidepicture'); ?>">
				<img height="80px" src="{{mainimage}}">
			</div>
			{{#images}}
				<li class="image-wrapper center t25"><img height="80px" src="{{src}}"></li>
			{{/images}}
		</ul>
		{{/mainimage}}
		<div class="elgg-body pts">
			<ul class="elgg-menu elgg-menu-entity elgg-menu-hz">
				<span class="elgg-icon elgg-icon-delete link"></span>
			</ul>
			<div class="">
				<h4 class="link_name pas mrl" contenteditable="true">{{title}}</h4>
				{{#url}}
				<span class="elgg-subtext pls">
					{{url}}
				</span>
				{{/url}}
				<input type="hidden" name="link_url" value="{{url}}">
				<div class="link_description pas" contenteditable="true">{{description}}</div>
			</div>
		</div>
	</div>
</script>


<!-- Template for popups -->
<script id="popup-template" type="text/template">
	<div id="{{popupID}}" class="elgg-module-popup deck-popup ui-draggable" style="position: relative; z-index: 100;">
		<div class="elgg-head">
			<h3>{{popupTitle}}</h3>
			<a href="#" class="pin">
				<span class="elgg-icon elgg-icon-push-pin tooltip s" title="<?php echo htmlspecialchars(elgg_echo('deck-river:popups:pin')); ?>"></span>
			</a>
			<a href="#">
				<span class="elgg-icon elgg-icon-delete-alt tooltip s" title="<?php echo elgg_echo('deck-river:popups:close'); ?>"></span>
			</a>
		</div>
		<div class="elgg-body">
			<div class="elgg-ajax-loader"></div>
		</div>
	</div>
</script>



<!-- Template for hashtag popup -->
<script id="hashtag-popup-template" type="text/template">
	<ul class="elgg-tabs elgg-htabs">
		<li><a class="elgg" href="#{{hashtag}}-elgg">Elgg</a></li>
		<li><a class="twitter" href="#{{hashtag}}-twitter">Twitter</a></li>
	</ul>
	<ul class="elgg-body">
		<li id="{{hashtag}}-elgg" class="column-river hidden">
			<ul class="column-header hidden" data-network="elgg" data-river_type="entity_river" data-entity="#{{hashtag}}"></ul>
			<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
		</li>
		<li id="{{hashtag}}-twitter" class="column-river hidden">
			<ul class="column-header hidden" data-network="twitter" data-river_type="twitter_OAuth" data-params="{&quot;method&quot;: &quot;get_searchTweets&quot;, &quot;q&quot;: &quot;%23{{hashtag}}&quot;, &quot;count&quot;: &quot;100&quot;, &quot;include_entities&quot;: &quot;1&quot;}"></ul>
			<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
		</li>
	</ul>
</script>



<!-- Template for Twitter user profile popup -->
<script id="twitter-user-profile-template" type="text/template">
	<ul class="elgg-tabs elgg-htabs">
		<li class="elgg-state-selected"><a target="_blank" href="#{{id}}-info-profile"><?php echo elgg_echo('profile'); ?></a></li>
		<li><a href="#{{id}}-get_statusesUser_timeline"><?php echo elgg_echo('activity'); ?></a></li>
		<li><a href="#{{id}}-get_searchTweets"><?php echo elgg_echo('river:mentions'); ?></a></li>
		<li><a href="#{{id}}-get_favoritesList"><?php echo elgg_echo('favorites'); ?></a></li>
	</ul>
	<ul class="elgg-body">
		<li id="{{id}}-info-profile">
			<div class="elgg-avatar elgg-avatar-large float prm">
				<a href="http://twitter.com/{{screen_name}}" title="{{screen_name}}" rel="nofollow">
					<span class="gwfb hidden"><br><?php echo elgg_echo('deck_river:go_to_profile'); ?></span>
					<div class="avatar-wrapper center">
						<img width="200px" title="{{screen_name}}" alt="{{screen_name}}" src="{{profile_image_url}}">
					</div>
				</a>
			</div>
			<div class="plm">
				<h1 class="pts mbm">{{screen_name}}</h1>
				<h2 class="mbs" style="font-weight:normal;">@{{screen_name}}</h2>
				<div>{{{description}}}</div>
				<div class="output-group mtm">
					{{^following}}
					<a class="elgg-button elgg-button-action" href="#" rel="nofollow" data-twitter_action="post_friendshipsCreate" data-user_id="{{id}}">
						<?php echo elgg_echo('deck_river:twitter:follow'); ?>
					</a>
					{{/following}}
					{{#following}}
					<a class="elgg-button elgg-button-action" href="#" rel="nofollow" data-twitter_action="post_friendshipsDestroy" data-user_id="{{id}}">
						<?php echo elgg_echo('deck_river:twitter:unfollow'); ?>
					</a>
					{{/following}}
					<ul class="elgg-button elgg-button-dropdown elgg-submenu">
						<ul class="elgg-menu elgg-module-popup hidden">
							<li><a href="#" data-twitter_action="post_friendshipsDestroy" data-user_id="{{id}}"><?php echo elgg_echo('deck_river:twitter:unfollow'); ?></a></li>
						</ul>
					</ul>
				</div>
			</div>
			<div id="profile-details" class="elgg-body pll">
				<ul class="user-stats mbm">
					<li><div class="stats">{{followers_count}}</div><?php echo elgg_echo('friends:followers'); ?></li>
					<li><div class="stats">{{friends_count}}</div><?php echo elgg_echo('friends:following'); ?></li>
					<li><div class="stats">{{listed_count}}</div><?php echo elgg_echo('deck_river:twitter:lists'); ?></li>
					<li><div class="stats">{{statuses_count}}</div><?php echo elgg_echo('item:object:thewire'); ?></li>
				</ul>
				<div class="even">
					<b><?php echo elgg_echo('Twitter'); ?> :</b> <a class="external" target="_blank" href="http://twitter.com/{{screen_name}}">http://twitter.com/{{screen_name}}</a>
				</div>
				{{#url}}
				<div class="even">
					<b><?php echo elgg_echo('site'); ?> :</b> <a class="external" target="_blank" href="{{url}}">{{url}}</a>
				</div>
				{{/url}}
				<div class="even">
					<b><?php echo elgg_echo('profile:time_created'); ?> :</b> {{created_at}}
				</div>
			</div>
		</li>
		<li id="{{id}}-get_statusesUser_timeline" class="column-river hidden" >
			<ul class="column-header hidden" data-network="twitter" data-river_type="twitter_OAuth" data-params="{&quot;method&quot;: &quot;get_statusesUser_timeline&quot;, &quot;user_id&quot;: &quot;{{id}}&quot;, &quot;count&quot;: &quot;100&quot;}"></ul>
			<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
		</li>
		<li id="{{id}}-get_searchTweets" class="column-river hidden">
			<ul class="column-header hidden" data-network="twitter" data-river_type="twitter_OAuth" data-params="{&quot;method&quot;: &quot;get_searchTweets&quot;, &quot;q&quot;: &quot;@{{screen_name}}&quot;, &quot;count&quot;: &quot;100&quot;}"></ul>
			<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
		</li>
		<li id="{{id}}-get_favoritesList" class="column-river hidden">
			<ul class="column-header hidden" data-network="twitter" data-river_type="twitter_OAuth" data-params="{&quot;method&quot;: &quot;get_favoritesList&quot;, &quot;user_id&quot;: &quot;{{id}}&quot;, &quot;count&quot;: &quot;100&quot;}"></ul>
			<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
		</li>
	</ul>
</script>



<!-- Templates for elgg river item -->
<script id="elgg-river-template" type="text/template"><li class="elgg-list-item item-elgg-{{id}} {{type}} {{subtype}} {{action_type}}"
	data-access_id="{{access_id}}"
	data-annotation_id="{{annotation_id}}"
	data-id="{{id}}"
	data-object_guid="{{object_guid}}"
	data-subject_guid="{{subject_guid}}"
	data-username="{{user.username}}"
	data-timeid="{{posted}}"
	data-text="{{message}}">
	<div class="elgg-image-block elgg-river-item clearfix">
		<div class="elgg-image">
			<div class="elgg-avatar elgg-avatar-small">
				<div class="user-info-popup" title="{{user.username}}">
					<img title="{{user.username}}" alt="{{user.username}}" src="{{user.icon}}">
				</div>
			</div>
		</div>
		<div class="elgg-body">
			{{#menu}}
			<ul class="elgg-menu elgg-menu-river elgg-menu-hz elgg-menu-river-default">
				{{#default}}
				<li class="elgg-menu-item-{{name}}">
					<a href="#" title="{{content}}" class="gwfb tooltip s"><span class="elgg-icon elgg-icon-{{name}}"></span></a>
				</li>
				{{/default}}
				{{#submenu}}
				<li class="elgg-submenu">
					<span class="elgg-icon elgg-icon-hover-menu link gwf"></span>
					<ul class="elgg-module-popup hidden">
						<li class="elgg-menu-item-{{name}}"><a href="#"><span class="elgg-icon elgg-icon-{{name}}"></span>{{{content}}}</a>
					</ul>
				</li>
				{{/submenu}}
			{{/menu}}
			</ul>
			<div class="elgg-river-summary">
				{{{summary}}}<br/>
				<span class="elgg-river-timestamp">
					<span class="elgg-friendlytime">
						<acronym class="tooltip w" title="{{posted_acronym}}" time="{{posted}}">{{friendly_time}}</acronym>
					</span>
				</span>
			</div>
			<div class="elgg-river-message">{{{message}}}</div>
			{{#responses}}
			<div class="elgg-river-responses">
				<div class="response-loader float hidden"></div>
				<span class="elgg-icon elgg-icon-speech-bubble-alt float gwfb prs"></span>
				<a href="#" class="thread float" data-thread="{{responses}}" data-network="elgg">
					<?php echo elgg_echo('deck_river:thread'); ?>
				</a>
			</div>
			{{/responses}}
		</div>
	</div>
</li></script>



<!-- Templates for elgg river twitter item -->
<script id="elgg-river-twitter-template" type="text/template"><li class="elgg-list-item item-twitter-{{id_str}}"
		data-timeid="{{id_str}}"
		data-username="{{user.screen_name}}"
		data-id="{{id_str}}"
		data-object_guid="{{id_str}}"
		data-text="{{text}}"
		>
		<div class="elgg-image-block elgg-river-item clearfix">
			<div class="elgg-image">
				<div class="elgg-avatar elgg-avatar-small">
					<div class="twitter-user-info-popup" title="{{user.screen_name}}">
						<img title="{{user.screen_name}}" alt="{{user.screen_name}}" src="{{user.profile_image_url_https}}">
					</div>
				</div>
			</div>
			<div class="elgg-body">
				<ul class="elgg-menu elgg-menu-river elgg-menu-hz elgg-menu-river-default">
					<li class="elgg-menu-item-response">
						<a href="#" title="<?php echo elgg_echo('reply'); ?>" class="gwfb tooltip s"><span class="elgg-icon elgg-icon-response"></span></a>
					</li>
					<li class="elgg-menu-item-share">
						<a href="#" title="<?php echo elgg_echo('retweet'); ?>" class="gwfb tooltip s"><span class="elgg-icon elgg-icon-share "></span></a>
					</li>
					{{{menu.default}}}
					{{#submenu}}
					<li class="elgg-submenu">
						<span class="elgg-icon elgg-icon-hover-menu link gwf"></span>
						<ul class="elgg-module-popup hidden">
							<li class="elgg-menu-item-{{name}}"><a href="#"><span class="elgg-icon elgg-icon-{{name}}"></span>{{{content}}}</a>
						</ul>
					</li>
					{{/submenu}}
				</ul>
				<div class="elgg-river-summary">
					<span class="twitter-user-info-popup" title="{{user.screen_name}}">{{user.screen_name}}</span><br/>
					<span class="elgg-river-timestamp">
						<a href="https://twitter.com/{{user.screen_name}}/status/{{id_str}}" target="_blank">
						<span class="elgg-friendlytime">
							<acronym class="tooltip w" title="{{created_at}}" time="{{posted}}">{{friendly_time}}</acronym>
						</span>
					</a>
					{{#source}}<?php echo elgg_echo('deck_river:via'); ?>&nbsp;{{{source}}}{{/source}}
					</span>
				</div>
				<div class="elgg-river-message">{{{message}}}</div>
				{{#responses}}
				<div class="elgg-river-responses">
					{{#responses.retweet}}
						<span class="elgg-icon elgg-icon-retweet-sub float gwfb"></span>
						<span class="pls">{{{responses.retweet}}}</span>
					{{/responses.retweet}}
					{{#responses.reply}}
					{{#responses.retweet}}<br/>{{/responses.retweet}}<div class="response-loader float clearfloat hidden"></div>
					<span class="elgg-icon elgg-icon-speech-bubble-alt float gwfb"></span>
					<a href="#" class="thread float prm" data-thread="{{id_str}}" data-network="twitter"><?php echo elgg_echo('deck_river:thread'); ?></a>
					{{/responses.reply}}
				</div>
				{{/responses}}
			</div>
		</div>
</li></script>



<!-- Templates for elgg river facebook item -->
<script id="elgg-river-facebook-template" type="text/template"><li class="elgg-list-item item-facebook-{{id}}" data-object_guid="{{id}}">
		<div class="elgg-image-block elgg-river-item clearfix">
			<div class="elgg-image">
				<div class="elgg-avatar elgg-avatar-small">
					<div class="facebook-user-info-popup" title="{{from.id}}">
						<img title="{{from.name}}" alt="{{from.name}}" src="http://graph.facebook.com/{{from.id}}/picture">
					</div>
				</div>
				{{#icon}}<br/><img class="float fb" src="{{icon}}">{{/icon}}
			</div>
			<div class="elgg-body">
				<ul class="elgg-menu elgg-menu-river elgg-menu-hz elgg-menu-river-default">
					<li class="elgg-menu-item-like">
						<a href="#" class="gwfb tooltip s" title="<?php echo elgg_echo('deck_river:facebook:action:like'); ?>"><span class="elgg-icon elgg-icon-thumbs-up"></span></a>
					</li>
					<li class="elgg-menu-item-share">
						<a href="#" class="gwfb tooltip s" title="<?php echo elgg_echo('deck_river:facebook:action:share'); ?>"><span class="elgg-icon elgg-icon-share"></span></a>
					</li>
					{{{menu.default}}}
					{{#submenu}}
					<li class="elgg-submenu">
						<span class="elgg-icon elgg-icon-hover-menu link gwf"></span>
						<ul class="elgg-module-popup hidden">
							<li class="elgg-menu-item-{{name}}"><a href="#"><span class="elgg-icon elgg-icon-{{name}}"></span>{{{content}}}</a>
						</ul>
					</li>
					{{/submenu}}
				</ul>
				<div class="elgg-river-summary">
					<span class="facebook-user-info-popup" title="{{from.name}}">{{from.name}}</span>{{#via}}&nbsp;<?php echo elgg_echo('deck_river:via'); ?>&nbsp;<a href="https://facebook.com/{{id}}" target="_blank">{{name}}</a>{{/via}}
					{{#properties}}
						<?php echo elgg_echo('river:facebook:photo:shared_story'); ?>&nbsp;<a target="_blank" href="{{link}}"><?php echo elgg_echo('river:facebook:photo:shared_story:photo'); ?></a>&nbsp;<?php echo elgg_echo('river:facebook:photo:shared_story:of'); ?>&nbsp;<a target="_blank" href="{{href}}">{{text}}</a>
					{{/properties}}
					<br><span class="elgg-river-timestamp">
						<a href="https://facebook.com/{{id}}" target="_blank">
							<span class="elgg-friendlytime">
								<acronym class="tooltip w" title="{{created_time}}" time="{{posted}}">{{friendly_time}}</acronym>
							</span>
						</a>
					</span>
				</div>
				<div class="elgg-river-message">{{{message}}}{{#typestatus}}&nbsp;<a target="_blank" href="https://facebook.com/{{id}}"><?php echo elgg_echo('river:facebook:show:status'); ?></a>{{/typestatus}}</div>
				{{#link}}
				{{#typevideo}}
				<a class="elgg-river-responses linkbox-droppable video-popup" href="{{link}}" data-source="{{source}}">
				{{/typevideo}}
				{{^typevideo}}
				<a class="elgg-river-responses linkbox-droppable" target="_blank" href="{{link}}">
				{{/typevideo}}
					<div class="elgg-river-image" data-mainimage="{{full_picture}}" data-title="{{name}}" data-url="{{caption}}" data-description="{{description}}">
						{{#full_picture}}
						<div id="img{{id}}" class="elgg-image float gwfb" style="background-image: url({{full_picture}});"></div>
						{{/full_picture}}
						<div class="elgg-body">
							<h4>{{name}}</h4>
							<span class="elgg-subtext">{{caption}}</span>
							<div>{{{description}}}</div>
						</div>
					</div>
				</a>
				{{/link}}
				<div class="elgg-river-responses pts">
					{{#likes}}<span class="elgg-icon elgg-icon-thumbs-up-alt float gwfb prs"></span><span class="float likes-popup prm" data-users="{{likes.users}}">{{likes.string}}</span>{{/likes}}
					{{#shares}}<span class="elgg-icon elgg-icon-retweet-sub float gwfb"></span><span class="float shares-popup prm">&nbsp;{{shares.string}}</span>{{/shares}}
				</div>
				{{#comments}}
				{{#comments.before}}
					<ul class="elgg-list elgg-river-comments">
						<li><a rel="toggle" href="#comment-part-{{id}}-{{rand}}">{{comments.before}}</a></li>
					</ul>
					<ul id="comment-part-{{id}}-{{rand}}" class="elgg-list elgg-river-comments hidden">
						{{#comments.dataBefore}}
							{{> erFBt-comment}}
						{{/comments.dataBefore}}
					</ul>
				{{/comments.before}}
				<ul class="elgg-list elgg-river-comments elgg-list-comments">
					{{#comments.data}}
						{{> erFBt-comment}}
					{{/comments.data}}
				</ul>
				{{/comments}}
				<ul class="elgg-list elgg-river-comments pts">
					<span class="elgg-icon elgg-icon-speech-bubble-alt float gwfb prs"></span><a href="#comment-form-{{id}}-{{rand}}" class="prm" rel="toggle"><?php echo elgg_echo('deck_river:facebook:action:comment'); ?></a>
					<div id="comment-form-{{id}}-{{rand}}" class="facebook-comment-form hidden">
						<textarea class="comment"></textarea>
						<a class="elgg-button elgg-button-submit">Commenter</a>
					</div>
				</ul>
			</div>
		</div>
</li></script>

<script id="erFBt-comment" type="text/template">
	<li class="elgg-item" id="{{id}}">
		<div class="elgg-image-block clearfix">
			<div class="elgg-image">
				<img alt="{{from.name}}" src="https://graph.facebook.com/{{from.id}}/picture?width=24&height=24" width="24" height="24">
			</div>
			<div class="elgg-body">
				<div class="elgg-river-summary">
					<span class="facebook-user-info-popup" title="{{from.name}}">{{from.name}}</span>
					<span class="elgg-river-timestamp">
					<a target="_blank" href="https://facebook.com/{{from.id}}/status/{{id}}" target="_blank">
						<br><span class="elgg-friendlytime">
							<acronym class="tooltip w" title="{{created_time}}" time="{{posted}}">{{friendly_time}}</acronym>
						</span>
					</a>
				</span>
				</div>
				<div class="elgg-river-message">{{{message}}}</div>
			</div>
		</div>
	</li>
</script>

</div>
<div id="fb-root"></div>


