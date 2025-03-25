<?php
//ini_set('session.name', 'GQILEUjyTsgZKDdfaegzlGFJH');
//ini_set('session.gc_maxlifetime', 2592000);
//ini_set('session.cookie_lifetime', 2592000);
//ini_set('session_cache_expire', 1440);
session_start();
//setlocale("LC_TIME", "fr_FR");

$kmLimit = 500;
$kmLimit2 = 1000;

$nom_annuaire_1_id = "1";
$nom_annuaire_1 = "Annonces";
$title_annuaire_1 = "Mise en relation";
$nom_annuaire_1_titre = "Mise en relation";
$nom_annuaire_1_meta = "Rechercher les projets.";

$nom_annuaire_2_id = "2";
$nom_annuaire_2 = "Missions";
$title_annuaire_2 = "Trouver des missions pour des Extras";
$nom_annuaire_2_titre = "Missions pour extras";
$nom_annuaire_2_meta = "Rechercher et trouver des missions pour extras dans la restauration et l'hotellerie en étant mis en relation sur la plateforme.";

///////////////////////////////Requêtes connexion
if(!empty($_SESSION['pseudo']) && !empty($_SESSION['4M8e7M5b1R2e8s'])){

///////////////////////////////SELECT
$req_select = $bdd->prepare("SELECT * FROM membres WHERE id=? AND Activer=?");
$req_select->execute(array($_SESSION['pseudo'],"oui"));
$ligne_select = $req_select->fetch();
$membreco = $ligne_select;
$req_select->closeCursor();
$user = $ligne_select['pseudo'];
$id_user = $ligne_select['id'];

if(empty($id_user)){
	unset($_SESSION['pseudo']);
	unset($_SESSION['4M8e7M5b1R2e8s']);
}

}
///////////////////////////////Requêtes connexion

///////////////////////////////Informations mise en page mail
///////////////////////////////SELECT
$req_select = $bdd->prepare("SELECT * FROM configuration_email WHERE id=?");
$req_select->execute(array("1"));
$ligne_select = $req_select->fetch();
$req_select->closeCursor();
$idcfgmail = $ligne_select['id'];
$entete = $ligne_select['entete'];
$pieddepage = $ligne_select['pieddepage'];
$login_smtp_site = $ligne_select["login_smtp_site"];
$password_smtp_site = $ligne_select["password_smtp_site"];
$Validation_openDKIM = $ligne_select["Validation_openDKIM"];
$Activation_du_TLS = $ligne_select["Activation_du_TLS"];
$SMTPDebug = $ligne_select["SMTPDebug"];
$LISTE_MAIL_CC = $ligne_select['LISTE_MAIL_CC'];
$nomsiteweb = $ligne_select['nom_siteweb'];
$emaildefault = $ligne_select['email_default'];
$logo_mail = $ligne_select['logo_mail']; 
///////////////////////////////Informations mise en page mail

///////////////////////////////Informations des préférences générales
///////////////////////////////SELECT
$req_select = $bdd->prepare("SELECT * FROM configurations_preferences_generales");
$req_select->execute();
$ligne_select = $req_select->fetch();
$req_select->closeCursor();
$Dashboard = $ligne_select['Dashboard'];
$type_template = $ligne_select['type_template'];
$nom_proprietaire = $ligne_select['nom_proprietaire'];
$text_informations_footer = $ligne_select['text_informations_footer'];
$nomsiteweb = $ligne_select['nom_siteweb'];
$http = $ligne_select['http'];
$valeurtva = $ligne_select['tva'];
$jeton_google = $ligne_select['jeton_google'];
$Page_Facebook = $ligne_select['Page_Facebook'];
$Page_twitter = $ligne_select['Page_twitter'];
$Page_instagram = $ligne_select['Page_instagram'];
$Page_Google = $ligne_select['Page_Google'];
$Page_Linkedin = $ligne_select['Page_Linkedin'];
$Chaine_Youtube = $ligne_select['Chaine_Youtube'];
$couleurFOND = $ligne_select['bloc_couleur_fond'];
$couleurbordure = $ligne_select['bloc_couleur_bordure'];
$bloc_couleur_complementaire = $ligne_select['bloc_couleur_complementaire'];
$Google_analytic = $ligne_select['Google_analytic'];
$lien_conditions_generales = $ligne_select['lien_conditions_generales'];
$lien_conditions_generales_compte = $ligne_select['lien_conditions_generales_compte'];
$cookies_validation_module = $ligne_select['cookies_validation_module'];
$texte_cookies = $ligne_select['texte_cookies'];
$type_cookies_alerte = $ligne_select['type_cookies_alerte'];
$cookies_bouton_accepter = $ligne_select['cookies_bouton_accepter'];
$Type_bouton_cookies_alerte = $ligne_select['Type_bouton_cookies_alerte'];
$mod_inscription = $ligne_select['mod_inscription'];
$logo = $ligne_select['logo'];
$favicon = $ligne_select['favicon'];
$prix_abonnement_1 = $ligne_select['prix_abonnement_1'];
$prix_abonnement_2  = $ligne_select['prix_abonnement_2'];
$prix_abonnement_3 = $ligne_select['prix_abonnement_3'];
$hauteur_pub_accueil = $ligne_select['hauteur_pub_accueil'];
$prix_annuaire_annonce = $ligne_select['prix_annuaire_annonce'];
$prix_annuaire_annonce_tranche = $ligne_select['prix_annuaire_annonce_tranche'];
$Prix_du_lead = $ligne_select['Prix_du_lead'];
$inscription_etablissement = $ligne_select['inscription_etablissement'];
$nbr_lead_gratuit = $ligne_select['nbr_lead_gratuit'];
$montant_publicite_bandeau_prix = $ligne_select['montant_publicite_bandeau_prix'];
$credits10 = $ligne_select['credits10'];
$credits20 = $ligne_select['credits20'];
$credits50 = $ligne_select['credits50'];
$prix_credits_cdd = $ligne_select['prix_credits_cdd'];
$prix_credits_cdi = $ligne_select['prix_credits_cdi'];

$platine_prix = $ligne_select['platine_prix'];
$platine_prix3 = $ligne_select['platine_prix3'];
$platine_prix6 = $ligne_select['platine_prix6'];
$platine_prix12 = $ligne_select['platine_prix12'];
$vip_prix = $ligne_select['vip_prix'];
$vip_prix3 = $ligne_select['vip_prix3'];
$vip_prix6 = $ligne_select['vip_prix6'];
$vip_1_semaine_prix = $ligne_select['vip_1_semaine_prix'];
$premium_prix = $ligne_select['premium_prix'];
$premium_prix3 = $ligne_select['premium_prix3'];
$premium_prix6 = $ligne_select['premium_prix6'];
$premium_prix12 = $ligne_select['premium_prix12'];

$prix_prestation_par_extra = $ligne_select['prix_prestation_par_extra'];
$prix_prestation_par_extra_regulier = $ligne_select['prix_prestation_par_extra_regulier'];
$prix_prestation_par_extra_free = $ligne_select['prix_prestation_par_extra_free'];
$prix_demande_de_devis = $ligne_select['prix_demande_de_devis'];
$prix_formation_manageriale = $ligne_select['prix_formation_manageriale'];
$prix_supplement_DUE_et_contrat = $ligne_select['prix_supplement_DUE_et_contrat'];

///////////////////////////////Requêtes informations structure / entreprise
///////////////////////////////SELECT
$req_select = $bdd->prepare("SELECT * FROM informations_structure WHERE id=1");
$req_select->execute();
$ligne_select = $req_select->fetch();
$req_select->closeCursor();
$Nom_ii = $ligne_select['Nom_i'];
$statut_entreprise_ii = $ligne_select['statut_entreprise_i'];
$Siret_ii = $ligne_select['Siret_i'];
$TVA_intra_ii = $ligne_select['TVA_intra_i'];
$adresse_ii = $ligne_select['adresse_i'];
$ville_ii = $ligne_select['ville_i'];
$cp_dpt_ii = $ligne_select['cp_dpt_i'];
$pays_ii = $ligne_select['pays_i'];
$telephone_fixe_ii = $ligne_select['telephone_fixe_i'];
$telephone_portable_ii = $ligne_select['telephone_portable_i'];
$fax_ii = $ligne_select['fax_i'];
$Skype_ii = $ligne_select['Skype_i'];
$activer_carte_map_ii = $ligne_select['activer_carte_map_i'];
$text_i = $ligne_select['text_i'];
$latitude_ii = $ligne_select['latitude_i'];
$longitude_ii = $ligne_select['longitude_i'];
$cle_api_google_i = $ligne_select['cle_api_google_map'];
$text_i = $ligne_select['text_i'];

if(!empty($telephone_fixe_ii)){
$telephone_information = "$telephone_fixe_ii";
}elseif(!empty($telephone_portable_ii)){
$telephone_information = "$telephone_portable_ii";
}
///////////////////////////////Requêtes informations structure / entreprise

///////////////////////////////Informations des préférences générales

///////////////////////////////Si membre enregistré
if(isset($user)){

//Informations membre enregistré
///////////////////////////////SELECT
$req_select = $bdd->prepare("SELECT * FROM membres WHERE pseudo=?");
$req_select->execute(array($user));
$ligne_select = $req_select->fetch();
$req_select->closeCursor();
$id_oo = $ligne_select['id'];
$pseudo_oo = $ligne_select['pseudo'];
$mail_oo = $ligne_select['mail'];
$civilites_oo = $ligne_select['civilites'];
$nom_oo = $ligne_select['nom'];
$prenom_oo = $ligne_select['prenom'];
$categorie_annonceur = $ligne_select['categorie_annonceur'];
$cat_socio = $ligne_select['cat_socio'];
$prenom_autres_oo = $ligne_select['prenom_autres'];
$datenaissance_oo = $ligne_select['datenaissance'];
//$datenaissance_mois1 = date('n', $datenaissance_oo);
//$datenaissance_jour1 = date('j', $datenaissance_oo);
//$datenaissance_annee1 = date('Y', $datenaissance_oo);
$Numero_oo = $ligne_select['Numero'];
$Type_extension_oo = $ligne_select['Type_extension'];
$type_voie_oo = $ligne_select['type_voie'];
$adresse_oo = $ligne_select['adresse'];
$ville_oo = $ligne_select['ville'];
$cp_oo = $ligne_select['cp'];
$newslettre_oo = $ligne_select['newslettre'];
$reglement_accepte_oo = $ligne_select['reglement_accepte'];
$admin_oo = $ligne_select['admin'];
$ip_inscription_oo = $ligne_select['ip_inscription'];
$ip_login_oo = $ligne_select['ip_login'];
$date_enregistrement_oo = $ligne_select['date_enregistrement'];
$Telephone_oo = $ligne_select['Telephone'];
$Telephone_portable_oo = $ligne_select['Telephone_portable'];
$Fax_oo = $ligne_select['Fax'];
$IM_oo = $ligne_select['IM'];
$IM_REGLEMENT_oo = $ligne_select['IM_REGLEMENT'];
$Pays_oo = $ligne_select['Pays'];
$Client_oo = $ligne_select['Client'];
$nbractivation_oo = $ligne_select['nbractivation'];
$statut_compte_oo = $ligne_select['statut_compte'];
$choix_langue_oo = $ligne_select['choix_langue'];
$site_web_oo = $ligne_select['site_web'];
$pseudo_skype_oo = $ligne_select['pseudo_skype'];
$note_profil_oo = $ligne_select['note_profil'];
$image_profil_oo = $ligne_select['image_profil'];
$lien_profil_oo = $ligne_select['lien_profil'];
$last_login_oo = $ligne_select['last_login'];
$latitude_adresse_oo = $ligne_select['latitude_adresse'];
$longitude_adresse_oo = $ligne_select['longitude_adresse'];
$Activer_oo = $ligne_select['Activer'];
$ville_naissance_oo = $ligne_select['ville_naissance'];
$pays_naissance_oo = $ligne_select['pays_naissance'];
$datenaissance_oo = $ligne_select['datenaissance'];
$nom_commercial = $ligne_select['nom_commercial'];

$id_stage = $ligne_select['id_stage'];

$last_login = $ligne_select['last_login'];
$last_ip = $ligne_select['last_ip'];
$date_update = $ligne_select['date_update'];
$date_update_ip = $ligne_select['date_update_ip'];

$platine = $ligne_select['platine'];
$vip = $ligne_select['vip'];
$premium = $ligne_select['premium'];
$paye = $ligne_select['paye'];

$nbr_prestation = $ligne_select['nbr_prestation'];
if($nbr_prestation == "" ){
	$nbr_prestation = 0;
}

if($vip== "oui"){
	$abonnement_information = "Freemium";
}elseif($platine == "oui"){
	$abonnement_information = "Régulier";
}elseif($premium == "oui"){
	$abonnement_information = "Premium";
}

$date_commande = $ligne_select['date_commande'];
$date_commande_fin  = $ligne_select['date_commande_fin'];

$duree_abonnement_mois  = $ligne_select['duree_abonnement_mois'];

$nom_professionnel = $ligne_select['nom_professionnel'];

///////////////////////////////SELECT
$req_select = $bdd->prepare("SELECT * FROM membres_type_de_compte WHERE id=?");
$req_select->execute(array($statut_compte_oo));
$ligne_select = $req_select->fetch();
$req_select->closeCursor();
$id_statut_compte_membre = $ligne_select['Nom_type'];

////////////////////////////////////////////////////MESSAGE NON LU
///////////////////////////////SELECT
$req_select = $bdd->prepare("SELECT COUNT(*) AS idd_message_o_rcc_count FROM membres_messages where pseudo_destinataire=? AND message_lu!='oui'");
$req_select->execute(array($user));
$ligne_select = $req_select->fetch();
$req_select->closeCursor();
$idd_message_o_rcc_count = $ligne_select['idd_message_o_rcc_count']; 

///////////////////////////////SELECT BOUCLE
$req_boucle = $bdd->prepare("SELECT * FROM membres_messages where pseudo_destinataire=? AND message_lu!='oui'");
$req_boucle->execute(array($user));
while($ligne_boucle = $req_boucle->fetch()){
	$idd_message_o_rcc_countb = $ligne_boucle['id']; 

	///////////////////////////////SELECT
	$req_select = $bdd->prepare("SELECT COUNT(*) AS idd_message_o_rc_count FROM membres_messages_reponse where id_message=? AND pseudo!=? AND message_reponse_lu!='oui'");
	$req_select->execute(array($idd_message_o_rcc_countb,$user));
	$ligne_select = $req_select->fetch();
	$req_select->closeCursor();
	$idd_message_o_rcc_count = $ligne_select['idd_message_o_rcc_count']; 
	$idd_message_o_rc_count = $ligne_select['idd_message_o_rc_count']; 
	$idd_message_o_rc_count_tt = ($idd_message_o_rc_count_tt+$idd_message_o_rc_count);
}
$req_boucle->closeCursor();
$total_message_non_lu = ($idd_message_o_rcc_count+$idd_message_o_rc_count_tt);
////////////////////////////////////////////////////MESSAGE NON LU

}
///////////////////////////////Si membre enregistré

// Mode de back office : Statut : jspanel OR stati
$mode_back_office = "jspanel";
$mode_back_office_jspanel_background_color = "DimGray";
$mode_back_office_jspanel_width = "800";
$mode_back_office_jspanel_height = "300";
$mode_back_office_jspanel_position_left = "10";
$mode_back_office_jspanel_position_top = "10";

$page_bak_office_static = "/administration/index-admin.php";

//////////////////SI JSPANEL ADMIN
if($mode_back_office == "jspanel" ){
//BOUTON RETOUR ADMIN BACK OFFICE
//////////////////SI JSPANEL ET OUVERTURE PANEL STATIC
$pageadmin_URI = explode("?", $_SERVER['REQUEST_URI']);
if($pageadmin_URI['0'] == "/index-admin.php"){
$mode_back_lien_interne = "".$page_bak_office_static."";
}else{
$mode_back_lien_interne = "".$page_bak_office_static."";
}
$mode_back_lien = "/administration/index-admin.php";
//BOUTON ADMIN MENU HEADER
$id_admin_bouton_jspanel = "id='ouverturejspanel'";
$id_admin_lien = "#";
$retour_false_admin_lien = "onclick='return false'";
//LIEN SWICTH PAGE JSPANEL INDEX ADMIN PAR DEFAUT
$panel_admin_jspanel = "/administration/index-admin-modules.php";

//////////////////SI PAS JSPANEL ADMIN
}else{
//BOUTON RETOUR ADMIN BACK OFFICE
$mode_back_lien = "".$page_bak_office_static."";
$mode_back_lien_interne = "".$page_bak_office_static."";
//BOUTON ADMIN MENU HEADER
$id_admin_bouton_jspanel = "";
$id_admin_lien = "".$http."".$nomsiteweb."/".$page_bak_office_static."";
}

///////////////////////////////Paypal
$req_select = $bdd->prepare("SELECT * FROM configuration_paypal WHERE id='1'");
$req_select->execute();
$ligne_select = $req_select->fetch();
$req_select->closeCursor();
$identifiant_api_paypal = $ligne_select['identifiant_api_paypal'];
$private_pwd_paypal = $ligne_select['private_pwd_paypal'];
$signature_api_paypal = $ligne_select['signature_api_paypal'];
$url_api_paypal = $ligne_select['url_api_paypal'];

$logo_page_panier = $ligne_select['logo_page_panier'];
define("identifiant_api_paypal", $identifiant_api_paypal);
define("private_pwd_paypal", $private_pwd_paypal);
define("signature_api_paypal", $signature_api_paypal);
define("url_api_paypal", $url_api_paypal);
///////////////////////////////Paypal

$mois_annee = array('', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre');

?>
