body {
	width: 100%;
}
body.fixed-deck {
	position: fixed;
}
.elgg-main {
	padding: 10px 0 0;
}
.deck-river {
	/*padding: 10px 0 0;*/
}

/* deck tabs */
.elgg-menu-deck-river {
	margin: 0;
}
.elgg-menu-deck-river > li {
	background-color: white;
}
.elgg-menu-deck-river > li > a:first-letter {
	text-transform: uppercase;
}
.elgg-menu-deck-river > .elgg-state-selected {
	background-color: #EEE;
}
.elgg-menu-deck-river > .elgg-state-selected > a {
	background-color: #EEE;
}
.elgg-menu-deck-river > .elgg-menu-item-refresh-all {
	border-radius: 5px 0 0 0;
	border-style: solid none none solid;
}
.elgg-menu-deck-river > .elgg-menu-item-refresh-all > a {
	padding-right: 0;
}
.elgg-menu-deck-river > .elgg-menu-item-plus-column {
	border-radius: 0 5px 0 0;
	border-style: solid solid none none;
	font-size: 1.5em;
	font-weight: bold;
	line-height: 14px;
	margin: 0;
}
.elgg-menu-deck-river > .elgg-menu-item-plus {
	font-weight: bold;
}
.elgg-menu-deck-river > .elgg-menu-item-refresh-all:hover,
.elgg-menu-deck-river > .elgg-menu-item-plus-column:hover,
.elgg-menu-deck-river > .elgg-menu-item-refresh-all:hover > a,
.elgg-menu-deck-river > .elgg-menu-item-plus-column:hover > a {
	background-color: #EEE;
}
.column-deletable, .delete-tab {
	float: left;
}
.elgg-icon-deck-river-delete {
	vertical-align: middle;
	background-size: 100% auto;
	height: 12px;
	width: 12px;
	margin: -3px 0 0 -12px;
	background-position: 0 -202px;
}
.elgg-icon-deck-river-delete:hover {
	background-position: 0 -189px;
}

.deck-river-lists {
	overflow-x: scroll;
	overflow-y: hidden;
	width: 100%;
}
.deck-river-lists .elgg-river {
	height: 100%;
	overflow-y: scroll;
	overflow-x: hidden;
}
.elgg-list {
	margin: 0;
}
.deck-river-lists .elgg-list > li {
	padding: 5px;
}
.column-river {
	float: left;
	border-left: 1px solid #CCC;
	border-right: 1px solid #CCC;
}
.column-placeholder {
	width: 300px;
	float: left;
	border: 2px dashed #dedede;
}

/* column header */
.column-river .column-header {
	cursor: move;
	height: 30px;
	background-color: #EEE;
	overflow: hidden;
	position: relative;
}
.column-river .column-header .title {
	padding-left:5px;
	color: #666;
}
.column-river .column-header .subtitle {
	padding-left: 5px;
	color: #999;
	line-height: 10px;
}
.column-river .column-header > li > a {
	position: absolute;
	top: 7px;
	display: inline-block;
	width: 18px;
	height: 18px;
}
.column-river .column-header > li a.elgg-column-refresh-button {
	right: 32px;
}
.column-river .column-header > li a.elgg-column-refresh-button .elgg-icon {
	height: 18px;
	width: 18px;
}
.column-river .column-header > li a.elgg-column-edit-button {
	right: 8px;
}

.elgg-river-item {
	padding: 0;
}
.elgg-river .elgg-module {
	margin-bottom: 0;
}
.elgg-river .elgg-ajax-loader {
	height: 100%;
}
.newRiverItem {
	display: none;
	border-right: 2px solid #4690D6;
}
.moreItem {
	background-color: #EEE;
	cursor: pointer;
	text-align: center;
}
.moreItem:hover {
	color: #4690D6;
}
.elgg-menu-river {
	opacity: 0;
}
.elgg-list-item:hover .elgg-menu-river {
	opacity: 1;
}
.column-river .elgg-river td.helper {
	padding: 0 10% 50%;
	text-align: center;
	vertical-align: middle;
}


/* comment */
.elgg-river-comments-tab {
	float:left;
}
.elgg-river-comments li:first-child {
	border-radius: 0 5px 0 0;
}
.elgg-river-comments .elgg-output {
	margin-top: 0;
}
.elgg-river-item form {
	border-radius: 0 0 5px 5px;
	height: 100%;
}
.elgg-river-item input[type="text"] {
	width: 100%;
}
.elgg-river-item input[type="submit"] {
	margin: 5px 0 0;
}

/* misc */
.elgg-river-item .elgg-icon-arrow-right {
	margin: 5px;
}
.elgg-river-item .elgg-avatar-small > div > img {
	width: 32px;
	height: 32px;
	border-radius: 0;
}
.user-info-popup {
	cursor: pointer;
}
.elgg-submenu-river.hover > .elgg-module-popup {
	box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
	display: block;
	position: absolute;
	right: 3px;
	top: 15px;
	width: 150px;
	color: #333;
	padding: 0;
}
.elgg-submenu-river > .elgg-module-popup > li {
	display: list-item;
	padding: 5px;
}
.elgg-submenu-river > .elgg-module-popup > li:hover {
	background-color: #CCC;
}
.elgg-submenu-river > .elgg-module-popup > li .elgg-icon-delete {
	width: 100%;
}
.elgg-icon-response, .elgg-icon-response-all, .elgg-icon-retweet, .elgg-icon-submenu-river {
	color: #CCC;
	cursor: pointer;
	font-size: 40px;
	background: none;
}
.elgg-icon-response:hover, .elgg-icon-response-all:hover, .elgg-icon-retweet:hover, .elgg-icon-submenu-river:hover, .elgg-submenu-river.hover .elgg-icon-submenu-river {
	color: #555;
}
.elgg-river-responses a:hover {
	cursor: pointer;
}
.elgg-icon-retweet-sub {
	color: #999;
	font-size: 32px;
	padding-right: 3px;
	background: none;
}
.elgg-icon-comment-sub {
	color: #999;
	font-size: 24px;
	background: none;
}
.elgg-river-responses .thread .elgg-list-item {
	color: black;
}
.elgg-river-responses .thread .elgg-avatar img {
	height: 24px;
	width: 24px;
}
.elgg-river-responses .thread .elgg-river-item.elgg-image-block .elgg-body {
	margin-left: 30px;
}
.response-loader {
	background: url(<?php echo elgg_get_site_url() . 'mod/elgg-deck_river/graphics/ajax-loader.gif'; ?>) no-repeat scroll 0 0 transparent;
	height: 16px;
	left: -20px;
	position: relative;
	width: 16px;
}
.elgg-list-item.responseAt {
	background-color: #FFFFCC !important;
}


/* settings */
#column-settings {
	left: 40%;
	position: fixed;
	top: 15%;
	z-index: 9999;
	min-width: 372px;
}
#column-settings .elgg-head, #user-info-popup .elgg-head, #group-info-popup .elgg-head, #hashtag-info-popup .elgg-head {
	background-color: #EEE;
	margin: -5px -5px 5px;
	padding-bottom: 5px;
	cursor: move;
}
#column-settings .elgg-head h3, #user-info-popup .elgg-head h3, #group-info-popup .elgg-head h3, #hashtag-info-popup .elgg-head h3 {
	color: #666666;
	float: left;
	padding: 4px 30px 0 5px;
}
#column-settings .elgg-head a, #user-info-popup .elgg-head a, #group-info-popup .elgg-head a, #hashtag-info-popup .elgg-head a {
	display: inline-block;
	height: 18px;
	position: absolute;
	right: 5px;
	top: 5px;
	width: 18px;
	cursor: pointer;
}
#deck-column-settings, #deck-column-settings > div {
	float:left;
	height: 240px;
}
#column-settings .networks {
	border-bottom: medium none;
	float: left;
	width: auto;
}
#column-settings .networks > li {
	border-color: #CCC;
	border-radius: 5px 0 0 5px;
	border-style: solid;
	border-width: 2px 0 2px 2px;
	clear: both;
	float: right;
	margin: 5px 0 0;
}
#column-settings .networks > .elgg-state-selected a {
	top: 0;
	right: -2px;
}
#column-settings .filter {
	float:left;
}
#column-settings .elgg-input-checkboxes label {
	font-weight: normal;
}
#column-settings .box-settings {
	border-right: 2px solid #CCC;
	border-left: 2px solid #CCC;
	float: left;
	height: 100%;
	width: 185px;
}
#column-settings .box-settings .elgg-input-dropdown, #column-settings .box-settings li {
	float: left;
}
#add-deck-river-tab, #rename-deck-river-tab {
	width: 260px;
}
#add-deck-river-tab .elgg-input-text, #rename-deck-river-tab .elgg-input-text {
	width: 200px;
	float: left;
}
#add-deck-river-tab .elgg-button-submit, #rename-deck-river-tab .elgg-button-submit {
	float: right;
}

/*
 * info popup
 */
#user-info-popup, #group-info-popup, #hashtag-info-popup {
	width: 480px;
	height: 600px;
	left: 40%;
	position: fixed !important;
	top: 15%;
	z-index: 9990;
}
#user-info-popup > .elgg-body > .elgg-body, #group-info-popup > .elgg-body > .elgg-body {
	height: 540px;
	overflow-y: auto;
}
#hashtag-info-popup > .elgg-body {
	height: 573px;
	overflow-y: auto;
}
#user-info-popup .elgg-ajax-loader, #group-info-popup .elgg-ajax-loader, #hashtag-info-popup .elgg-ajax-loader {
	height: 540px;
}
#user-info-popup .elgg-tabs a {
	cursor: pointer;
}
#user-info-popup #user-info-activity {
	overflow: hidden;
}
#user-info-popup #profile-details, #group-info-popup #profile-details {
	clear: both;
	padding: 5px 0 0 0;
}
.user-stats {
	background: none repeat scroll 0 0 #EEEEEE;
	border-radius: 5px 5px 5px 5px;
	clear: both;
	display: inline-block;
	padding: 5px 0;
	width: 100%;
}
.user-stats li {
	color: #333333;
	font-weight: bold;
	display: block;
	float: left;
	margin: 0 10px;
	min-width: 100px;
	vertical-align: top;
}
.user-stats .stats {
	font-size: 200%;
	line-height: 0.9em;
}

/* single view */
.single-view .elgg-list-item {
	opacity: 0.6;
}
.single-view .elgg-list-item:hover {
	opacity: 1;
}
.single-view .viewed {
	opacity: 1;
	padding: 10px 0 !important;
	background: none;
}
.single-view .elgg-river-responses {
	display: none;
}
.viewed .elgg-image-block {
	box-shadow: 0 0 10px #CCCCCC;
	margin: 10px;
	padding: 10px;
}
.viewed .elgg-avatar-small > div > img {
	height: 40px ;
	width: 40px;
}
.viewed .elgg-image-block .elgg-body {
	margin-left: 50px;
}
.viewed .elgg-menu-river {
	opacity: 1;
}
.viewed .elgg-river-summary {
	font-size: 115%;
}
.viewed .elgg-river-message {
	font-size: 130%;
	color: #333;
}
