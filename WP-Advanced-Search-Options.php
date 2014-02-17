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
	$wp_advanced_search_resulttext		= $_POST['wp_advanced_search_resulttext'];
	$wp_advanced_search_errortext		= $_POST['wp_advanced_search_errortext'];
	$wp_advanced_search_colonneswhere	= $_POST['wp_advanced_search_colonneswhere'];
	$wp_advanced_search_typesearch		= $_POST['wp_advanced_search_typesearch'];
	$wp_advanced_search_encoding		= $_POST['wp_advanced_search_encoding'];
	$wp_advanced_search_exactsearch		= $_POST['wp_advanced_search_exactsearch'];
	$wp_advanced_search_accents			= $_POST['wp_advanced_search_accents'];
	$wp_advanced_search_exclusionwords	= $_POST['wp_advanced_search_exclusionwords'];
	$wp_advanced_search_stopwords		= $_POST['wp_advanced_search_stopwords'];
	$wp_advanced_search_posttype		= $_POST['wp_advanced_search_posttype'];
	$wp_advanced_search_categories 		= array();
	foreach($_POST['wp_advanced_search_categories'] as $ctgSave) {
		echo $ctgSave;
		array_push($wp_advanced_search_categories, $ctgSave);
	}
	print($wp_advanced_search_categories);
	if(is_numeric($_POST['wp_advanced_search_numberPerPage']) || !empty($_POST['wp_advanced_search_numberPerPage'])) {
		$wp_advanced_search_numberPerPage = $_POST['wp_advanced_search_numberPerPage'];
	} else {
		$wp_advanced_search_numberPerPage = 0;
	}
	
	// Mise en gras et ordre des résultats
	$wp_advanced_search_strong		= $_POST['wp_advanced_search_strong'];
	$wp_advanced_search_orderOK		= $_POST['wp_advanced_search_orderOK'];
	$wp_advanced_search_orderColumn	= $_POST['wp_advanced_search_orderColumn'];
	$wp_advanced_search_ascdesc		= $_POST['wp_advanced_search_ascdesc'];
	$wp_advanced_search_algoOK		= $_POST['wp_advanced_search_algoOK'];
		
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
			"NumberPerPage" => $wp_advanced_search_numberPerPage,
			"strongWords" => $wp_advanced_search_strong,
			"OrderOK" => $wp_advanced_search_orderOK,
			"OrderColumn" => $wp_advanced_search_orderColumn,
			"AscDesc" => $wp_advanced_search_ascdesc,
			"AlgoOK" => $wp_advanced_search_algoOK,
			"postType" => $wp_advanced_search_posttype,
			"categories" => serialize($wp_advanced_search_categories),
			"ResultText" => $wp_advanced_search_resulttext,
			"ErrorText" => $wp_advanced_search_errortext
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
	echo '<div class="wrap advanced-search-admin">';
	echo '<div class="icon32 icon"><br /></div>';
	echo '<h2>'; _e('Aide et réglages de WP-Advanced-Search.','WP-Advanced-Search'); echo '</h2><br/>';
	echo '<div class="text">';
	_e('<strong>WP Advanced Search</strong> permet d\'activer un moteur de recherche puissant pour WordPress.', 'WP-Advanced-Search'); echo '<br/>';
	_e('Plusieurs types de recherche ("LIKE", "REGEXP" ou "FULLTEXT"), algorithme de pertinence, mise en surbrillance des mots recherchés, pagination, affichage paramétrable... ', 'WP-Advanced-Search');
	_e('Tout est entièrement modulable pour obtenir des résultats précis !', 'WP-Advanced-Search');	echo '<br/>';
	_e('<strong>Consultez la documentation pour plus d\'informations si nécessaire...</strong>', 'WP-Advanced-Search');	echo '<br/>';
	echo '</div>';

	// Sélection des données dans la base de données		
	$select = $wpdb->get_row("SELECT * FROM $table_WP_Advanced_Search WHERE id=1");
?>
	<script type="text/javascript">
		function montrer(object) {
		   if (document.getElementById) document.getElementById(object).style.display = 'block';
		}
		
		function cacher(object) {
		   if (document.getElementById) document.getElementById(object).style.display = 'none';
		}
    </script>

		<!-- Formulaire pour installer les index FULLTEXT (si activé en cliquant sur le lien) -->
        <form id="WP-Advanced-Search-Form" method="post">
        	<input type="hidden" name="wp_advanced_search_fulltext" value="" />
        </form>
        
        <!-- Formulaire de mise à jour des données -->
        <form method="post" action="">
       	<div class="block">
            <div class="col">
            	<h4><?php _e('Options générales du moteur','WP-Advanced-Search'); ?></h4>
                <p class="tr">
                <select name="wp_advanced_search_table" id="wp_advanced_search_table" />
                <?php
                    $tablesSearch = $wpdb->get_results("SHOW TABLES FROM ".$select->db." LIKE '".$wpdb->prefix."%'");					
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
				<label for="wp_advanced_search_table"><strong><?php _e('Table de recherche','WP-Advanced-Search'); ?></strong></label>
				</p>
                <p class="tr">
                <input value="<?php echo $select->nameField; ?>" name="wp_advanced_search_name" id="wp_advanced_search_name" type="text" />
                <label for="wp_advanced_search_name"><strong><?php _e('Attribut "name" du champ de recherche','WP-Advanced-Search'); ?></strong></label>
				</p>
                <p class="tr">
                    <input value="<?php echo $select->colonnesWhere; ?>" name="wp_advanced_search_colonneswhere" id="wp_advanced_search_colonnewhere" type="text" />
                    <label for="wp_advanced_search_colonneswhere"><strong><?php _e('Colonnes de la table dans lesquelles rechercher','WP-Advanced-Search'); ?></strong></label>
                    <br/><em><?php _e('Séparez les valeurs par des virgules','WP-Advanced-Search'); ?></em>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_typesearch" id="wp_advanced_search_typesearch">
                        <option value="FULLTEXT" <?php if($select->typeSearch == 'FULLTEXT') { echo 'selected="selected"'; } ?>><?php _e('FULLTEXT','WP-Advanced-Search'); ?></option>
                        <option value="REGEXP" <?php if($select->typeSearch == 'REGEXP') { echo 'selected="selected"'; } ?>><?php _e('REGEXP','WP-Advanced-Search'); ?></option>
                        <option value="LIKE" <?php if($select->typeSearch == 'LIKE') { echo 'selected="selected"'; } ?>><?php _e('LIKE','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_typesearch"><strong><?php _e('Type de recherche PHP-MySQL','WP-Advanced-Search'); ?></strong></label>
                    <br/><em><?php _e('<a href="#" onclick="','WP-Advanced-Search'); ?>getElementById('WP-Advanced-Search-Form').submit()<?php _e('">Installez les index FULLTEXT</a> pour que la recherche FULLTEXT puisse fonctionner...','WP-Advanced-Search'); ?></em>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_posttype" id="wp_advanced_search_posttype">
                        <option value="post" <?php if($select->postType == 'post') { echo 'selected="selected"'; } ?> onclick="montrer('ctgBlock')";><?php _e('Articles','WP-Advanced-Search'); ?></option>
                        <option value="page" <?php if($select->postType == 'page') { echo 'selected="selected"'; } ?> onclick="cacher('ctgBlock')";><?php _e('Pages','WP-Advanced-Search'); ?></option>
                        <option value="pagepost" <?php if($select->postType == 'pagepost') { echo 'selected="selected"'; } ?> onclick="cacher('ctgBlock')"><?php _e('Articles + Pages','WP-Advanced-Search'); ?></option>
                        <option value="all" <?php if($select->postType == 'all') { echo 'selected="selected"'; } ?> onclick="cacher('ctgBlock')"><?php _e('Tout','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_posttype"><strong><?php _e('Type de contenus pour la recherche ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr" id="ctgBlock" <?php if($select->postType == 'post') { echo 'style="display:block;"'; } else { echo 'style="display:none;"'; } ?>>
					<?php
                        $tabSlugCategories = $wpdb->get_results("SELECT TE.slug FROM $wpdb->terms as TE INNER JOIN $wpdb->term_taxonomy as TT WHERE TT.taxonomy = 'category' AND TE.term_id = TT.term_id"); // Ajouter AND TT.count !=0 pour ne garder que les catégories contenant des articles !
						$tabNameCategories = $wpdb->get_results("SELECT TE.name FROM $wpdb->terms as TE INNER JOIN $wpdb->term_taxonomy as TT WHERE TT.taxonomy = 'category' AND TE.term_id = TT.term_id"); // Ajouter AND TT.count !=0 pour ne garder que les catégories contenant des articles !
						//$tabCategories = array_combine($tabSlugCategories, $tabNameCategories);
						foreach($tabSlugCategories as $slugTab) {
							foreach($slugTab as $slug) {
								$tabSlug[] = $slug;	
							}
						}
						foreach($tabNameCategories as $nameTab) {
							foreach($nameTab as $name) {
								$tabName[] = $name;	
							}
						}
						$tabCategories = array_combine($tabSlug, $tabName);
						$select->categories = unserialize($select->categories);
                    ?>
                    <select name="wp_advanced_search_categories[]" id="wp_advanced_search_categories" multiple="multiple" size="5">
                        <option value="toutes" <?php if(in_array('toutes', $select->categories)) { echo 'selected="selected"'; } ?>><?php _e('Toutes les catégories','WP-Advanced-Search'); ?></option>
                        <?php
						foreach($tabCategories as $tabKey => $tabCtg) {
						?>
								<option value="<?php echo $tabKey; ?>" <?php if(in_array($tabKey, $select->categories)) { echo 'selected="selected"'; } ?> name="categories"><?php _e($tabCtg,'WP-Advanced-Search'); ?></option>
                        <?php
						}
                        ?>
                    </select>
                    <label for="wp_advanced_search_categories"><strong><?php _e('Catégories de recherche (articles uniquement)','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <input value="<?php echo $select->NumberPerPage; ?>" name="wp_advanced_search_numberPerPage" id="wp_advanced_search_numberPerPage" type="text" />
                    <label for="wp_advanced_search_numberPerPage"><strong><?php _e('Nombre de résultats par page','WP-Advanced-Search'); ?></strong></label>
                    <br/><em><?php _e('0 ou vide pour tout afficher dans une page (sans pagination)','WP-Advanced-Search'); ?></em>
                </p>

                <h4><br/><?php _e('Mise en surbrillance et rendu','WP-Advanced-Search'); ?></h4>
                <p class="tr">
                    <select name="wp_advanced_search_strong" id="wp_advanced_search_strong">
                        <option value="exact" <?php if($select->strongWords == "exact") { echo 'selected="selected"'; } ?>><?php _e('Précise','WP-Advanced-Search'); ?></option>
                        <option value="total" <?php if($select->strongWords == "total") { echo 'selected="selected"'; } ?>><?php _e('Approchante','WP-Advanced-Search'); ?></option>
                        <option value="aucun" <?php if($select->strongWords == "aucun") { echo 'selected="selected"'; } ?>><?php _e('Aucune mise en gras','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_strong"><strong><?php _e('Mise en surbrillance des mots clés','WP-Advanced-Search'); ?></strong></label>
                    <br/><em><?php _e('"Précise" pour rechercher la chaîne exacte, "Approchante" pour chercher les mots contenant une chaîne (si recherche LIKE)','WP-Advanced-Search'); ?></em>
                </p>
                <p class="tr">
                <input value="<?php echo $select->ResultText; ?>" name="wp_advanced_search_resulttext" id="wp_advanced_search_resulttext" type="text" />
                <label for="wp_advanced_search_resulttext"><strong><?php _e('Texte pour la requête recherchée','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                <input value="<?php echo $select->ErrorText; ?>" name="wp_advanced_search_errortext" id="wp_advanced_search_errortext" type="text" />
                <label for="wp_advanced_search_errortext"><strong><?php _e('Texte affiché si aucun résultat','WP-Advanced-Search'); ?></strong></label>
                </p>
        	</div>
            <div class="col">
				<h4><?php _e('Ordre des résultats','WP-Advanced-Search'); ?></h4>
                <p class="tr">
                    <select name="wp_advanced_search_orderOK" id="wp_advanced_search_orderOK">
                        <option value="1" <?php if($select->OrderOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->OrderOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_orderOK"><strong><?php _e('Ordonner les résultats ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_algoOK" id="wp_advanced_search_algoOK">
                        <option value="1" <?php if($select->AlgoOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->AlgoOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_algoOK"><strong><?php _e('Algorithme de pertinence ?','WP-Advanced-Search'); ?></strong></label>
                    <br/><em><?php _e('L\'algorithme de pertinence affiche en ordre décroissant les résultats qui ont le plus de correspondances avec la requête','WP-Advanced-Search'); ?></em>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_orderColumn" id="wp_advanced_search_orderColumn">
                    	<?php
							$columns = $wpdb->get_results("SELECT column_name FROM information_schema.COLUMNS WHERE table_name = '".$select->tables."'");							
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
                    <label for="wp_advanced_search_orderColumn"><strong><?php _e('Colonne de classement','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_ascdesc" id="wp_advanced_search_ascdesc">
                        <option value="ASC" <?php if($select->AscDesc == "ASC") { echo 'selected="selected"'; } ?>><?php _e('Croissant (ASC)','WP-Advanced-Search'); ?></option>
                        <option value="DESC" <?php if($select->AscDesc == "DESC") { echo 'selected="selected"'; } ?>><?php _e('Décroissant (DESC)','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_ascdesc"><strong><?php _e('Croissant ou décroissant ?','WP-Advanced-Search'); ?></strong></label>
                </p>

                <h4><br/><?php _e('Options de formatage des requêtes','WP-Advanced-Search'); ?></h4>
                <p class="tr">
                    <select name="wp_advanced_search_stopwords" id="wp_advanced_search_stopwords">
                        <option value="1" <?php if($select->stopWords == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->stopWords == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_stopwords"><strong><?php _e('Activer les "stop words" ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_exclusionwords" id="wp_advanced_search_exclusionwords">
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
                    <label for="wp_advanced_search_exclusionwords"><strong><?php _e('Exclure les mots courts ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_exactsearch" id="wp_advanced_search_exactsearch">
                        <option value="1" <?php if($select->exactSearch == true) { echo 'selected="selected"'; } ?>><?php _e('Exacte','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->exactSearch == false) { echo 'selected="selected"'; } ?>><?php _e('Approchante','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_exactsearch"><strong><?php _e('Recherche exacte ou approchante ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_encoding" id="wp_advanced_search_encoding">
                        <option value="utf-8" <?php if($select->encoding == "utf-8") { echo 'selected="selected"'; } ?>><?php _e('UTF-8','WP-Advanced-Search'); ?></option>
                        <option value="iso-8859-1" <?php if($select->encoding == "iso-8859-1") { echo 'selected="selected"'; } ?>><?php _e('ISO-8859-1 (Latin-1)','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_encoding"><strong><?php _e('Choix de l\'encodage des caractères','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_accents" id="wp_advanced_search_accents">
                        <option value="1" <?php if($select->accents == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->accents == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_accents"><strong><?php _e('Suppression des accents de la requête ?','WP-Advanced-Search'); ?></strong></label>
                    <br/><em><?php _e('Utile si les contenus sont sans accent dans la base de données','WP-Advanced-Search'); ?></em>
                </p>
			</div>
        </div>
        <p class="clear"><input type="submit" name="wp_advanced_search_action" class="button-primary" value="<?php _e('Enregistrer' , 'WP-Advanced-Search'); ?>" /></p>
        </form>
<?php
	echo '</div>'; // Fin de la page d'admin
} // Fin de la fonction Callback
?>