<?php
////FUNCTIONS
include(''.$dir_fonction.'function/title-metas/page-title-metas.php');
include(''.$dir_fonction.'function/404/404r_generateur.php');
include(''.$dir_fonction.'function/301/301.php');

include(''.$dir_fonction.'function/logs/function-logs-historiques.php');

include(''.$dir_fonction.'function/php_pass.php');
include(''.$dir_fonction.'function/mails/mail-send.php');
include(''.$dir_fonction.'function/mails/mail-bibliotheques.php');
include(''.$dir_fonction.'function/pagination/pagination.php');
include(''.$dir_fonction.'function/inscription/creation_compte.php');
include(''.$dir_fonction.'function/algo_caracteres.php');
include(''.$dir_fonction.'function/function_rapport_bloc.php');
include(''.$dir_fonction.'function/page/page-bandeaux/bandeaux.php');

include(''.$dir_fonction.'function/inscription/compte-debloque.php');

//include('pages/Newsletter/Abonnement-lettre-information.php');

include(''.$dir_fonction.'function/cara_replace_function.php');

include(''.$dir_fonction.'function/function_ajout_panier.php');

include(''.$dir_fonction.'function/filtres/filtre_telephone.php');
include(''.$dir_fonction.'function/filtres/filtre_url_lien.php');
include(''.$dir_fonction.'function/filtres/filtre_supprimer_url_lien.php');
include(''.$dir_fonction.'function/filtres/filtre_supprimer_mail.php');

include(''.$dir_fonction.'function/avis.php');

include(''.$dir_fonction.'function/favoris.php');

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
// Activer l'affichage des erreurs de démarrage
ini_set('display_startup_errors', 1);
// Configurer le rapport d'erreurs pour exclure les notices (E_NOTICE) et les warnings (E_WARNING)
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);

?>