<?php
/**
 * Deck-river French language file.
 *
 */

$french = array(
	'deck_river:activity:none' => "Il n'y a pas d'activité à afficher.",
	'deck_river:edit' => 'Éditer les paramètres de la colonne',
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
	
	// river menu
	'replyall' => "Répondre à tous",
	'river:timeline' => "Le flux",
	'river:timeline:definition' => "Activité de mes abonnements",
	'river:group' => "Groupe",
	'river:filtred' => "filtré",
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
	'deck_river:settings:blank' => 'Paramètres',
	'deck_river:settings' => 'Paramètres de la colonne "%s"',
	'deck_river:type' => 'Type :',
	'deck_river:filter' => 'Filtre :',
	'deck_river:title' => 'Titre de la colonne :',
	'deck_river:search' => 'Recherche :',
	'deck_river:filter:all' => 'Tout',
	
	//info popup
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
	
	// messages
	'deck_river:url-not-exist' => "Il n'y a pas l'url à réduire.",
	'deck_river:url-bad-format' => "Le format d'url n'est pas bon.",
	
	// Twitter
	'deck_river:twitter:usersettings:request' => "Vous devez d'abord <a id=\"authorize-twitter\" data-url=\"%s\" class=\"elgg-button elgg-button-action mts\">autoriser %s</a><br/>à accéder à votre compte Twitter.",
	
);

add_translation('fr', $french);
