<?php
// Fonction d'affichage de la page d'aide et de réglages de l'extension
function WP_Advanced_Search_Callback_Pagination() {
	global $wpdb, $table_WP_Advanced_Search; // insérer les variables globales

	// Déclencher la fonction de mise à jour (upload)
	if(isset($_POST['wp_advanced_search_action']) && $_POST['wp_advanced_search_action'] == __('Enregistrer' , 'WP-Advanced-Search')) {
		WP_Advanced_Search_update_pagination();
	}

	/* --------------------------------------------------------------------- */
	/* ------------------------ Affichage de la page ----------------------- */
	/* --------------------------------------------------------------------- */
	echo '<div class="wrap advanced-search-admin">';
	echo '<div class="icon32 icon"><br /></div>';
	echo '<h2>'; _e('Gestion de la pagination','WP-Advanced-Search'); echo '</h2><br/>';
	echo '<div class="text">';
	_e('<strong>WP-Advanced-Search</strong> permet d\'activer une pagination personnalisable.', 'WP-Advanced-Search'); echo '<br/>';
	_e('Modifiez les options de pagination, le style final et les libellés associés pour obtenir un résultat adéquat.', 'WP-Advanced-Search');	echo '<br/>';
	echo '</div>';
	
	// Sélection des données dans la base de données		
	$select = $wpdb->get_row("SELECT * FROM $table_WP_Advanced_Search WHERE id=1");
?> 
        <form method="post" action="">
       	<div class="block">
            <div class="col">
                <h4><?php _e('Options pour la pagination','WP-Advanced-Search'); ?></h4>
                <p class="tr">
                    <select name="wp_advanced_search_pagination_active" id="wp_advanced_search_pagination_active">
                        <option value="1" <?php if($select->paginationActive == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->paginationActive == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_pagination_active"><strong><?php _e('Activer la pagination','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_pagination_firstlast" id="wp_advanced_search_pagination_firstlast">
                        <option value="1" <?php if($select->paginationFirstLast == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->paginationFirstLast == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_pagination_firstlast"><strong><?php _e('Affichage de "première page" et "dernière page" ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <select name="wp_advanced_search_pagination_prevnext" id="wp_advanced_search_pagination_prevnext">
                        <option value="1" <?php if($select->paginationPrevNext == true) { echo 'selected="selected"'; } ?>><?php _e('Oui','WP-Advanced-Search'); ?></option>
                        <option value="0" <?php if($select->paginationPrevNext == false) { echo 'selected="selected"'; } ?>><?php _e('Non','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_pagination_prevnext"><strong><?php _e('Affichage de "précédent" et "suivant" ?','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                	<select name="wp_advanced_search_pagination_style" id="wp_advanced_search_pagination_style">
                        <option value="aucun" <?php if($select->paginationStyle == "aucun") { echo 'selected="selected"'; } ?>><?php _e('Aucun style CSS','WP-Advanced-Search'); ?></option>
                        <option value="vide" <?php if($select->paginationStyle == "vide") { echo 'selected="selected"'; } ?>><?php _e('Feuille CSS Vide','WP-Advanced-Search'); ?></option>
                        <option value="c-blue" <?php if($select->paginationStyle == "c-blue") { echo 'selected="selected"'; } ?>><?php _e('Classic blue','WP-Advanced-Search'); ?></option>
                        <option value="c-red" <?php if($select->paginationStyle == "c-red") { echo 'selected="selected"'; } ?>><?php _e('Classic red','WP-Advanced-Search'); ?></option>
                        <option value="c-black" <?php if($select->paginationStyle == "c-black") { echo 'selected="selected"'; } ?>><?php _e('Classic black','WP-Advanced-Search'); ?></option>
                        <option value="geek-zone" <?php if($select->paginationStyle == "geek-zone") { echo 'selected="selected"'; } ?>><?php _e('Geek zone','WP-Advanced-Search'); ?></option>
                        <option value="flat" <?php if($select->paginationStyle == "flat") { echo 'selected="selected"'; } ?>><?php _e('Sober flat design','WP-Advanced-Search'); ?></option>
                        <option value="flat-2" <?php if($select->paginationStyle == "flat-2") { echo 'selected="selected"'; } ?>><?php _e('Sober flat design blue','WP-Advanced-Search'); ?></option>
                        <option value="flat-color" <?php if($select->paginationStyle == "flat-color") { echo 'selected="selected"'; } ?>><?php _e('Colored flat design','WP-Advanced-Search'); ?></option>
                    </select>
                    <label for="wp_advanced_search_pagination_style"><strong><?php _e('Style CSS pour la pagination','WP-Advanced-Search'); ?></strong></label>
                </p>
            </div>
            <div class="col">
                <h4><?php _e('Libellés pour la pagination','WP-Advanced-Search'); ?></h4>
                <p class="tr">
                    <input value="<?php echo $select->paginationFirstPage; ?>" name="wp_advanced_search_pagination_firstpage" id="wp_advanced_search_pagination_firstpage" type="text" />
                    <label for="wp_advanced_search_pagination_firstpage"><strong><?php _e('Texte pour "première page"','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <input value="<?php echo $select->paginationLastPage; ?>" name="wp_advanced_search_pagination_lastpage" id="wp_advanced_search_pagination_lastpage" type="text" />
                    <label for="wp_advanced_search_pagination_lastpage"><strong><?php _e('Texte pour "dernière page"','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <input value="<?php echo $select->paginationPrevText; ?>" name="wp_advanced_search_pagination_prevtext" id="wp_advanced_search_pagination_prevtext" type="text" />
                    <label for="wp_advanced_search_pagination_prevtext"><strong><?php _e('Texte pour "précédent"','WP-Advanced-Search'); ?></strong></label>
                </p>
                <p class="tr">
                    <input value="<?php echo $select->paginationNextText; ?>" name="wp_advanced_search_pagination_nexttext" id="wp_advanced_search_pagination_nexttext" type="text" />
                    <label for="wp_advanced_search_pagination_nexttext"><strong><?php _e('Texte pour "suivant"','WP-Advanced-Search'); ?></strong></label>
                </p>
            </div>
        </div>
        <p class="clear"><input type="submit" name="wp_advanced_search_action" class="button-primary" value="<?php _e('Enregistrer' , 'WP-Advanced-Search'); ?>" /></p>
        </form>
<?php
	echo '</div>'; // Fin de la page d'admin
} // Fin de la fonction Callback

// Mise à jour des données par défaut
function WP_Advanced_Search_update_pagination() {
	global $wpdb, $table_WP_Advanced_Search; // insérer les variables globales

	// Pagination
	$wp_advanced_search_pagination_active		= $_POST['wp_advanced_search_pagination_active'];
	$wp_advanced_search_pagination_style		= $_POST['wp_advanced_search_pagination_style'];
	$wp_advanced_search_pagination_firstlast	= $_POST['wp_advanced_search_pagination_firstlast'];
	$wp_advanced_search_pagination_prevnext		= $_POST['wp_advanced_search_pagination_prevnext'];
	$wp_advanced_search_pagination_firstpage	= $_POST['wp_advanced_search_pagination_firstpage'];
	$wp_advanced_search_pagination_lastpage		= $_POST['wp_advanced_search_pagination_lastpage'];
	$wp_advanced_search_pagination_prevtext		= $_POST['wp_advanced_search_pagination_prevtext'];
	$wp_advanced_search_pagination_nexttext		= $_POST['wp_advanced_search_pagination_nexttext'];
		
	$wp_advanced_search_update = $wpdb->update(
		$table_WP_Advanced_Search,
		array(
			"paginationActive" => $wp_advanced_search_pagination_active,
			"paginationStyle" => $wp_advanced_search_pagination_style,
			"paginationFirstLast" => $wp_advanced_search_pagination_firstlast,
			"paginationPrevNext" => $wp_advanced_search_pagination_prevnext,
			"paginationFirstPage" => $wp_advanced_search_pagination_firstpage,
			"paginationLastPage" => $wp_advanced_search_pagination_lastpage,
			"paginationPrevText" => $wp_advanced_search_pagination_prevtext,
			"paginationNextText" => $wp_advanced_search_pagination_nexttext,
		), 
		array('id' => 1)
	);
}
?>