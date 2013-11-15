<!-- Templates for elgg river facebook item -->
<script id="elgg-river-facebook-template" type="text/template"><li class="elgg-list-item item-facebook-{{id}}" data-object_guid="{{id}}">
		<div class="elgg-image-block elgg-river-item clearfix">
			<div class="elgg-image">
				<div class="elgg-avatar elgg-avatar-small">
					<div class="facebook-user-info-popup info-popup" title="{{from.id}}">
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
					<li class="elgg-menu-item-retweet pls prm">
						<a href="#" class="gwfb tooltip s" title="<?php echo elgg_echo('deck_river:facebook:action:share'); ?>"><span class="elgg-icon elgg-icon-retweet"></span></a>
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
				<div class="elgg-river-summary prl">
					<span class="facebook-user-info-popup info-popup" title="{{from.name}}">{{from.name}}</span>{{#via}}&nbsp;<?php echo elgg_echo('deck_river:via'); ?>&nbsp;<a href="https://facebook.com/{{id}}" target="_blank">{{name}}</a>{{/via}}
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
				<div class="elgg-river-message main" data-message_original="{{message_original}}">{{{message}}}{{#typestatus}}&nbsp;<a target="_blank" href="https://facebook.com/{{id}}"><?php echo elgg_echo('river:facebook:show:status'); ?></a>{{/typestatus}}</div>
				{{#link}}
				{{#typevideo}}
				<a class="elgg-river-responses linkbox-droppable media-video-popup" href="{{link}}" data-source="{{source}}">
				{{/typevideo}}
				{{^typevideo}}
				<a class="elgg-river-responses linkbox-droppable" target="_blank" href="{{link}}">
				{{/typevideo}}
					<div class="elgg-river-image" data-mainimage="{{full_picture}}" data-title="{{name}}" data-url="{{caption}}" data-description="{{description}}">
						{{#full_picture}}
						<div id="img{{id}}" class="elgg-image float gwfb" style="background-image: url({{full_picture}});"></div>
						{{/full_picture}}
						<div class="elgg-body">
							{{#name}}<h4>{{name}}</h4>{{/name}}
							{{#caption}}<span class="elgg-subtext">{{caption}}</span>{{/caption}}
							{{#description}}<div>{{{description}}}</div>{{/description}}
						</div>
					</div>
				</a>
				{{/link}}
				<div class="elgg-river-responses pts">
					{{#likes}}<span class="elgg-icon elgg-icon-thumbs-up-alt float gwfb prs{{#liked}} liked{{/liked}}"></span><span class="float likes-popup prm" data-users="{{likes.users}}">{{likes.string}}</span>{{/likes}}
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
				{{^comments}}
				<ul class="elgg-list elgg-river-comments elgg-list-comments"></ul>
				{{/comments}}
				<ul class="elgg-list elgg-river-comments pts">
					<span class="elgg-icon elgg-icon-speech-bubble-alt float gwfb prs"></span><a href="#comment-form-{{id}}-{{rand}}" class="prm" rel="toggle"><?php echo elgg_echo('deck_river:facebook:action:comment'); ?></a>
					<div id="comment-form-{{id}}-{{rand}}" class="facebook-comment-form hidden">
						<textarea class="comment"></textarea>
						<a href="#" class="elgg-button elgg-button-submit">Commenter</a>
					</div>
				</ul>
			</div>
		</div>
</li></script>



<!-- Templates for elgg river facebook comment item -->
<script id="erFBt-comment" type="text/template">
	<li class="elgg-item" id="{{id}}">
		<div class="elgg-image-block clearfix">
			<div class="elgg-image">
				<img alt="{{from.name}}" src="https://graph.facebook.com/{{from.id}}/picture?width=24&height=24" width="24" height="24">
			</div>
			<div class="elgg-body">
				<div class="elgg-river-summary prl">
					<span class="facebook-user-info-popup info-popup" title="{{from.name}}">{{from.name}}</span>
					<span class="elgg-river-timestamp">
						<a target="_blank" href="https://facebook.com/{{from.id}}/status/{{id}}" target="_blank">
							<br><span class="elgg-friendlytime">
								<acronym class="tooltip w" title="{{created_time}}" time="{{posted}}">{{friendly_time}}</acronym>
							</span>
						</a>&nbsp;&bull;<a class="comment-item-like phs{{#user_likes}} unlike{{/user_likes}}" href="#">{{like}}</a>{{#like_count}}<span>&bull;</span><span class="elgg-icon elgg-icon-thumbs-up{{#user_likes}} liked{{/user_likes}}"></span><span class="counter">{{like_count}}</span>{{/like_count}}
					</span>
				</div>
				<div class="elgg-river-message">{{{message}}}</div>
			</div>
		</div>
	</li>
</script>