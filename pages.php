<?php

/*****************************************************\
* Adresse e-mail => direction@codi-one.fr             *
* La conception est assujettie à une autorisation     *
* spéciale de codi-one.com. Si vous ne disposez pas de*
* cette autorisation, vous êtes dans l'illégalité.    *
* L'auteur de la conception est et restera            *
* codi-one.fr                                         *
* Codage, script & images (all contenu) sont réalisés * 
* par codi-one.fr                                     *
* La conception est à usage unique et privé.          *
* La tierce personne qui utilise le script se porte   *
* garante de disposer des autorisations nécessaires   *
*                                                     *
* Copyright ... Tous droits réservés auteur (Fabien B)*
\*****************************************************/

if (!empty($_GET['page'])) {

  switch ($_GET['page']) {

    case "page-introuvable-404":
      include("function/404/404r.php");
      break;

    case "Avis-plateforme":
      include("pages/Avis-plateforme/Avis-plateforme.php");
      break;

    //www
    case "sitemap":
      include("sitemap.php");
      break;

    //Extra
    case "Extra":
      include("pages/Extra/Extra.php");
      break;

    //Pro
    case "Pro":
      include("pages/Pro/Pro.php");
      break;

    //Plateforme
    case "Plateforme":
      include("pages/Plateforme/Plateforme.php");
      break;

    case "Plateforme2":
      include("pages/Plateforme/Plateforme2.php");
      break;

    case "Fiche":
      include("pages/Plateforme/Fiche/fiche.php");
      break;

    case "Fiche-annonce":
      include("pages/Plateforme/Fiche-annonce/fiche.php");
      break;

    //Inscription-entreprise
    case "Inscription-entreprise":
      include("pages/Inscription-entreprise/Inscription-entreprise.php");
      break;

    //pages / mot-de-passe-oublie
    case "mot-de-passe-oublie":
      include("pages/mot-de-passe-oublie/mot-de-passe-oublie.php");
      break;

    //Pages
    case "Avis":
      include("pages/avis/Avis.php");
      break;
    //Contact
    case "Contact":
      include("pages/contact/contact.php");
      break;
    //Page dynamique
    case "page-dynamique":
      include("pages/page-dynamique/page-dynamique.php");
      break;
    //Page catégorie dynamique
    case "page-categorie-dynamique":
      include("pages/page-dynamique/page-categorie-dynamique.php");
      break;
    //Pages / Blog
    case "Blog":
      include("pages/blog/blog.php");
      break;
    //Newsletter
    case "Desabonnement-lettre-information":
      include("function/Newsletter/Desabonnement-lettre-information.php");
      break;
    case "Abonnement-lettre-information":
      include("function/Newsletter/Abonnement-lettre-information.php");
      break;
    //Paiements
    case "Panier":
      include("pages/paiements/Panier/Panier.php");
      break;
    case "Traitement-Paiement":
      include("pages/paiements/Traitement-paiement-mangopay.php");
      break;
    case "Traitements-paypal":
      include("pages/paiements/Api-Paypal/Traitements.php");
      break;
    case "Traitements":
      include("pages/paiements/Traitements.php");
      break;
    case "Traitements-informations":
      include("pages/paiements/Traitements-informations.php");
      break;
    case "Traitements-gratuit":
      include("pages/paiements/Traitements-gratuit.php");
      break;
    case "Traitements-admin":
      include("pages/paiements/Traitements-admin.php");
      break;
    //Pages / Trouver-des-extras
    case "Trouver-des-extras":
      include("pages/Trouver-des-extras/Trouver-des-extras.php");
      break;

    //Pages / Trouver-des-extras
    case "Offres":
      include("pages/Offres/Offres.php");
      break;

    //Pop-up
    case "mot-de-passe-perdu":
      include("pop-up/password_popup_actions.php");
      break;
    //Confirmation inscription
    case "inscription-confirmation":
      include("pop-up/inscription/inscription-confirmation.php");
      break;

    ////////////////////////////////////////////////////////////////////////////////////////////////PANEL

    //panel / Avatar
    case "Avatar":
      include("panel/Avatar/Avatar.php");
      break;
    case "modifier-profil-photo":
      include("panel/Profil/Modifier-profil-photo.php");
      break;

    case "photos-publicites":
      include("panel/Blocs-publicites/Blocs-publicites-photo.php");
      break;

    //panel / Demandes-de-mission
    case "Demandes-de-mission":
      include("panel/Demandes-de-mission/Demandes-de-mission.php");
      break;

    //panel / Mes-documents
    case "Mes-documents":
      include("panel/Mes-documents/Mes-documents.php");
      break;

    //panel / Dashboard-etablissement
    case "Dashboard-etablissement":
      include("panel/Dashboard-etablissement/Dashboard-etablissement.php");
      break;

    //panel / Guide
    case "Guide":
      include("panel/Guide/Guide.php");
      break;

    //panel / Commandes-de-DUE
    case "Commandes-de-DUE":
      include("panel/Commandes-de-DUE/Commandes-de-DUE.php");
      break;
    //panel / Commandes-de-formation
    case "Commandes-de-formation":
      include("panel/Commandes-de-formation/Commandes-de-formation.php");
      break;
    //panel / Demande-de-devis
    case "Demande-de-devis":
      include("panel/Demande-de-devis/Demande-de-devis.php");
      break;
    //panel / Commandes-cdd-cdi
    case "Commandes-cdd-cdi":
      include("panel/Commandes-cdd-cdi/Commandes-cdd-cdi.php");
      break;

    //panel / Disponibilites
    case "Disponibilites":
      include("panel/Disponibilites/Disponibilites.php");
      break;

    //panel / Agenda
    case "Agenda":
      include("panel/Agenda/Agenda.php");
      break;

    //panel / Agenda-pro
    case "Agendapro":
      include("panel/Agendapro/Agendapro.php");
      break;

    //panel / FAQ
    case "faq-extras":
      include("panel/FAQ/faq-extras.php");
      break;
    case "faq-etablissements":
      include("panel/FAQ/faq-etablissements.php");
      break;

    //panel / Profil
    case "Compte-modifications":
      include("panel/Profil/Compte-modifications.php");
      break;
    //panel / Profil / Confirmation-mail-telephone-ajax
    case "Confirmation-mail":
      include("panel/Profil/Confirmation-mail-telephone-ajax/Confirmation-mail.php");
      break;
    //panel / Notifications
    case 'Notifications':
      include("panel/Notifications/Notifications.php");
      break;

    //panel / Facturations
    case "factures":
      include("panel/Facturations/factures.php");
      break;

    //panel / Messagerie
    case "Messagerie":
      include("panel/Messagerie/Messagerie.php");
      break;
    case "Message":
      include("panel/Messagerie/Message.php");
      break;

    //panel / Abonnements-annuaires
    case "Abonnements-annuaires":
      include("panel/Abonnements-annuaires/Abonnements-annuaires.php");
      break;

    //panel / Mes-photos
    case "Mes-photos":
      include("panel/Mes-photos/photos-banniere.php");
      break;

    //panel / Favoris
    case "Favoris":
      include("panel/favoris/favoris.php");
      break;

    //panel / Mon-profil
    case "Mon-profil":
      include("panel/Mon-profil/Mon-profil.php");
      break;
    //panel / Missions
    case "Missions":
      include("panel/Missions/Missions.php");
      break;
    //panel / Mes-missions
    case "Mes-missions":
      include("panel/Mes-missions/Mes-missions.php");
      break;
    //panel / Reservations
    case "Reservations":
      include("panel/Reservations/Reservations.php");
      break;
    //panel / Formules
    case "Formules":
      include("panel/Formules/Formules.php");
      break;
    //panel / Formules-credits
    case "Formules-credits":
      include("panel/Formules-credits/Formules-credits.php");
      break;
    //panel / Mes-annonces
    case "Mes-annonces":
      include("panel/Mes-annonces/Mes-annonces.php");
      break;
    //panel / Publicites
    case "Blocs-publicites":
      include("panel/Blocs-publicites/Blocs-publicites.php");
      break;
    //panel / Blocs-publicites-images
    case "Blocs-publicites-images":
      include("panel/Blocs-publicites-images/Blocs-publicites-images.php");
      break;
    //panel / Blocs-publicites-recadrage-images
    case "Blocs-publicites-recadrage-images":
      include("panel/Blocs-publicites-images/Blocs-publicites-recadrage-images.php");
      break;

    //Financial planning module
    case "Planificator":
      // Only check for session, not $user which could be scope-limited
      if (isset($_SESSION['4M8e7M5b1R2e8s'])) {
          error_log("Including planificator module");
          include("modules/planificator/index.php");
      } else {
          error_log("Redirecting to homepage - no valid session");
          header('Location: /', true, 301);
          exit();
      }
      break;

  }

  ////////////////////////////////////////////////////////////////////////////////////////////////PAGE HOME

} elseif (empty($page)) {
  /////////////////////////////SI JSPANEL POUR PANEL ADMINISTRATEUR EN IFRAM

  if (!empty($panel_admin_jspanel_index) && isset($_SESSION['7A5d8M9i4N9']) && isset($_SESSION['4M8e7M5b1R2e8s']) && isset($user) && $admin_oo == 1) {
    include("$panel_admin_jspanel");
  } else {
    /////////////////////////////SI PAGES STATICS
    include("index-accueil.php");
  }

}

?>