<?php
  $cfgHote       = "localhost";
  $cfgUser       = "zenfamili";
  $cfgPass       = "gc6Xc91@1";
  $cfgBase       = "zenfamili";
if (!isset($connexion) && !isset($db)){
	//$connexion = mysql_connect($cfgHote, $cfgUser, $cfgPass) or die("Connexion au serveur mysql impossible...");
	//$db = mysql_select_db($cfgBase, $connexion) or die("Selection de la bdd impossible...");
	try{
		error_log('mysql:host='.$cfgHote.';dbname='.$cfgBase.';charset=utf8');
	    $bdd = new PDO('mysql:host='.$cfgHote.';dbname='.$cfgBase.';charset=utf8', $cfgUser, $cfgPass, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
	}
	catch (Exception $e){
	    die('Erreur : ' . $e->getMessage());
	}
}
	//mysql_set_charset('utf8');
?>