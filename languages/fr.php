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
	'deck_river:limitColumnReached' => 'Le nombre maximum de colonnes est atteint.',
	'river:mentions' => "Mentions",
	'river:a_group_activity' => "Activité d'un groupe",
	'river:group_activity' => "Activité du groupe",
	'river:a_group_mentions' => "Mentions d'un !groupe",
	'river:group_mentions' => "Mentions du groupe",
	'deck_river:more' => "Plus...",
	'deck-river:reduce_url:string' => "Réduire un lien...",
	'deck-river:reduce_url' => "Réduire",
	'deck-river:copy_url' => "Insérer",
	'deck-river:clean_url' => "Effacer",
	'responseToHelper:text' => "En réponse à %s : <span>%s</span>",
	'responseToHelper:delete' => "Ne plus répondre à %s",
	'deck_river:column:gotop' => "⬆ %s éléments non-lus ⬆",
	'deck_river:linkbox:hidepicture' => "Cliquez pour ne pas ajouter d'image",

	'deck_river:helper:friends' => "<span class='gwf'>w</span><br/>Vous n'avez pas d'abonnement ou les personnes que vous suivez n'ont aucune activité.<br/><br/><a href='" . elgg_get_site_url() . "members'>Suivez des personnes</a> dont l'activité pourrait vous intéresser ou cherchez des personnes <a href='" . elgg_get_site_url() . "groups/members/%s'>de votre commune</a> ou <a href='" . elgg_get_site_url() . "groups/members/%s'>votre département</a>.",
	'deck_river:helper:mine' => "<span class='gwf'>A</span><br/><a href='#' onclick='$(\"#thewire-textarea\").focus();'>Dites bonjour à tout le monde</a> en envoyant un message de bienvenue, votre humeur ou votre motivation...",
	//'deck_river:helper:mine' => "Vous n'avez pas d'activité.<br/>Publiez un message, entrez dans un groupe pour collaborer, ou participez à une action...",
	'deck_river:helper:mention' => "<span class='gwf'>m</span><br/>Personne ne vous a mentionné pour l'instant.<br/><br/>Des éléments seront affichés dans cette colonne quand quelqu'un vous aura mentionné dans un message, un commentaire, un article...",
	'deck_river:helper:group' => "<span class='gwf'>K</span><br/>Regardez ce qui se passe près de chez vous <a href='" . elgg_get_site_url() . "groups/profile/%s'>dans votre commune</a> ou <a href='" . elgg_get_site_url() . "groups/profile/%s'>votre département</a>.<br/><br/>Pour collaborer et participer à des actions collectives, <a href='" . elgg_get_site_url() . "groups/all'>cherchez un groupe</a> qui partage vos centres d'intérêts, vos motivations...",
	'deck_river:helper:group_mention' => "<span class='gwf'>!</span><br/>Le groupe n'a pas été mentionné.<br/><br/>Peut-être qu'il n'est pas très actif. Vous pouvez essayer de faire quelque chose ?<br/>Si c'est votre groupe local, faites venir du monde et essayez d'agir ensemble !",
	'deck_river:helper:search' => "<span class='gwf'>L</span><br/>Rien ne correspond à votre recherche.<br/>Essayez autre chose...",
	'deck_river:helper:nothing' => "Aucun élément à afficher.",

	'usersettings:authorize:applications' => "Vos réseaux connectés",
	'deck_river:account:createdby' => "Compte %s connecté à %s par %s ",
	'deck_river:account:deleteconfirm' => "Êtes-vous sur de vouloir supprimer ce compte ?",

	// wire network
	'deck-river:add:network' => "Ajouter un réseau",
	'deck-river:network:pin' => "<div style=\"text-align: left;\">Épingler<br><span class=\"elgg-text-help\">Ce compte restera toujours actifs.<br/>Vous pourrez le désactiver temporairement en cliquant dessus.</span></div>",
	'deck-river:add:network:helper' => "<div style=\"text-align: left;\">Au dessus :<br><span class=\"elgg-text-help\">Vos comptes actifs vers lesquels vos messages seront envoyés.</span>Dessous :<br><span class=\"elgg-text-help\">Vos comptes enregistrés et inactifs.</span></div>",
	'deck-river:add:network:slide' => "<span>↕</span> Glissez pour ajouter ou enlever <span>↕</span>",
	'deck_river:error:pin:too_much' => "Vous ne pouvez pas activer plus de 5 comptes !",

	// river menu
	'replyall' => "Répondre à tous",
	'river:timeline' => "Le flux",
	'river:timeline:definition' => "Activité de mes abonnements",
	'river:group' => "Groupe",
	'river:filtred' => "filtré",
	'river:search' => "Recherche sur %s",
	'retweet' => "Retweeter",
	'retweeted_by' => "Retweeté par %s",
	'retweeted_which' => "%s retweets dont %s",
	'retweet:one' => "1 retweet",
	'retweet:twoandmore' => "%s retweets",
	'deck_river:thread' => "la discussion",
	'deck_river:thread:show' => "Afficher ", // !space at the end
	'deck_river:thread:hide' => "Masquer ", // !space

	// add tab form
	'deck_river:add_tab_title' => "Ajouter un nouvel onglet :",
	'deck_river:add:tab:error' => "Erreur : impossible d'ajouter un nouvel onglet.",
	'deck_river:rename_tab_title' => "Renommer l'onglet :",

	// delete
	'deck_river:delete:tab:confirm' => "Êtes-vous sûr de vouloir supprimer l'onglet '%s' ?",
	'deck_river:delete:tab:error' => "Erreur : impossible de supprimer l'onglet.",
	'deck-river:delete:column:confirm' => "Êtes-vous sûr de supprimer cette colonne ?",

	// column-settings form
	'deck_river:settings' => 'Paramètres de la colonne',
	'deck_river:type' => "Sélectionnez le type de flux :",
	'deck_river:filter' => 'Filtre :',
	'deck_river:title' => 'Titre de la colonne :',
	'deck_river:search' => 'Recherche :',
	'deck_river:a_search' => 'Une recherche',
	'deck_river:filter:all' => 'Tout',

	// accounts managment
	'deck_river:network:add:account' => "Ajouter un autre compte",
	'deck_river:network:manage_account' => "Gérer mes comptes",
	'deck_river:network:too_many_accounts' => "Vous avez trop de comptes associés à %s. Vous ne pouvez plus en ajouter d'autres...",
	'deck_river:network:authorize:already_done' => "Vous avez déjà associé ce compte.",
	'deck_river:network:authorize:error' => "Le compte n'a pas pu être autorisé.",
	'deck_river:network:revoke:error' => "Le compte n'a pas pu être supprimé.",

	'deck_river:twitter:authorize:servor_fail' => "%s ne peut pas accéder à Twitter.",
	'deck_river:twitter:authorize:request:title' => "Autorisez %s à accéder à votre compte Twitter",
	'deck_river:twitter:authorize:request:button' => "Faire la demande à Twitter",
	'deck_river:twitter:authorize:success' => "Twitter a autorisé l'accès à votre compte.",
	'deck_river:twitter:revoke:success' => "L'accès à Twitter a été supprimé.",
	'deck_river:twitter:columnsettings:request' => "Vous pourrez ainsi ajouter des colonnes avec les flux de vos abonnements, vos listes, vos messages directs...",
	'deck_river:twitter:add_network:request' => "<li>Vous pourrez suivre les flux vos abonnements, vos listes, vos messages directs... directement depuis %s !</li><li>Vous pourrez aussi envoyer vos tweets sans aller sur Twitter...</li>",
	'deck_river:twitter:your_account' => "Votre profil Twitter lié à %s :",
	'deck_river:twitter:choose:account' => "Choisissez le compte Twitter pour cette colonne :",

	'deck_river:facebook:authorize:servor_fail' => "%s ne peut pas accéder à Facebook.",
	'deck_river:facebook:authorize:request:title' => "Autorisez %s à accéder à votre compte Facebook",
	'deck_river:facebook:authorize:request:button' => "Faire la demande à Facebook",
	'deck_river:facebook:authorize:success' => "Facebook a autorisé l'accès à votre compte.",
	'deck_river:facebook:authorize:error' => "Le compte Facebook n'a pas pu être autorisé par Facebook.",
	'deck_river:facebook:revoke:success' => "L'accès à Facebook a été supprimé.",
	'deck_river:facebook:revoke:error' => "Le compte Facebook n'a pas pu être supprimé.",
	'deck_river:facebook:columnsettings:request' => "Vous pourrez ainsi ajouter des colonnes affichant l'activité de votre mur, vos groupes et vos pages...",
	'deck_river:facebook:add_network:request' => "<li>Vous pourrez suivre l'activité de votre mur, vos groupes et vos pages... depuis le hub de communication !</li><li>Vous pourrez aussi publier sur Facebook directement depuis %s...</li>",
	'deck_river:facebook:your_account' => "Votre profil Facebook lié à %s :",
	'deck_river:facebook:choose:account' => "Choisissez le compte Facebook pour cette colonne :",
	'deck_river:facebook:account:add_groups' => "Ajouter un groupe à partir de ce compte",

	//info popups
	'deck-river:popups:close' => "Fermer cette fenêtre",
	'deck-river:popups:pin' => "<div style=\"text-align: left;\">Épingler cette fenêtre<br><span class=\"elgg-text-help\">Elle ne disparaîtra pas lors des changements de page.</span></div>",
	'deck_river:user-not-exist' => "Cet utilisateur ne semble pas exister.",
	'deck_river:user-info-header' => "Informations sur %s",
	'deck_river:group-info-header' => "Informations sur le groupe %s",
	'deck_river:hashtag-info-header' => "Recherche : %s",
	'deck_river:go_to_profile' => "Aller sur le profil",
	'deck_river:twitter:choose_account' => "Choisissez le compte Twitter",
	'deck_river:facebook:groups' => "Groupes Facebook",
	'deck_river:facebook:groups:choose' => "Cliquez sur un groupe pour l'ajouter dans vos comptes :",

	// plugin settings
	'deck_river:settings:min_width_column' => "Largeur minimum des colonnes",
	'deck_river:settings:max_nbr_column' => "Nombre maximum de colonnes",
	'deck_river:settings:default_column' => "Colonnes par défault pour les nouveaux utilisateurs",
	'deck_river:settings:default_column_default_params' => "Colonnes standards :",
	'deck_river:settings:column_type' => "Type de colonnes possibles",
	'deck_river:settings:keys_to_merge' => "Entités à combiner dans les paramètres de colonnes",
	'deck_river:settings:keys_to_merge_string_register_entity' => '<strong>Exemple :</strong> page=page_top (le premier élément sera affiché. Séparez par des virgules)<br /><strong>Entités enregistrées sur ce site :</strong>',
	'deck_river:settings:reset_user' => "Remettre à zéro les paramètres des colonnes d'un utilisateur. Entrez son ID",
	'deck_river:settings:site_shorturl' => "ShortURL de votre site elgg",
	'deck_river:settings:googleApiKey' => "Clé API de google",
	'deck_river:settings:reset_user:ok' => "Les paramètres des colonnes de l'utilisateur %s ont été remis à zéro.",
	'deck_river:settings:reset_user:nok' => "Impossible de remettre à zéro les paramètres des colonnes de l'utilisateur %s.",
	'deck_river:settings:twitter_consumer_key' => "Consumer key :",
	'deck_river:settings:twitter_consumer_secret' => "Consumer secret :",
	'deck_river:settings:facebook_app_id' => "App ID :",
	'deck_river:settings:facebook_app_secret' => "App secret :",

	// urlshortener
	'deck_river:url-not-exist' => "Il n'y a pas l'url à réduire.",
	'deck_river:url-bad-format' => "Le format d'url n'est pas bon.",

	// Twitter
	'item:object:twitter_account' => "Comptes Twitter",
	'deck_river:twitter:feed:search' => "Recherche sur Twitter",
	'deck_river:twitter:feed:search:tweets' => "Rechercher un mot ou un hashtag",
	'deck_river:twitter:feed:search:popular' => "Recherche triée par tweets les plus populaires",
	'deck_river:twitter:feed:users:search' => "Rechercher des utilisateurs",
	'deck_river:twitter:list' => "Liste",
	'deck_river:twitter:lists' => "Listes",
	'deck_river:twitter:via' => "via",

	'deck_river:twitter:feed:home' => "Flux d'accueil",
	'deck_river:twitter:feed:user' => "Mes tweets",
	'deck_river:twitter:feed:dm:recept' => "Messages directs (Boîte de réception)",
	'deck_river:twitter:feed:dm:sent' => "Messages directs (Boîte d'envoi)",
	'deck_river:twitter:feed:favorites' => "Mes tweets favoris",
	'deck_river:twitter:notweet' => "Pas de tweet.",
	'deck_river:twitter:follow' => "Suivre sur Twitter",
	'deck_river:twitter:unfollow' => "Ne plus suivre sur Twitter",

	'deck_river:twitter:access:error' => "Impossible d'accéder à Twitter. Erreur retournée :<br/>%s %s",

	// messages
	'deck_river:message:blank' => "??? Il faut écrire le message avant de l'envoyer...",
	'deck_river:nonetwork' => "Vous n'avez pas sélectionné de réseau.",
	'deck_river:error:pin' => "Impossible de ne plus épingler ou épingler ce compte.",
	'deck_river:ok:pin' => "Ce compte a été épinglé.",
	'deck_river:ok:unpin' => "Ce compte n'est plus épinglé.",
	'deck_river:error:pin:too_much' => "Vous ne pouvez pas épingler plus de 5 comptes.",
	'deck_river:delete:network:error' => "Impossible de supprimer ce compte.",
	'deck_river:network:too_many_accounts' => "Vous ne pouvez pas associer plus de compte !",

	'deck_river:twitter:posted' => "Votre message a été publié sur Twitter.",
	'deck_river:twitter:post:error' => "Votre message n'a pas pu être publié sur Twitter.<br>Erreur retourné : %s<br/>%s",
	'deck_river:twitter:post:error:150' => "Vous ne pouvez pas envoyer un message privé à quelqu'un qui ne vous suit pas sur Twitter.", // "You cannot send messages to users who are not following you",
	'deck_river:twitter:post:error:187' => "Vous avez déjà envoyé ce message. C'est un doublon.", // "Status is a duplicate"
	'deck_river:twitter:post:post_friendshipsCreate' => "Vous suivez maintenant %s sur Twitter.",
	'deck_river:twitter:error' => "Il y a eu une erreur avec Twitter.<br>Erreur retourné : %s<br/>%s",
	'deck_river:twitter:error:discussion' => "Twitter indique que ce tweet est inconnu !",
	'deck_river:twitter:error:34' => "La page ou l'utilisateur n'existe pas sur Twitter.", // "Sorry, that page does not exist",
	'deck_river:twitter:error:215' => "Il y a une erreur d'identification avec Twitter.", // "Bad authentication data"
	//'deck_river:twitter:error:34' => "Vous avez atteint la limite de requêtes sur Twitter.<br/>Attendez un peu (15 min max).", // "Rate limit exceeded",

	//Facebook
	'deck_river:facebook:posted' => "Votre message a été publié sur Facebook. <a href=\"%s\" target=\"_blank\">Voir le message</a>.",
	'deck_river:facebook:posted:error' => "Erreur avec l'API de Facebook",

);

add_translation('fr', $french);
