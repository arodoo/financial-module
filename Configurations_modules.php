<?php

//////////////////////////MODULES CMS CONFIGURATIONS
///////////////////////////////SELECT
$req_select = $bdd->prepare("SELECT * FROM CMS_MODULES_CONFIGURATIONS WHERE id=?");
$req_select->execute(array('1'));
$ligne_select = $req_select->fetch();
$req_select->closeCursor();

$id_type_cms = $ligne_select['id_type_cms'];

$url_abonnement_sufix = $ligne_select['url_abonnement_sufix'];
$titre_page_abonnement = $ligne_select['titre_page_abonnement'];
$description_sous_titre_abonnement = $ligne_select['description_sous_titre_abonnement'];
$description_sous_les_blocs_des_forfaits = $ligne_select['description_sous_les_blocs_des_forfaits'];
$Title_de_la_page_abonnement = $ligne_select['Title_de_la_page_abonnement'];
$meta_description_de_la_page_abonnement = $ligne_select['meta_description_de_la_page_abonnement'];
$meta_keywords_de_la_page_abonnement = $ligne_select['meta_keywords_de_la_page_abonnement'];

$afficher_bloc_abonnement = $ligne_select['afficher_bloc_abonnement'];
$afficher_bloc_abonnement_accueil = $ligne_select['afficher_bloc_abonnement_accueil'];
$afficher_grille_abonnement = $ligne_select['afficher_grille_abonnement'];
$afficher_grille_abonnement_accueil = $ligne_select['afficher_grille_abonnement_accueil'];
$titre_h2_grille_abonnement = $ligne_select['titre_h2_grille_abonnement'];

$Operation_code_promotion= $ligne_select['Operation_code_promotion'];

$type_de_compte_module= $ligne_select['type_de_compte_module'];
$messagerie_module= $ligne_select['messagerie_module'];
$paiements_module= $ligne_select['paiements_module'];
$Facturations_module= $ligne_select['Facturations_module'];
$code_promo_module= $ligne_select['code_promo_module'];
$Devis_module= $ligne_select['Devis_module'];
$Demande_de_devis_page_module= $ligne_select['Demande_de_devis_page_module'];
$Module_facture_commercial_active= $ligne_select['Module_facture_commercial_active'];
$Calendrier_page_module = $ligne_select['Calendrier_page_module'];
$activer_option_menu_categorie_page= $ligne_select['activer_option_menu_categorie_page'];
$Services_et_produits= $ligne_select['Services_et_produits'];
$Commandes= $ligne_select['Commandes'];
$Commandes_tracking= $ligne_select['Commandes_tracking'];
$type_de_compte_profil= $ligne_select['type_de_compte_profil'];
$Profil_module= $ligne_select['Profil_module'];
$Remplissage_du_profil_obligatoire= $ligne_select['Remplissage_du_profil_obligatoire'];

$Profil_public_coordonnees= $ligne_select['Profil_public_coordonnees'];

$Profil_compte_avatar_image= $ligne_select['Profil_compte_avatar_image'];
$Profil_compte_avatar_image_obligatoire= $ligne_select['Profil_compte_avatar_image_obligatoire'];

$Profil_type_compte_service_ouvert= $ligne_select['Profil_type_compte_service_ouvert'];

$Profil_public_titre= $ligne_select['Profil_public_titre'];
$Profil_public_titre_obligatoire= $ligne_select['Profil_public_titre_obligatoire'];
$Profil_public_description= $ligne_select['Profil_public_description'];
$Profil_public_description_obligatoire= $ligne_select['Profil_public_description_obligatoire'];

$Profil_module_url_page= $ligne_select['Profil_module_url_page'];

$Profil_module_video= $ligne_select['Profil_module_video'];
$Profil_portfolio= $ligne_select['Profil_portfolio'];
$Profil_avis= $ligne_select['Profil_avis'];
$Profil_public_google_map= $ligne_select['Profil_public_google_map'];
$Profil_public_reseaux_sociaux= $ligne_select['Profil_public_reseaux_sociaux'];
$Profil_public_site_web= $ligne_select['Profil_public_site_web'];
$Profil_public_site_web_obligatoire= $ligne_select['Profil_public_site_web_obligatoire'];
$Profil_public_skype= $ligne_select['Profil_public_skype'];
$Profil_public_skype_obligatoire= $ligne_select['Profil_public_skype_obligatoire'];

$Profil_public_messagerie= $ligne_select['Profil_public_messagerie'];

$Profil_compte_mail_confirmer= $ligne_select['Profil_compte_mail_confirmer'];
$Profil_compte_mail_confirmer_obligatoire= $ligne_select['Profil_compte_mail_confirmer_obligatoire'];
$Profil_compte_telephone_confirmer= $ligne_select['Profil_compte_telephone_confirmer'];
$Profil_compte_telephone_confirmer_obligatoire= $ligne_select['Profil_compte_telephone_confirmer_obligatoire'];

$Module_commerciaux_type_compte= $ligne_select['Module_commerciaux_type_compte'];
$Profil_facture= $ligne_select['Profil_facture'];
$Profil_devis= $ligne_select['Profil_devis'];
$Profil_demande_de_devis= $ligne_select['Profil_demande_de_devis'];

$cookies_validation_module = $ligne_select['cookies_validation_module'];
$page_ajouter_module = $ligne_select['page_ajouter_module'];
$page_photos_module = $ligne_select['page_photos_module'];
$page_information_module = $ligne_select['page_information_module'];
$gestion_page_dans_menu = $ligne_select['gestion_page_dans_menu'];
$gestion_page_dans_footer = $ligne_select['gestion_page_dans_footer'];

$Abonnements_forfaits_activer = $ligne_select['Abonnements_forfaits_activer'];
$Abonnements_forfaits_type_de_compte = $ligne_select['Abonnements_forfaits_type_de_compte'];
$url_abonnement_sufix = $ligne_select['url_abonnement_sufix'];

$activer_bandeau_flash_abonnement = $ligne_select['activer_bandeau_flash_abonnement'];
$type_bandeau_flash_abonnement = $ligne_select['type_bandeau_flash_abonnement'];
$type_cible_abonnement = $ligne_select['type_cible_abonnement'];
$type_icone_flash_abonnement = $ligne_select['type_icone_flash_abonnement'];
$contenu_bandeau_flash_abonnement = $ligne_select['contenu_bandeau_flash_abonnement'];
$activer_bandeau_options_abonnement = $ligne_select['activer_bandeau_options_abonnement'];
$contenu_bandeau_options_abonnement = $ligne_select['contenu_bandeau_options_abonnement'];
$activer_bandeau_contact_abonnement = $ligne_select['activer_bandeau_contact_abonnement'];
$contenu_bandeau_contact_abonnement = $ligne_select['contenu_bandeau_contact_abonnement'];
$activer_bandeau_renouvellement_abonnement = $ligne_select['activer_bandeau_renouvellement_abonnement'];
$contenu_bandeau_renouvellement_abonnement = $ligne_select['contenu_bandeau_renouvellement_abonnement'];

$Mise_en_relation_module_litige = $ligne_select['Mise_en_relation_module_litige'];

/////////////////////GOOGLE MAP
$google_map_accueil = "";
//Google map position variables 1 (haut) ou 2 (bas)
$google_map_accueil_position = "2";
$google_map_accueil_hauteur_en_px = "400";
/////////////////////GOOGLE MAP

////////////////////////////////////CONDITIONS POUR REQUÊTES SELON MODULES ACTIVES
if($inscription != "oui"){
$inscription_activer_non = "AND Page_inscription!='oui'";
}
if($blog_actualite != "oui"){
$blog_actualite_activer_non = "AND Page_blog_actualite!='oui'";
}
if($livre_d_or != "oui"){
$livre_d_or_activer_non = "AND Page_livre_d_or!='oui'";
}

$requete_sql_module_activee = "$inscription_activer_non $portefolio_activer_non $blog_actualite_activer_non $livre_d_or_activer_non";
////////////////////////////////////CONDITIONS POUR REQUÊTES SELON MODULES ACTIVES

?>