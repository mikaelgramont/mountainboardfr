<?php
/**
 * English
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
	'appSlogan' => 'mountainboarding in France',
	'appBaseTitle' => GLOBAL_DOMAIN_FULL,

    // Misc.
    'at' => 'at',
    'by' => 'by',
	'for' => 'for',
    'dateOn' => 'on',
    'author' => 'author',
    'postedBy' => 'posted by',
	'readMore' => 'read more',
	'aLongTimeAgo' => 'a long time ago!',

    //Actions
    'edit' => 'edit',
    'edited' => 'edited',
    'submit' => 'submit',
    'delete' => 'delete',
	'confirm' => 'confirm',
	'cancel' => 'cancel',
	'search' => 'search',
	'markAsRead' => 'mark as read',
	'markAsNotRead' => 'mark as not read',
	'yes' => 'yes',
	'no' => 'no',
	'goToButtonLabel' => 'Go!',
	'type' => 'type',
	'name' => 'name',
	'info' => 'info',
	'and' => 'and',
	'or' => 'or',
	'defaultLoadingMessage' => 'loading...',

    // Languages
    'language' => 'language',
    'fr' => 'français',
    'en' => 'english',

	// Location
	'northAbbr' => 'n',
	'southAbbr' => 's',
	'eashAbbr' => 'e',
	'westAbbr' => 'w',
	'viewOnGoogleMaps' => 'view on Google Maps',

    // Login messages
    'welcome' => 'welcome',
    'loginErrorTryAgain' => 'please try again',
    'backToHomePage' => 'back to home page',

    //Errors
    'logFailure' => 'log write operation failure',

    // CommonViews Controller
        // footer.phtml
        'footerTitle' => 'footer',

        // header.phtml
        'headerTitle' => 'header',

    // Error Controller
        // 404.phtml
        'notFoundTitle' => 'page not found',
		'404picBy' => 'photo by %s',
		'404HtmlMessage' => '
	<h1 class="pageTitle">Whoops !</h1>
	<p>Now that\'s unexpected: the requested page could not be found !</p>
	<p>We\'re taking note of the problem, though, and will try to fix it soon... if possible !</p>',

		// 500.phtml
		'500HtmlMessage' => '
	<h1 class="pageTitle">Boom !</h1>
	<p>Life sucks... some error just happened and you\'re not gonna get what you wanted. Sorry!</p>
	<p>We\'re doing our best, but if you feel like this is urgent, drop us a line on the <a href="/contact">contact page.</a></p>',

        // database.phtml
        'detailedDatabaseProblem' => 'database problem',
        'simpleDatabaseProblem' => 'the service is momentarily unavailable - please try again later.',

        // exception.phtml
        'defaultExceptionMessage' => 'an error occured, we apologize for the inconvenience.',

        // maintenance.phtml
        'maintenanceMessage' => 'We are currently performing maintenance operations. The service will be back online as soon as possible . We apologize for the inconvenience.',

    // General
        'homepage' => 'home page',
		'homepageTitle' => 'mountainboarding in France',
		'error' => 'error',
        'useraccount' => 'my page',
        'defaults' => 'home page',

    // Months
        'january' => 'january',
        'february' => 'february',
        'march' => 'march',
        'april' => 'april',
        'may' => 'may',
        'june' => 'june',
        'july' => 'july',
        'august' => 'august',
        'september' => 'september',
        'october' => 'october',
        'november' => 'november',
        'december' => 'december',

    // Categories
        'noCat' => '',
        'startCat' => 'start',
        'communityCat' => 'community',
        'articlesCat' => 'articles',
        'accountCat' => 'my account',
        'editionCat' => 'edition',
        'adminCat' => 'administration',

    // Routes
        'spots' => 'spots',
        'tricks' => 'tricks',
        'articles' => 'articles',
        'news' => 'news',
        'editnews' => 'editnews',
        'archives' => 'archives',
        'usermyprofile' => 'my profile',
        'nearby' => 'nearby',
        'userlist' => 'membres',
        'forums' => 'forums',
        'photos' => 'photos',
        'videos' => 'videos',
        'portfolio' => 'portfolio',

    // Index page
        'indexMoreArticles' => 'more articles',

    // Pagination
        'previousItem' => 'previous',
        'nextItem' => 'next',

    // Maps
        'mapString' => 'map',
        'clearLocation' => 'clear location',
		'locateMe' => 'locate me automatically',
		'mapLabel' => 'location',

    // Files
    	'Byte' => 'B',
    	'KByte' => 'KB',
    	'MByte' => 'MB',
    	'GByte' => 'GB',
    	'TByte' => 'TB',

	// Articles
		'articleSubCategoriesTitle' => 'articles',
		'articleSubCategoriesDescription' => 'if you\'re looking for mountaiboard-related content, this is the place.',
		'dossiersDescription' => 'here, among other things, you\'ll find a list of detailed articles to help you ride better and smarter, to build and install jump features.',
		'newsDescription' => 'this is the complete archive of past news articles, dating back to early 2002. A good place to see the progression of our sport in the early years.',
		'testsDescription' => 'board and accessories reviews, with pictures and our unbiased view on them (or so we like to think).',
		'portfolioDescription' => 'this is a list of particularly awesome pictures, hand-picked for your viewing pleasure. Ideally, this is a place to point people to when you want to show them what mountainboarding is all about',

		'communitySubCategoriesTitle' => 'community',
		'communitySubCategoriesDescription' => 'mountainboarding is a young sport and it is defined by the riders. The following pages are here to help keep the community alive. Cos we\'re all about social!',
		'forumsDescription' => 'the forum (chat) has long been the heart of the site. It helps French mountainboarders, beginners and veterans, keep in touch throughout the year. We also have regional sub-forums or forums for associations.',
		'photosDescription' => 'here you can post your pictures, or just see what others are up to. Just don\'t hesitate, don\'t be shy, we were all beginners at some point!',
		'videosDescription' => 'here you can post links to videos you found online, or those you made yourself and want to show your dirt brothers.',
		'franceDescription' => 'a map of France to find riders and spots near you.',
		'usersDescription' => 'a comprehensive list of the members of this site.',
		'spotsDescription' => 'submit your favorite spot to our database, that\'s bound to make somebody\'s day sooner or later.',
		'tricksDescription' => 'the list of tricks you can pull on a mountainboard is not infinite, but it\s damn close.. If you\'re looking for inspiration or advice, this is the place!',
		'blogsDescription' => 'with mountainboarding blogs, tell us all about your life on a mountainboard.',

	// Contact
		'contactTitle' => 'contact',
		'contactPageDescription' => 'if you need to contact us, please do so using the following form, we\'ll do our best to reply as quickly as possible.',
		'contactMessageHint' => 'type your message',
		'message' => 'message',
		'sendMessage' => 'send',
		'contactEmail_Html' => $contactEmail_Html,
		'contactEmail_Txt' => $contactEmail_Txt,
		'contactSuccess' => 'your message was sent successfully. We\'ll do our best to reply as quickly as possible.',
		'contactFailure' => '<p>A problem prevented your message from reaching us.</p><p>We were just informed of the problem, but in the meantime, you may choose to <a href="http://www.facebook/mountainboardfr">contact us on Facebook</a></p>',

	// Search
		'searchResultsInfo' => 'found %s result(s).',


	// Chat
		'chatJoinNotification' => 'entered the chat room.',
		'chatLeaveNotification' => 'left the chat room.',

	// Google +1
		'likeUsOnGoogle' => 'click the +1 button if you like %s!',
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