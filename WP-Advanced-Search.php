<?php
/*
Plugin Name: WP-Advanced-Search
Plugin URI: http://blog.internet-formation.fr/2013/10/wp-advanced-search/
Description: ajout d'un moteur de recherche avancé dans WordPress plutôt que le moteur de base (mise en surbrillance, trois types de recherche, algorithme optionnel...). (<em>Plugin adds a advanced search engine for WordPress with a lot of options (three type of search, bloded request, algorithm...</em>).
Author: Mathieu Chartier
Version: 2.5
Author URI: http://blog.internet-formation.fr
*/

// Instanciation des variables globales
global $wpdb, $table_WP_Advanced_Search, $WP_Advanced_Search_Version;
$table_WP_Advanced_Search = $wpdb->prefix.'advsh';

// Version du plugin
$WP_Advanced_Search_Version = "2.5";

function WP_Advanced_Search_Lang() {
	load_plugin_textdomain('wp-advanced-search', false, dirname(plugin_basename( __FILE__ )).'/lang/');
}
add_action('plugins_loaded', 'WP_Advanced_Search_Lang' );

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
		nbResultsOK BOOLEAN NOT NULL,
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
		BlocOrder VARCHAR(10) NOT NULL,
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
		paginationType VARCHAR(50) NOT NULL,
		paginationNbLimit INT NOT NULL,
		paginationDuration INT NOT NULL,
		paginationText VARCHAR(250) NOT NULL,
		postType VARCHAR(8) NOT NULL,
		categories TEXT,
		ResultText TEXT,
		ErrorText TEXT,
		autoCompleteActive BOOLEAN NOT NULL,
		autoCompleteSelector VARCHAR(50) NOT NULL,
		autoCompleteAutofocus BOOLEAN NOT NULL,
		autoCompleteType TINYINT,
		autoCompleteNumber TINYINT,
		autoCompleteTypeSuggest BOOLEAN NOT NULL,
		autoCompleteCreate BOOLEAN NOT NULL,
		autoCompleteTable VARCHAR(50) NOT NULL,
		autoCompleteColumn VARCHAR(50) NOT NULL,
		autoCompleteGenerate BOOLEAN NOT NULL,
		autoCompleteSizeMin TINYINT
		) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
	require_once(ABSPATH.'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	
	// Prise en compte de la version en cours
	add_option("wp_advanced_search_version", $WP_Advanced_Search_Version);
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
			"nbResultsOK" => false,
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
			"BlocOrder" => "D-A-C",
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
			"paginationType" => "trigger",
			"paginationNbLimit" => 5,
			"paginationDuration" => 300,
			"paginationText" => "Afficher plus de résultats",
			"postType" => 'pagepost',
			"categories" => serialize(array('toutes')),
			"ResultText" => 'Résultats de la recherche :',
			"ErrorText" => 'Aucun résultat, veuillez effectuer une autre recherche !',
			"autoCompleteActive" => true,
			"autoCompleteSelector" => ".search-field",
			"autoCompleteAutofocus" => false,
			"autoCompleteType" => 0,
			"autoCompleteNumber" => 5,
			"autoCompleteTypeSuggest" => true,
			"autoCompleteCreate" => false,
			"autoCompleteTable" => $wpdb->prefix."autosuggest",
			"autoCompleteColumn" => "words",
			"autoCompleteGenerate" => true,
			"autoCompleteSizeMin" => 2
		);
		$champ = wp_parse_args($instance, $defaut);
		$default = $wpdb->insert($table_WP_Advanced_Search, array('db' => $champ['db'], 'tables' => $champ['tables'], 'nameField' => $champ['nameField'], 'colonnesWhere' => $champ['colonnesWhere'], 'typeSearch' => $champ['typeSearch'], 'encoding' => $champ['encoding'], 'exactSearch' => $champ['exactSearch'], 'accents' => $champ['accents'], 'exclusionWords' => $champ['exclusionWords'], 'stopWords' => $champ['stopWords'], 'nbResultsOK' => $champ['nbResultsOK'], 'NumberOK' => $champ['NumberOK'], 'NumberPerPage' => $champ['NumberPerPage'], 'Style' => $champ['Style'], 'formatageDate' => $champ['formatageDate'], 'DateOK' => $champ['DateOK'], 'AuthorOK' => $champ['AuthorOK'], 'CategoryOK' => $champ['CategoryOK'], 'TitleOK' => $champ['TitleOK'], 'ArticleOK' => $champ['ArticleOK'], 'CommentOK' => $champ['CommentOK'], 'ImageOK' => $champ['ImageOK'], 'BlocOrder' => $champ['BlocOrder'], 'strongWords' => $champ['strongWords'], 'OrderOK' => $champ['OrderOK'], 'OrderColumn' => $champ['OrderColumn'], 'AscDesc' => $champ['AscDesc'], 'AlgoOK' => $champ['AlgoOK'], 'paginationActive' => $champ['paginationActive'], 'paginationStyle' => $champ['paginationStyle'], 'paginationFirstLast' => $champ['paginationFirstLast'], 'paginationPrevNext' => $champ['paginationPrevNext'], 'paginationFirstPage' => $champ['paginationFirstPage'], 'paginationLastPage' => $champ['paginationLastPage'], 'paginationPrevText' => $champ['paginationPrevText'], 'paginationNextText' => $champ['paginationNextText'], 'paginationType' => $champ['paginationType'], 'paginationNbLimit' => $champ['paginationNbLimit'], 'paginationDuration' => $champ['paginationDuration'], 'paginationText' => $champ['paginationText'], 'postType' => $champ['postType'], 'categories' => $champ['categories'], 'ResultText' => $champ['ResultText'], 'ErrorText' => $champ['ErrorText'], 'autoCompleteActive' => $champ['autoCompleteActive'], 'autoCompleteSelector' => $champ['autoCompleteSelector'], 'autoCompleteAutofocus' => $champ['autoCompleteAutofocus'], 'autoCompleteType' => $champ['autoCompleteType'], 'autoCompleteNumber' => $champ['autoCompleteNumber'], 'autoCompleteCreate' => $champ['autoCompleteCreate'], 'autoCompleteTable' => $champ['autoCompleteTable'], 'autoCompleteColumn' => $champ['autoCompleteColumn'], 'autoCompleteTypeSuggest' => $champ['autoCompleteTypeSuggest'], 'autoCompleteGenerate' => $champ['autoCompleteGenerate'], 'autoCompleteSizeMin' => $champ['autoCompleteSizeMin']));

		// Création de l'index inversé par défaut (pour l'autocomplétion)
		$wpdb->query("CREATE TABLE IF NOT EXISTS ".$champ['autoCompleteTable']." (
					idindex INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					".$champ['autoCompleteColumn']." VARCHAR(250) NOT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci"
		);
	}
}
// Quand ça désactive l'extension, la table est supprimée...
function WP_Advanced_Search_desinstall() {
	global $wpdb, $table_WP_Advanced_Search;
	
	// Sélectionne les données de la table
	$select = $wpdb->get_row("SELECT autoCompleteTable FROM $table_WP_Advanced_Search WHERE id=1");
	
	// Suppression de l'index inversé si existant
	$wpdb->query("DROP TABLE IF EXISTS ".$select->autoCompleteTable);
	
	// Suppression de la table de base
	$wpdb->query("DROP TABLE IF EXISTS $table_WP_Advanced_Search");
}

// Quand le plugin est mise à jour, on relance la fonction
function WP_Advanced_Search_Upgrade() {
    global $WP_Advanced_Search_Version;
    if(get_site_option('wp_advanced_search_version') != $WP_Advanced_Search_Version) {
        WP_Advanced_Search_install_update();
    }
}
add_action('plugins_loaded', 'WP_Advanced_Search_Upgrade');

// Fonction d'update v1.2 vers 2.5
function WP_Advanced_Search_install_update() {
	global $wpdb, $table_WP_Advanced_Search, $WP_Advanced_Search_Version;	
	// Récupération de la version en cours (pour voir si mise à jour...)
	$installed_ver = get_option("wp_advanced_search_version");

	if($installed_ver != $WP_Advanced_Search_Version) {
		$encodeSQL = $wpdb->query("ALTER TABLE $table_WP_Advanced_Search DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci");
		
		$sqlShow = $wpdb->query("SHOW COLUMNS FROM $table_WP_Advanced_Search LIKE 'categories'");
		if($sqlShow != 1) {
			$sqlUpgrade = $wpdb->query("ALTER TABLE $table_WP_Advanced_Search ADD categories TEXT");
			// Mise à jour des des nouvelles valeurs par défaut
			$defautUpgrade = array(
				"categories" => serialize(array('toutes'))
			);
			$chp = wp_parse_args($instance, $defautUpgrade);
			$defaultUpgrade = $wpdb->update($table_WP_Advanced_Search, array('categories' => $chp['categories']), array('id' => 1));
		}
		
		$sqlShowTIS = $wpdb->query("SHOW COLUMNS FROM $table_WP_Advanced_Search LIKE 'paginationType'");
		if($sqlShowTIS != 1) {
			$tableTISUpgrade = $wpdb->query("ALTER TABLE $table_WP_Advanced_Search ADD (
				paginationType VARCHAR(50) NOT NULL,
				paginationNbLimit INT NOT NULL,
				paginationDuration INT NOT NULL,
				paginationText VARCHAR(250) NOT NULL
			)");
			$defautsTIS = array(
				"paginationType" => "trigger",
				"paginationNbLimit" => 5,
				"paginationDuration" => 300,
				"paginationText" => "Afficher plus de résultats"
			);
			$fldTIS = wp_parse_args($instance, $defautsTIS);
			$TISUpgrade = $wpdb->update($table_WP_Advanced_Search, array('paginationType' => $fldTIS['paginationType'], 'paginationNbLimit' => $fldTIS['paginationNbLimit'], 'paginationDuration' => $fldTIS['paginationDuration'], 'paginationText' => $fldTIS['paginationText']), array('id' => 1));

			// Mise à jour de la version
			update_option("wp_advanced_search_version", $WP_Advanced_Search_Version);
		}
		
		$sqlShowAC = $wpdb->query("SHOW COLUMNS FROM $table_WP_Advanced_Search LIKE 'autoCompleteActive'");
		if($sqlShowAC != 1) {
			$tableUpgrade = $wpdb->query("ALTER TABLE $table_WP_Advanced_Search ADD (
			autoCompleteActive BOOLEAN NOT NULL,
			autoCompleteSelector VARCHAR(50) NOT NULL,
			autoCompleteAutofocus BOOLEAN NOT NULL,
			autoCompleteType INT,
			autoCompleteNumber INT,
			autoCompleteTypeSuggest BOOLEAN NOT NULL,
			autoCompleteCreate BOOLEAN NOT NULL,
			autoCompleteTable VARCHAR(50) NOT NULL,
			autoCompleteColumn VARCHAR(50) NOT NULL,
			autoCompleteGenerate BOOLEAN NOT NULL,
			autoCompleteSizeMin TINYINT
			)");
			$defauts = array(
				"autoCompleteActive" => true,
				"autoCompleteSelector" => ".search-field",
				"autoCompleteAutofocus" => false,
				"autoCompleteType" => 0,
				"autoCompleteNumber" => 5,
				"autoCompleteTypeSuggest" => true,
				"autoCompleteCreate" => false,
				"autoCompleteTable" => $wpdb->prefix."autosuggest",
				"autoCompleteColumn" => "words",
				"autoCompleteGenerate" => true,
				"autoCompleteSizeMin" => 2
			);
			$fld = wp_parse_args($instance, $defauts);
			$autoCompleteUpgrade = $wpdb->update($table_WP_Advanced_Search, array('autoCompleteActive' => $fld['autoCompleteActive'], 'autoCompleteSelector' => $fld['autoCompleteSelector'], 'autoCompleteAutofocus' => $fld['autoCompleteAutofocus'], 'autoCompleteType' => $fld['autoCompleteType'], 'autoCompleteNumber' => $fld['autoCompleteNumber'], 'autoCompleteTypeSuggest' => $fld['autoCompleteTypeSuggest'], 'autoCompleteCreate' => $fld['autoCompleteCreate'], 'autoCompleteTable' => $fld['autoCompleteTable'], 'autoCompleteColumn' => $fld['autoCompleteColumn'], 'autoCompleteGenerate' => $fld['autoCompleteGenerate'], 'autoCompleteSizeMin' => $fld['autoCompleteSizeMin']), array('id' => 1));
			
			// Création de l'index inversé par défaut (pour l'autocomplétion)
			$wpdb->query("CREATE TABLE IF NOT EXISTS ".$fld['autoCompleteTable']." (
						 idindex INT(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						 ".$fld['autoCompleteColumn']." VARCHAR(250) NOT NULL)
						 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci"
			);

			// Mise à jour de la version
			update_option("wp_advanced_search_version", $WP_Advanced_Search_Version);
		}
	}
}

// Ajout du menu et des sous-menus associés
function WP_Advanced_Search_admin() {
	$page_title		= 'Aide et réglages de WP-Advanced-Search';		// Titre interne à la page de réglages
	$menu_title		= 'Advanced Search';							// Titre du sous-menu
	$capability		= 'manage_options';								// Rôle d'administration qui a accès au sous-menu
	$menu_slug		= 'wp-advanced-search';							// Alias (slug) de la page
	$function		= 'WP_Advanced_Search_Callback';				// Fonction appelée pour afficher la page de réglages
	$function2		= 'WP_Advanced_Search_Callback_Styles';			// Fonction appelée pour afficher la page de gestion des styles
	$function3		= 'WP_Advanced_Search_Callback_Pagination';		// Fonction appelée pour afficher la page d'options pour la pagination
	$function4		= 'WP_Advanced_Search_Callback_Autocompletion';	// Fonction appelée pour afficher la page d'options pour l'autocomplétion
	$function5		= 'WP_Advanced_Search_Callback_Documentation';	// Fonction appelée pour afficher la page de documentation

	add_menu_page($page_title, $menu_title, $capability, $menu_slug, $function, plugins_url('img/icon-16.png',__FILE__), 200);
	add_submenu_page($menu_slug, __('Thèmes et styles','wp-advanced-search'), __('Thèmes et styles','wp-advanced-search'), $capability, $function2, $function2);
	add_submenu_page($menu_slug, __('Options des SERP','wp-advanced-search'), __('Options des SERP','wp-advanced-search'), $capability, $function3, $function3);
	add_submenu_page($menu_slug, __('Autocomplétion','wp-advanced-search'), __('Autocomplétion','wp-advanced-search'), $capability, $function4, $function4);
	add_submenu_page($menu_slug, __('Documentation','wp-advanced-search'), __('Documentation','wp-advanced-search'), $capability, $function5, $function5);
}
add_action('admin_menu', 'WP_Advanced_Search_admin');

// Ajout d'une feuille de style pour l'admin
function WP_Advanced_Search_Admin_CSS() {
	$handle = 'admin_css';
	$style	= plugins_url('css/wp-advanced-search-admin.css', __FILE__);
	wp_enqueue_style($handle, $style, 15);
}
add_action('admin_print_styles', 'WP_Advanced_Search_Admin_CSS');

// Ajout conditionné d'une feuille de style personnalisée pour la pagination
function WP_Advanced_Search_CSS($bool) {
	if($bool == "vide") {
		$url = plugins_url('css/templates/style-empty.css',__FILE__);
		wp_register_style('style-empty', $url);
		wp_enqueue_style('style-empty');
	} else
	if($bool == "c-blue") {
		$url = plugins_url('css/templates/classic-blue/style-bleu.css',__FILE__);
		wp_register_style('style-bleu', $url);
		wp_enqueue_style('style-bleu');
	} else
	if($bool == "c-black") {
		$url = plugins_url('css/templates/classic-black/style-noir.css',__FILE__);
		wp_register_style('style-noir', $url);
		wp_enqueue_style('style-noir');
	} else
	if($bool == "c-red") {
		$url = plugins_url('css/templates/classic-red/style-rouge.css',__FILE__);
		wp_register_style('style-rouge', $url);
		wp_enqueue_style('style-rouge');
	} else
	if($bool == "geek-zone") {
		$url = plugins_url('css/templates/geek-zone/style-geek-zone.css',__FILE__);
		wp_register_style('style-geek-zone', $url);
		wp_enqueue_style('style-geek-zone');
	} else
	if($bool == "flat") {
		$url = plugins_url('css/templates/flat-design/style-flat-design.css',__FILE__);
		wp_register_style('style-flat-design', $url);
		wp_enqueue_style('style-flat-design');
	} else
	if($bool == "flat-2") {
		$url = plugins_url('css/templates/flat-design-blue/style-flat-design-blue.css',__FILE__);
		wp_register_style('style-flat-design-blue', $url);
		wp_enqueue_style('style-flat-design-blue');
	} else
	if($bool == "flat-color") {
		$url = plugins_url('css/templates/colored-flat-design/style-colored-flat-design.css',__FILE__);
		wp_register_style('style-colored-flat-design', $url);
		wp_enqueue_style('style-colored-flat-design');
	}
	if($bool == "orange-grey") {
		$url = plugins_url('css/templates/orange-grey/style-orange-design.css',__FILE__);
		wp_register_style('style-orange-grey', $url);
		wp_enqueue_style('style-orange-grey');
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
	if($bool == "c-blue") {
		$url = plugins_url('css/pagination/style-pagination-bleu.css',__FILE__);
		wp_register_style('style-pagination-bleu', $url);
		wp_enqueue_style('style-pagination-bleu');
	} else
	if($bool == "c-black") {
		$url = plugins_url('css/pagination/style-pagination-noir.css',__FILE__);
		wp_register_style('style-pagination-noir', $url);
		wp_enqueue_style('style-pagination-noir');
	} else
	if($bool == "c-red") {
		$url = plugins_url('css/pagination/style-pagination-rouge.css',__FILE__);
		wp_register_style('style-pagination-rouge', $url);
		wp_enqueue_style('style-pagination-rouge');
	} else
	if($bool == "geek-zone") {
		$url = plugins_url('css/pagination/geek-zone.css',__FILE__);
		wp_register_style('style-pagination-geek-zone', $url);
		wp_enqueue_style('style-pagination-geek-zone');
	} else
	if($bool == "flat") {
		$url = plugins_url('css/pagination/flat-design.css',__FILE__);
		wp_register_style('style-pagination-flat-design', $url);
		wp_enqueue_style('style-pagination-flat-design');
	} else
	if($bool == "flat-2") {
		$url = plugins_url('css/pagination/flat-design-blue.css',__FILE__);
		wp_register_style('style-pagination-flat-design-blue', $url);
		wp_enqueue_style('style-pagination-flat-design-blue');
	} else
	if($bool == "flat-color") {
		$url = plugins_url('css/pagination/colored-flat-design.css',__FILE__);
		wp_register_style('style-pagination-colored-flat-design', $url);
		wp_enqueue_style('style-pagination-colored-flat-design');
	}
	if($bool == "orange-grey") {
		$url = plugins_url('css/pagination/orange-design.css',__FILE__);
		wp_register_style('style-pagination-orange-design', $url);
		wp_enqueue_style('style-pagination-orange-design');
	}
	add_action('wp_enqueue_scripts', 'WP_Advanced_Search_Pagination_CSS');
}

// Inclusion des fichiers utiles (trigger, scroll infini et autocomplétion)
include('WP-Advanced-Search-Includes.php');

// Inclusion des options de réglages
include('WP-Advanced-Search-Options.php');

// Inclusion des options de style
include_once('WP-Advanced-Search-Styles.php');

// Inclusion des options de pagination
include_once('WP-Advanced-Search-Pagination.php');

// Inclusion des options pour l'autocomplétion
include_once('WP-Advanced-Search-Autocompletion.php');

// Inclusion des options de documentation
include_once('WP-Advanced-Search-Documentation.php');

// Inclusion de la fonction finale
include_once('WP-Advanced-Search-Function.php');
?>