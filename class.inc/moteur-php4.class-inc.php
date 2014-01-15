<?php
/*------------------------------------------------------------------------*/
/*----------------- Class pour créer les index FullText ------------------*/
/*-- 1. Lancer $alterTable = new alterTableFullText(); -------------------*/
/*-- 2. Trois paramères : nom de la base, de la table puis des colonnes --*/
/*-- N.B. : la fonction modifie la table en MyISAM (pour les FullText) ---*/
/*------------------------------------------------------------------------*/
class alterTableFullText {
	function alterTableFullText($nomBDD, $table, $colonnes) {
		// Vérification du type de table SQL pour savoir si c'est en MyISAM
		$engineSQL = mysql_query("SHOW TABLE STATUS FROM $nomBDD LIKE '".$table."'");
		$engine = mysql_fetch_assoc($engineSQL);
		
		// Modification de la table en MyISAM si nécessaire (compatibilité FULLTEXT)
		if($engine["Engine"] != "MyISAM") {
			$MyISAMConverter = mysql_query("ALTER TABLE $table ENGINE=MYISAM") or die();
		}
		
		// Création des index FULLTEXT dans les colonnes s'ils n'existent pas déjà...
		if (is_array($colonnes)) {
			foreach($colonnes as $colonne) {
				$ifFullTextExists = mysql_query("SHOW INDEX FROM $table WHERE column_name = '$colonne' AND Index_type = 'FULLTEXT'");
				$fullTextExists = mysql_fetch_assoc($ifFullTextExists);
				if($fullTextExists['Index_type'] != 'FULLTEXT') {
					$alterTableFullText = mysql_query("ALTER TABLE $table ADD FULLTEXT($colonne)") or die();
				}
			}
		} else {
			$colonnes = str_ireplace(' ', '', $colonnes);
			$SQLFields = explode(',',$colonnes);
			foreach($SQLFields as $colonne) {
				$ifFullTextExists = mysql_query("SHOW INDEX FROM $table WHERE column_name = '$colonne' AND Index_type = 'FULLTEXT'");
				$fullTextExists = mysql_fetch_assoc($ifFullTextExists);
				if($fullTextExists['Index_type'] != 'FULLTEXT') {
					$alterTableFullText = mysql_query("ALTER TABLE $table ADD FULLTEXT($colonne)") or die();
				}
			}
		}
	}
}

/*--------------------------------------------------------------------------*/
/*--------------------- Class du moteur de recherche -----------------------*/
/*-- 1. Lancer $moteur = new moteurRecherche(args); ------------------------*/
//-- 2. Exécuter $moteur->moteurRequetes(tableauColonnesWhere); ------------*/
//-- 3. Créer une fonction d'affichage (ex : "affichage()") ----------------*/
//-- 4. Lancer $moteur->moteurAffichage('affichage', $colonnesSelect); -----*/
//-- N.B. : ajouter si besoin $moteur->moteurPagination(args) -> si $_GET --*/
/*--------------------------------------------------------------------------*/
class moteurRecherche {
	var $tableBDD;			// Nom de la table de la base de données
	var $encode;			// Type d'encodage ("utf-8" ou "iso-8859-1" notamment)
	var $searchType;		// Type de recherche ("like", "regexp" ou "fulltext")
	var $exactmatch;		// Méthode de recherche (précise ou approchante --> true ou false)
	var $colonnesWhere;		// Tableau contenant les colonnes dans lesquelles la recherche est effectuée
	var $algoRequest;		// Tableau contenant chaque mot ou expression clé (après découpage)
	var $request;			// Tableau contenant chaque mot ou expression clé (après découpage)
	var $motsExpressions;	// Tableau contenant chaque mot ou expression clé (après nettoyage)
	var $condition;			// Ensemble du WHERE de la requête finale
	
	var $orderBy;			// Tableau des composantes du ORDER BY pesonnalisé (si fonction Callback)
	var $limitMinMax;		// Tableau des composantes du LIMIT pesonnalisé (si fonction Callback)
	var $limitArg;			// Numéro de départ de la clause LIMIT (0 par défaut)
	var $limit;				// Nombre de résultats par page (si pagination sur "true")
	
	var $requete;			// Requete de recherche de l'utilisateur
	var $countWords;		// Nombre de mots et expressions qui composent la requête
	var $nbResults;			// Nombre de résultats retournés via la BDD (LIMIT 0, $moteur->nbResults pour tout afficher)
	var $nbResultsChiffre;	// Nombre de résultats retournés mais en chiffre cette fois (pour la pagination)
	var $requeteTotale;		// Requete SQL finale (après algorithme, etc.)

	/*------------------------------------------------------------------------------------*/
	/*------------------------ Constructeur de la class (8 paramètres)- ------------------*/
	/*-- 1. $champ est la requête de recherche -------------------------------------------*/
	/*-- 2. $table est la table de la base de données dans laquelle chercher -------------*/
	/*-- 3. $typeRecherche pour choisir son mode de recherche (like, regexp ou fulltext) -*/
	/*-- 4. $stopWords permet d'exclure les mots "vides" provenant d'un tableau ----------*/
	/*-- => Inclure le fichier stopwords.php (variable $stowords) pour gagner du temps ---*/
	/*-- 5. $exclusion permet d'exclure les mots plus courts que la taille donnée --------*/
	/*-- => Si vide, aucune exclusion ne sera faite (mais les résultats moins précis) ----*/
	/*-- 6. $encoding est l'encodage souhaité (utf8, utf-8, iso-8859-1, latin1...) -------*/
	/*-- 7. $exact (true/false) pour une recherche exacte ou d'un ou plusieurs des mots --*/
	/*-- 8. $accent (true/false) faire des recherches sans accent si la BDD le permet ----*/
	/*------------------------------------------------------------------------------------*/
	function moteurRecherche($champ = '', $table = '', $typeRecherche = 'regexp', $stopWords = array(), $exclusion = '', $encoding = 'utf-8', $exact = true, $accent = false) {
		$this->requete		= $champ;
		$this->tableBDD		= $table;
		$this->encode		= strtolower($encoding);
		$this->searchType	= $typeRecherche;
		$this->exactmatch	= $exact;

		// Suppression des balises HTML (sécurité)
		if($this->encode == 'latin1' || $this->encode == 'Latin1' || $this->encode == 'latin-1' || $this->encode == 'Latin-1') {
			$mb_encode = "ISO-8859-1";
		} elseif($this->encode == 'utf8' || $this->encode == 'UTF8' || $this->encode == 'utf-8' || $this->encode == 'UTF-8') {
			$mb_encode = "UTF-8";
		} else {
			$mb_encode = $encoding;	
		}
		$champ = mb_strtolower(strip_tags($champ), $mb_encode);
//		$champ = mb_convert_case(strip_tags($champ), MB_CASE_LOWER, $mb_encode);

		// 1. si une expression est entre guillemets, on cherche l'expression complète (suite de mots)
		// 2. si les mots clés sont hors des guillemets, la recherche mot par mot est activée
		if(preg_match_all('/["]{1}([^"]+[^"]+)+["]{1}/i', $champ, $entreGuillemets)) {
			// Ajoute toutes les expressions entre guillemets dans un tableau
			foreach($entreGuillemets[1] as $expression) {
				$results[] = $expression;
			}
			// Récupère les mots qui ne sont pas entre guillemets dans un tableau
			$sansExpressions = str_ireplace($entreGuillemets[0],"",$champ);
			$motsSepares = explode(" ",$sansExpressions);		
		} else {
			$motsSepares = explode(" ",$champ);
		}
		// Supprimer les clés vides du tableau (à cause des espaces de trop et strip_tags)
		foreach($motsSepares as $key => $value) {
			// Remplace les mots exclus (trop courts) par des chaines vides
			if(!empty($exclusion)) {
				if(strlen($value) <= $exclusion) {
					$value = '';
				}
			}
			// Supprime les stops words s'ils existent
			if(!empty($stopWords)) {
				if(in_array($value, $stopWords)) {
					$value = '';
				}
			}
			// Supprime les chaines vides du tableau (et donc les mots exclus)
			if(empty($value)) {
				unset($motsSepares[$key]);
			}
		}
		// Ajoute chaque mot unique dans la liste des mots à chercher
		foreach($motsSepares as $motseul) {
			$results[] = $motseul;
		}
		
		// Si le tableau des mots et expressions n'est pas vide, alors on cherche... (sinon pas de résultats !)
		if(!empty($results)) {
			// Nettoie chaque champ pour éviter les risques de piratage...
			for($y=0; $y < count($results); $y++) {
				$expression = $results[$y];
				
				// Recherche les mots-clés originaux ou sans accent si l'option est activée
				if($accent == false) {
					$recherche[] = htmlspecialchars(trim(strip_tags($expression)));
				} else {
					$withaccent = array('à','á','â','ã','ä','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ','À','Á','Â','Ã','Ä','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ñ','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý');
					$withnoaccent = array('a','a','a','a','a','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','u','u','u','u','y','y','A','A','A','A','A','C','E','E','E','E','I','I','I','I','N','O','O','O','O','O','U','U','U','U','Y');
					$recherche[] = str_ireplace($withaccent, $withnoaccent, htmlspecialchars(trim(strip_tags($expression))));
				}
			}
			$this->algoRequest = $recherche; // Tableau contenant les mots et expression de la requête (doublon utile !)
			$this->request = $recherche; // Tableau contenant les mots et expression de la requête de l'utilisateur
			$this->countWords = count($recherche,1); // nombre de mots contenus dans la requête
		} else {
			$recherche = array('');
			$this->algoRequest = $recherche; // Tableau contenant les mots et expression de la requête (doublon utile !)
			$this->request = $recherche; // Tableau contenant les mots et expression de la requête de l'utilisateur
			$this->countWords = count($recherche,1); // nombre de mots contenus dans la requête	
		}
	}

	/*--------------------------------------------------------------------------------*/
	/*-- Objet (privé) d'échappement des caractères spéciaux (dans les regex) --------*/
	/*--------------------------------------------------------------------------------*/
	function regexEchap($regex = '([\+\*\?])') {
		if(preg_match($regex, $mot)) {
			$mot = str_ireplace(array('+','*','?'),array('\+','\*','\?'),$mot);
		}
	}

	/*--------------------------------------------------------------------------------*/
	/*-- Méthode (privée) pour traiter la requête de recherche -----------------------*/
	/*--------------------------------------------------------------------------------*/
	function requestKey($val) {
		/*---------- Adaptation du charset (UTF-8 de préférence) -------------*/
		if($this->encode == 'utf8' || $this->encode == "utf-8") {
			$encode = "utf8";			
		} else if($this->encode == 'iso-8859-1' || $this->encode == "iso-latin-1" || $this->encode == "latin1") {
			$encode = "latin1";
		} else {
			$encode = "utf8";
		}

		/*------------------------- Options de recherche -------------------------*/
		switch($this->searchType) {
			/*------------------------ Recherche FULLTEXT ------------------------*/
			/*-- Recherche la plus performante mais à paramétrer... --------------*/
			/*-- Modifier (ou ajouter) ft_min_word_len=1 pour les mots courts ----*/
			/*-- (situé dans la section [mysqld] du fichier my.ini de MySQL ------*/
			/*--------------------------------------------------------------------*/
			case "FULLTEXT":
			case "fulltext":
				foreach($this->request as $this->request[$val]) {
					if(preg_match('/(^[+-?!:;$^])|([+-?!:;^]$)/i',$this->request[$val])) {
						$this->request[$val] = str_ireplace(array("+", "-", "?", "!", ";", ":", "^"),"",$this->request[$val]);
					}
					
					// Si un signe + est compris dans la chaîne de caractère, comprendre le mot comme exact
					if(preg_match("/([+])+/i",$this->request[$val])) {
						$this->request[$val] = str_ireplace(array("+"),array(" "),$this->request[$val]);
						$this->request[$val] = preg_replace('/('.$this->request[$val].')/', '"$1"', $this->request[$val]);
						$this->request[$val] = str_ireplace(array(" "),array("+"),$this->request[$val]);
					}
					
					// Si la chaîne contient un espace (donc entre guillemets) ou des caractères de liaison (' ou -)
					if(preg_match("/([[:blank:]-'])+/i",$this->request[$val])) {
						$this->request[$val] = preg_replace('/('.$this->request[$val].')/i', '"$1"', $this->request[$val]);
					}
					
					// Ajoute un échappement devant les apostrophes qui traînent...
					$this->request[$val] = str_ireplace(array("'"),array("\'"),$this->request[$val]);
					
					// Ajout chaque mot ou expression dans un tableau
					$valueModif[] = $this->request[$val];
					
					// Variable utilisée en cas de surlignage des mots...
					$this->motsExpressions = $valueModif;
				}
				
				if($this->exactmatch == true) {
					$this->request[$val] = implode(' +', $valueModif);
					return " AGAINST(CONVERT(_".$encode." '+".$this->request[$val]."' USING ".$encode.") IN BOOLEAN MODE) ";
				} else {
					$this->request[$val] = implode(' ', $valueModif);
					return " AGAINST(CONVERT(_".$encode." '".$this->request[$val]."' USING ".$encode.") IN BOOLEAN MODE) ";
				}				
				break;

				
			/*------------------------ Recherche REGEXP --------------------------*/
			/*-- Recherche avec un regex (seuls les mots complets fonctionnent) --*/
			/*--------------------------------------------------------------------*/
			case "REGEXP":
			case "regexp":		
				// Variable utilisée en cas de surlignage des mots...
				$this->motsExpressions = $this->request;				
				if(preg_match("/^[+\?$\*§\|\[\]\(\)]/i",$this->request[$val])) {
					$this->request[$val] = substr($this->request[$val],1,strlen($this->request[$val]));
				}
				if(preg_match("/[+\?$\*§\|\[\]\(\)]$/i",$this->request[$val])) {
					$this->request[$val] = substr($this->request[$val],0,-1);
				}
				if(preg_match("/^[²°]/i",$this->request[$val])) {
					$this->request[$val] = substr($this->request[$val],1,strlen($this->request[$val]));
				}
				
				if($this->exactmatch == true) {
					return " REGEXP CONVERT(_".$encode." '[[:<:]]".addslashes($this->request[$val])."[[:>:]]' USING ".$encode.") ";
				} else {
					return " REGEXP CONVERT(_".$encode." '".addslashes($this->request[$val])."' USING ".$encode.") ";
				}
				break;
			
			/*------------------------ Recherche LIKE ----------------------------*/
			/*-- Recherche la plus imprécise mais fonctionnelle ------------------*/
			/*--------------------------------------------------------------------*/
			case "LIKE":
			case "like":
				// Variable utilisée en cas de surlignage des mots...
				$this->motsExpressions = $this->request;
				if(preg_match("/^[\(]/i",$this->request[$val])) {
					$this->request[$val] = substr($this->request[$val],1,strlen($this->request[$val]));
				}
				if(preg_match("/[\)]$/i",$this->request[$val])) {
					$this->request[$val] = substr($this->request[$val],0,-1);
				}

				return " LIKE CONVERT(_".$encode." '%".addslashes($this->request[$val])."%' USING ".$encode.") ";
				break;

			default:
				// Variable utilisée en cas de surlignage des mots...
				$this->motsExpressions = $this->request;
				if(preg_match("/^[\(]/i",$this->request[$val])) {
					$this->request[$val] = substr($this->request[$val],1,strlen($this->request[$val]));
				}
				if(preg_match("/[\)]$/i",$this->request[$val])) {
					$this->request[$val] = substr($this->request[$val],0,-1);
				}
				
				return " LIKE CONVERT(_".$encode." '%".addslashes($this->request[$val])."%' USING ".$encode.") ";
				break;
		}
	}

	/*---------------------------------------------------------------------------*/
	/*-- Méthode de conception de la requête de recherche avec 1 paramètre ------*/
	/*-- 1. Tableau des colonnes dans lesquelles chercher (condition WHERE...) --*/
	/*---------------------------------------------------------------------------*/
	function moteurRequetes($colonnesWhere = array()) {

		$this->colonnesWhere = $colonnesWhere;
		// Opérateur entre les champs de requête (OR si vous voulez beaucoup de laxisme)
		$operateur = "AND";
		// Opérateur au sein d'une requête (AND si vous voulez absolument que le mot soit dans plusieurs colonnes SQL)
		$operateurGroupe = "OR";
		// Nombre total de colonnes SQL dans lequel rechercher
		$nbColumn= count($colonnesWhere,1);
		
		/*--------------------------------------------------------------------------------*/
		/*-- Adapte la requête SQL de recherche en fonction du type de recherche choisi --*/
		/*--------------------------------------------------------------------------------*/
		if($this->searchType == "LIKE" || $this->searchType == "REGEXP" || $this->searchType == "like" || $this->searchType == "regexp") { // Si recherche "like" ou "regexp"
			$query = " (";
			$query .= $colonnesWhere[0].$this->requestKey(0);
			if($nbColumn > 1) {
				for($nb=1; $nb < $nbColumn; $nb++) {
					$query .= $operateurGroupe." ".$colonnesWhere[$nb].$this->requestKey(0);
				}
			}
			$query .= ") ";
			
			if($this->countWords > 1) {
				for($i=1; $i < $this->countWords; $i++) {
					$query .= $operateur." (".$colonnesWhere[0].$this->requestKey($i);
					if($nbColumn > 1) {
						for($nb=1; $nb < $nbColumn; $nb++) {
							$query .= $operateurGroupe." ".$colonnesWhere[$nb].$this->requestKey($i);
						}
					}
					$query .= ") ";
				}
			}
		} else { // si recherche en "fulltext"
			$colonnesStrSQL = implode(', ',$colonnesWhere);
			$query = " MATCH (".$colonnesStrSQL.")".$this->requestKey(0);
		}

		// récupération de la requête de recherche du moteur
		$this->condition = $query;
	}

	/*---------------------------------------------------------------------------------*/
	/*-------- Fonction d'affichage des résultats (avec Callback) ---------------------*/
	/*-------- 6 arguments possibles... -----------------------------------------------*/
	/*-- 1. appel à la fonction callback d'affichage (obligatoire) --------------------*/
	/*-- 2. colonnes à sélectionner dans la base (toutes s'il est laissé "vide") ------*/
	/*-- 3. LIMIT en SQL : tableau avec 3 valeurs : true/false, numDépart, intervale --*/
	/*-- 4. ORDER BY : tableau avec 3 valeurs : true/false, colonne d'ordre, ASC/DESC -*/
	/*-- 5. ORDER BY avec algorithme de pertinence : tableau avec 4 valeurs : ---------*/
	/*-- => true/false, colonne de classement (inédite !), ASC/DESC, colonne de l'ID --*/
	/*-- N.B. : la fonction ajoute la colonne de classement si elle n'existe pas ! ----*/
	/*-- 6. Fin de requête perso : écriture de son propre ORDER BY et/ou LIMIT --------*/
	/*---------------------------------------------------------------------------------*/
	function moteurAffichage($callback = '', $colonnesSelect = '', $limit = array(false, 0, 10), $ordre = array(true, "post_date", "DESC"), $algo = array(false,'algo','DESC','id'), $orderLimitPerso = '') {

		// Récupération des colonnes de sélections
		if(empty($colonnesSelect)) {
			$selectColumn = "*";
		} else if (is_array($colonnesSelect)) {
			$selectColumn = implode(", ",$colonnesSelect);
		} else {
			$selectColumn = $colonnesSelect;
		}
		
		// Limite le nombre d'affichage par page
		if($limit[0] == true) {
			self::$limitArg = $limit[1];
			self::$limit	= $limit[2];
			
			if(!isset($_GET['page'])) {
				$limitDeb = 0;
			} else
			if($limit[1] == 0) {
				$limitDeb = $limit[1] * $limit[2];
			} else {
				$limitDeb = ($limit[1] - 1) * $limit[2];
			}
			$this->limitMinMax = " LIMIT $limitDeb, $limit[2]";
		} else {
			$this->limitMinMax = "";
		}
		
		// Algorithme de pertinence (plus il y a de mots dans le résultat, plus c'est haut)
		if($algo[0] == true) {
			// Ajout une colonne dans la base de données pour recueillir les valeurs de l'algorithme
			$ifColumnExist = mysql_query("SHOW COLUMNS FROM $this->tableBDD LIKE '".$algo[1]."'");
			$columnExist = mysql_fetch_row($ifColumnExist);
			if($columnExist[0] != $algo[1]) {
				$addColumn = mysql_query("ALTER TABLE $this->tableBDD ADD ".$algo[1]." DECIMAL(10,3)");
			}
			
			$colonnesStrSQL = implode(', ',$this->colonnesWhere);
			$requeteType = mysql_query("SELECT $algo[3], $colonnesStrSQL FROM $this->tableBDD WHERE $this->condition") or die("Erreur : ".mysql_error());
			while($ligne =  mysql_fetch_row($requeteType)) {
				$count = 0;
				for($p=1; $p < count($this->colonnesWhere)+1; $p++) {
					foreach($this->algoRequest as $mots) {
						$count += substr_count(utf8_encode(strtolower($ligne[$p])), strtolower($mots));
					}
				}
				// Met à jour la colonne de l'algorithme avec les nouvelles valeurs
				$requeteAdd = mysql_query("UPDATE $this->tableBDD SET $algo[1] = '$count' WHERE $this->condition AND $algo[3] = '$ligne[0]'");
			}
		}


		// Affiche au choix la fin de requête personnalisée ou les classements classiques
		if($algo[0] == true && $ordre[0] != true) {			
			$this->orderBy = " ORDER BY $algo[1] $algo[2]";
		} else if($algo[0] == true && $ordre[0] == true) {
			// Cumule l'algorithme et le classement classique si les deux sont sur "true"
			$this->orderBy = " ORDER BY $algo[1] $algo[2], $ordre[1] $ordre[2]";
		} else {		
			// Ajout des critères d'ordre (si l'option du tableau est sur "true")
			if($ordre[0] == true) {
				$this->orderBy = " ORDER BY $ordre[1] $ordre[2]";
			} else {
				$this->orderBy = "";
			}
		}

		/*-------------------------------------------------------------------*/
		/*------------------------ Requête SQL totale -----------------------*/
		/*-------------------------------------------------------------------*/
		if(empty($orderLimitPerso)) {
			$this->requeteTotale = mysql_query("SELECT $selectColumn FROM $this->tableBDD WHERE $this->condition $this->orderBy $this->limitMinMax")
			or die("<div>Erreur dans la requête, vérifiez bien votre paramétrage complet !</div>");
			// Pour calculer le nombre total de résultats justes
			$this->nbResults = mysql_query("SELECT count(*) FROM $this->tableBDD WHERE $this->condition")
			or die("<div>Erreur dans la requête, vérifiez bien votre paramétrage complet !</div>");
			$compte = mysql_query("SELECT count(*) FROM $this->tableBDD WHERE $this->condition")
			or die("<div>Erreur dans la requête, vérifiez bien votre paramétrage complet !</div>");

		} else {
			if($limit[0] == true && $ordre[0] == true) {
				$this->requeteTotale = mysql_query("SELECT $selectColumn FROM $this->tableBDD WHERE $this->condition $orderLimitPerso $this->orderBy $this->limitMinMax")
				or die("<div>Erreur dans la requête, vérifiez bien votre paramétrage complet !</div>");
			} else if($limit[0] == true && $ordre[0] == false) {
				$this->requeteTotale = mysql_query("SELECT $selectColumn FROM $this->tableBDD WHERE $this->condition $orderLimitPerso $this->limitMinMax")
				or die("<div>Erreur dans la requête, vérifiez bien votre paramétrage complet !</div>");
			} else {
				$this->requeteTotale = mysql_query("SELECT $selectColumn FROM $this->tableBDD WHERE $this->condition $orderLimitPerso")
				or die("<div>Erreur dans la requête, vérifiez bien votre paramétrage complet !</div>");
			}
			// Pour calculer le nombre total de résultats justes
			$this->nbResults = mysql_query("SELECT count(*) FROM $this->tableBDD WHERE $this->condition $orderLimitPerso");
			$compte = mysql_query("SELECT count(*) FROM $this->tableBDD WHERE $this->condition $orderLimitPerso");
		}
		
		// Récupération du nombre de résultats
		$compteTotal = mysql_fetch_row($compte);
		self::$nbResultsChiffre = $compteTotal[0];

		// Affiche le résultat de la fonction de rappel Callback
		if(!empty($callback)) {
			// Enregistre le nombre de résultats totalisés par la requête totale
			$nbResultats = mysql_fetch_row($this->nbResults);
			// Appel à la fonction de rappel avec quatre paramètres oblibatoires !!!
			// 1. une variable au choix pour récupérer l'ensemble de la requête (tableau)
			// 2. une variable au choix pour le nombre de résultats retournés par la requête totale
			// 3. une variable au choix pour l'ensemble des mots et expressions de la requête
			call_user_func_array($callback, array(&$this->requeteTotale, &$nbResultats[0], &$this->motsExpressions));
		} else {
			echo "<p>Attention ! Aucune fonction de rappel appelée pour afficher les résultats</p>";	
		}
	}

	/*----------------------------------------------------------------------------------------------------*/
	/*--------------------------------------- Fonction de pagination -------------------------------------*/
	/*-------- 7 arguments possibles... ------------------------------------------------------------------*/
	/*-- 1. $param est le nom du paramètre GET de la page ('page' par défaut)-----------------------------*/
	/*-- 2. $NbVisible correspond au nombre de pages affichées autour de la page courante ----------------*/
	/*-- 3. $debutFin pour afficher des liens (premières et dernières pages) => 0 pour vide --------------*/
	/*-- 4. $suivPrec (true/false) pour afficher ou non "page suivante" et "page précédente" -------------*/
	/*-- 5. $firstLast (true/false) pour afficher ou non "première page" et "dernière page" --------------*/
	/*-- 6. $arrayAff est un tableau qui contient les éléments de mise en forme (8 args) -----------------*/
	/*-- => (Précédent, Suivant, firstPage, LastPage, classPrecSuiv, classPage, classBloc, classInactif) -*/
	/*-- 7. $arraySeparateur est un tableau qui contient les séparateur (5 args) -------------------------*/
	/*-- => (pointSuspension, sepPremiereDernierePage, $sepNumPage, sepSuivPrec, sepDebutFin) ------------*/
	/*-- Source du code : http://seebz.net/archive/34-pagination-2-comme-avant-en-mieux.html -------------*/
	/*----------------------------------------------------------------------------------------------------*/
	function moteurPagination($param = "", $NbVisible = 2, $debutFin = 0, $suivPrec = true, $firstLast = true, $arrayAff = array('&laquo; Précédent', 'Suivant &raquo;', 'Première page', 'Dernière page', 'precsuiv', 'current', 'pagination', 'inactif'), $arraySeparateur = array('&hellip;', ' ', ' ', ' ', ' ')) {
		
		// Nombre total de pages à afficher (en fonction de LIMIT)
		$nb_pages = ceil(self::$nbResultsChiffre / self::$limit);
		
		// Formatage de la requête (pour éviter les problèmes avec les guillemets)
		$this->requete = htmlspecialchars($this->requete);
		
		// Numero de page courante (1 par défaut)
		$parametreGetPost = self::$limitArg;
		if(isset($parametreGetPost) && is_numeric($parametreGetPost)) {
			if($parametreGetPost == 0) {
				$current_page = 1;
			} else {
				$current_page = $parametreGetPost;
			}
		} else {
			$current_page = 1;
		}

		// Récupération des paramètres d'URL et formatage des liens (paramètre de la page à la fin)
		if($_GET[$param] < $nb_pages+1 && is_numeric($_GET[$param]) || !isset($_GET[$param])) {
			preg_match_all('#([^=])+([^\?\&])+#i', $_SERVER['QUERY_STRING'], $valueArgs);
			$urlPage = $_SERVER['PHP_SELF'].'?';
			foreach($valueArgs[0] as $arg) {
				$urlPage .= $arg;
				$urlPage = str_replace("&".$param."=".($parametreGetPost), "", $urlPage);
			}
			$urlPage .= "&".$param."=";
		} else {
			$urlpropre = str_ireplace("?".$param."=".$_GET[$param],"",$_SERVER['REQUEST_URI']);
			$urlpropre = str_ireplace("&".$param."=".$_GET[$param],"",$_SERVER['REQUEST_URI']);
			header('location:'.$urlpropre);
		}

		// Début du bloc de pagination (avec classBloc)
		$pagination = '<div class="'.$arrayAff[6].'">';
		
		// S'il y a plus d'une page
		if($nb_pages > 1) {
			// Affichage du lien "Première page" avant "page précédente"
			if($firstLast == true) {
				for($i=1; $i<=1; $i++) {
					$pagination .= ($current_page==$i) ? '<span class="'.$arrayAff[4].' '.$arrayAff[7].'">'.$arrayAff[2].'</span>' : '<a href="'.$urlPage.$i.'">'.$arrayAff[2].'</a>';
					$pagination .= $arraySeparateur[1];
				}
			}

			// Lien pour la page précédente (si $precSuiv = true)
			if($suivPrec == true) {
				if ($current_page > 1) {
					$pagination .= '<a class="'.$arrayAff[4].'" href="'.$urlPage.($current_page-1).'" title="'.$arrayAff[0].'">'.$arrayAff[0].'</a>';
					$pagination .= $arraySeparateur[3];
				} else {
					$pagination .= '<span class="'.$arrayAff[4].' '.$arrayAff[7].'">'.$arrayAff[0].'</span>';
					$pagination .= $arraySeparateur[3];
				}
			}
			
			// Lien(s) du début (avant page précédente et les éventuels "...")
			for($i=1; $i<=$debutFin; $i++) {
				$pagination .= ($current_page==$i) ? '<span class="'.$arrayAff[5].'">'.$i.'</span>' : '<a href="'.$urlPage.$i.'">'.$i.'</a>';
				$pagination .= $arraySeparateur[4];
			}
	
			// "..." après le début
			if(($current_page-$NbVisible) > ($debutFin+1)) {
				$pagination .= ' '.$arraySeparateur[0];
			}
			
			// On boucle autour de la page courante
			$start = ($current_page-$NbVisible) > $debutFin ? $current_page-$NbVisible : $debutFin+1;
			$end = ($current_page+$NbVisible)<=($nb_pages-$debutFin) ? $current_page+$NbVisible : $nb_pages-$debutFin;
			for($i=$start; $i<=$end; $i++) {
				$pagination .= $arraySeparateur[2];
				if($i==$current_page) {
					$pagination .= '<span class="'.$arrayAff[5].'">'.$i.'</span>';
				} else {
					$pagination .= '<a href="'.$urlPage.$i.'">'.$i.'</a>';
				}
			}
	
			// "..." affiché avant la fin
			if(($current_page+$NbVisible) < ($nb_pages-$debutFin)) {
				$pagination .= ' '.$arraySeparateur[0];
			}
			
			// Lien(s) de fin (avant page suivante et avant les éventuels "...")
			$start = $nb_pages-$debutFin+1;
			if($start <= $debutFin) { $start = $debutFin+1; }
			for($i=$start; $i<=$nb_pages; $i++) {
				$pagination .= $arraySeparateur[4];
				$pagination .= ($current_page==$i) ? '<span class="'.$arrayAff[5].'">'.$i.'</span>' : '<a href="'.$urlPage.$i.'">'.$i.'</a>';
			}
	
			// Lien pour la page suivante (si $precSuiv = true)
			if($suivPrec == true) {
				if($current_page < $nb_pages) {
					$pagination .= $arraySeparateur[3];
					$pagination .= ' <a class="'.$arrayAff[4].'" href="'.$urlPage.($current_page+1).'" title="'.$arrayAff[1].'">'.$arrayAff[1].'</a>';
				} else {
					$pagination .= $arraySeparateur[3];
					$pagination .= ' <span class="'.$arrayAff[4].' '.$arrayAff[7].'">'.$arrayAff[1].'</span>';
				}
			}
			
			// Affichage du lien "Dernière page" après "page suivante"
			if($firstLast == true) {
				$start = $nb_pages-1;
				for($i=$start+1; $i<=$nb_pages; $i++) {
					$pagination .= $arraySeparateur[1];
					$pagination .= ($current_page==$i) ? '<span class="'.$arrayAff[4].' '.$arrayAff[7].'">'.$arrayAff[3].'</span>' : '<a href="'.$urlPage.$i.'">'.$arrayAff[3].'</a>';
				}
			}
		}
		$pagination .= "</div>"; // Fin du bloc de pagination
		echo $pagination;
	}
	
	function limit() {
		return self::$limit;	
	}
	function nbResults() {
		return self::$nbResultsChiffre;	
	}
	
}

/*--------------------------------------------------------*/
/*------- Class Fille pour afficher les résultats --------*/
/*-- 4 paramètres optionnels : ---------------------------*/
/*-- 1. Tableau pour afficher "résultat" et "résultats" --*/
/*-- 2. Fin de la phrase ("pour votre recherche") --------*/
/*-- 3. Coordination pour le nb de résultats par page ----*/
/*-- 4. Affichage différent s'il n'y a pas de LIMIT ------*/
/*--------------------------------------------------------*/
class affichageResultats extends moteurRecherche {
	function nbResultats($wordsResults = array("résultat", "résultats"), $phrase = 'pour votre recherche', $coord = " à ", $illimite = false) {
		if($illimite == true) {
			if(parent::nbResults() < 2) {
				$res = " ".$wordsResults[0];	
			} else {
				$res = " ".$wordsResults[1];
			}
			return "<div class=\"searchNbResults\">".parent::nbResults().$res." ".$phrase."</div>";
		} else {
			if(parent::$limitArg == 0) {
				$nbDebut = 1;
				if(parent::nbResults() > parent::$limit) {
					$nbFin = (parent::$limitArg+1) * parent::$limit;
				} else {
					$nbFin = parent::nbResults();
				}
			} else {
				$nbDebut = ((parent::$limitArg-1) * parent::$limit)+1;
				
				if(ceil(parent::nbResults()/(parent::$limit*parent::$limitArg)) != 1) {
					$nbFin = parent::$limitArg * parent::$limit;
				} else {
					$nbFin = parent::nbResults();
				}
			}
			
			if(parent::nbResults() < 2) {
				$res = " ".$wordsResults[0];	
			} else {
				$res = " ".$wordsResults[1];
			}
			return "<div class=\"searchNbResults\">".parent::nbResults().$res." ".$phrase." (".$nbDebut.$coord.$nbFin.")</div>";
		}
	}
}

/*------------------------------------------------------------------------------------------------*/
/*-------------------------------- Class pour surligne les mots ----------------------------------*/
/*------------ Structure : faire new surlignageMot() dans la fonction d'affichage ----------------*/
//-- 5 arguments :
//-- -> 1. Tableau des mots à surligner
//-- -> 2. Texte dans lequel s'applique le surlignage
//-- -> 3. Type de surlignage :
//--        -> "exact" pour la chaîne tapée précise (par défaut)
//--        -> "total" ou "complet" pour les mots complets
//-- -> 4. Exactitude du surlignage :
//-- 		-> true pour surligner le mot précis (selon le type de recherche)
//--		-> false pour surligner le mot contenant une chaîne précise (selon le type de recherche)
//-- -> 5. Type de recherche : FULLTEXT, REGEXP ou LIKE en valeur
//--		-> N.B. : il détermine aussi la précision du surlignage (FULLTEXT est le plus précis)
/*--------------------------------------------------------------------------------------------------*/
class surlignageMot {
	var $contenu;
	
	/*----------------------------------------------------------------------------------*/
	/*----------------------- Méthode de surlignage avec 5 arguments -------------------*/
	/*------ $gras = new surlignageMot($mots, $texte, 'exact', true, "FULLTEXT"); ------*/
	/*----------------------------------------------------------------------------------*/
	function surlignageMot($mots, &$contenu, $typeSurlignage = "exact", $exact = true, $typeRecherche = "FULLTEXT") {
		foreach($mots as $mot) {			
			// Permet d'afficher les expressions entre guillemets en gras
			if(preg_match_all('/"(.*)"/i', $mot, $args)) {
				foreach($args[0] as $arg) {
					$mot = str_ireplace(array('"','\"'),array(' ',' '),$mot);
				}
			}
			// Permet d'échapper les caractères du regex
			if(preg_match_all('([\+\*\?\/\'\"\-])', $mot, $args)) {
				foreach($args[0] as $arg) {
					$mot = str_ireplace(array('+', '*', '?', '/', "'", '"'),array('\+','\*','\?', '\/', '\'', ''),$mot);
				}
			}
		
			// Adapte le surlignage des mots selon les besoins (chaîne exacte, mot complet ou sans)
			if($typeSurlignage == "exact" && (($exact == true && $typeRecherche != "LIKE") || ($exact == false && $typeRecherche == "FULLTEXT"))) {
				$contenu = preg_replace('/([[:blank:]<>\(\[\{].?:?;?,?)('.$mot.')([\)\]\}.,;:[:blank:]<>])/i', '$1<b>$2</b>$3', $contenu);
			} else if($typeSurlignage == "exact" && (($exact == true && $typeRecherche == "LIKE") || ($exact == false) && $typeRecherche != "FULLTEXT")) {
				$contenu = preg_replace('/('.$mot.'{1,'.strlen($mot).'})/i', '<b>$1</b>', $contenu);
			} else if($typeSurlignage == "total" || $typeSurlignage == "complet") {
				$contenu = preg_replace('/([[:blank:]<>])([^[:blank:]<>]*'.$mot.'[^[:blank:]<>]*)([[:blank:]])/i', '$1<b>$2</b>$3', $contenu);
			}

			// Nettoyage des balises <hn> inféctées par la mise en gras
			if(preg_match_all('/<[\/]?[hH]+<b>('.$mot.')<\/b>+/i', $contenu, $args)) {
				foreach($args[0] as $arg) {
					$contenu = preg_replace('/(<[\/]?[a-zA-Z]+)<b>('.$mot.')<\/b(>)+/i', '$1$2$3', $contenu);
				}
			}

			// Nettoyage des autres balises inféctées par la mise en gras
			if(preg_match_all('/<[\/]?[^hH]?<b>('.$mot.')<\/b>?(^>)*/i', $contenu, $args)) {
				foreach($args[0] as $arg) {
					$contenu = preg_replace('/(<[\/]?[^hH]?)<b>('.$mot.')<\/b>?(^>)*/i', '$1$2$3$4', $contenu);
				}
			}
			
			// Nettoie les <strong> ajoutés en "trop" dans les attributs HTML courants (surtout src et href)
			// Ainsi, si un mot recherché est dans une URL, une class (...), les <strong> seront omis et tout fonctionnera...
			//preg_match_all('/(src|href|alt|title|class|id|rel)=["\']{1}[^\'"]+('.$mot.')[^\'"]+["\']{1}/i',$contenu, $args)
			if(preg_match_all('/(src|href|alt|title|class|id|rel)=["\']{1}[^\'"]+('.$mot.')[^\'"]+["\']{1}/i',$contenu, $args)) {
				foreach($args[0] as $arg) {
					$contenu = preg_replace('/(src|href|alt|title|class|id|rel)*(=["\']{1}[^\'"]*)<b>+('.$mot.')<\/b>+([^\'"]*["\']{1})/i', '$1$2$3$4', $contenu);
				}
			}
		}
		$this->contenu = $contenu;
	}
} // Fin de la class
?>