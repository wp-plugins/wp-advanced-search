<?php
/*
Plugin Name: WP-Advanced-Search
Plugin URI: http://blog.internet-formation.fr/2013/10/wp-advanced-search/
Description: ajout d'un moteur de recherche avancé dans WordPress plutôt que le moteur de base (mise en surbrillance, trois types de recherche, algorithme optionnel...). (<em>Plugin adds a advanced search engine for WordPress with a lot of options (three type of search, bloded request, algorithm...</em>).
Author: Mathieu Chartier
Version: 1.1.5
Author URI: http://blog.internet-formation.fr
*/

// Instanciation des variables globales
global $wpdb, $table_WP_Advanced_Search, $WP_Advanced_Search_Version;
$table_WP_Advanced_Search = $wpdb->prefix.'advsh';

// Version du plugin
$WP_Advanced_Search_Version = "1.1.5";

// Gestion des langues
function WP_Advanced_Search_Lang() {
   $path = dirname(plugin_basename(__FILE__)).'/lang/';
   load_plugin_textdomain('WP_Advanced_Search', NULL, $path);
}
add_action('plugins_loaded', 'WP_Advanced_Search_Lang');

// Fonction lancée lors de l'activation ou de la desactivation de l'extension
register_activation_hook( __FILE__, 'WP_Advanced_Search_install');
register_activation_hook( __FILE__, 'WP_Advanced_Search_install_data');
register_deactivation_hook( __FILE__, 'WP_Advanced_Search_desinstall');

function WP_Advanced_Search_install() {	
	global $wpdb, $table_WP_Advanced_Search, $WP_Advanced_Search_Version;
	
	// Création de la table de base
	$sql = "CREATE TABLE IF NOT EXISTS $table_WP_Advanced_Search (
		id INT(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		db VARCHAR(50) NOT NULL,
		tables VARCHAR(30) NOT NULL,
		nameField VARCHAR(30) NOT NULL,
		colonnesWhere TEXT NOT NULL, 
		typeSearch VARCHAR(8) NOT NULL,
		encoding VARCHAR(25) NOT NULL,
		exactSearch BOOLEAN NOT NULL,
		accents BOOLEAN NOT NULL,
		exclusionWords TEXT,
		stopWords BOOLEAN NOT NULL,
		NumberOK BOOLEAN NOT NULL,
		NumberPerPage INT(2),
		Style VARCHAR(10) NOT NULL,
		formatageDate VARCHAR(25),
		DateOK BOOLEAN NOT NULL,
		AuthorOK BOOLEAN NOT NULL,
		CategoryOK BOOLEAN NOT NULL,
		TitleOK BOOLEAN NOT NULL,
		ArticleOK VARCHAR(12) NOT NULL,
		CommentOK BOOLEAN NOT NULL,
		ImageOK BOOLEAN NOT NULL,
		strongWords VARCHAR(10) NOT NULL,
		OrderOK BOOLEAN NOT NULL,
		OrderColumn VARCHAR(25) NOT NULL,
		AscDesc VARCHAR(4) NOT NULL,
		AlgoOK BOOLEAN NOT NULL,
		paginationActive BOOLEAN NOT NULL,
		paginationStyle VARCHAR(30) NOT NULL,
		paginationFirstLast BOOLEAN NOT NULL,
		paginationPrevNext BOOLEAN NOT NULL,
		paginationFirstPage VARCHAR(50) NOT NULL,
		paginationLastPage VARCHAR(50) NOT NULL,
		paginationPrevText VARCHAR(50) NOT NULL,
		paginationNextText VARCHAR(50) NOT NULL,
		postType VARCHAR(8) NOT NULL,
		ResultText TEXT,
		ErrorText TEXT
		);";
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	
	// Prise en compte de la version en cours
	add_option("wp_advanced_search_version", $WP_Advanced_Search_Version);

	// Récupération de la version en cours (pour voir si mise à jour...)
	$installed_ver = get_option("wp_advanced_search_version");
	if($installed_ver != $WP_Advanced_Search_Version) {
		$sql = "CREATE TABLE IF NOT EXISTS $table_WP_Advanced_Search (
			id INT(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			db VARCHAR(50) NOT NULL,
			tables VARCHAR(30) NOT NULL,
			nameField VARCHAR(30) NOT NULL,
			colonnesWhere TEXT NOT NULL, 
			typeSearch VARCHAR(8) NOT NULL,
			encoding VARCHAR(25) NOT NULL,
			exactSearch BOOLEAN NOT NULL,
			accents BOOLEAN NOT NULL,
			exclusionWords TEXT,
			stopWords BOOLEAN NOT NULL,
			NumberOK BOOLEAN NOT NULL,
			NumberPerPage INT(2),
			Style VARCHAR(10) NOT NULL,
			formatageDate VARCHAR(25),
			DateOK BOOLEAN NOT NULL,
			AuthorOK BOOLEAN NOT NULL,
			CategoryOK BOOLEAN NOT NULL,
			TitleOK BOOLEAN NOT NULL,
			ArticleOK VARCHAR(12) NOT NULL,
			CommentOK BOOLEAN NOT NULL,
			ImageOK BOOLEAN NOT NULL,
			strongWords VARCHAR(10) NOT NULL,
			OrderOK BOOLEAN NOT NULL,
			OrderColumn VARCHAR(25) NOT NULL,
			AscDesc VARCHAR(4) NOT NULL,
			AlgoOK BOOLEAN NOT NULL,
			paginationActive BOOLEAN NOT NULL,
			paginationStyle VARCHAR(30) NOT NULL,
			paginationFirstLast BOOLEAN NOT NULL,
			paginationPrevNext BOOLEAN NOT NULL,
			paginationFirstPage VARCHAR(50) NOT NULL,
			paginationLastPage VARCHAR(50) NOT NULL,
			paginationPrevText VARCHAR(50) NOT NULL,
			paginationNextText VARCHAR(50) NOT NULL,
			postType VARCHAR(8) NOT NULL,
			ResultText TEXT,
			ErrorText TEXT
			);";
		require_once(ABSPATH.'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		update_option("wp_advanced_search_version", $WP_Advanced_Search_Version);
	}
}
function WP_Advanced_Search_install_data() {		
	global $wpdb, $table_WP_Advanced_Search;
	// Récupération automatique du nom de la base de données
	$databaseNameSearch = $wpdb->get_results("SELECT DATABASE()");
	foreach($databaseNameSearch[0] as $databaseSearch) {
		// Insertion de valeurs par défaut (premier enregistrement)
		$defaut = array(
			"db" => $databaseSearch,
			"tables" => $wpdb->posts,
			"nameField" => 's',
			"colonnesWhere" => 'post_title, post_content, post_excerpt',
			"typeSearch" => "REGEXP",
			"encoding" => "utf-8",
			"exactSearch" => true,
			"accents" => false,
			"exclusionWords" => 1,
			"stopWords" => true,
			"NumberOK" => true,
			"NumberPerPage" => 10,
			"Style" => 'aucun',
			"formatageDate" => 'j F Y',
			"DateOK" => true,
			"AuthorOK" => true,
			"CategoryOK" => true,
			"TitleOK" => true,
			"ArticleOK" => 'aucun',
			"CommentOK" => true,
			"ImageOK" => false,
			"strongWords" => "exact",
			"OrderOK" => true,
			"OrderColumn" => 'post_date',
			"AscDesc" => 'DESC',
			"AlgoOK" => false,
			"paginationActive" => true,
			"paginationStyle" => "aucun",
			"paginationFirstLast" => true,
			"paginationPrevNext" => true,
			"paginationFirstPage" => "Première page",
			"paginationLastPage" => "Dernière page",
			"paginationPrevText" => "&laquo; Précédent",
			"paginationNextText" => "Suivant &raquo;",
			"postType" => 'pagepost',
			"ResultText" => 'Résultats de la recherche :',
			"ErrorText" => 'Aucun résultat, veuillez effectuer une autre recherche !'
		);
		$champ = wp_parse_args($instance, $defaut);
		$default = $wpdb->insert($table_WP_Advanced_Search, array('db' => $champ['db'], 'tables' => $champ['tables'], 'nameField' => $champ['nameField'], 'colonnesWhere' => $champ['colonnesWhere'], 'typeSearch' => $champ['typeSearch'], 'encoding' => $champ['encoding'], 'exactSearch' => $champ['exactSearch'], 'accents' => $champ['accents'], 'exclusionWords' => $champ['exclusionWords'], 'stopWords' => $champ['stopWords'], 'NumberOK' => $champ['NumberOK'], 'NumberPerPage' => $champ['NumberPerPage'], 'Style' => $champ['Style'], 'formatageDate' => $champ['formatageDate'], 'DateOK' => $champ['DateOK'], 'AuthorOK' => $champ['AuthorOK'], 'CategoryOK' => $champ['CategoryOK'], 'TitleOK' => $champ['TitleOK'], 'ArticleOK' => $champ['ArticleOK'], 'CommentOK' => $champ['CommentOK'], 'ImageOK' => $champ['ImageOK'], 'strongWords' => $champ['strongWords'], 'OrderOK' => $champ['OrderOK'], 'OrderColumn' => $champ['OrderColumn'], 'AscDesc' => $champ['AscDesc'], 'AlgoOK' => $champ['AlgoOK'], 'paginationActive' => $champ['paginationActive'], 'paginationStyle' => $champ['paginationStyle'], 'paginationFirstLast' => $champ['paginationFirstLast'], 'paginationPrevNext' => $champ['paginationPrevNext'], 'paginationFirstPage' => $champ['paginationFirstPage'], 'paginationLastPage' => $champ['paginationLastPage'], 'paginationPrevText' => $champ['paginationPrevText'], 'paginationNextText' => $champ['paginationNextText'], 'postType' => $champ['postType'], 'ResultText' => $champ['ResultText'], 'ErrorText' => $champ['ErrorText']));
	}
}
// Quand ça désactive l'extension, la table est supprimée...
function WP_Advanced_Search_desinstall() {
	global $wpdb, $table_WP_Advanced_Search;
	// Suppression de la table de base
	$wpdb->query("DROP TABLE IF EXISTS $table_WP_Advanced_Search");
}
// Quand le plugin est mise à jour, on relance la fonction
function WP_Advanced_Search_Update() {
    global $WP_Advanced_Search_Version;
    if (get_site_option('wp_advanced_search_version') != $WP_Advanced_Search_Version) {
        WP_Advanced_Search_install();
    }
}
add_action('plugins_loaded', 'WP_Advanced_Search_Update');


// Ajout d'une page de sous-menu
function WP_Advanced_Search_admin() {
	$parent_slug	= 'options-general.php';					// Page dans laquelle est ajoutée le sous-menu
	$page_title		= 'Aide et réglages de WP-Advanced-Search';	// Titre interne à la page de réglages
	$menu_title		= 'Advanced Search';						// Titre du sous-menu
	$capability		= 'manage_options';							// Rôle d'administration qui a accès au sous-menu
	$menu_slug		= 'wp-advanced-search';						// Alias (slug) de la page
	$function		= 'WP_Advanced_Search_Callback';				// Fonction appelé pour afficher la page de réglages
	add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}
add_action('admin_menu', 'WP_Advanced_Search_admin');

// Ajout conditionné d'une feuille de style personnalisée pour la pagination
function WP_Advanced_Search_CSS($bool) {
	if($bool == "vide") {
		$url = plugins_url('css/style-empty.css',__FILE__);
		wp_register_style('style-empty', $url);
		wp_enqueue_style('style-empty');
	} else
	if($bool == "bleu") {
		$url = plugins_url('css/style-bleu.css',__FILE__);
		wp_register_style('style-bleu', $url);
		wp_enqueue_style('style-bleu');
	} else
	if($bool == "noir") {
		$url = plugins_url('css/style-noir.css',__FILE__);
		wp_register_style('style-noir', $url);
		wp_enqueue_style('style-noir');
	} else
	if($bool == "gris") {
		$url = plugins_url('css/style-gris.css',__FILE__);
		wp_register_style('style-gris', $url);
		wp_enqueue_style('style-gris');
	} else
	if($bool == "rouge") {
		$url = plugins_url('css/style-rouge.css',__FILE__);
		wp_register_style('style-rouge', $url);
		wp_enqueue_style('style-rouge');
	} else
	if($bool == "vert") {
		$url = plugins_url('css/style-vert.css',__FILE__);
		wp_register_style('style-vert', $url);
		wp_enqueue_style('style-vert');
	} else
	if($bool == "blanc") {
		$url = plugins_url('css/style-blanc.css',__FILE__);
		wp_register_style('style-blanc', $url);
		wp_enqueue_style('style-blanc');
	}
	add_action('wp_enqueue_scripts', 'WP_Advanced_Search_CSS');
}

// Ajout conditionné d'une feuille de style personnalisée pour la pagination
function WP_Advanced_Search_Pagination_CSS($bool) {
	if($bool == "vide") {
		$url = plugins_url('css/pagination/style-pagination-empty.css',__FILE__);
		wp_register_style('style-pagination-empty', $url);
		wp_enqueue_style('style-pagination-empty');
	} else
	if($bool == "bleu") {
		$url = plugins_url('css/pagination/style-pagination-bleu.css',__FILE__);
		wp_register_style('style-pagination-bleu', $url);
		wp_enqueue_style('style-pagination-bleu');
	} else
	if($bool == "noir") {
		$url = plugins_url('css/pagination/style-pagination-noir.css',__FILE__);
		wp_register_style('style-pagination-noir', $url);
		wp_enqueue_style('style-pagination-noir');
	} else
	if($bool == "gris") {
		$url = plugins_url('css/pagination/style-pagination-gris.css',__FILE__);
		wp_register_style('style-pagination-gris', $url);
		wp_enqueue_style('style-pagination-gris');
	} else
	if($bool == "rouge") {
		$url = plugins_url('css/pagination/style-pagination-rouge.css',__FILE__);
		wp_register_style('style-pagination-rouge', $url);
		wp_enqueue_style('style-pagination-rouge');
	} else
	if($bool == "vert") {
		$url = plugins_url('css/pagination/style-pagination-vert.css',__FILE__);
		wp_register_style('style-pagination-vert', $url);
		wp_enqueue_style('style-pagination-vert');
	} else
	if($bool == "blanc") {
		$url = plugins_url('css/pagination/style-pagination-blanc.css',__FILE__);
		wp_register_style('style-pagination-blanc', $url);
		wp_enqueue_style('style-pagination-blanc');
	}
	add_action('wp_enqueue_scripts', 'WP_Advanced_Search_Pagination_CSS');
}
/*
function WP_Advanced_Search_Upgrade() {
	global $wpdb, $table_WP_Advanced_Search;
	// Création de la table de base
	$sql = "ALTER TABLE $table_WP_Advanced_Search ADD COLUMN postType VARCHAR(8) NOT NULL, ResultText TEXT,	ErrorText TEXT);";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	
	// Insertion de valeurs par défaut (premier enregistrement)
	$defaut = array(
		"postType" => 'pagepost',
		"ResultText" => 'Résultats de la recherche :',
		"ErrorText" => 'Aucun résultat, veuillez effectuer une autre recherche !'
	);
	$champ = wp_parse_args($instance, $defaut);
	$default = $wpdb->insert($table_WP_Advanced_Search, array('postType' => $champ['postType'], 'ResultText' => $champ['ResultText'], 'ErrorText' => $champ['ErrorText']));
}
add_action('init', 'WP_Advanced_Search_Upgrade');
*/

// Inclusion des options de réglages
include('WP-Advanced-Search-Options.php');

// Inclusion de la fonction finale
include('WP-Advanced-Search-Function.php');
?>