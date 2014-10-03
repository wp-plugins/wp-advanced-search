<?php
/*--------------------------------------------*/
/*--------- Fonction d'autocomplétion --------*/
/*--------------------------------------------*/
// Ajout conditionné du système d'autocomplétion
function addAutoCompletion() {
	global $wpdb, $table_WP_Advanced_Search, $link;
	
	// Sélection des données dans la base de données		
	$select = $wpdb->get_row("SELECT * FROM $table_WP_Advanced_Search WHERE id=1");

	// Instanciation des variables utiles
	$selector		= $select->autoCompleteSelector;
	$dbName			= $select->db;
	$tableName		= $select->autoCompleteTable;
	$tableColumn	= $select->autoCompleteColumn;
	$limitDisplay	= $select->autoCompleteNumber;
	$multiple		= $select->autoCompleteTypeSuggest;
	$type			= $select->autoCompleteType;
	$autoFocus		= $select->autoCompleteAutofocus;
	$create			= false; // On laisse sur false car la table est créée par ailleurs
	$encoding		= $select->encoding;
	
	// Lancement de la fonction d'autocomplétion si activé...
	if($select->autoCompleteActive == 1) {
		include_once('class.inc/moteur-php5.5.class-inc.php');
		$autocompletion = new autoCompletion($wpdb, plugins_url("class.inc/autocompletion/autocompletion-PHP5.5.php", __FILE__ ), $selector, $tableName, $tableColumn, $multiple, $limitDisplay, $type, $autoFocus, $create, $encoding);
	}
}
add_action('wp_footer', 'addAutoCompletion');

// Ajout conditionné du fichier d'autocomplétion
function WP_Advanced_Search_AutoCompletion() {
	$urlstyle = plugins_url('class.inc/autocompletion/jquery.autocomplete.css',__FILE__);
	wp_enqueue_style('autocomplete', $urlstyle, false, '1.0');
	//$urljquery = plugins_url('class.inc/autocompletion/jquery.js',__FILE__);
	//wp_enqueue_script('jquery2',$urljquery,false);	
	$url = plugins_url('class.inc/autocompletion/jquery.autocomplete.js',__FILE__);
	wp_enqueue_script('autocomplete', $url, array('jquery'), '1.0');
}
add_action('wp_enqueue_scripts', 'WP_Advanced_Search_AutoCompletion');


/*--------------------------------------------*/
/*-------- Fonction trigger et scroll --------*/
/*--------------------------------------------*/
include_once('class.inc/ajaxResults.php'); // Fichier d'affichage Ajax des résultats

// Fonction du trigger
function WP_Advanced_Search_Trigger() {
	global $wpdb, $table_WP_Advanced_Search;
	
	//Récupération des variables utiles dynamiquement
	$select		= $wpdb->get_row("SELECT * FROM $table_WP_Advanced_Search WHERE id=1");
	$nameSearch = $select->nameField;							// Nom du champ
	$imgUrl		= plugins_url('img/loadingGrey.gif',__FILE__);	// URL des images choisies
	$duration	= $select->paginationDuration;					// temps d'attente avant la réponse
	$limitR		= $select->paginationNbLimit;					// Pallier d'affichage des résultats
	if(isset($_GET[$nameSearch])) {
		$queryAS = stripslashes($_GET[$nameSearch]);
	}
	
	// Tableau des données envoyées au script
	$scriptData = array(
		'ajaxurl' => admin_url('/admin-ajax.php'),
		'nameSearch' => "$nameSearch",
		'query' => "$queryAS",
		'limitR' => "$limitR",
		'duration' => "$duration",
		'loadImg' => "$imgUrl"
	);
	
	// Chargement des variables et des scripts
	wp_enqueue_script('ajaxTrigger', plugins_url('js/ajaxTrigger-min.js',__FILE__), array('jquery'), '1.0');
	wp_enqueue_script('ajaxTriggerStart', plugins_url('js/ajaxTriggerStart-min.js',__FILE__), array('jquery'), '1.0');
	wp_localize_script('ajaxTriggerStart', 'ASTrigger', $scriptData);
}
// Fonction de l'infinite scroll
function WP_Advanced_Search_InfiniteScroll() {
	global $wpdb, $table_WP_Advanced_Search;
	
	//Récupération des variables utiles dynamiquement
	$select		= $wpdb->get_row("SELECT * FROM $table_WP_Advanced_Search WHERE id=1");
	$nameSearch = $select->nameField;			// Nom du champ
	$duration	= $select->paginationDuration;	// temps d'attente avant la réponse
	$limitR		= $select->paginationNbLimit;	// Pallier d'affichage des résultats
	if(isset($_GET[$nameSearch])) {
		//$queryAS = stripslashes($_GET[$nameSearch]);
	}

	// Tableau des données envoyées au script
	$scriptDataIS = array(
		'ajaxurl' => admin_url('/admin-ajax.php'),
		'nameSearch' => "$nameSearch",
		'query' => "$queryAS",
		'limitR' => "$limitR",
		'duration' => "$duration"
	);
	
	// Chargement des variables et des scripts
	wp_enqueue_script('ajaxInfiniteScroll', plugins_url('js/ajaxInfiniteScroll-min.js',__FILE__), array('jquery'), '1.0');
	wp_enqueue_script('ajaxInfiniteScrollStart', plugins_url('js/ajaxInfiniteScrollStart-min.js',__FILE__), array('jquery'), '1.0');
	wp_localize_script('ajaxInfiniteScrollStart', 'ASInfiniteScroll', $scriptDataIS);
}
?>