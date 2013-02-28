<?php
/**
 * Deck-river French language file.
 *
 */

$french = array(
	'deck_river:activity:none' => "Il n'y a pas d'activité à afficher.",
	'deck_river:edit' => 'Modifier les paramètres de la colonne',
	'deck_river:refresh' => 'Rafraîchir la colonne',
	'deck_river:refresh-all' => 'Rafraîchir toutes les colonnes',
	'deck_river:add-column' => 'Ajouter une nouvelle colonne',
	'deck_river:add-tab' => 'Ajouter un nouvel onglet',
	'deck_river:limitColumnReached' => 'Le nombre maximum de colonne est atteint.',
	'river:mentions' => "Mentions",
	'deck_river:more' => "Plus...",
	'deck-river:reduce_url:string' => "Réduire un lien...",
	'deck-river:reduce_url' => "Réduire",
	'deck-river:copy_url' => "Insérer",
	'deck-river:clean_url' => "Effacer",
	'responseToHelper:text' => "En réponse à %s : <span>%s</span>",
	'responseToHelper:delete' => "Ne plus répondre à %s",

	'deck_river:helper:friends' => "Vous n'avez pas d'abonnement ou les personnes que vous suivez n'ont aucune activité.<br/><br/><a href='" . elgg_get_site_url() . "members'>Suivez des personnes</a> dont l'activité pourrait vous intéresser ou cherchez des personnes <a href='" . elgg_get_site_url() . "groups/members/%s'>de votre commune</a> ou <a href='" . elgg_get_site_url() . "groups/members/%s'>votre département</a>.",
	'deck_river:helper:now' => "<h3>Bienvenue sur ggouv.fr !</h3><br/><a href='#' onclick='$(\"#thewire-textarea\").focus();'>Dites bonjour à tout le monde</a> et regardez ce qui se passe près de chez vous <a href='" . elgg_get_site_url() . "groups/profile/%s'>dans votre commune</a> ou <a href='" . elgg_get_site_url() . "groups/profile/%s'>votre département</a>.<br/><br/>Pour collaborer et participer à des actions collectives, <a href='" . elgg_get_site_url() . "groups/all'>cherchez un groupe</a> qui partage vos centres d'intérêts, vos motivations...",
	'deck_river:helper:mine' => "Vous n'avez pas d'activité.<br/>Publiez un message, entrez dans un groupe pour collaborer, ou participez à une action...",
	'deck_river:helper:mention' => "Personne ne vous a mentionné pour l'instant.",

	// wire network
	'deck-river:add:network' => "Ajouter un réseau",
	'deck-river:ggouv:account' => "Compte Ggouv :",
	'deck-river:twitter:account' => "Compte Twitter :",
	'deck-river:network:pin' => "<div style=\"text-align: left;\">Épingler<br><span class=\"elgg-text-help\">Ce compte restera toujours dans les réseaux favoris.</span></div>",
	'deck-river:add:network:slide' => "<span>↕</span> Glissez pour ajouter ou enlever <span>↕</span>",

	// river menu
	'replyall' => "Répondre à tous",
	'river:timeline' => "Le flux",
	'river:timeline:definition' => "Activité de mes abonnements",
	'river:group' => "Groupe",
	'river:filtred' => "filtré",
	'river:search' => "Recherche sur %s",
	'retweet' => "Retweeter",
	'retweet:one' => "%s retweet",
	'retweet:twoandmore' => "%s retweets",
	'deck_river:show_discussion' => "Afficher la discussion",
	'deck_river:toggle_discussion' => "Afficher/masquer la discussion",

	// add tab form
	'deck_river:add_tab_title' => 'Ajouter un nouvel onglet :',
	'deck_river:add:tab:error' => 'Cannot add a new tab.',
	'deck_river:rename_tab_title' => "renomer l'onglet :",

	// delete
	'deck_river:delete:tab:confirm' => "Êtes-vous sûr de supprimer l'onglet '%s' ?",
	'deck_river:delete:tab:error' => "Impossible de supprimer l'onglet.",
	'deck-river:delete:column:confirm' => "Êtes-vous sûr de supprimer cette colonne ?",

	// column-settings form
	'deck_river:settings' => 'Paramètres de la colonne',
	'deck_river:type' => "Sélectionnez le type de flux :",
	'deck_river:filter' => 'Filtre :',
	'deck_river:title' => 'Titre de la colonne :',
	'deck_river:search' => 'Recherche :',
	'deck_river:filter:all' => 'Tout',
	'deck_river:twitter:usersettings:request:title' => "Autorisez %s à accéder à votre compte Twitter",
	'deck_river:twitter:usersettings:request' => "Vous pourrez ainsi ajouter des colonnes avec les flux de vos abonnés, vos abonnements, vos listes...<br/><a id=\"authorize-twitter\" data-url=\"%s\" class=\"elgg-button elgg-button-action mtm\">Faire la demande à Twitter</a>",
	'deck_river:twitter:your_account' => "Votre profil Twitter lié à %s :",
	'deck_river:twitter:choose:account' => "Choisissez le compte Twitter pour cette colonne :",
	'deck_river:twitter:add:account' => "Ajouter un autre compte Twitter",

	//info popups
	'deck-river:popups:close' => "Fermer cette fenêtre",
	'deck-river:popups:pin' => "<div style=\"text-align: left;\">Épingler cette fenêtre<br><span class=\"elgg-subtext\">Elle ne disparaîtra pas lors des changements de page.</span></div>",
	'deck_river:user-not-exist' => "Cet utilisateur ne semble pas exister.",
	'deck_river:user-info-header' => "Informations sur %s",
	'deck_river:hashtag-info-header' => "Recherche : %s",

	// plugin settings
	'deck_river:settings:min_width_column' => 'Largeur minimum des colonnes',
	'deck_river:settings:max_nbr_column' => 'Nombre maximum de colonnes',
	'deck_river:settings:default_column' => 'Colonnes par défault pour les nouveaux utilisateurs',
	'deck_river:settings:default_column_default_params' => 'Colonnes standards :',
	'deck_river:settings:column_type' => "Type de colonnes possibles",
	'deck_river:settings:keys_to_merge' => 'Entités à combiner dans les paramètres de colonnes',
	'deck_river:settings:keys_to_merge_string_register_entity' => '<strong>Exemple :</strong> page=page_top (le premier élément sera affiché. Séparez par des virgules)<br /><strong>Entités enregistrées sur ce site :</strong>',
	'deck_river:settings:reset_user' => "Remettre à zéro les paramètres de colonnes d'un utilisateur. Entrez son ID",
	'deck_river:settings:reset_user:ok' => "Les paramètres des colonnes de l'utilisateur %s ont été remis à zéro.",
	'deck_river:settings:reset_user:nok' => "Impossible de remettre à zéro les paramètres des colonnes de l'utilisateur %s.",
	'deck_river:settings:twitter_consumer_key' => "Consumer key :",
	'deck_river:settings:twitter_consumer_secret' => "Consumer secret :",

	// urlshortener
	'deck_river:url-not-exist' => "Il n'y a pas l'url à réduire.",
	'deck_river:url-bad-format' => "Le format d'url n'est pas bon.",

	// Twitter
	'deck_river:twitter:authorize:success' => "Twitter a autorisé l'accès à votre compte.",
	'deck_river:twitter:authorize:success' => "Le compte Twitter n'a pas pu être autorisé par Twitter.",
	'deck_river:twitter:revoke:success' => "L'accès à Twitter a été supprimé.",
	'deck_river:twitter:revoke:error' => "Le compte Twitter n'a pas pu être supprimé.",
	'deck_river:twitter:feed:search' => "Recherche sur Twitter",
	'deck_river:twitter:feed:search:tweets' => "Rechercher un mot ou un hashtag",
	'deck_river:twitter:feed:search:popular' => "Recherche triée par tweets les plus populaires",
	'deck_river:twitter:feed:users:search' => "Rechercher des utilisateurs",

	'deck_river:twitter:access:error' => "Impossible d'accéder à Twitter. Erreur retournée :<br/>%s %s",

	// messages
	'thewire:twitter:posted' => "Votre message a été publié sur Twitter.",
	'thewire:blank' => "??? Il faut écrire un peu avant d'envoyer son message...",
	'thewire:nonetwork' => "Vous n'avez pas sélectionné de réseau.",

);

add_translation('fr', $french);
