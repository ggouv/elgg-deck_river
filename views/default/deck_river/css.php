body {
	width: 100%;
}
body.fixed-deck {
	position: fixed;
}
.elgg-main {
	padding: 10px 0 0;
}

/* the wire-search textarea */
#thewire-header {
	background-color: white;
	border-radius: 6px;
	height: 33px;
	-webkit-box-shadow: inset 0 2px 2px 0 #1F2E3D;
	-moz-box-shadow: inset 0 2px 2px 0 #1F2E3D;
	box-shadow: inset 0 2px 2px 0 #1F2E3D;
}
#thewire-header #thewire-textarea-border {
	display: none;
}
#thewire-textarea {
	background-color: transparent;
	resize: none;
	height: 32px !important;
	padding: 10px 2px 0px 12px !important;
	margin: 0;
	color: #666;
	font: 130% Arial,Helvetica,sans-serif;
	border: none;
	width: 570px;
	line-height: 1em;
	overflow: hidden;
	-webkit-box-shadow: none;
	-moz-box-shadow: none;
	box-shadow: none; 
}
#thewire-textarea-border {
	background-color: #4690D6;
	border-radius: 0 0 6px 6px;
	box-shadow: 3px 3px 3px 0 rgba(0, 0, 0, 0.5);
	height: 0px;
	left: -4px;
	position: absolute;
	top: 35px;
	width: 665px;
	z-index: -1;
}
#thewire-characters-remaining {
	position: absolute;
	right: 47px;
	top: 0;
	z-index: 7003;
	overflow: hidden;
	width: 40px;
	text-align: right;
	font-weight: bold;
	color: #333333;
}
#thewire-characters-remaining span {
	color: #00CC00;
	background-color: white;
	border-radius: 0 6px 6px 0;
	display: block;
	font-size: 1.2em;
	margin-left: -12px;
	padding: 9px 6px 6px 0;
	height: 18px;
	-webkit-box-shadow: inset 0 2px 2px 0 #1F2E3D;
	-moz-box-shadow: inset 0 2px 2px 0 #1F2E3D;
	box-shadow: inset 0 2px 2px 0 #1F2E3D;
}
#thewire-header > .thewire-button {
	position: absolute;
	top: 0;
	right: 0;
	border-radius: 6px 6px 6px 6px;
	height: 33px;
	overflow: hidden;
	right: 0;
	background-color: #FFE6E6;
	-webkit-box-shadow: inset 0 2px 2px 0 #1F2E3D;
	-moz-box-shadow: inset 0 2px 2px 0 #1F2E3D;
	box-shadow: inset 0 2px 2px 0 #1F2E3D;
}
#thewire-header > .thewire-button:before {
	content: "S";
	color: #B40000;
	font-size: 54px;
	position: relative;
	right: 5px;
	top: 9px;
	position: absolute;
}
#thewire-header > .thewire-button:hover {
	background-color: #FF0000;
}
#thewire-header > .thewire-button:hover:before {
	color: white;
	text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
}
#thewire-submit-button {
	color: transparent;
	height: 33px;
	text-indent: -9999px;
	background-color: transparent;
	border: none;
	width: 60px;
}
#thewire-header #submit-loader {
	background-color: white;
	background-position: 50% center;
	left: 573px;
	padding: 2px 8px;
	top: -25px;
	z-index: 7004;
}
#thewire-header.extended #submit-loader {
	background-color: transparent;
	left: 526px;
	top: 8px;
}
#thewire-header.extended #thewire-textarea {
	height: 115px !important;
}
#thewire-header.extended {
	border-radius: 6px 6px 0 0;
}
#thewire-header.extended #thewire-textarea-border {
	display: block;
}
#thewire-header.extended #thewire-textarea {
	width: 100%;
	overflow-y: auto;
}
#thewire-header.extended #thewire-characters-remaining {
	bottom: -31px;
	left: 3px;
	top: auto;
}
#thewire-header.extended #thewire-characters-remaining span {
	background-color: transparent;
	box-shadow: none;
	margin-left: 5px;
	text-align: left;
}
#thewire-header.extended #thewire-textarea-bottom {
	background-color: #F4F4F4;
	border-radius: 0 0 6px 6px;
	bottom: -31px;
	height: 40px;
	position: absolute;
	width: 100%;
	z-index: -1;
	-webkit-box-shadow: inset 0 2px 2px 0 #1F2E3D;
	-moz-box-shadow: inset 0 2px 2px 0 #1F2E3D;
	box-shadow: inset 0 2px 2px 0 #1F2E3D;
}
#thewire-header.extended .thewire-button {
	background-color: white;
	border: 1px solid #999999;
	box-shadow: none;
	height: 21px;
	margin: 4px;
	padding: 0 0 1px 24px;
	top: auto;
	bottom: -32px;
	width: 72px;
	-webkit-box-shadow: inset 0px -10px 10px 2px rgba(0, 0, 0, 0.1);
	-moz-box-shadow: inset 0px -10px 10px 2px rgba(0, 0, 0, 0.1);
	box-shadow: inset 0px -10px 10px 2px rgba(0, 0, 0, 0.1);
}
#thewire-header.extended > .thewire-button:before {
	font-size: 40px;
	left: 2px;
	right: auto;
	top: 2px;
}
#thewire-header.extended #thewire-submit-button {
	color: #333333;
	float: left;
	height: 22px;
	padding-left: 30px;
	position: absolute;
	right: 0;
	text-indent: 0;
	width: 97px;
}
#thewire-header.extended > .thewire-button:hover {
	background-color: #FF3019;
	border: 1px solid #CF0404;
	color: white;
}
#thewire-header.extended > .thewire-button:hover #thewire-submit-button {
	color: white;
}
#thewire-header.extended .options {
	display: block;
}
#thewire-header .url-shortener {
	border-top: 1px solid #DEDEDE;
	margin: 0 1px;
	padding: 4px;
	width: 647px;
	position: relative;
}
#thewire-header .url-shortener .elgg-input-text {
	font-size: 100%;
	padding-right: 70px;
}
#thewire-header .url-shortener .elgg-button {
	font-size: 90%;
	padding: 2px 2px 0;
	position: absolute;
	top: 8px;
}
#thewire-header .url-shortener .elgg-button-submit {
	right: 7px;
}
#thewire-header .url-shortener .elgg-button-action {
	right: 75px;
}
#thewire-header .url-shortener .elgg-icon {
	position: absolute;
	top: 8px;
	right: 138px;
}
#thewire-header .responseTo {
	background-color: #FFC;
	color: #666;
	margin: 0 2px;
	padding: 2px 5px;
	height: 18px;
	overflow: hidden;
}
#thewire-header .responseTo span {
	color: #999;
	font-size: 85%;
	font-style: italic;
}
#thewire-header .responseTo:hover {
	background-color: #FDD;
	color: red;
	cursor: pointer;
}


#thewire-network {
	right: -201px;
	top: 0;
	width: 194px;
	position: absolute;
}
#thewire-network .selected-profile {
	background-color: white;
	border: medium none;
	border-radius: 4px 4px 4px 4px;
	height: 23px;
	width: 100%;
	box-shadow: inset 0 2px 2px 0 #1F2E3D;
	-webkit-box-shadow: inset 0 2px 2px 0 #1F2E3D;
	-moz-box-shadow: inset 0 2px 2px 0 #1F2E3D;
}
#thewire-network .net-profiles {
	box-shadow: 0 2px 2px 0 #1F2E3D inset;
	background: white;
	float: left;
	margin-top: -10px;
	min-height: 39px;
	padding-top: 10px;
	width: 194px;
	z-index: -1;
}
#thewire-network .selected-profile.ui-state-highlight, #thewire-network .non-pinned .net-profiles.ui-state-highlight {
	background: #FFFFCC;
}
#thewire-network .selected-profile.ui-state-active, #thewire-network .non-pinned .net-profiles.ui-state-active {
	background: #DDFFDD;
}
#thewire-network .selected-profile.ui-start, #thewire-network .non-pinned .net-profiles.ui-start {
	background: white;
}
#thewire-network .net-profile {
	position: relative;
}
#thewire-network .network {
	background-color: white;
	border: 1px solid #666666;
	height: 10px;
	left: 17px;
	position: absolute;
	top: -3px;
	width: 10px;
}
#thewire-network .net-profile.ggouv .network {
	background-image: url(<?php echo elgg_get_site_url() . 'mod/elgg-ggouv_template/graphics/favicon.png'; ?>);
	background-size: 10px 10px;
}
#thewire-network .net-profile.twitter .network {
	background-color: #00ACED;
	border: 1px solid #00ACED;
	color: white;
	font-size: 15px;
	line-height: 10px;
}
#thewire-network .elgg-icon-delete {
	background-color: rgba(0, 0, 0, 0.3);
	height: 15px;
	left: -2px;
	position: absolute;
	text-indent: 1.5px;
	width: 15px;
	cursor: pointer;
}
#thewire-network .elgg-icon-delete:before {
	color: red;
}
#thewire-network .net-profile:hover .elgg-icon {
	display: block;
}
#thewire-network .net-profile.ui-draggable-dragging:hover .elgg-icon-delete {
	display: none;
}
#thewire-network .net-profile:hover .elgg-module-popup {
	display: block;
	left: -77px;
	position: absolute;
	top: 31px;
	background: #1F2E3D;
	color: white;
	width: 160px;
	border-radius: 6px;
	border: none;
	box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.5);
	-moz-box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.5);
}
#thewire-network .selected-profile .net-profile.ui-draggable-dragging:hover .elgg-module-popup {
	display: none;
}
#thewire-network .selected-profile .net-profile.ui-draggable-dragging:hover {
	cursor: move;
}
#thewire-network .triangle {
	border-style: solid;
	border-width: 0 10px 10px;
	border-color: #1F2E3D transparent;
	left: 80px;
	position: absolute;
	top: -6px;
}
#thewire-network .user-info-popup {
	font-weight: bold;
}
#thewire-network .elgg-module-popup a:hover, #thewire-network .elgg-module-popup span:hover {
	color: #e4ecf5;
}
#thewire-network .more_networks {
	color: #CCCCCC;
	position: absolute;
	right: 0;
	top: 10px;
	font-size: 3em;
	cursor: pointer;
}
#thewire-network .more_networks:hover {
	color: #4690D6;
}
#thewire-network.extended .non-pinned {
	display: block;
	position: absolute;
	top: 33px;
	z-index: 0;
}
#thewire-network .non-pinned {
	background-color: #4690D6;
	border-radius: 0 0 6px 6px;
	box-shadow: 3px 3px 3px 0 rgba(0, 0, 0, 0.5);
	padding: 4px;
	margin-left: -4px;
	float: left;
}
#thewire-network .non-pinned .content {
	padding-top: 22px;
	width: 100%;
}
#thewire-network .helper {
	height: 24px;
	overflow: hidden;
	position: relative;
	margin: -6px 0 -18px;
	width: 194px;
}
#thewire-network .helper div {
	margin-top: -7px;
	padding: 8px 0 3px;
	background: none repeat scroll 0 0 #F0F0F0;
	box-shadow: 0 2px 2px 0 #1F2E3D inset;
	color: #999999;
	font-size: 0.85em;
	text-align: center;
	cursor: default;
}
#thewire-network .helper span {
	font-size: 2em;
	vertical-align: text-bottom;
	color: #CCC;
}
/* hack Chrome / Safari */
@media screen and (-webkit-min-device-pixel-ratio:0) {
	#thewire-network .helper span {
		vertical-align: bottom;
	}
	#thewire-network .helper div {
		padding: 10px 0 3px;
	}
}
#thewire-network .non-pinned .net-profile {
	margin: 2px;
	padding: 3px;
	width: 184px;
}
#thewire-network .non-pinned .net-profile:hover {
	background: white;
	cursor: move;
	box-shadow: 0 0 1px #CCC;
}
#thewire-network .non-pinned .network {
	left: 20px;
	top: 0;
}
#thewire-network .non-pinned .net-profile .elgg-module-popup {
	background: white;
	border: medium none;
	box-shadow: none;
	display: block;
	float: left;
	font-size: 0.8em;
	height: 0;
	left: 38px;
	padding: 0;
	position: absolute;
	top: -1px;
	width: 155px;
}
#thewire-network .non-pinned .net-profile:hover .elgg-module-popup {
	color: black !important;
}
#thewire-network .non-pinned .triangle, #thewire-network .non-pinned .elgg-icon-delete, #thewire-network .non-pinned .elgg-river-timestamp {
	display: none !important;
}
#thewire-network .non-pinned .elgg-module-popup span:hover {
	color: #555;
}
#thewire-network .non-pinned .pin {
	float: right;
	font-size: 0.8em;
	padding-top: 8px;
}
#thewire-network .footer {
	background: #F0F0F0;
	border-radius: 0 0 6px 6px;
	box-shadow: 0 2px 2px 0 #1F2E3D inset;
	float: left;
	margin-top: -12px;
	padding: 15px 5px 5px;
	position: relative;
	width: 184px;
	z-index: -1;
}
#thewire-network .footer li {
	display: inline-block;
	font-size: 0.8em;
	line-height: 1em;
	margin-top: 3px;
	padding: 0 5px;
	text-align: center;
	width: 74px;
}
#thewire-network .footer li:first-child {
	border-right: 1px solid #CCC;
	padding-right: 15px;
}
#thewire-network .footer a {
	color: #999;
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
	width: auto;
	height: auto;
	min-width: 372px;
	max-width: 560px;
	min-height: 300px;
}
#column-settings .elgg-ajax-loader {
	height: 270px;
}
.deck-popup > .elgg-head {
	background-color: #EEE;
	margin: -5px -5px 5px;
	padding-bottom: 5px;
	cursor: move;
}
.deck-popup > .elgg-head h3 {
	color: #666666;
	float: left;
	padding: 4px 30px 0 5px;
}
.deck-popup > .elgg-head a {
	display: inline-block;
	height: 18px;
	position: absolute;
	right: 5px;
	top: 5px;
	width: 18px;
	cursor: pointer;
}
.deck-popup .elgg-head a.pin {
	right: 20px;
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
	width: 100%;
}
#column-settings .networks > li a {
	text-align: left;
}
#column-settings .networks > .elgg-state-selected a {
	top: 0;
	right: -2px;
}
#column-settings .elgg-input-checkboxes label {
	font-weight: normal;
}
#column-settings .tab > * {
	border-left: 2px solid #CCC;
	float: left;
	height: 100%;
	width: 449px;
}
#column-settings .elgg > * {
	width: 205px;
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
#column-settings .twitter .elgg-module-info {
	background: none repeat scroll 0 0 #EEEEEE;
}
#column-settings .twitter .elgg-module {
	bottom: 5px;
	font-size: 0.9em;
	padding: 5px 5px 0;
	position: absolute;
	width: 440px;
}
#column-settings .twitter .elgg-module.multi {
	position: relative;
	padding: 0 5px;
}
#column-settings select[name="twitter-account"] {
	position: absolute;
	right: 40px;
	top: 38px;
}
#column-settings .addAccount {
	font-size: 1.4em;
	font-weight: bold;
	position: absolute;
	right: 7px;
	top: 8px;
	cursor: pointer;
}
#column-settings .addAccount:hover {
	text-decoration: none;
}

/*
 * info popup
 */
.deck-popup {
	width: 480px;
	height: 600px;
	left: 40%;
	position: fixed !important;
	top: 15%;
	z-index: 9990;
}
.deck-popup > .elgg-body > .elgg-body {
	height: 540px;
	overflow-y: auto;
}
#hashtag-info-popup > .elgg-body {
	height: 573px;
	overflow-y: auto;
}
.deck-popup .elgg-ajax-loader {
	height: 540px;
}
.deck-popup #profile-details {
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

/* twitter */
.elgg-river-summary .twitter-user-info-popup {
	color: #4690D6;
	font-weight: bold;
}
.twitter-user-info-popup:hover {
	color: #555;
	text-decoration: underline;
	cursor: pointer;
}