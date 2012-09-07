<?php
/**
 * Deck-river French language file.
 *
 */

$french = array(
	'deck_river:edit' => 'Éditer les paramètres de la colonne',
	'deck_river:refresh' => 'Rafraîchir la colonne',
	'deck_river:refresh-all' => 'Rafraîchir toutes les colonnes',
	'deck_river:add-column' => 'Ajouter une nouvelle colonne',
	'deck_river:add-tab' => 'Ajouter un nouvel onglet',
	'deck_river:limitColumnReached' => 'Le nombre maximum de colonne est atteint.',
	'river:mentions' => "Mentions",
	
	// river menu
	'replyall' => "Répondre à tous",
	'river:timeline' => "Le flux",
	'river:timeline:definition' => "Activité de vos abonnés",
	'river:group' => "Groupe",
	'river:filtred' => "filtré",
	'retweet' => "Retweet",
	
	// add tab form
	'deck_river:add_tab_title' => 'Ajouter un nouvel onglet :',
	'deck_river:add:tab:error' => 'Cannot add a new tab.',
	'deck_river:rename_tab_title' => "renomer l'onglet :",
	
	// delete tab
	'deck_river:delete:tab:confirm' => "Êtes-vous sûr de supprimer l'onglet '%s' ?",
	'deck_river:delete:tab:error' => "Impossible de supprimer l'onglet.",
	
	// column-settings form
	'deck_river:settings' => 'Paramètres de la colonne "%s"',
	'deck_river:type' => 'Type :',
	'deck_river:filter' => 'Filtre :',
	'deck_river:title' => 'Titre de la colonne :',
	'deck_river:search' => 'Recherche :',
	
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
	
);

add_translation('fr', $french);
