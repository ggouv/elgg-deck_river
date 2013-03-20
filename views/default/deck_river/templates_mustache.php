<div id="templates" class="hidden">



<!-- Template for popups -->
<div class="popup-template">
	<div id="{{popupID}}" class="elgg-module-popup deck-popup ui-draggable" style="position: relative;">
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
</div>



<!-- Template for hashtag popup -->
<div class="hashtag-popup">
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
			<ul class="column-header hidden" data-network="twitter" data-direct="https://search.twitter.com/search.json?q=%23{{hashtag}}&rpp=100&include_entities=1"></ul>
			<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
		</li>
	</ul>
</div>



<!-- Template for Twitter user profile popup -->
<div class="twitter-user-profile">
	<ul class="elgg-tabs elgg-htabs">
		<li class="elgg-state-selected"><a href="#{{id}}-info-profile"><?php echo elgg_echo('profile'); ?></a></li>
		<li><a href="#{{id}}-get_statusesUser_timeline"><?php echo elgg_echo('activity'); ?></a></li>
		<li><a href="#{{id}}-mentions"><?php echo elgg_echo('river:mentions'); ?></a></li>
		<li><a href="#{{id}}-get_favoritesList"><?php echo elgg_echo('favorites'); ?></a></li>
	</ul>
	<ul class="elgg-body">
		<li id="{{id}}-info-profile">
			<div class="elgg-avatar elgg-avatar-large float">
				<a href="http://twitter.com/{{screen_name}}" title="{{screen_name}}" rel="nofollow">
					<div style="width: 200px; text-align: center; overflow: hidden; line-height: 200px; height: 200px;">
						<img width="200px" title="{{screen_name}}" alt="{{screen_name}}" src="{{profile_image_url}}">
					</div>
				</a>
			</div>
			<div class="elgg-body plm">
				<h1 class="pts mbm">{{screen_name}}</h1>
				<h2 class="mbs" style="font-weight:normal;">@{{screen_name}}</h2>
				<div>{{description}}</div>
			</div>
			<div id="profile-details" class="elgg-body pll">
				<ul class="user-stats mbm">
					<li><div class="stats">{{followers_count}}</div><?php echo elgg_echo('friends:followers'); ?></li>
					<li><div class="stats">{{friends_count}}</div><?php echo elgg_echo('friends:following'); ?></li>
					<li><div class="stats">{{listed_count}}</div><?php echo elgg_echo('twitter:list'); ?></li>
					<li><div class="stats">{{statuses_count}}</div><?php echo elgg_echo('item:object:thewire'); ?></li>
				</ul>
				<div class="even">
					<b><?php echo elgg_echo('Twitter'); ?> :</b> <a target="_blank" href="http://twitter.com/{{screen_name}}">http://twitter.com/{{screen_name}}</a>
				</div>
				<div class="even">
					<b><?php echo elgg_echo('site'); ?> :</b> <a target="_blank" href="{{url}}">{{url}}</a>
				</div>
				<div class="even">
					<b><?php echo elgg_echo('profile:time_created'); ?> :</b> {{created_at}}
				</div>
			</div>
		</li>
		<li id="{{id}}-get_statusesUser_timeline" class="column-river hidden" >
			<ul class="column-header hidden" data-network="twitter" data-river_type="twitter_OAuth" data-entity="#{{id}}"></ul>
			<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
		</li>
		<li id="{{id}}-mentions" class="column-river hidden">
			<ul class="column-header hidden" data-network="twitter" data-direct="https://search.twitter.com/search.json?q=%40{{screen_name}}&rpp=100&include_entities=1"></ul>
			<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
		</li>
		<li id="{{id}}-get_favoritesList" class="column-river hidden">
			<ul class="column-header hidden" data-network="twitter" data-river_type="twitter_OAuth" data-entity="#{{id}}"></ul>
			<ul class="elgg-river elgg-list"><div class="elgg-ajax-loader"></div></ul>
		</li>
	</ul>
</div>



<!-- Templates for elgg river item -->
<div class="elgg-river-template"><li class="elgg-list-item item-elgg-{{id}} {{type}} {{subtype}} {{action_type}}"
	data-access_id="{{access_id}}"
	data-annotation_id="{{annotation_id}}"
	data-id="{{id}}"
	data-object_guid="{{object_guid}}"
	data-subject_guid="{{subject_guid}}"
	data-username="{{user.username}}"
	data-timeid="{{posted}}">
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
				{{{menu.default}}}
				{{#menu.submenu}}
				<li class="elgg-submenu-river">
					<span class="elgg-icon elgg-icon-hover-menu link gwf"></span>
					<ul class="elgg-module-popup hidden">
						{{{menu.submenu}}}
					</ul>
				</li>
				{{/menu.submenu}}
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
				<span class="elgg-icon elgg-icon-speech-bubble-alt float gwfb"></span>
				<a href="#" class="thread float" data-thread="{{responses}}">
					<?php echo elgg_echo('deck_river:thread'); ?>
				</a>
				<div class="response-loader hidden"></div>
			</div>
			{{/responses}}
		</div>
	</div>
</li></div>



<!-- Templates for elgg river twitter item -->
<div class="elgg-river-twitter-template"><li class="elgg-list-item item-twitter-{{id_str}}"
	data-timeid="{{id_str}}"
	data-username="{{user.screen_name}}"
	data-id="{{id_str}}"
	data-object_guid="{{id_str}}"
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
			{{#menu}}
			<ul class="elgg-menu elgg-menu-river elgg-menu-hz elgg-menu-river-default">
				{{{menu.default}}}
				{{#menu.submenu}}
				<li class="elgg-submenu-river">
					<span class="elgg-icon elgg-icon-hover-menu link gwf"></span>
					<ul class="elgg-module-popup hidden">
						{{{menu.submenu}}}
					</ul>
				</li>
				{{/menu.submenu}}
			{{/menu}}
			</ul>
			<div class="elgg-river-summary">
				<span class="twitter-user-info-popup" title="{{user.screen_name}}">{{user.screen_name}}</span><br/>
				<span class="elgg-river-timestamp">
					<a href="https://twitter.com/{{user.screen_name}}/status/{{id_str}}" target="_blank">
					<span class="elgg-friendlytime">
						<acronym class="tooltip w" title="{{created_at}}" time="{{posted}}">{{friendly_time}}</acronym>
					</span>
				</a>
				</span>
			</div>
			<div class="elgg-river-message">{{{text}}}</div>
			{{#responses}}
			<div class="elgg-river-responses">
				{{#responses.reply}}
				<span class="elgg-icon elgg-icon-speech-bubble-alt float gwfb"></span>
				<a href="#" class="thread float prm" data-thread="{{in_reply_to_status_id_str}}">
					<?php echo elgg_echo('deck_river:thread'); ?>
				</a>
				<div class="response-loader hidden"></div>
				{{/responses.reply}}
				{{#responses.retweet}}
					<span class="elgg-icon elgg-icon-retweet-sub float gwfb"></span>
					<span class="pls float">{{responses.retweet}}</span>
				{{/responses.retweet}}
			</div>
			{{/responses}}
		</div>
	</div>
</li></div>



</div>
