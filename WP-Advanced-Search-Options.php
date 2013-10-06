<?php
function WP_Advanced_Search_FullText() {
	global $wpdb, $table_WP_Advanced_Search;
	$select = $wpdb->get_row("SELECT * FROM $table_WP_Advanced_Search WHERE id=1");
	
	// Récupération des valeurs des variables utiles
	$columnSelectSearch = $select->colonnesWhere;
	$databaseSearch = $select->db;
	$tableSearch = $select->tables;

	// Inclusion des class du moteur de recherche
	if(phpversion() < 5) {
		include('class.inc/moteur-php4.class-inc.php');
	} else {
		include('class.inc/moteur-php5.class-inc.php');
	}

	$alterTable = new alterTableFullText($databaseSearch, $tableSearch, $columnSelectSearch);
	echo '<script type="text/javascript">alert("'.__('Index FULLTEXT créés avec succès !\nVous pouvez utiliser le type FULLTEXT dorénavant...','WP-Advanced-Search').'");</script>';
}


// Mise à jour des données par défaut
function WP_Advanced_Search_update() {
	global $wpdb, $table_WP_Advanced_Search; // insérer les variables globales

	// Réglages de base
	$wp_advanced_search_table			= $_POST['wp_advanced_search_table'];
	$wp_advanced_search_name			= $_POST['wp_advanced_search_name'];
	$wp_advanced_search_colonneswhere	= $_POST['wp_advanced_search_colonneswhere'];
	$wp_advanced_search_typesearch		= $_POST['wp_advanced_search_typesearch'];
	$wp_advanced_search_encoding		= $_POST['wp_advanced_search_encoding'];
	$wp_advanced_search_exactsearch		= $_POST['wp_advanced_search_exactsearch'];
	$wp_advanced_search_accents			= $_POST['wp_advanced_search_accents'];
	$wp_advanced_search_exclusionwords	= $_POST['wp_advanced_search_exclusionwords'];
	$wp_advanced_search_stopwords		= $_POST['wp_advanced_search_stopwords'];
	
	// Options d'affichage
	$wp_advanced_search_numberOK		= $_POST['wp_advanced_search_numberOK'];
	if(is_numeric($_POST['wp_advanced_search_numberPerPage']) || !empty($_POST['wp_advanced_search_numberPerPage'])) {
		$wp_advanced_search_numberPerPage = $_POST['wp_advanced_search_numberPerPage'];
	} else {
		$wp_advanced_search_numberPerPage = 0;
	}
	$wp_advanced_search_style			= $_POST['wp_advanced_search_style'];
	$wp_advanced_search_formatageDateOK	= $_POST['wp_advanced_search_formatageDateOK'];
	$wp_advanced_search_dateOK			= $_POST['wp_advanced_search_dateOK'];
	$wp_advanced_search_authorOK		= $_POST['wp_advanced_search_authorOK'];
	$wp_advanced_search_categoryOK		= $_POST['wp_advanced_search_categoryOK'];
	$wp_advanced_search_titleOK			= $_POST['wp_advanced_search_titleOK'];
	$wp_advanced_search_articleOK		= $_POST['wp_advanced_search_articleOK'];
	$wp_advanced_search_commentOK		= $_POST['wp_advanced_search_commentOK'];
	$wp_advanced_search_imageOK			= $_POST['wp_advanced_search_imageOK'];
	
	// Mise en gras et ordre des résultats
	$wp_advanced_search_strong		= $_POST['wp_advanced_search_strong'];
	$wp_advanced_search_orderOK		= $_POST['wp_advanced_search_orderOK'];
	$wp_advanced_search_orderColumn	= $_POST['wp_advanced_search_orderColumn'];
	$wp_advanced_search_ascdesc		= $_POST['wp_advanced_search_ascdesc'];
	$wp_advanced_search_algoOK		= $_POST['wp_advanced_search_algoOK'];
	
	// Pagination
	$wp_advanced_search_pagination_active	= $_POST['wp_advanced_search_pagination_active'];
	$wp_advanced_search_pagination_style	= $_POST['wp_advanced_search_pagination_style'];
	$wp_advanced_search_pagination_firstlast= $_POST['wp_advanced_search_pagination_firstlast'];
	$wp_advanced_search_pagination_prevnext	= $_POST['wp_advanced_search_pagination_prevnext'];
	$wp_advanced_search_pagination_firstpage= $_POST['wp_advanced_search_pagination_firstpage'];
	$wp_advanced_search_pagination_lastpage	= $_POST['wp_advanced_search_pagination_lastpage'];
	$wp_advanced_search_pagination_prevtext	= $_POST['wp_advanced_search_pagination_prevtext'];
	$wp_advanced_search_pagination_nexttext	= $_POST['wp_advanced_search_pagination_nexttext'];
		
	$wp_advanced_search_update = $wpdb->update(
		$table_WP_Advanced_Search,
		array(
			"tables" => $wp_advanced_search_table,
			"nameField" => $wp_advanced_search_name,
			"colonnesWhere" => $wp_advanced_search_colonneswhere,
			"typeSearch" => $wp_advanced_search_typesearch,
			"encoding" => $wp_advanced_search_encoding,
			"exactSearch" => $wp_advanced_search_exactsearch,
			"accents" => $wp_advanced_search_accents,
			"exclusionWords" => $wp_advanced_search_exclusionwords,
			"stopWords" => $wp_advanced_search_stopwords,
			"NumberOK" => $wp_advanced_search_numberOK,
			"NumberPerPage" => $wp_advanced_search_numberPerPage,
			"Style" => $wp_advanced_search_style,
			"formatageDate" => $wp_advanced_search_formatageDateOK,
			"DateOK" => $wp_advanced_search_dateOK,
			"AuthorOK" => $wp_advanced_search_authorOK,
			"CategoryOK" => $wp_advanced_search_categoryOK,
			"TitleOK" => $wp_advanced_search_titleOK,
			"ArticleOK" => $wp_advanced_search_articleOK,
			"CommentOK" => $wp_advanced_search_commentOK,
			"ImageOK" => $wp_advanced_search_imageOK,
			"strongWords" => $wp_advanced_search_strong,
			"OrderOK" => $wp_advanced_search_orderOK,
			"OrderColumn" => $wp_advanced_search_orderColumn,
			"AscDesc" => $wp_advanced_search_ascdesc,
			"AlgoOK" => $wp_advanced_search_algoOK,
			"paginationActive" => $wp_advanced_search_pagination_active,
			"paginationStyle" => $wp_advanced_search_pagination_style,
			"paginationFirstLast" => $wp_advanced_search_pagination_firstlast,
			"paginationPrevNext" => $wp_advanced_search_pagination_prevnext,
			"paginationFirstPage" => $wp_advanced_search_pagination_firstpage,
			"paginationLastPage" => $wp_advanced_search_pagination_lastpage,
			"paginationPrevText" => $wp_advanced_search_pagination_prevtext,
			"paginationNextText" => $wp_advanced_search_pagination_nexttext
		), 
		array('id' => 1)
	);
}

// Fonction d'affichage de la page d'aide et de réglages de l'extension
function WP_Advanced_Search_Callback() {
	global $wpdb, $table_WP_Advanced_Search; // insérer les variables globales

	// Déclencher la fonction de mise à jour (upload)
	if(isset($_POST['wp_advanced_search_action']) && $_POST['wp_advanced_search_action'] == __('Enregistrer' , 'WP-Advanced-Search')) {
		WP_Advanced_Search_update();
	}
	
	// Déclencher la fonction de mise à jour (upload)
	if(isset($_POST['wp_advanced_search_fulltext'])) {
		WP_Advanced_Search_FullText();
	}

	/* --------------------------------------------------------------------- */
	/* ------------------------ Affichage de la page ----------------------- */
	/* --------------------------------------------------------------------- */
	echo '<div class="wrap">';
	echo '<div id="icon-options-general" class="icon32"><br /></div>';
	echo '<h2>'; _e('Aide et réglages de WP-Advanced-Search.','WP-Advanced-Search'); echo '</h2><br/>';
	_e('<strong>WP-Advanced-Search</strong> permet d\'activer un moteur de recherche puissant pour WordPress', 'WP-Advanced-Search'); echo '<br/>';
	_e('Plusieurs types de recherche ("LIKE", "REGEXP" ou "FULLTEXT"), algorithme de pertinence, mise en surbrillance des mots recherchés, pagination, affichage paramétrable...', 'WP-Advanced-Search');	echo '<br/>';
	_e('Tout est entièrement modulable pour obtenir des résultats précis !', 'WP-Advanced-Search');	echo '<br/>';
	echo '<ol>';
	echo '<li>'; _e('Préparez le formulaire de recherche (search-form.php)','WP-Advanced-Search'); echo '</li>';
	echo '<li>'; _e('Préparez la page de résultats (search.php)','WP-Advanced-Search'); echo '</li>';
	echo '<li>'; _e('Paramétrez le moteur ci-dessous','WP-Advanced-Search'); echo '</li>';
	echo '<li>'; _e('Ajouter le code <strong>&lt;?php WP_Advanced_Search(); ?&gt;</strong> pour afficher les résultats','WP-Advanced-Search'); echo '</li>';
	echo '</ol>';
	_e('<em>N.B. : n\'hésitez pas à contacter <a href="http://blog.internet-formation.fr" target="_blank">Mathieu Chartier</a>, le créateur du plugin, pour de plus amples informations.</em>' , 'WP-Advanced-Search'); echo '<br/><br/>';

	// Formulaire de configuration du Shortcode
	echo '<h2>'; _e('Paramètres de l\'extension','WP-Advanced-Search'); echo '</h2>';

		// Sélection des données dans la base de données		
		$select = $wpdb->get_row("SELECT * FROM $table_WP_Advanced_Search WHERE id=1");
?>
		<!-- Formulaire pour installer les index FULLTEXT (si activé en cliquant sur le lien) -->
        <form id="WP-Advanced-Search-Form" method="post">
        	<input type="hidden" name="wp_advanced_search_fulltext" value="" />
        </form>
        
        <!-- Formulaire de mise à jour des données -->
        <form method="post" action="">
       	<table>
			<tr>
            	<td><label for="wp_advanced_search_table"><strong><?php _e('Table de recherche','WP-Advanced-Search'); ?></strong></label></td>
                <td>
					<select name="wp_advanced_search_table" id="wp_advanced_search_table" style="width:100%;border:1px solid #ccc;" />
					<?php
                        $tablesSearch = $wpdb->get_results("SHOW TABLES FROM ".$select->db);					
						$numberTables = count($tablesSearch,1);
						for($i=0; $i < $numberTables; $i++) {
							foreach($tablesSearch[$i] as $table => $value) {
						?>
							<option value="<?php echo $value; ?>" <?php if($select->tables == $value) { echo 'selected="selected"'; } ?>><?php _e($value,'WP-Advanced-Search'); ?></option>
						<?php
							}
						}
                    ?>
                	</select>
				</td>
            </tr>
        </table>
        <table>
            <tr>
            	<td><label for="wp_advanced_search_name"><strong><?php _e('Attribut "name" du champ de recherche','WP-Advanced-Search'); ?></strong></label></td>
                <td><input value="<?php echo $select->nameField; ?>" name="wp_advanced_search_name" id="wp_advanced_search_name" type="text" style="width:100%;border:1px solid #ccc;" /></td>
            </tr>
        </table>
        
        <table cols="4" width="100%">
        	<tr valign="top">
            <td width="25%">
                <h3><br/><?php _e('Options générales du moteur','WP-Advanced-Search'); ?></h3>
                <p>
                    <label for="wp_advanced_search_colonneswhere"><strong><?php _e('Colonnes de la table dans lesquelles rechercher','WP-Advanced-Search'); ?></strong></label><br />
                    <input value="<?php echo $select->colonnesWhere; ?>" name="wp_advanced_search_colonneswhere" id="wp_advanced_search_colonnewhere" type="text" style="width:80%;border:1px solid #ccc;" />
                    <br/><em><?php _e('Séparez les valeurs par des virgules','WP-Advanced-Search'); ?></em>
                </p>
                <p>
                    <label for="wp_advanced_search_typesearch"><strong><?php _e('Ordre et options d\'affichage','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_typesearch" id="wp_advanced_search_typesearch" style="width:60%;border:1px solid #ccc;">
                        <option value="FULLTEXT" <?php if($select->typeSearch == 'FULLTEXT') { echo 'selected="selected"'; } ?>><?php _e('FULLTEXT','WP-Advanced-Search'); ?></option>
                        <option value="REGEXP" <?php if($select->typeSearch == 'REGEXP') { echo 'selected="selected"'; } ?>><?php _e('REGEXP','WP-Advanced-Search'); ?></option>
                        <option value="LIKE" <?php if($select->typeSearch == 'LIKE') { echo 'selected="selected"'; } ?>><?php _e('LIKE','WP-Advanced-Search'); ?></option>
                    </select>
                    <br/><em><?php _e('<a href="#" onclick="','WP-Advanced-Search'); ?>getElementById('WP-Advanced-Search-Form').submit()<?php _e('">Installez les index FULLTEXT</a> pour que la recherche<br/>FULLTEXT fonctionne bien...','WP-Advanced-Search'); ?></em>
                </p>
                <p>
                    <label for="wp_advanced_search_encoding"><strong><?php _e('Choix de l\'encodage des caractères','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_encoding" id="wp_advanced_search_encoding" style="width:60%;border:1px solid #ccc;">
                        <option value="utf-8" <?php if($select->encoding == "utf-8") { echo 'selected="selected"'; } ?>><?php _e('UTF-8','WP-Advanced-Search'); ?></option>
                        <option value="iso-8859-1" <?php if($select->encoding == "iso-8859-1") { echo 'selected="selected"'; } ?>><?php _e('ISO-8859-1 (Latin-1)','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_exactsearch"><strong><?php _e('Recherche exacte ou approchante ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_exactsearch" id="wp_advanced_search_exactsearch" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->exactSearch == true) { echo 'selected="selected"'; } ?>><?php _e('Exacte','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->exactSearch == false) { echo 'selected="selected"'; } ?>><?php _e('Approchante','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <h3><br/><?php _e('Options de formatage des requêtes','WP-Advanced-Search'); ?></h3>
                <p>
                    <label for="wp_advanced_search_stopwords"><strong><?php _e('Activer les "stop words" ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_stopwords" id="wp_advanced_search_stopwords" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->stopWords == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->stopWords == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_accents"><strong><?php _e('Suppression des accents de la requête ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_accents" id="wp_advanced_search_accents" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->accents == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->accents == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <br/><em><?php _e('Utile si les contenus sont sans accent<br/>dans la base de données','WP-Advanced-Search'); ?></em>
                </p>
                <p>
                    <label for="wp_advanced_search_exclusionwords"><strong><?php _e('Exclure les mots courts ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_exclusionwords" id="wp_advanced_search_exclusionwords" style="width:60%;border:1px solid #ccc;">
                        <option value="" <?php if(empty($select->accents)) { echo 'selected="selected"'; } ?>><?php _e('Désactivé','WP-Advanced-Search'); ?></option>
                        <option value="1" <?php if($select->exclusionWords == 1) { echo 'selected="selected"'; } ?>><?php _e('< 1 caractère','WP-Advanced-Search'); ?></option>
                        <option value="2" <?php if($select->exclusionWords == 2) { echo 'selected="selected"'; } ?>><?php _e('< 2 caractères','WP-Advanced-Search'); ?></option>
                        <option value="3" <?php if($select->exclusionWords == 3) { echo 'selected="selected"'; } ?>><?php _e('< 3 caractères','WP-Advanced-Search'); ?></option>
                        <option value="4" <?php if($select->exclusionWords == 4) { echo 'selected="selected"'; } ?>><?php _e('< 4 caractères','WP-Advanced-Search'); ?></option>
                        <option value="5" <?php if($select->exclusionWords == 5) { echo 'selected="selected"'; } ?>><?php _e('< 5 caractères','WP-Advanced-Search'); ?></option>
                        <option value="6" <?php if($select->exclusionWords == 6) { echo 'selected="selected"'; } ?>><?php _e('< 6 caractères','WP-Advanced-Search'); ?></option>
                        <option value="7" <?php if($select->exclusionWords == 7) { echo 'selected="selected"'; } ?>><?php _e('< 7 caractères','WP-Advanced-Search'); ?></option>
                        <option value="8" <?php if($select->exclusionWords == 8) { echo 'selected="selected"'; } ?>><?php _e('< 8 caractères','WP-Advanced-Search'); ?></option>
                        <option value="9" <?php if($select->exclusionWords == 9) { echo 'selected="selected"'; } ?>><?php _e('< 9 caractères','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
        	</td>
            <td width="25%">
                <h3><br/><?php _e('Options de rendu','WP-Advanced-Search'); ?></h3>
                <p>
                    <label for="wp_advanced_search_strong"><strong><?php _e('Mise en surbrillance des mots clés','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_strong" id="wp_advanced_search_strong" style="width:60%;border:1px solid #ccc;">
                        <option value="exact" <?php if($select->strongWords == "exact") { echo 'selected="selected"'; } ?>><?php _e('Précise','WP-Advanced-Search'); ?></option>
                        <option value="total" <?php if($select->strongWords == "total") { echo 'selected="selected"'; } ?>><?php _e('Approchante','WP-Advanced-Search'); ?></option>
                        <option value="aucun" <?php if($select->strongWords == "aucun") { echo 'selected="selected"'; } ?>><?php _e('Aucune mise en gras','WP-Advanced-Search'); ?></option>
                    </select>
                    <br/><em><?php _e('"Précise" pour la chaîne exacte, "Approchante" pour le mot contenant une chaîne (si recherche LIKE)','WP-Advanced-Search'); ?></em>
                </p>
                <p>
                    <label for="wp_advanced_search_numberOK"><strong><?php _e('Numéroter les résultats ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_numberOK" id="wp_advanced_search_numberOK" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->NumberOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->NumberOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_numberPerPage"><strong><?php _e('Nombre de résultats par page','WP-Advanced-Search'); ?></strong></label><br />
                    <input value="<?php echo $select->NumberPerPage; ?>" name="wp_advanced_search_numberPerPage" id="wp_advanced_search_numberPerPage" type="text" style="width:60%;border:1px solid #ccc;" />
                    <br/><em><?php _e('0 ou vide pour tout afficher dans une page sans pagination','WP-Advanced-Search'); ?></em>
                </p>
				<h3><br/><?php _e('Ordre des résultats','WP-Advanced-Search'); ?></h3>
                <p>
                    <label for="wp_advanced_search_orderOK"><strong><?php _e('Ordonner les résultats ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_orderOK" id="wp_advanced_search_orderOK" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->OrderOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->OrderOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_orderColumn"><strong><?php _e('Colonne de classement','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_orderColumn" id="wp_advanced_search_orderColumn" style="width:60%;border:1px solid #ccc;">
                    	<?php
							$columns = $wpdb->get_results("SELECT column_name FROM information_schema.columns WHERE table_name = '".$select->tables."'");							
							$numberColumn = count($columns,1);
							for($i=0; $i < $numberColumn; $i++) {
								foreach($columns[$i] as $column => $value) {
						?>
							<option value="<?php echo $value; ?>" <?php if($select->OrderColumn == $value) { echo 'selected="selected"'; } ?>><?php _e($value,'WP-Advanced-Search'); ?></option>
                        <?php
								}
							}
						?>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_ascdesc"><strong><?php _e('Croissant ou décroissant ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_ascdesc" id="wp_advanced_search_ascdesc" style="width:60%;border:1px solid #ccc;">
                        <option value="ASC" <?php if($select->AscDesc == "ASC") { echo 'selected="selected"'; } ?>><?php _e('Croissant (ASC)','WP-Advanced-Search'); ?></option>
                        <option value="DESC" <?php if($select->AscDesc == "DESC") { echo 'selected="selected"'; } ?>><?php _e('Décroissant (DESC)','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_algoOK"><strong><?php _e('Algorithme de pertinence ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_algoOK" id="wp_advanced_search_algoOK" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->AlgoOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->AlgoOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <br/><em><?php _e('L\'algorithme de pertinence affiche en ordre décroissant les résultats qui ont le plus de correspondances avec la requête','WP-Advanced-Search'); ?></em>
                </p>
        	</td>
            <td width="25%">
                <h3><br/><?php _e('Blocs à afficher','WP-Advanced-Search'); ?></h3>
                <p>
                    <label for="wp_advanced_search_style"><strong><?php _e('Style CSS pour les blocs','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_style" id="wp_advanced_search_style" style="width:60%;border:1px solid #ccc;">
                        <option value="aucun" <?php if($select->Style == "aucun") { echo 'selected="selected"'; } ?>><?php _e('Aucun style CSS','WP-Advanced-Search'); ?></option>
                        <option value="vide" <?php if($select->Style == "vide") { echo 'selected="selected"'; } ?>><?php _e('Feuille CSS Vide','WP-Advanced-Search'); ?></option>
                        <option value="bleu" <?php if($select->Style == "bleu") { echo 'selected="selected"'; } ?>><?php _e('Bleu','WP-Advanced-Search'); ?></option>
                        <option value="rouge" <?php if($select->Style == "rouge") { echo 'selected="selected"'; } ?>><?php _e('Rouge','WP-Advanced-Search'); ?></option>
                        <option value="vert" <?php if($select->Style == "vert") { echo 'selected="selected"'; } ?>><?php _e('Vert','WP-Advanced-Search'); ?></option>
                        <option value="gris" <?php if($select->Style == "gris") { echo 'selected="selected"'; } ?>><?php _e('Gris','WP-Advanced-Search'); ?></option>
                        <option value="noir" <?php if($select->Style == "noir") { echo 'selected="selected"'; } ?>><?php _e('Noir','WP-Advanced-Search'); ?></option>
                        <option value="blanc" <?php if($select->Style == "blanc") { echo 'selected="selected"'; } ?>><?php _e('Blanc (discret)','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_titleOK"><strong><?php _e('Affichage du titre ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_titleOK" id="wp_advanced_search_titleOK" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->TitleOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->TitleOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_dateOK"><strong><?php _e('Affichage de la date ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_dateOK" id="wp_advanced_search_dateOK" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->DateOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->DateOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_formatageDateOK"><strong><?php _e('Formatage de la date (si active)','WP-Planification'); ?></strong></label><br />
                    <input value="<?php echo $select->formatageDate; ?>" name="wp_advanced_search_formatageDateOK" id="wp_advanced_search_formatageDateOK" type="text" style="width:75%;border:1px solid #ccc;" />
                    <br/><em><?php _e('<a href="http://php.net/manual/fr/function.date.php" target="_blank">Voir documentation PHP sur les dates</a></em><br/><em>(ex : "l j F Y" pour "mardi 25 juin 2013")','WP-Planification'); ?></em>
                </p>
                <p>
                    <label for="wp_advanced_search_authorOK"><strong><?php _e('Affichage du nom de l\'auteur ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_authorOK" id="wp_advanced_search_authorOK" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->AuthorOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->AuthorOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_categoryOK"><strong><?php _e('Affichage de la catégorie de l\'article ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_categoryOK" id="wp_advanced_search_categoryOK" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->CategoryOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->CategoryOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_commentOK"><strong><?php _e('Affichage du nombre de commentaires ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_commentOK" id="wp_advanced_search_commentOK" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->CommentOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->CommentOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_articleOK"><strong><?php _e('Affichage de l\'article ou l\'extrait ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_articleOK" id="wp_advanced_search_articleOK" style="width:60%;border:1px solid #ccc;">
                        <option value="aucun" <?php if($select->ArticleOK == "aucun") { echo 'selected="selected"'; } ?>><?php _e('Aucun des deux','WP-Advanced-Search'); ?></option>
                        <option value="excerpt" <?php if($select->ArticleOK == "excerpt") { echo 'selected="selected"'; } ?>><?php _e('Extrait','WP-Advanced-Search'); ?></option>
                        <option value="excerptmore" <?php if($select->ArticleOK == "excerptmore") { echo 'selected="selected"'; } ?>><?php _e('Extrait + "Lire la suite..."','WP-Advanced-Search'); ?></option>
                        <option value="article" <?php if($select->ArticleOK == "article") { echo 'selected="selected"'; } ?>><?php _e('Article complet','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_imageOK"><strong><?php _e('Affichage de l\'image à la Une ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_imageOK" id="wp_advanced_search_imageOK" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->ImageOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->ImageOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
			</td>
            <td width="25%">
                <h3><br/><?php _e('Options pour la pagination','WP-Advanced-Search'); ?></h3>
                <p>
                    <label for="wp_advanced_search_pagination_active"><strong><?php _e('Activer la pagination','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_pagination_active" id="wp_advanced_search_pagination_active" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->paginationActive == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->paginationActive == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_pagination_style"><strong><?php _e('Style CSS pour la pagination','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_pagination_style" id="wp_advanced_search_pagination_style" style="width:60%;border:1px solid #ccc;">
                        <option value="aucun" <?php if($select->paginationStyle == "aucun") { echo 'selected="selected"'; } ?>><?php _e('Aucun style CSS','WP-Advanced-Search'); ?></option>
                        <option value="vide" <?php if($select->paginationStyle == "vide") { echo 'selected="selected"'; } ?>><?php _e('Feuille CSS Vide','WP-Advanced-Search'); ?></option>
                        <option value="bleu" <?php if($select->paginationStyle == "bleu") { echo 'selected="selected"'; } ?>><?php _e('Bleu','WP-Advanced-Search'); ?></option>
                        <option value="rouge" <?php if($select->paginationStyle == "rouge") { echo 'selected="selected"'; } ?>><?php _e('Rouge','WP-Advanced-Search'); ?></option>
                        <option value="vert" <?php if($select->paginationStyle == "vert") { echo 'selected="selected"'; } ?>><?php _e('Vert','WP-Advanced-Search'); ?></option>
                        <option value="gris" <?php if($select->paginationStyle == "gris") { echo 'selected="selected"'; } ?>><?php _e('Gris','WP-Advanced-Search'); ?></option>
                        <option value="noir" <?php if($select->paginationStyle == "noir") { echo 'selected="selected"'; } ?>><?php _e('Noir','WP-Advanced-Search'); ?></option>
                        <option value="blanc" <?php if($select->paginationStyle == "blanc") { echo 'selected="selected"'; } ?>><?php _e('Blanc','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_pagination_firstlast"><strong><?php _e('Affichage de "première page" et "dernière page" ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_pagination_firstlast" id="wp_advanced_search_pagination_firstlast" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->paginationFirstLast == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->paginationFirstLast == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_pagination_prevnext"><strong><?php _e('Affichage de "précédent" et "suivant" ?','WP-Advanced-Search'); ?></strong></label><br />
                    <select name="wp_advanced_search_pagination_prevnext" id="wp_advanced_search_pagination_prevnext" style="width:60%;border:1px solid #ccc;">
                        <option value="1" <?php if($select->paginationPrevNext == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->paginationPrevNext == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                </p>
                <p>
                    <label for="wp_advanced_search_pagination_firstpage"><strong><?php _e('Texte pour "première page"','WP-Advanced-Search'); ?></strong></label><br />
                    <input value="<?php echo $select->paginationFirstPage; ?>" name="wp_advanced_search_pagination_firstpage" id="wp_advanced_search_pagination_firstpage" type="text" style="width:60%;border:1px solid #ccc;" />
                </p>
                <p>
                    <label for="wp_advanced_search_pagination_lastpage"><strong><?php _e('Texte pour "dernière page"','WP-Advanced-Search'); ?></strong></label><br />
                    <input value="<?php echo $select->paginationLastPage; ?>" name="wp_advanced_search_pagination_lastpage" id="wp_advanced_search_pagination_lastpage" type="text" style="width:60%;border:1px solid #ccc;" />
                </p>
                <p>
                    <label for="wp_advanced_search_pagination_prevtext"><strong><?php _e('Texte pour "précédent"','WP-Advanced-Search'); ?></strong></label><br />
                    <input value="<?php echo $select->paginationPrevText; ?>" name="wp_advanced_search_pagination_prevtext" id="wp_advanced_search_pagination_prevtext" type="text" style="width:60%;border:1px solid #ccc;" />
                </p>
                <p>
                    <label for="wp_advanced_search_pagination_nexttext"><strong><?php _e('Texte pour "suivant"','WP-Advanced-Search'); ?></strong></label><br />
                    <input value="<?php echo $select->paginationNextText; ?>" name="wp_advanced_search_pagination_nexttext" id="wp_advanced_search_pagination_nexttext" type="text" style="width:60%;border:1px solid #ccc;" />
                </p>
        	</td>
        </table>
        <p class="submit"><input type="submit" name="wp_advanced_search_action" class="button-primary" value="<?php _e('Enregistrer' , 'WP-Advanced-Search'); ?>" /></p>
        </form>
<?php
	echo '</div>'; // Fin de la page d'admin
} // Fin de la fonction Callback
?>