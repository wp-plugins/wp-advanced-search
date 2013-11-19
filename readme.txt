=== WordPress WP-Advanced-Search ===
Contributors: Mathieu Chartier
Donate link: http://www.mathieu-chartier.com
Author URI: http://blog.internet-formation.fr/2013/07/wp-advanced-search/
Plugin URI: http://wordpress.org/extend/plugins/wordpress-wp-advanced-search/
Tags: advanced, search, advanced search, recherche, moteur, search engine, engine, seo, form, formulaire, moteur de recherche, recherches, moteurs, algorithme, algorithm, algo, stop words, stopwords, words, fulltext, like, regexp, exclusion, pagination, pages, searchform, search form, search.php, avanc&eacute;, modulable, php, class, poo, programmation, objet, object, full text, exacte, strong, bold, gras, surbrillance, relevance, relevance algorithm, automatic, highlight
License: GPLv2 or later
Requires at least: 2.5
Tested up to: 3.6.1
Stable Tag: 1.6

"WP-Advanced-Search" est un moteur de recherche complet et puissant pour WordPress enti&agrave;rement modulable.

== Description ==

<p><em>En fran&ccedil;ais</em></p>
<p><strong>WP-Advanced-Search</strong> fonctionne en anglais et en fran&ccedil;ais<br/>
<strong>WP-Advanced-Search</strong> fonctionne avec un code court <strong>&lt;?php WP_Advanced_Search(); ?&gt;</strong> &agrave; ajouter dans la page de recherche du th&egrave;me WordPress (search.php). <strong>Tout est enti&egrave;rement param&eacute;trable</strong> dans les r&eacute;glages de WordPress (sous-menu 'Advanced Search').</p>
<p>
Quelques exemples d'options existantes :
<ul>
<li>Choix des colonnes de recherche</li>
<li>Ordre des r&eacute;sultats modulable</li>
<li>Utilisation d'un algorithme de pertinence pour le classement final</li>
<li>Ajout d'une pagination automatique</li>
<li>Mise en surbrillance ou non des mots recherch&eacute;s</li>
<li>Gestion des blocs &agrave; afficher dans les SERP</li>
<li>Possibilit&eacute; de faire une recherche exacte avec des mots entre guillemets</li>
<li>...</li>
</ul>
</p>

<p><em>For English people</em></p>
<p><strong>WP-Advanced-Search</strong> works in English and in French<br/>
<strong>WP-Advanced-Search</strong> works with a simple code &lt;?php WP_Advanced_Search (); ?&gt; to add to the search page of WordPress (search.php). Everything is fully configurable in the settings of WordPress (sub-menu 'Advanced Search').</p>
<p>
Some examples of existing options:
<ul>
<li>Choice search columns</li>
<li>Flexible order results</li>
<li>Using a relevance algorithm for the final classification</li>
<li>Adding an automatic pagination</li>
<li>Highlight search terms, or not</li>
<li>Manage blocks to display in the SERP</li>
<li>Ability to write an exact search with words in quotation marks</li>
<li>...</li>
</ul>
</p>

== Installation ==

<p><em>En fran&ccedil;ais</em></p>
<p>1. Recopier le contenu de l'archive dans le r&eacute;pertoire des extensions (wp-content/plugins) et activer dans le panneau des extensions du backoffice de Wordpress.<br/>
2. R&eacute;gler les param&egrave;tres dans les options de l'extension.<br/>
3. Remplacer la boucle d'affichage de la page de recherche (search.php) par <br/><strong>&lt;?php WP_Advanced_Search(); ?&gt;</strong>.<br/>
N.B. : Pensez &agrave; installer les index FULLTEXT si vous utilisez ce mode de recherche (lien en dessous du choix du type de recherche)</p>

<p><em>For English people</em></p>
<p>1. Copy the content of the archive in "wp-content/plugins" and activate the plugin.<br/>
2. Adjust the settings in the options.<br/>
3. Replace the display loop search page (search.php) by <br/><strong>&lt;?php WP_Advanced_Search(); ?&gt;</strong>.<br/>
NB: Remember to install the FULLTEXT index if you use this method of research (link below choosing the type of research)</p>

== Screenshots ==

1. "WP-Planification" dans le backoffice de Wordpress (BackOffice screenshot).
2. Panneau du widget (widget panel).
3. Exemple d'usage complet (for example : complete usage of plugin).
4. Exemple d'affichage diff&eacute;rent (for example : another type of use of plugin).

== Changelog ==

= Version 1.0 (05/10/2013) =

- Premi&egrave;re version du moteur de recherche (first version of advanced search engine)
- Possibilit&eacute; de d&eacute;sactiver chaque &eacute;l&eacutement (all options can be disabled)
- Possibilit&eacute; d'ordonner les r&eacute;sultats avec ou sans algorithme de pertinence (ability to order the results with or without relevance algorithm)
- Possibilit&eacute; d'ajouter une pagination automatique (ability to add an automatic pagination)
- Possibilit&eacute; de mettre les mots recherch&eacute;s en gras (ability to put search words in bold)
- Ajout d'un syst&egrave;me de traduction (fran&ccedil;ais et anglais par d&eacute;faut) (French and English translation included)

= Version 1.1 (06/10/2013) =

- Correction de petits bugs (Fixed small bugs)
- Ajout du type de contenus &agrave; prendre en compte : pages, articles, les deux ou tout (Adding the type of content to consider: pages, sections, or both)
- Possibilit&eacute; de modifier la phrase d'erreur (Ability to change the error sentence)
- Possibilit&eacute; de modifier la phrase qui rappelle la requ&ecirc;te recherch&eacute;e (Ability to change the sentence that reminds the search query)

= Version 1.1.5 (06/10/2013) =

- Corrige quelques bugs de mise &agrave; jour (fix some bugs with upgrade)


= Version 1.2 (10/10/2013) =

- D&eacute;sactivez puis r&eacute;activez l'extension pour ceux qui rencontrent des probl&egrave;mes avec l'ancienne mise &agrave; jour... Pardon ! (Deactivate and reactivate the extension for those who have problems with the old update ... Sorry!)
- Ajout des stop words dans plusieurs langues (adding stop words in several languages)
- Correction de la gestion des images à la Une trop grandes... (Fixed post thumbnails too large ...)