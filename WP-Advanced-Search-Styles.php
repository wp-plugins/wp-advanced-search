<?php
// Fonction d'affichage de la page d'aide et de réglages de l'extension
function WP_Advanced_Search_Callback_Styles() {
	global $wpdb, $table_WP_Advanced_Search; // insérer les variables globales

	// Déclencher la fonction de mise à jour (upload)
	if(isset($_POST['wp_advanced_search_action']) && $_POST['wp_advanced_search_action'] == __('Enregistrer' , 'WP-Advanced-Search')) {
		WP_Advanced_Search_update_styles();
	}

	/* --------------------------------------------------------------------- */
	/* ------------------------ Affichage de la page ----------------------- */
	/* --------------------------------------------------------------------- */
	echo '<div class="wrap advanced-search-admin">';
	echo '<div class="icon32 icon"><br /></div>';
	echo '<h2>'; _e('Réglages des thèmes et des styles','WP-Advanced-Search'); echo '</h2><br/>';
	echo '<div class="text">';
	_e('<strong>WP-Advanced-Search</strong> permet d\'activer un moteur de recherche puissant pour WordPress', 'WP-Advanced-Search'); echo '<br/>';
	_e('Plusieurs types de recherche ("LIKE", "REGEXP" ou "FULLTEXT"), algorithme de pertinence, mise en surbrillance des mots recherchés, pagination, affichage paramétrable...', 'WP-Advanced-Search');	echo '<br/>';
	echo '</div>';

	// Sélection des données dans la base de données		
	$select = $wpdb->get_row("SELECT * FROM $table_WP_Advanced_Search WHERE id=1");
?>
        <form method="post" action="">
       	<div class="block">
            <div class="col">
                <h4><?php _e('Blocs à afficher','WP-Advanced-Search'); ?></h4>
                <p class="tr">
                    <select name="wp_advanced_search_titleOK" id="wp_advanced_search_titleOK">
                        <option value="1" <?php if($select->TitleOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->TitleOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_titleOK"><strong><?php _e('Affichage du titre ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_dateOK" id="wp_advanced_search_dateOK">
                        <option value="1" <?php if($select->DateOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->DateOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_dateOK"><strong><?php _e('Affichage de la date ?','WP-Advanced-Search'); ?></strong></label>
                </p>
<p class="tr">
                    <select name="wp_advanced_search_authorOK" id="wp_advanced_search_authorOK">
                        <option value="1" <?php if($select->AuthorOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->AuthorOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_authorOK"><strong><?php _e('Affichage du nom de l\'auteur ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_categoryOK" id="wp_advanced_search_categoryOK">
                        <option value="1" <?php if($select->CategoryOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->CategoryOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_categoryOK"><strong><?php _e('Affichage de la catégorie de l\'article ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_commentOK" id="wp_advanced_search_commentOK">

                        <option value="1" <?php if($select->CommentOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->CommentOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_commentOK"><strong><?php _e('Affichage du nombre de commentaires ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_articleOK" id="wp_advanced_search_articleOK">
                        <option value="aucun" <?php if($select->ArticleOK == "aucun") { echo 'selected="selected"'; } ?>><?php _e('Aucun des deux','WP-Advanced-Search'); ?></option>
                        <option value="excerpt" <?php if($select->ArticleOK == "excerpt") { echo 'selected="selected"'; } ?>><?php _e('Extrait','WP-Advanced-Search'); ?></option>
                        <option value="excerptmore" <?php if($select->ArticleOK == "excerptmore") { echo 'selected="selected"'; } ?>><?php _e('Extrait + "Lire la suite..."','WP-Advanced-Search'); ?></option>
                        <option value="article" <?php if($select->ArticleOK == "article") { echo 'selected="selected"'; } ?>><?php _e('Article complet','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_articleOK"><strong><?php _e('Affichage de l\'article ou l\'extrait ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_imageOK" id="wp_advanced_search_imageOK">
                        <option value="1" <?php if($select->ImageOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->ImageOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_imageOK"><strong><?php _e('Affichage de l\'image à la Une ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_blocOrder" id="wp_advanced_search_blocOrder">
                        <option value="D-A-C" <?php if($select->BlocOrder == "D-A-C") { echo 'selected="selected"'; } ?>><?php _e('Date - Auteur - Catégorie','WP-Advanced-Search'); ?></option>
                        <option value="D-C-A" <?php if($select->BlocOrder == "D-C-A") { echo 'selected="selected"'; } ?>><?php _e('Date - Catégorie - Auteur','WP-Advanced-Search'); ?></option>
                        <option value="A-D-C" <?php if($select->BlocOrder == "A-D-C") { echo 'selected="selected"'; } ?>><?php _e('Auteur - Date - Catégorie','WP-Advanced-Search'); ?></option>
                        <option value="A-C-D" <?php if($select->BlocOrder == "A-C-D") { echo 'selected="selected"'; } ?>><?php _e('Auteur - Catégorie - Date','WP-Advanced-Search'); ?></option>
                        <option value="C-A-D" <?php if($select->BlocOrder == "C-A-D") { echo 'selected="selected"'; } ?>><?php _e('Catégorie - Auteur - Date','WP-Advanced-Search'); ?></option>
                        <option value="C-D-A" <?php if($select->BlocOrder == "C-D-A") { echo 'selected="selected"'; } ?>><?php _e('Catégorie - Date - Auteur','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_blocOrder"><strong><?php _e('Ordre d\'affichage des informations','WP-Advanced-Search'); ?></strong></label>
                </p>
            </div>
			<div class="col">
                <h4><?php _e('Style des blocs','WP-Advanced-Search'); ?></h4>
                <p class="tr">
                    <select name="wp_advanced_search_style" id="wp_advanced_search_style">
                        <option value="aucun" <?php if($select->Style == "aucun") { echo 'selected="selected"'; } ?>><?php _e('Aucun style CSS','WP-Advanced-Search'); ?></option>
                        <option value="vide" <?php if($select->Style == "vide") { echo 'selected="selected"'; } ?>><?php _e('Feuille CSS Vide','WP-Advanced-Search'); ?></option>
                        <option value="c-blue" <?php if($select->Style == "c-blue") { echo 'selected="selected"'; } ?>><?php _e('Classic blue','WP-Advanced-Search'); ?></option>
                        <option value="c-red" <?php if($select->Style == "c-red") { echo 'selected="selected"'; } ?>><?php _e('Classic red','WP-Advanced-Search'); ?></option>
                        <option value="c-black" <?php if($select->Style == "c-black") { echo 'selected="selected"'; } ?>><?php _e('Classic black','WP-Advanced-Search'); ?></option>
                        <option value="geek-zone" <?php if($select->Style == "geek-zone") { echo 'selected="selected"'; } ?>><?php _e('Geek zone','WP-Advanced-Search'); ?></option>
                        <option value="flat" <?php if($select->Style == "flat") { echo 'selected="selected"'; } ?>><?php _e('Sober flat design','WP-Advanced-Search'); ?></option>
                        <option value="flat-2" <?php if($select->Style == "flat-2") { echo 'selected="selected"'; } ?>><?php _e('Sober flat design blue','WP-Advanced-Search'); ?></option>
                        <option value="flat-color" <?php if($select->Style == "flat-color") { echo 'selected="selected"'; } ?>><?php _e('Colored flat design','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_style"><strong><?php _e('Style CSS pour les blocs','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <input value="<?php echo $select->formatageDate; ?>" name="wp_advanced_search_formatageDateOK" id="wp_advanced_search_formatageDateOK" type="text" />
                    <label for="wp_advanced_search_formatageDateOK"><strong><?php _e('Formatage de la date (si active)','WP-Planification'); ?></strong></label>
                        <br/><em><?php _e('<a href="http://php.net/manual/fr/function.date.php" target="_blank">Voir documentation PHP sur les dates</a> (exemple : "l j F Y" pour "mardi 25 juin 2013")','WP-Planification'); ?></em>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_nbResultsOK" id="wp_advanced_search_nbResultsOK">
                        <option value="1" <?php if($select->nbResultsOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->nbResultsOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_nbResultsOK"><strong><?php _e('Affichage du nombre de résultats ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_numberOK" id="wp_advanced_search_numberOK">
                        <option value="1" <?php if($select->NumberOK == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->NumberOK == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_numberOK"><strong><?php _e('Numéroter les résultats ?','WP-Advanced-Search'); ?></strong></label>
                </p>
        	</div>
		</div>
        <p class="clear"><input type="submit" name="wp_advanced_search_action" class="button-primary" value="<?php _e('Enregistrer' , 'WP-Advanced-Search'); ?>" /></p>
        </form>
<?php
	echo '</div>'; // Fin de la page d'admin
} // Fin de la fonction Callback

// Mise à jour des données par défaut
function WP_Advanced_Search_update_styles() {
	global $wpdb, $table_WP_Advanced_Search; // insérer les variables globales
	
	// Options d'affichage
	$wp_advanced_search_nbResultsOK		= $_POST['wp_advanced_search_nbResultsOK'];
	$wp_advanced_search_numberOK		= $_POST['wp_advanced_search_numberOK'];
	$wp_advanced_search_style			= $_POST['wp_advanced_search_style'];
	$wp_advanced_search_formatageDateOK	= $_POST['wp_advanced_search_formatageDateOK'];
	$wp_advanced_search_dateOK			= $_POST['wp_advanced_search_dateOK'];
	$wp_advanced_search_authorOK		= $_POST['wp_advanced_search_authorOK'];
	$wp_advanced_search_categoryOK		= $_POST['wp_advanced_search_categoryOK'];
	$wp_advanced_search_titleOK			= $_POST['wp_advanced_search_titleOK'];
	$wp_advanced_search_articleOK		= $_POST['wp_advanced_search_articleOK'];
	$wp_advanced_search_commentOK		= $_POST['wp_advanced_search_commentOK'];
	$wp_advanced_search_imageOK			= $_POST['wp_advanced_search_imageOK'];
	$wp_advanced_search_blocOrder		= $_POST['wp_advanced_search_blocOrder'];
		
	$wp_advanced_search_update = $wpdb->update(
		$table_WP_Advanced_Search,
		array(
			"nbResultsOK" => $wp_advanced_search_nbResultsOK,
			"NumberOK" => $wp_advanced_search_numberOK,
			"Style" => $wp_advanced_search_style,
			"formatageDate" => $wp_advanced_search_formatageDateOK,
			"DateOK" => $wp_advanced_search_dateOK,
			"AuthorOK" => $wp_advanced_search_authorOK,
			"CategoryOK" => $wp_advanced_search_categoryOK,
			"TitleOK" => $wp_advanced_search_titleOK,
			"ArticleOK" => $wp_advanced_search_articleOK,
			"CommentOK" => $wp_advanced_search_commentOK,
			"ImageOK" => $wp_advanced_search_imageOK,
			"BlocOrder" => $wp_advanced_search_blocOrder
		), 
		array('id' => 1)
	);
}
?>