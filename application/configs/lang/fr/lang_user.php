<?php
$contactUrl = APP_URL.'/contact';

$user = array(
	// Guests
		'guest' => 'invité(e)',
		'guests' => 'invité(e)s',

	// Login Forms
        'email' => 'email',
        'password' => 'mot de passe',
        'passwordConfirm' => 'mot de passe (confirmation)',
        'passwordOld' => 'ancien mot de passe',
        'rememberMe' => 'rester connecté',
        'login' => 'connexion',
        'loginpage' => 'connexion',
        'logout' => 'déconnexion',
        'identity' => 'identité',
        'openIDIdentity' => 'compte OpenId',
		'noAccountRegister' => 'Pas encore de compte ? Crées-en un !',
		'homePageLogin' => 'mon compte',
		'loginLinkTitle' => 'connecte toi à %s avec ton compte existant',
		'registerLinkTitle' => 'crée un nouveau compte pour te connecter à %s',

    // Login error messages
        'wrongPassword' => 'mot de passe erroné',
        'unexpectedLoginMethod' => 'méthode de login inconnue',
        'loginError_noSuchUser' => 'ce pseudo n\'existe pas',
        'loginError_ambiguousUser' => 'le pseudo est ambigü',
        'loginError_invalidLoginPostData' => 'données erronées pour le login',
        'loginError_openIDNotActive' => 'open ID est désactivé',
        'loginError_missingPostIdentity' => 'tu dois entrer un identifiant OpenId valide',
        'loginError_missingGetIdentity' => 'pas d\'identité GET',
        'loginError_noUserForGivenIdentity' => 'il n\'y a pas de compte attaché à cette identité OpenID',
        'loginError_directRequesNotAllowed' => 'requête directe non autorisée',
        'loginError_defaultErrorMessage' => 'une erreur inconnue empêche le login',
        'loginError_invalidPwOrNN' => 'le pseudo ou le mot de passe est incorrect',
        'loginError_mustBeLoggedIn' => 'tu dois être loggé pour accéder à cette page',
        'activationFailed' => 'l\'activation du compte a échoué',
        'subscriptionEmailTitle' => 'activation de ton compte sur '.GLOBAL_DOMAIN_FULL,
        'userConfirmation_alreadyDone' =>'ton compte était déjà activé !',
        'userConfirmation_success' =>'ton compte a bien été activé! tu peux maintenant utiliser la totalité du site !',
        'userConfirmation_failure' =>'impossible d\'activer ton compte. Merci de contacter l\'administrateur du site.',
        'errorLoggedIn' =>'impossible d\'accéder à cette page en etant loggé(e)',
        'noSuchUser' =>'il n\'y a aucun utilisateur sous ce nom',
        'newPasswordFailed' =>'une erreur est apparue, le nouveau mot de passe n\'a pas pu être généré',
        'newPasswordEmailFailed' =>'impossible d\'envoyer l\'email avec le nouveau mot de passe',
        'newPasswordActivationFailed' =>'impossible d\'activer le nouveau mot de passe',
        'lostPasswordEmailTitle' => 'nouveau mot de passe pour '.GLOBAL_DOMAIN_FULL,
        'badOpenIdIdentity' => 'cet identifiant OpenId n\'est pas reconnu',
        'resourceAccessDenied' => 'tu n\'as pas le droit d\'accéder à cet élément',
		'loginPageTitle' => 'connexion',

    // User Controller

        // registration
        'register' =>'s\'enregistrer',
		'loginInOrderTo' => 'me connecter pour',
		'loginOrRegisterInOrderTo' => 'me connecter ou m\'enregistrer pour',
        'userregister' =>'m\'enregistrer!',
        'userupdate' =>'modifier mon profil',
        'registerButton' =>'créer mon compte',
        'registerTitle' =>'créer mon compte',
		'createMyAccountText1' => 'pour profiter au maximum du site, pour poster des photos, poser des questions, etc. il te faut un compte!',
		'createMyAccountText2' => 'et pour prouver que tu n\'es pas un robot de la mafia russe venu ici pour laisser des messages douteux, il y a une question piège, à la fin du formulaire. On t\'aide un peu: <strong>la réponse, c\'est "slide".</strong>',
		'registrationError_alreadyRegistered' => 'tu es déjà enregistré(e)',
        'usernameExists' => 'ce pseudo est déjà pris !',
        'usernameDoesNotExist' => 'ce pseudo n\'existe pas !',
        'emailExists' => 'cet email est déjà utilisé',
        'emailInvalid' => 'email invalide',
        'registrationError_failure' => 'une erreur est survenue lors de la création de ton compte',
        'registrationError_emailFailure' => 'une erreur est survenue pendant l\'envoi de ton email de confirmation',
		'registrationError_updateFailure' => 'une erreur est survenue lors de la mise à jour de ton compte',
        'userAccountNotValidated_msg1' => 'ton compte a bien été créé!</p><p class="paragraph">Cependant, il n\'est pas encore validé (suis les instructions dans l\'email que l\'on vient de t\'envoyer). En attendant, pourquoi ne pas remplir ton profil ?',
        'authMethod' => 'méthode de login',
        'openIdExists' => 'cette identité est déjà utilisée',

        // update
        'firstName' => 'prénom',
        'lastName' => 'nom',
        'age' => 'âge',
        'country' => 'pays',
        'city' => 'ville',
        'zip' => 'code postal',
        'location' => 'position',
        'level' => 'niveau',
        'ride' => 'ride',
        'birthDate' => 'date de naissance',
        'gender' => 'genre',
        'male' => 'masculin',
        'female' => 'féminin',
        'level' => 'niveau de ride',
        'level_beginner' => 'débutant',
        'level_intermediate' => 'intermédiaire',
        'level_pro' => 'pro',
        'site' => 'site internet',
        'occupation' => 'métier',
        'avatarUrl' => 'avatar (url externe - fichiers JPG ou PNG uniquement)',
        'avatarFile' => 'avatar (nouveau fichier - JPG ou PNG uniquement)',
        'gear' => 'matos',
        'otherSports' => 'autres sports',
        'rideType' => 'pratique',
        'rideType_freeride' => 'freeride',
        'rideType_freestyle' => 'freestyle',
        'rideType_kite' => 'kite',
        'doUpdateProfile'=>'mettre à jour le profil',

		// anonymous users
		'anonymousUserName' => 'anonyme',
		'anonymousUserTitle' => 'visible par les utilisateurs enregistrés',

		//confirmation
		'userUpdateWaitForConfirmation' => 'Ton profil a bien été créé! <br/>Tu n\'as plus qu\'à attendre de recevoir l\'email de confirmation maintenant !</p><p>En cas de problème, ou si tu ne reçois pas le mail, écris à <a href="mailto'.APP_EMAIL_CONTACT.'">'.APP_EMAIL_CONTACT.'</a>...',

        //lost password
        'lostpassword' => 'mot de passe perdu?',
        'sendPasswordButton' => 'je veux un nouveau mot de passe !',
		'newPasswordActivated' => 'ton nouveau mot de passe est activé !<br/>Pour le changer, clique sur: ',
		'newPasswordActivatedForm' => 'ton nouveau mot de passe est activé !<br/>Pour le changer, utilise le formulaire suivant:<br/>',

		'actionLinkModalInstructions' => 'merci de t\'identifier pour continuer',

		// profile page
		'realName' => 'vrai nom',
		'livesIn' => 'vit à',
		'lastLoggedIn' => 'dernière visite',
		'userAlbum' => 'album',
		'userBlog' => 'blog',

		'privatemessageshome' => 'messages',
		'toUser' => 'destinataire',
		'userProfileUpdated' => 'Profil mis à jour ! Tu vas être %s redirigé(e) %s',

		// newstuff
		'newStuffFor' => 'Nouveaux trucs à voir pour %s',

		// suggestions
		'userActionSuggestion_header' => 'en attendant...',
		'userActionSuggestion_Location_link' => 'Indique où tu trouves sur une carte',
		'userActionSuggestion_Location_text' => '%s pour trouver d\'autres riders près de chez toi!',

		'userActionSuggestion_Avatar_link' => 'ajoutes-en une!',
		'userActionSuggestion_Avatar_text' => 'On dirait que tu n\'as pas de photo pour ton profil, %s',

		'userActionSuggestion_NewPhoto_link' => 'ca te dit d\'en envoyer une maintenant?',
		'userActionSuggestion_NewPhoto_text' => 'Ca fait un moment que tu n\'as pas posté de photo, %s',

		'userActionSuggestion_FirstPhoto_link' => 'envoies-en une tout de suite!',
		'userActionSuggestion_FirstPhoto_text' => 'Tu n\'as pas encore posté une seule photo, sacrebleu! N\'attends plus, %s',
);


// EMAILS
$user['creationConfirmationEmail_Html'] = <<<EMAIL
<p>Salut %s !</p>

<p>Ton compte sur %s est bien enregistré, il ne te reste plus qu'à l'activer !</p>

<p>Clique sur ce lien pour terminer ton inscription: <a href='%s'>[activer mon compte]</a></p>

<p>Au cas où, on te rappelle le %s que tu as choisi: %s</p>

<p>A bientôt sur %s !</p>

<p>Le webmaster</p>
EMAIL;

$user['creationConfirmationEmail_Txt'] = <<<EMAIL
Salut %s !

Ton compte sur %s est bien enregistré, il ne te reste plus qu'à l'activer !

Clique sur ce lien pour terminer ton inscription: %s

Au cas où, on te rappelle le %s que tu as choisi: %s

A bientôt sur %s !

Le webmaster
EMAIL;

// Lost password - HTML Version
$user['lostPasswordEmail_Html'] = <<<EMAIL
<p>Salut %s !</p>

<p>
    On a reçu une demande de génération de nouveau mot de passe pour toi.<br/>
    Si tu n'as pas fait cette demande, tu peux ignorer cet email, et on s'excuse (oui, on s'excuse!) du dérangement.
</p>

<p>
    En revanche, si c'est bien toi qui as fait cette demande, il te faut maintenant cliquer sur ce lien :
    <a href='%s'>[activer mon nouveau mot de passe]</a>
</p>

<p>Une fois que tu auras cliqué sur le lien, ton mot de passe deviendra: <strong>%s</strong></p>

<p>N'hésite pas à <a href="{$contactUrl}">nous contacter en cas de problème</a> avec cette procédure.</p>

<p>Le webmaster</p>
EMAIL;

$user['lostPasswordEmail_Txt'] = <<<EMAIL
Salut %s !

On a reçu une demande de génération de nouveau mot de passe pour toi.
Si tu n'as pas fait cette demande, tu peux ignorer cet email, et on s'excuse (oui, on s'excuse!) du dérangement.

En revanche, si c'est bien toi qui as fait cette demande, il te faut maintenant cliquer sur ce lien : %s

Une fois que tu auras cliqué sur le lien, ton mot de passe deviendra: %s

N'hésite pas à nous contacter en cas de problème avec cette procédure: {$contactUrl}

Le webmaster
EMAIL;
