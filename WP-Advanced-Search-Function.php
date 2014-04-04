<?php
/*--------------------------------------------*/
/*--------- Fonction d'autocomplétion --------*/
/*--------------------------------------------*/
// Ajout conditionné du système d'autocomplétion
function addAutoCompletion() {
	global $wpdb, $table_WP_Advanced_Search;
	
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
		include_once('class.inc/moteur-php5.class-inc.php');
		$autocompletion = new autoCompletion(plugins_url("autocompletion/autocompletion.php", __FILE__ ), $selector, $tableName, $tableColumn, $multiple, $limitDisplay, $type, $autoFocus, $create, $encoding);
	}
}
add_action('wp_footer', 'addAutoCompletion');

/*--------------------------------------------*/
/*------------ Fonction du moteur ------------*/
/*--------------------------------------------*/
function WP_Advanced_Search() {
	global $wpdb, $table_WP_Advanced_Search;
	global $moteur;
	global $select;
	global $wp_rewrite;

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
	// Autres variables utiles
	$table = $select->tables;
	$nameSearch = $select->nameField;
	$typeRecherche = $select->typeSearch;
	$encoding = $select->encoding;
	$exclusion = $select->exclusionWords;
	$exact = $select->exactSearch;
	$accent = $select->accents;
	$firstlast = $select->paginationFirstLast;
	$prevnext = $select->paginationPrevNext;
	$firstpage = $select->paginationFirstPage;
	$lastpage = $select->paginationLastPage;
	$prevtext = $select->paginationPrevText;
	$nexttext = $select->paginationNextText;

	// Inclusion des class du moteur de recherche
	include_once('class.inc/moteur-php5.class-inc.php');
	if($select->autoCompleteActive == 1) {
		$autocompletion = new autoCompletion(plugins_url("autocompletion/autocompletion.php", __FILE__ ), $selector, $tableName, $tableColumn, $multiple, $limitDisplay, $type, $autoFocus, $create, $encoding);

		// Lancement de la fonction de remplissage automatique de l'index inversé (si activé)
		if($select->autoCompleteGenerate == true) {
			$autocompletion->autoComplete(stripslashes($_GET[$nameSearch]), $select->autoCompleteSizeMin);
		}
	}

	if(empty($select->colonnesWhere)) {
		$colonnesWhere = array('post_title', 'post_content', 'post_excerpt');
	} else {
		$colonnesWhere = explode(',',$select->colonnesWhere);
	}
	if($select->stopWords == true) {
		// Récupération de la langue par défaut et des stopwords adaptés
		if (WPLANG == '') {
			$lang = "fr_FR";
		} else {
			$lang = WPLANG;
		}
		include('class.inc/stopwords/stopwords-'.$lang.'.php');
	} else {
		$stopwords = '';	
	}

	// Ajout conditionné des feuilles de style pour l'affichage
	WP_Advanced_Search_CSS($select->Style);

	// Ajout conditionné des feuilles de style pour la pagination
	WP_Advanced_Search_Pagination_CSS($select->paginationStyle);

	// Lancement du moteur de recherche
	$moteur = new moteurRecherche(stripslashes($_GET[$nameSearch]), $table, $typeRecherche, $stopwords, $exclusion, $encoding, $exact, $accent);
	$moteur->moteurRequetes($colonnesWhere);
	
	// Affichage des résultats si le moteur est en marche !
	if(isset($moteur)) {
		function affichage($query, $nbResults, $words) {
			global $select, $wpdb, $moteur, $wp_rewrite;

			$output = '<div class="WPAdvancedSearch">'."\n";
			$output .= '<h3>'.__($select->ResultText,'wp-advanced-search').' <em>'.htmlspecialchars($moteur->requete).'</em></h3>'."\n";

			if($nbResults == 0) {
				$output .= "<div class=\"WPBlockSearch\">\n";	
				$output .= '<p class="WPErrorSearch">'.__($select->ErrorText,'wp-advanced-search').'</p>'."\n";
				$output .= "</div>\n";
			} else {
				$nb = 0;
				if(isset($_GET['page'])) {
					$nb = $nb + ($select->NumberPerPage * ($_GET['page']-1));
				}

				// Afficher le nombre de résultats
				if($select->nbResultsOK == true) {
					$affichageResultats = new affichageResultats();
					if($select->NumberPerPage == 0) {
						$output .= $affichageResultats->nbResultats(array(__('résultat','wp-advanced-search'), __('résultats','wp-advanced-search')), __('pour votre recherche','wp-advanced-search'), __(' à ','wp-advanced-search'), true);
					} else {
						$output .= $affichageResultats->nbResultats(array(__('résultat','wp-advanced-search'), __('résultats','wp-advanced-search')), __('pour votre recherche','wp-advanced-search'), __(' à ','wp-advanced-search'));
					}
				}

				while($key = mysql_fetch_assoc($query)) { // On lance la boucle d'affichage des résultats
					// Récupération du numéro du résultat
					$nb++;
					
					// Boucle utile si on doit ajouter (utf8_encode) par exemple (non activée par défaut...)
					foreach($key as $k => $v) {
						$key[$k] = $v;
					}

					// Trouver les images à la Une, les catégories et les auteurs
					$tableCible = $wpdb->posts; // Récupération de la table de base de donnée à parcourir (ici, "posts" pour celles des pages et articles)
					$tableMeta = $wpdb->postmeta; // Récupération des métas pour l'image à la Une
					$tableRelationship = $wpdb->term_relationships; // Récupération des relations de taxonomie
					$tableTaxonomy = $wpdb->term_taxonomy; // Récupération des termes de la taxonomie
					$tableTerms = $wpdb->terms; // Récupération des termes	
					$tableUsers = $wpdb->users; // Récupération des auteurs
					$ImageOK = $wpdb->get_results("SELECT * FROM ".$tableCible." AS p INNER JOIN ".$tableMeta." AS m1 ON (m1.post_id = '".$key['ID']."' AND m1.meta_value = p.ID AND m1.meta_key = '_thumbnail_id' AND p.post_type = 'attachment')");	
					$CategoryOK = $wpdb->get_results("SELECT name FROM ".$tableTerms." AS terms LEFT JOIN ".$tableTaxonomy." AS tax ON (terms.term_id = tax.term_id AND tax.taxonomy = 'category') INNER JOIN ".$tableRelationship." AS rel ON (tax.term_taxonomy_id = rel.term_taxonomy_id) WHERE rel.object_id = '".$key['ID']."'");
					$AuthorOK = $wpdb->get_results("SELECT users.ID, user_nicename, display_name FROM ".$tableUsers." AS users INNER JOIN ".$tableCible." AS p ON users.ID = p.post_author WHERE p.ID = '".$key['ID']."'");
					
					// Nombre de catégorie (si plusieurs, on affichera différement)
					$nbCategory = count($CategoryOK);
					
					// Bloc global
					$output .= "<div class=\"WPBlockSearch\">\n";

					// Affichage conditionné de la date et du titre
					if($select->TitleOK == true && $select->NumberOK == true) {
						$output .= '<p class="WPFirstSearch">'."\n";
						$output .= '<span class="WPnumberSearch">'.$nb.'</span>'."\n";
						$output .= '<span class="WPtitleSearch"><a href="'.$key['guid'].'">'.$key['post_title'].'</a></span>'."\n";				
						$output .= '</p>'."\n";
					} else if($select->TitleOK == true && $select->NumberOK == false) {
						$output .= '<p class="WPFirstSearch">'."\n";
						$output .= '<span class="WPtitleSearch"><a href="'.$key['guid'].'">'.$key['post_title'].'</a></span>'."\n";				
						$output .= '</p>'."\n";
					} else if($select->TitleOK == false && $select->NumberOK == true) {
						$output .= '<p class="WPFirstSearch">'."\n";
						$output .= '<span class="WPnumberSearch">'.$nb.'</span>'."\n";	
						$output .= '</p>'."\n";
					}

					// Affichage d'un bloc pour date + auteur + categorie
					$output .= '<p class="WPSecondSearch">'."\n";
						if($select->DateOK == true || $select->AuthorOK == true || $select->CategoryOK == true) {
							$output .= '<span class="WPdateSearch">'.__('Publié ','wp-advanced-search').'</span>';
						}
						if($select->BlocOrder == "D-A-C") // Ordre : Date - Auteur - Catégorie
						{
							// Affichage conditionné de la date
							if($select->DateOK == true) {
								$dateInfo = mysql2date($select->formatageDate, $key['post_date']);
								$output .= '<span class="WPdateSearch">'.__('le ','wp-advanced-search').$dateInfo.'</span>'."\n";
							}
							// Affichage conditionné de l'auteur
							if($select->AuthorOK == true) {
								foreach($AuthorOK as $author) {
									$authorURL = get_author_posts_url($author->ID, $author->user_nicename);
									$output .= '<span class="WPauthorSearch">'.__('par ','wp-advanced-search').'<a href="'.esc_url($authorURL).'">'.$author->display_name.'</a></span>'."\n";
								}
							}
							// Affichage conditionné de la catégorie
							if($select->CategoryOK == true) {
								if($nbCategory > 0) {
									$output .= '<span class="WPcategorySearch">'.__('dans ','wp-advanced-search')."\n";
								}
								$counter = 0;
								foreach($CategoryOK as $ctg) {
									$categoryID = get_cat_ID($ctg->name);
									$categoryURL = get_category_link($categoryID);
									$output .= '<a href="'.esc_url($categoryURL).'">'.$ctg->name.'</a>';
									if($nbCategory > 1 && $counter < ($nbCategory-1)) {
										$output .= ", \n";
									}
									$counter++;
								}
								$output .= '</span>'."\n";
							}
						} else // Ordre : Date - Catégorie - Auteur
						if($select->BlocOrder == "D-C-A") {
							// Affichage conditionné de la date
							if($select->DateOK == true) {
								$dateInfo = mysql2date($select->formatageDate, $key['post_date']);
								$output .= '<span class="WPdateSearch">'.__('le ','wp-advanced-search').$dateInfo.'</span>'."\n";
							}
							// Affichage conditionné de la catégorie
							if($select->CategoryOK == true) {
								if($nbCategory > 0) {
									$output .= '<span class="WPcategorySearch">'.__('dans ','wp-advanced-search')."\n";
								}
								$counter = 0;
								foreach($CategoryOK as $ctg) {
									$categoryID = get_cat_ID($ctg->name);
									$categoryURL = get_category_link($categoryID);
									$output .= '<a href="'.esc_url($categoryURL).'">'.$ctg->name.'</a>';
									if($nbCategory > 1 && $counter < ($nbCategory-1)) {
										$output .= ", \n";
									}
									$counter++;
								}
								$output .= '</span>'."\n";
							}
							// Affichage conditionné de l'auteur
							if($select->AuthorOK == true) {
								foreach($AuthorOK as $author) {
									$authorURL = get_author_posts_url($author->ID, $author->user_nicename);
									$output .= '<span class="WPauthorSearch">'.__('par ','wp-advanced-search').'<a href="'.esc_url($authorURL).'">'.$author->display_name.'</a></span>'."\n";
								}
							}
						} else // Ordre : Auteur - Catégorie - Date
						if($select->BlocOrder == "A-C-D") {
							// Affichage conditionné de l'auteur
							if($select->AuthorOK == true) {
								foreach($AuthorOK as $author) {
									$authorURL = get_author_posts_url($author->ID, $author->user_nicename);
									$output .= '<span class="WPauthorSearch">'.__('par ','wp-advanced-search').'<a href="'.esc_url($authorURL).'">'.$author->display_name.'</a></span>'."\n";
								}
							}
							// Affichage conditionné de la catégorie
							if($select->CategoryOK == true) {
								if($nbCategory > 0) {
									$output .= '<span class="WPcategorySearch">'.__('dans ','wp-advanced-search')."\n";
								}
								$counter = 0;
								foreach($CategoryOK as $ctg) {
									$categoryID = get_cat_ID($ctg->name);
									$categoryURL = get_category_link($categoryID);
									$output .= '<a href="'.esc_url($categoryURL).'">'.$ctg->name.'</a>';
									if($nbCategory > 1 && $counter < ($nbCategory-1)) {
										$output .= ", \n";
									}
									$counter++;
								}
								$output .= '</span>'."\n";
							}
							// Affichage conditionné de la date
							if($select->DateOK == true) {
								$dateInfo = mysql2date($select->formatageDate, $key['post_date']);
								$output .= '<span class="WPdateSearch">'.__('le ','wp-advanced-search').$dateInfo.'</span>'."\n";
							}
						} else // Ordre : Auteur - Date - Catégorie
						if($select->BlocOrder == "A-D-C") {
							// Affichage conditionné de l'auteur
							if($select->AuthorOK == true) {
								foreach($AuthorOK as $author) {
									$authorURL = get_author_posts_url($author->ID, $author->user_nicename);
									$output .= '<span class="WPauthorSearch">'.__('par ','wp-advanced-search').'<a href="'.esc_url($authorURL).'">'.$author->display_name.'</a></span>'."\n";
								}
							}
							// Affichage conditionné de la date
							if($select->DateOK == true) {
								$dateInfo = mysql2date($select->formatageDate, $key['post_date']);
								$output .= '<span class="WPdateSearch">'.__('le ','wp-advanced-search').$dateInfo.'</span>'."\n";
							}
							// Affichage conditionné de la catégorie
							if($select->CategoryOK == true) {
								if($nbCategory > 0) {
									$output .= '<span class="WPcategorySearch">'.__('dans ','wp-advanced-search')."\n";
								}
								$counter = 0;
								foreach($CategoryOK as $ctg) {
									$categoryID = get_cat_ID($ctg->name);
									$categoryURL = get_category_link($categoryID);
									$output .= '<a href="'.esc_url($categoryURL).'">'.$ctg->name.'</a>';
									if($nbCategory > 1 && $counter < ($nbCategory-1)) {
										$output .= ", \n";
									}
									$counter++;
								}
								$output .= '</span>'."\n";
							}
						} else // Ordre : Catégorie - Date - Auteur
						if($select->BlocOrder == "C-D-A") {
							// Affichage conditionné de la catégorie
							if($select->CategoryOK == true) {
								if($nbCategory > 0) {
									$output .= '<span class="WPcategorySearch">'.__('dans ','wp-advanced-search')."\n";
								}
								$counter = 0;
								foreach($CategoryOK as $ctg) {
									$categoryID = get_cat_ID($ctg->name);
									$categoryURL = get_category_link($categoryID);
									$output .= '<a href="'.esc_url($categoryURL).'">'.$ctg->name.'</a>';
									if($nbCategory > 1 && $counter < ($nbCategory-1)) {
										$output .= ", \n";
									}
									$counter++;
								}
								$output .= '</span>'."\n";
							}
							// Affichage conditionné de la date
							if($select->DateOK == true) {
								$dateInfo = mysql2date($select->formatageDate, $key['post_date']);
								$output .= '<span class="WPdateSearch">'.__('le ','wp-advanced-search').$dateInfo.'</span>'."\n";
							}
							// Affichage conditionné de l'auteur
							if($select->AuthorOK == true) {
								foreach($AuthorOK as $author) {
									$authorURL = get_author_posts_url($author->ID, $author->user_nicename);
									$output .= '<span class="WPauthorSearch">'.__('par ','wp-advanced-search').'<a href="'.esc_url($authorURL).'">'.$author->display_name.'</a></span>'."\n";
								}
							}
						} else // Ordre : Catégorie - Auteur - Date
						if($select->BlocOrder == "C-A-D") {
							// Affichage conditionné de la catégorie
							if($select->CategoryOK == true) {
								if($nbCategory > 0) {
									$output .= '<span class="WPcategorySearch">'.__('dans ','wp-advanced-search')."\n";
								}
								$counter = 0;
								foreach($CategoryOK as $ctg) {
									$categoryID = get_cat_ID($ctg->name);
									$categoryURL = get_category_link($categoryID);
									$output .= '<a href="'.esc_url($categoryURL).'">'.$ctg->name.'</a>';
									if($nbCategory > 1 && $counter < ($nbCategory-1)) {
										$output .= ", \n";
									}
									$counter++;
								}
								$output .= '</span>'."\n";
							}
							// Affichage conditionné de l'auteur
							if($select->AuthorOK == true) {
								foreach($AuthorOK as $author) {
									$authorURL = get_author_posts_url($author->ID, $author->user_nicename);
									$output .= '<span class="WPauthorSearch">'.__('par ','wp-advanced-search').'<a href="'.esc_url($authorURL).'">'.$author->display_name.'</a></span>'."\n";
								}
							}
							// Affichage conditionné de la date
							if($select->DateOK == true) {
								$dateInfo = mysql2date($select->formatageDate, $key['post_date']);
								$output .= '<span class="WPdateSearch">'.__('le ','wp-advanced-search').$dateInfo.'</span>'."\n";
							}
						}
							
							// Affichage conditionné des commentaires
							if($select->CommentOK == true) {
								if($key['comment_count'] == 0) {
									$output .= '<span class="WPcommentSearch"><a href="'.$key['guid'].'#comments">'.__('Aucun commentaire','wp-advanced-search').'</a></span>'."\n";
								} else if($key['comment_count'] == 1) {
									$output .= '<span class="WPcommentSearch"><a href="'.$key['guid'].'#comments">'.$key['comment_count'].' '.__('commentaire','wp-advanced-search').'</a></span>'."\n";
								} else {
									$output .= '<span class="WPcommentSearch"><a href="'.$key['guid'].'#comments">'.$key['comment_count'].' '.__('commentaires','wp-advanced-search').'</a></span>'."\n";
								}
							}

					$output .= '</p>'."\n";
					
					// Affichage conditionné de l'article, de l'extrait et de l'image à la Une
					if(($select->ArticleOK == "excerpt" || $select->ArticleOK == "excerptmore" || $select->ArticleOK == "article") && $select->ImageOK == true) {
						$output .= '<div class="WPBlockContent">'."\n";
						
						$output .= get_the_post_thumbnail($key['ID'],'thumbnail');
						/*
						$ImageOK = $wpdb->get_results("SELECT * FROM ".$tableCible." AS p INNER JOIN ".$tableMeta." AS m1 ON (m1.post_id = '".$key['ID']."' AND m1.meta_value = p.ID AND m1.meta_key = '_thumbnail_id' AND p.post_type = 'attachment')");
						foreach($ImageOK as $img) {
							$imageThumb = '<img src="'.$img->guid.'" alt="'.$img->post_title.'" />'."\n"; // Image à la Une
							$output .= $imageThumb;
						}
						*/
						
						if($select->ArticleOK == "excerpt") {
							$output .= '<div class="WPtextSearch">'."\n";
							$output .= $key['post_excerpt'];
							$output .= '</div>'."\n";
						} else if($select->ArticleOK == "excerptmore") {
							$output .= '<div class="WPtextSearch">'."\n";
							$output .= $key['post_excerpt'];
							$output .= '<p class="WPReadMoreSearch"><a href="'.$key['guid'].'">'.__('Lire la suite...','wp-advanced-search').'</a></p>'."\n";
							$output .= '</div>'."\n";							
						} else {
							$output .= '<div class="WPtextSearch">'.$key['post_content'].'</div>'."\n";
						}
						$output .= '<p class="clearBlock"></p>'."\n";
						$output .= '</div>'."\n";
					
					// Affichage conditionné de l'image à la Une sans titre ou extrait (déconseillé)
					} else if(($select->ArticleOK == "excerpt" || $select->ArticleOK == "excerptmore" || $select->ArticleOK == "article") && $select->ImageOK == false) {
						$output .= '<div class="WPBlockContent">'."\n";
						
						if($select->ArticleOK == "excerpt") {
							$output .= '<div class="WPtextSearch">'."\n";
							$output .= $key['post_excerpt'];
							$output .= '</div>'."\n";
						} else if($select->ArticleOK == "excerptmore") {
							$output .= '<div class="WPtextSearch">'."\n";
							$output .= $key['post_excerpt'];
							$output .= '<p class="WPReadMoreSearch"><a href="'.$key['guid'].'">'.__('Lire la suite...','wp-advanced-search').'</a></p>'."\n";
							$output .= '</div>'."\n";							
						} else {
							$output .= '<div class="WPtextSearch">'.$key['post_content'].'</div>'."\n";
						}
						$output .= '<p class="clearBlock"></p>'."\n";
						$output .= '</div>';					
					
					// Affichage conditionné de l'image à la Une sans titre ou extrait (déconseillé)
					} else if($select->ArticleOK == "aucun" && $select->ImageOK == true) {
						$output .= '<div class="WPBlockContent">'."\n";
						
						$output .= get_the_post_thumbnail($key['ID'],'thumbnail');
						$output .= '<p class="clearBlock"></p>'."\n";
						$output .= '</div>'."\n";
					}
					$output .= "</div>\n";
				}
				// Utilisation ou non du surlignage
				if($select->strongWords != 'aucun') {
					$strong = new surlignageMot($words, $output, $select->strongWords, $select->exactSearch, $select->typeSearch);
					$output = $strong->contenu;
				}
			}
			$output .= "</div>\n";
			echo $output;
		} // Fin de la fonction callback d'affichage

		// Affichage des résultats en fonction d'une ou plusieurs catégories sélectionnés (pour les articles uniquement !)
		if(!in_array('toutes', unserialize($select->categories)) && $select->categories != 'a:0:{}' && $select->postType == "post") {
			$conditions = "as WPP INNER JOIN $wpdb->term_relationships as TR INNER JOIN $wpdb->terms as TT WHERE WPP.ID = TR.object_id AND TT.term_id = TR.term_taxonomy_id AND (";
			$nbCat = 0;
			foreach(unserialize($select->categories) as $cate) {
				 $conditions .= "TT.slug = '".$cate."'";
				 if($nbCat < (count(unserialize($select->categories)) -1)) {
				 	$conditions .= " OR ";
				 }
				 $nbCat++;
			}
			$conditions .= ") AND";
		} else {
			$conditions = '';	
		}
		
		// Récupération du type de contenu à afficher
		if($select->postType == "post") {
			$wpAdaptation = "AND post_type = 'post' AND post_status = 'publish'";
		} else if($select->postType == "page") {
			$wpAdaptation = "AND post_type = 'page' AND post_status = 'publish'";
		} else if($select->postType == "pagepost") {
			$wpAdaptation = "AND (post_type = 'page' OR post_type = 'post') AND post_status = 'publish'";
		} else {
			$wpAdaptation = "AND post_status = 'publish'";
		}
		
		// Lancement de la fonction d'affichage	
		if($select->NumberPerPage == 0) {
			$moteur->moteurAffichage('affichage', '', array(false, $_GET['page'], $select->NumberPerPage), array($select->OrderOK, $select->OrderColumn, $select->AscDesc), $algo = array($select->AlgoOK,'algo','DESC','ID'), $wpAdaptation, $conditions);
		} else {
			$moteur->moteurAffichage('affichage', '', array(true, $_GET['page'], $select->NumberPerPage), array($select->OrderOK, $select->OrderColumn, $select->AscDesc), $algo = array($select->AlgoOK,'algo','DESC','ID'), $wpAdaptation, $conditions);
			$paginationValide = true;
		}
		
		// Lancement de la fonction de pagination si elle est activée...
		if($select->paginationActive == true && $paginationValide == true) {
			$moteur->moteurPagination('page', 2, 0, $prevnext, $firstlast, $arrayAff = array($prevtext, $nexttext, $firstpage, $lastpage, 'precsuiv', 'pagination-current', 'pagination-block', 'pagination-disabled'), $arraySeparateur = array('&hellip;', ' ', ' ', ' ', ' '));
		}
	}
}
?>