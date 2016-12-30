<?php
/**
 * Français
 */
require 'lang_content.php';
require 'lang_data.php';
require 'lang_facebook.php';
require 'lang_form.php';
require 'lang_forum.php';
require 'lang_media.php';
require 'lang_routes.php';
require 'lang_user.php';

$contactEmail_Html = <<<HTML
<p>nouveau message via le formulaire de contact de mountainboard.fr:</p>
<p>%s</p>
<dl>
	<dt>IP</dt>
	<dd>%s</dd>
	<dt>Host</dt>
	<dd>%s</dd>
	<dt>User</dt>
	<dd>%s - %s</dd>

</dl>
HTML;

$contactEmail_Txt = <<<HTML
nouveau message via le formulaire de contact de mountainboard.fr:

%s


	IP: %s
	Host: %s
	User: %s - %s

HTML;


$array = array(
	// Title
	'appName' => GLOBAL_DOMAIN_FULL,
	'appSlogan' => 'le mountainboard en France',
	'appBaseTitle' => GLOBAL_DOMAIN_FULL,

    // Misc.
    'by' => 'par',
    'at' => 'à',
	'for' => 'pour',
	'dateOn' => 'le',
    'author' => 'auteur',
    'postedBy' => 'posté par',
	'readMore' => 'lire la suite',
	'aLongTimeAgo' => 'il y a bien longtemps!',

    //Actions
    'edit' => 'modifier',
    'edited' => 'édité',
    'submit' => 'envoyer',
	'delete' => 'supprimer',
	'confirm' => 'confirmer',
	'cancel' => 'annuler',
	'search' => 'recherche',
	'markAsRead' => 'marquer comme lu',
	'markAsNotRead' => 'marquer comme non-lu',
	'yes' => 'oui',
	'no' => 'non',
	'goToButtonLabel' => 'Go!',
	'type' => 'type',
	'name' => 'nom',
	'info' => 'info',
	'and' => 'et',
	'or' => 'ou',
	'defaultLoadingMessage' => 'chargement en cours...',

    // Languages
    'language' => 'langue',
    'fr' => 'français',
    'en' => 'english',

	// Location
	'northAbbr' => 'n',
	'southAbbr' => 's',
	'eastAbbr' => 'e',
	'westAbbr' => 'o',
	'viewOnGoogleMaps' => 'voir sur Google Maps',
    'coordinates' => 'coordonnées',

    // Login messages
    'welcome' => 'bienvenue',
    'loginErrorTryAgain' => 'merci de réessayer',
    'backToHomePage' => 'retourner à la page d\'accueil',

    //Errors
    'logFailure' => 'impossible d\'écrire dans le fichier de log',

    // CommonViews Controller
        // footer.phtml
        'footerTitle' => 'pied de page',

        // header.phtml
        'headerTitle' => 'haut de page',

    // Error Controller
        // 404.phtml
        'notFoundTitle' => 'page introuvable',
		'404picBy' => 'photo par %s',
		'404HtmlMessage' => '
	<h1 class="pageTitle">Whoops !</h1>
	<p>Voilà qui est inattendu: la page demandée est introuvable !</p>
	<p>Nous en prenons bien note, et allons corriger le problème... si possible !</p>',

		// 500.phtml
		'500HtmlMessage' => '
	<h1 class="pageTitle">Boum !</h1>
	<p>Ca craint... un truc chelou vient de se passer, et tu vas sans doute pas pouvoir faire ce que tu voulais. Désolé!</p>
	<p>On fait de notre mieux, mais si tu veux tu peux nous laisser un message sur la <a href="/contact">page contact.</a></p>',

        // database.phtml
        'detailedDatabaseProblem' => 'problème de base de données.',
        'simpleDatabaseProblem' => 'le service est momentanément indisponible - merci de réessayer un peu plus tard.',

        // exception.phtml
        'defaultExceptionMessage' => 'une erreur est apparue, veuillez nous en excuser.',

        // maintenance.phtml
        'maintenanceMessage' => 'nous effectuons des opérations de maintenance. Le service sera rétabli au plus vite. Veuillez nous excuser du désagrément.',

    // General
        'homepage' => 'accueil',
		'homepageTitle' => 'le mountainboard en France',
        'error' => 'erreur',
        'useraccount' => 'ma page',
        'defaults' => 'accueil',

    // Months
        'january' => 'janvier',
        'february' => 'février',
        'march' => 'mars',
        'april' => 'avril',
        'may' => 'mai',
        'june' => 'juin',
        'july' => 'juillet',
        'august' => 'août',
        'september' => 'septembre',
        'october' => 'octobre',
        'november' => 'novembre',
        'december' => 'décembre',

    // Categories
        'noCat' => '',
        'startCat' => 'général',
        'communityCat' => 'communauté',
        'articlesCat' => 'articles',
        'accountCat' => 'mon compte',
        'editionCat' => 'rédaction',
        'adminCat' => 'administration',

        // Routes
        'spots' => 'spots',
        'tricks' => 'tricks',
        'articles' => 'articles',
        'news' => 'news',
        'editnews' => 'editnews',
        'archives' => 'archives',
        'usermyprofile' => 'mon profil',
        'nearby' => 'près de chez moi',
        'userlist' => 'membres',
        'forums' => 'forums',
        'photos' => 'photos',
        'videos' => 'vidéos',
        'portfolio' => 'portfolio',

    // Index page
        'indexMoreArticles' => 'plus d\'articles',

    // Pagination
        'previousItem' => 'précédent',
        'goToPreviousPage' => 'page précédente',
        'nextItem' => 'suivant',
        'goToNextPage' => 'page suivante',

    // Maps
        'mapString' => 'carte',
        'clearLocation' => 'effacer la position',
		'locateMe' => 'détecter ma position',
        'mapLabel' => 'position',

    // Files
    	'Byte' => 'o',
    	'KByte' => 'Ko',
    	'MByte' => 'Mo',
    	'GByte' => 'Go',
    	'TByte' => 'To',

	// Articles
		'articleSubCategoriesTitle' => 'articles',
		'articleSubCategoriesDescription' => 'dans les pages suivantes, tu trouveras un condensé d\'information mountainboardistique. T\'en veux du contenu?',
		'dossiersDescription' => 'tu trouveras ici, entre autres, une liste d\'articles détaillés pour t\'aider à rider mieux et plus malin, et des plans et indications pour construire des modules.',
		'newsDescription' => 'toutes les news publiées sur ce site depuis 2002! Un bon endroit pour voir la progression du mountainboard en France depuis ses débuts.',
		'testsDescription' => 'des tests de boards et d\'accessoires et protecs, avec des photos et des points de vue neutres (ou en tout cas on essaie).',
		'portfolioDescription' => 'une liste de photos particulièrement belles ou originales, choisies à la main pour le plaisir de tes yeux. Dans l\'idéal, c\'est la page à montrer à quelqu\'un qui ne connait pas du tout le mountainboard.',

		'communitySubCategoriesTitle' => 'communauté',
		'communitySubCategoriesDescription' => 'le mountainboard est un sport jeune, pas pourri par l\'argent, et porté par les riders avant tout. Les pages suivantes sont là pour aider à rapprocher les riders et les aider à partager leur expérience... du social, bordel!',
		'forumsDescription' => 'le forum (chat) est depuis longtemps le coeur du site. Il permet aux mountainboardeurs Français débutants comme vétérans de dialoguer et de maintenir le contact toute l\'année. Il y a aussi des sous-forums régionaux, ou dédiés à des associations.',
		'photosDescription' => 'c\'est là que tu peux poster tes photos, ou juste regarder ce que les autres font. Faut pas hésiter, ni être timide, on a tous été débutants!',
		'franceDescription' => 'une carte de France pour trouver des riders et des spots pres de chez toi.',
		'videosDescription' => 'poste ici des liens vers les vidéos que tu trouves sur le net, ou que tu as préparées avec amour pour le plaisir de tes confrères de la boue.',
		'usersDescription' => 'la liste de tous les riders inscrits sur le site.',
		'spotsDescription' => 'si tu as un spot préféré, soumets-le à notre base de données, tu feras très certainement des heureux à un moment ou à un autre.',
		'tricksDescription' => 'la liste des tricks qu\'on peut rentrer sur un mountainboard n\'est pas infinie, mais elle s\'en approche (...). Si tu as besoin d\'inspiration ou de conseils, c\'est ici que ça se passe!',
		'blogsDescription' => 'le coin "expression libre" pour raconter ta vie sur un mountainboard.',

	// Contact
		'contactTitle' => 'contact',
		'contactPageDescription' => 'si tu souhaites nous contacter, merci d\'utiliser le formulaire suivant, nous ferons de notre mieux pour répondre rapidement.',
		'contactMessageHint' => 'tape ton message',
		'message' => 'message',
		'sendMessage' => 'envoyer',
		'contactEmail_Html' => $contactEmail_Html,
		'contactEmail_Txt' => $contactEmail_Txt,
		'contactSuccess' => 'ton message a bien été envoyé. Nous allons faire de notre mieux pour répondre dans les meilleurs délais.',
		'contactFailure' => '<p>Un probleme est apparu et a empeché l\'envoi de ton message.</p><p>Nous avons été prévenus de ce probleme, mais en attendant, tu peux aussi <a href="http://www.facebook/mountainboardfr">nous contacter sur Facebook</a></p>',

	// Search
		'searchResultsInfo' => '%s résultat(s).',

	// Chat
		'chatJoinNotification' => 'débarque dans le chat.',
		'chatLeaveNotification' => 'vient de quitter le chat.',

	// Google +1
		'likeUsOnGoogle' => 'clique sur le bouton +1 si tu aimes %s!',

);

$array = array_merge($array, $content);
$array = array_merge($array, $data);
$array = array_merge($array, $facebook);
$array = array_merge($array, $form);
$array = array_merge($array, $forum);
$array = array_merge($array, $media);
$array = array_merge($array, $routes);
$array = array_merge($array, $user);

return $array;