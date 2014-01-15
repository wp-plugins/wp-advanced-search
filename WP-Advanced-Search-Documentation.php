<?php
// Fonction d'affichage de la page d'aide et de réglages de l'extension
function WP_Advanced_Search_Callback_Documentation() {

	// Déclencher la fonction de mise à jour des index FULLTEXT (upload)
	if(isset($_POST['wp_advanced_search_fulltext'])) {
		WP_Advanced_Search_FullText_Doc();
	}

	/* --------------------------------------------------------------------- */
	/* ------------------------ Affichage de la page ----------------------- */
	/* --------------------------------------------------------------------- */
	echo '<div class="wrap advanced-search-admin">';
	echo '<div class="icon32 icon"><br /></div>';
	echo '<h2>'; _e('Documentation','WP-Advanced-Search'); echo '</h2><br/>';
	echo '<div class="text">';
	_e('<strong>WP Advanced Search</strong> est un moteur de recherche complet pour WordPress qui corrige de nombreuses failles du moteur initial.', 'WP-Advanced-Search');
	echo '<br/>';
	_e('La documentation ci-dessous explique l\'installation et le fonctionnement global du plugin de recherche avancé.', 'WP-Advanced-Search');
	echo '<br/>';
	_e('<em>N.B. : n\'hésitez pas à contacter <a href="http://blog.internet-formation.fr/2013/10/wp-advanced-search-moteur-de-recherche-avance-pour-wordpress/" target="_blank">Mathieu Chartier</a>, le créateur du plugin, pour de plus amples informations.</em>', 'WP-Advanced-Search'); echo '<br/>';
	echo '</div>';
?>
    <div class="block clear">
        <div class="col">
           	<h4><?php _e('Installation du plugin','WP-Advanced-Search'); ?></h4>
        	<p class="tr"><?php _e('1. Préparez le formulaire de recherche (exemple : search-form.php)','WP-Advanced-Search'); ?></p>
            <div class="tr-info">
            	<p><?php _e('Repérez le formulaire de recherche WordPress','WP-Advanced-Search') ?></p>
                <ol>
                    <li><?php _e('Localisez l\'attribut "name" du champ de recherche ("s" par défaut) et le modifiez si désiré.','WP-Advanced-Search') ?></li>
                    <li><?php _e('S\'assurer que le formulaire pointe vers la page de recherche (search.php par défaut).','WP-Advanced-Search') ?></li>
                </ol>
            </div>
        </div>
        <div class="col">
        	<h4><?php _e('Capture d\'écran','WP-Advanced-Search'); ?></h4>
        	<p class="tr"><img src="<?php echo plugins_url('img/screenshot-1.png',__FILE__); ?>" alt="Capture WP Advanced Search - 1" /></p>
        </div>
    </div>
    <div class="block clear">
        <div class="col">
        	<p class="tr">
            	<?php _e('2. Préparez la page de résultats (exemple : search.php)','WP-Advanced-Search'); ?>
                <br/>
                <?php _e('&nbsp;&nbsp;&nbsp;&nbsp;Ajouter le code <strong>&lt;?php WP_Advanced_Search(); ?&gt;</strong> pour afficher les résultats','WP-Advanced-Search'); ?>
            </p>
            <div class="tr-info">
            	<p><?php _e('Installation simple et rapide !','WP-Advanced-Search') ?></p>
                <ol>
                    <li><?php _e('Supprimez toute la boucle d\'affichage initiale des résultats de recherche.','WP-Advanced-Search') ?></li>
                    <li><?php _e('Placez-vous dans le bloc qui doit recevoir les résultats de recherche.','WP-Advanced-Search') ?></li>
                    <li><?php _e('Remplacez la boucle par le code <strong>&lt;?php WP_Advanced_Search(); ?&gt;</strong>.','WP-Advanced-Search') ?></li>
                </ol>
            </div>
        </div>
        <div class="col">
            <p class="tr"><img src="<?php echo plugins_url('img/screenshot-2.png',__FILE__); ?>" alt="Capture WP Advanced Search - 2" /></p>
        </div>
    </div>
    <div class="block clear">
        <div class="col">
        	<p class="tr"><?php _e('3. Paramétrez le moteur à votre guise','WP-Advanced-Search'); ?></p>
            <div class="tr-info">
            	<p><?php _e('Les réglages par défaut répondent aux fonctionnalités essentielles du moteur de recherche.','WP-Advanced-Search') ?></p>
                <ol>
                    <li><?php _e('Entrez la valeur de l\'attribut "name" du champ de recherche ("s" par défaut) et celui des tables dans lesquelles rechercher (laissez par défaut si vous avez des doutes)','WP-Advanced-Search') ?></li>
                    <li><?php _e('Choisissez le type de recherche : FULLTEXT (texte intégral), REGEXP (relativement précis), LIKE (recherche approchante)','WP-Advanced-Search') ?></li>
                    <li><?php _e('Paramétrez l\'ordre d\'affichage des résultats du moteur en choisissant la colonne de classement (dates des articles et pages par défaut) et/ou en activant l\'algorithme de pertinence (il affiche les résultats qui répondent le plus à la recherche).','WP-Advanced-Search') ?></li>
                    <li><?php _e('Activez ou non la surbrillance des mots-clés recherchés.','WP-Advanced-Search') ?></li>
                    <li><?php _e('Activez ou non les "stop words", c\'est-à-dire l\'exclusion des mots vides lors des recherches. Il est aussi possible d\'exclure les mots qui ne dépassent pas un certain nombre de caractères.','WP-Advanced-Search') ?></li>
                </ol>
            </div>
        </div>
        <div class="col">
			<p class="tr"><img src="<?php echo plugins_url('img/screenshot-3.png',__FILE__); ?>" alt="Capture WP Advanced Search - 3" /></p>
        </div>
    </div>
    <div class="block clear">
        <div class="col">
        	<p class="tr"><?php _e('<strong>N.B. : particularités des index FULLTEXT</strong>'); ?></p>
            <div class="tr-info">
            	<p><?php _e('La recherche en texte intégrale (FULLTEXT) est la plus aboutie mais demande quelques paramétrages.','WP-Advanced-Search') ?></p>
                <ol>
                    <li><?php _e('<strong>Installation d\'index FULLTEXT</strong> sur les tables de recherche. Cliquez sur ce lien pour gagner du temps : <a href="#" onclick="','WP-Advanced-Search'); ?>getElementById('WP-Advanced-Search-Form').submit()<?php _e('">créez les index FULLTEXT</a>.','WP-Advanced-Search'); ?></li>
                    <li><?php _e('La recherche FULLTEXT répond à certains paramètres du fichier my.ini de MySQL, il convient d\'avoir accès à ce fichier pour gérer totalement la recherche en texte intégrale...','WP-Advanced-Search') ?></li>
                    <li><?php _e('Par défaut, la recherche FULLTEXT exclut quelques "stopwords" mais aussi les mots courts (de 1 à 3 caractères), ce qui peut poser problème. Il faut modifier le <strong>paramètre ft_min_word_len</strong> si vous avez un serveur dédié. Si vous détenez un serveur mutualisé, il est conseillé d\'opter pour la recherche REGEXP ou LIKE si vous voulez que tous les mots offrent des résultats...','WP-Advanced-Search') ?></li>
                </ol>
            </div>
        </div>
        <div class="col">
			<p class="tr"><img src="<?php echo plugins_url('img/screenshot-4.png',__FILE__); ?>" alt="Capture WP Advanced Search - 3" /></p>
        </div>
    </div>
    <div class="block clear">
        <div class="col">
        	<p class="tr"><?php _e('4. Stylisez l\'ensemble et les résultats de recherche','WP-Advanced-Search'); ?></p>
            <div class="tr-info">
            	<p><?php _e('Plusieurs options disponibles pour personnaliser l\'affichage des résultats.','WP-Advanced-Search') ?></p>
                <ol>
                    <li><?php _e('Choisissez les blocs à afficher (tout est modulable).','WP-Advanced-Search') ?></li>
                    <li><?php _e('Choisissez un thème dans la liste ou désactivez les thèmes (style personnalisé).','WP-Advanced-Search') ?></li>
                    <li><?php _e('Paramétrez les classes CSS selon vos envies.','WP-Advanced-Search') ?></li>
                    <li><?php _e('Formatez la date comme bon vous semble (si vous souhaitez l\'afficher).','WP-Advanced-Search') ?></li>
                    <li><?php _e('Numérotez ou non les résultats de recherche.','WP-Advanced-Search') ?></li>
                </ol>
            </div>
        </div>
        <div class="col">
			<p class="tr"><img src="<?php echo plugins_url('img/screenshot-5.png',__FILE__); ?>" alt="Capture WP Advanced Search - 4" /></p>
        </div>
    </div>
    <div class="block clear">
        <div class="col">
        	<p class="tr"><?php _e('5. Réglez la pagination (optionnel)','WP-Advanced-Search'); ?></p>
            <div class="tr-info">
            	<p><?php _e('Plusieurs options de personnalisation de la pagination.','WP-Advanced-Search') ?></p>
                <ol>
                    <li><?php _e('Activez ou non la pagination (affiche tous les résultats si désactivé).','WP-Advanced-Search') ?></li>
                    <li><?php _e('Sélectionnez les liens à afficher dans la pagination.','WP-Advanced-Search') ?></li>
                    <li><?php _e('Choisissez un thème ou non pour la pagination (plusieurs couleurs disponibles).','WP-Advanced-Search') ?></li>
                    <li><?php _e('Modifiez les libellés de la pagination si besoin.','WP-Advanced-Search') ?></li>
                </ol>
            </div>
        </div>
        <div class="col">
			<p class="tr"><img src="<?php echo plugins_url('img/screenshot-6.png',__FILE__); ?>" alt="Capture WP Advanced Search - 5" /></p>
        </div>
    </div>
    
    <!-- Formulaire pour installer les index FULLTEXT (si activé en cliquant sur le lien) -->
    <form id="WP-Advanced-Search-Form" method="post">
        <input type="hidden" name="wp_advanced_search_fulltext" value="" />
    </form>
<?php
	echo '</div>';
} // Fin de la fonction Callback

function WP_Advanced_Search_FullText_Doc() {
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
?>