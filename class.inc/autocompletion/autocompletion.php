<?php
if(isset($_GET['q']) && !empty($_GET['q'])) {
    $query = htmlspecialchars(stripslashes($_GET['q']));

	// Récupération à la volée des informations transmises par le script d'autocomplétion
	$table	 = htmlspecialchars($_GET['t']);
	$field	 = htmlspecialchars($_GET['f']);
	$type	 = htmlspecialchars($_GET['type']);
	$encode	 = htmlspecialchars($_GET['e']);
	
	if(is_numeric($_GET['l'])) {
		$limitS  = htmlspecialchars($_GET['l']);
	} else {
		$limitS = 5;	
	}
	
	if($type == 0 || $type > 1) {
		$arg = "";
	} else {
		$arg = "%";	
	}

	// Fonctionne si une donnée est reçue dans le champ de recherche
	include_once('../../../../wp-load.php');
	if (!$BDDquery = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)) {
		echo 'Connexion impossible à Mysql';
		exit;
	}
	if (!mysql_select_db(DB_NAME, $BDDquery)) {
		echo 'Sélection de base de données impossible !';
	}

    // Requête de recherche dans l'index inversé (base de mots clés auto-générés)
    $requeteSQL = "SELECT DISTINCT ".$field." FROM ".$table." WHERE ".$field." LIKE '".$arg.mysql_real_escape_string($query)."%' ORDER BY ".$field." ASC, idindex DESC LIMIT 0 , ".$limitS."";
	
	// Lancement de la requête
    $results = mysql_query($requeteSQL) or die("Erreur : ".mysql_error());
    
	// Retourne les résultats avec le système d'autocomplétion
    while($donnees = mysql_fetch_assoc($results)) {
        if($encode == "utf-8" || $encode == "utf8" || $encode == "UTF-8" || $encode == "UTF8") {
			echo $donnees[$field]."\n";
		} else {
			echo $donnees[$field]."\n";	
		}
    }
}
?>