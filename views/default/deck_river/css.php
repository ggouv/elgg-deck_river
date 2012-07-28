body {
	width: 100%;
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
	height: 26px;
	background-color: #EEE;
	overflow: hidden;
	position: relative;
}
.column-river .column-header h3 {
	float: left;
	padding: 4px 45px 0 5px;
	color: #666;
}
.column-river .column-header > li a {
	position: absolute;
	top: 4px;
	display: inline-block;
	width: 18px;
	height: 18px;
}
.column-river .column-header > li a.elgg-column-refresh-button {
	right: 45px;
}
.column-river .column-header > li a.elgg-column-refresh-button .elgg-icon {
	height: 18px;
	width: 18px;
}
.column-river .column-header > li a.elgg-column-edit-button {
	right: 20px;
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
.elgg-icon-arrow-right {
	margin: 5px;.elgg-icon-arrow-right
}
.elgg-avatar-small > a > img {
	background-size: 32px !important;
	width: 32px;
	height: 32px;
	border-radius: 0;
}

/* settings */
#column-settings {
	left: 40%;
	position: fixed;
	top: 25%;
	z-index: 9999;
	min-width: 372px;
}
#column-settings .elgg-head {
	background-color: #EEE;
	margin: -5px -5px 5px;
	padding-bottom: 5px;
	cursor: move;
}
#column-settings .elgg-head h3 {
	color: #666666;
	float: left;
	padding: 4px 30px 0 5px;
}
#column-settings .elgg-head a {
	display: inline-block;
	height: 18px;
	position: absolute;
	right: 5px;
	top: 5px;
	width: 18px;
	cursor: pointer;
}
#column-settings #deck-column-settings {
	float:left;
}
#column-settings .filter {
	float:left;
	border-right: 2px solid #EEE;
	padding-right: 10px;
	margin-right: 10px;
}
#column-settings .elgg-input-checkboxes label {
	font-weight: normal;
}
#column-settings .box-settings {
	width: 200px;
	float: left;
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
