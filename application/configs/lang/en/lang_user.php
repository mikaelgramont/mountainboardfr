<?php
$contactUrl = APP_URL.'/contact';

$user = array(
	// Guests
		'guest' => 'guest',
		'guests' => 'guests',

    // Login Forms
        'email' => 'email',
        'password' => 'password',
        'passwordConfirm' => 'password (confirmation)',
        'passwordOld' => 'old password',
        'rememberMe' => 'remember me',
        'login' => 'login',
        'loginpage' => 'login',
        'logout' => 'logout',
        'identity' => 'identity',
        'openIDIdentity' => 'OpenId Account',
		'noAccountRegister' => 'No account yet ? Create one !',
		'homePageLogin' => 'my account',
		'loginLinkTitle' => 'connect to %s with your existing account',
		'registerLinkTitle' => 'create a new account to connect to %s',

    // Login error messages
        'wrongPassword' => 'wrong password',
        'unexpectedLoginMethod' => 'unknown login method',
        'loginError_noSuchUser' => 'no such nickname',
        'loginError_ambiguousUser' => 'nickname is ambiguous',
        'loginError_invalidLoginPostData' => 'bad data for login',
        'loginError_openIDNotActive' => 'openID is deactivated',
        'loginError_missingPostIdentity' => 'you must enter a valid OpenId identity',
        'loginError_missingGetIdentity' => 'no GET identity',
        'loginError_noUserForGivenIdentity' => 'there is no user attached to this OpenID identity',
        'loginError_directRequesNotAllowed' => 'direct request not allowed',
        'loginError_defaultErrorMessage' => 'an unexpected error prevented you from logging in.',
        'loginError_invalidPwOrNN' => 'wrong nickname or password',
        'loginError_mustBeLoggedIn' => 'you must be logged in to access this page',
        'activationFailed' => 'activation of account failed',
        'subscriptionEmailTitle' => 'account activation for '.GLOBAL_DOMAIN_FULL,
        'userConfirmation_alreadyDone' =>'your account was already activated !',
        'userConfirmation_success' =>'your account is activated, you can now access all of the website !',
        'userConfirmation_failure' =>'an error occured while activating your account. Please contact the webmaster.',
        'errorLoggedIn' =>'you can\'t access this page while logged in',
        'noSuchUser' =>'there is no user by that name',
        'newPasswordFailed' =>'an error occured while generating a new password',
        'newPasswordEmailFailed' =>'an error occured while sending the email containing your new password',
        'newPasswordActivationFailed' =>'an error occured while activating your new password',
        'lostPasswordEmailTitle' => 'new password for '.GLOBAL_DOMAIN_FULL,
        'badOpenIdIdentity' => 'this OpenId identity seems invalid',
        'resourceAccessDenied' => 'you cannot access this element',
		'loginPageTitle' => 'login',

    // User Controller
        // registration
        'register' =>'register',
		'loginInOrderTo' => 'login to',
		'loginOrRegisterInOrderTo' => 'login or register to',
        'userregister' =>'register !',
        'userupdate' =>'change my profile',
        'registerButton' =>'create my account',
        'registerTitle' =>'create my account',
		'createMyAccountText1' => 'to make the most of the website, to post photos, ask questions, etc. you need an account!',
		'createMyAccountText2' => 'and to make sure you\'re not a russian mafia bot sent here to spam everyone, there\'s a trick question at the end of the form. Because we know mountainboarders aren\'t the brightest, <strong>the answer is "slide"</strong>.',
		'registrationError_alreadyRegistered' => 'you are already registered',
        'usernameExists' => 'this username already exists !',
        'usernameDoesNotExist' => 'this user does not exist !',
        'emailExists' => 'this email is already used',
        'emailInvalid' => 'invalid email',
        'registrationError_failure' => 'an error occured while trying to create your account',
        'registrationError_emailFailure' => 'an error occured while trying to send you your confirmation email',
		'registrationError_updateFailure' => 'an error occured while trying to update your account',
        'userAccountNotValidated_msg1' => 'your account has been created!</p><p class="paragraph">However, it is not validated yet (follow the instructions in the email we sent you to validate it). In the meantime, why not fill out your profile ?',
        'authMethod' => 'login method',
        'openIdExists' => 'this identity is already used',

          // update
        'firstName' => 'first name',
        'lastName' => 'last name',
        'age' => 'age',
        'country' => 'country',
        'city' => 'city',
        'zip' => 'zip code',
        'location' => 'location',
        'level' => 'level',
        'ride' => 'ride',
        'birthDate' => 'date of birth',
        'gender' => 'gender',
        'male' => 'male',
        'female' => 'female',
        'level' => 'riding level',
        'level_beginner' => 'beginner',
        'level_intermediate' => 'intermediate',
        'level_pro' => 'pro',
        'site' => 'website',
        'occupation' => 'occupation',
        'avatarUrl' => 'avatar (external url - JPG or PNG files only)',
        'avatarFile' => 'avatar (new file - JPG or PNG files only)',
        'gear' => 'gear',
        'otherSports' => 'other sports',
        'rideType' => 'ride type',
        'rideType_freeride' => 'freeriding',
        'rideType_freestyle' => 'freestyle',
        'rideType_kite' => 'kiteboarding',
        'doUpdateProfile'=>'update my profile',

		// anonymous users
		'anonymousUserName' => 'anonymous',
		'anonymousUserTitle' => 'name available to registered users',

		//confirmation
		'userUpdateWaitForConfirmation' => '<p>Your profile has been updated, <br/>Now you just need to wait for the confirmation email !</p><p>In case of problem, or if you don\'t receive the email, write to <a href="mailto'.APP_EMAIL_CONTACT.'">'.APP_EMAIL_CONTACT.'</a>...</p>',

        //lost password
        'lostpassword' => 'lost password?',
        'sendPasswordButton' => 'I need a new password !',
		'newPasswordActivated' => 'your new password is activated !<br/>To change it, click on:',
		'newPasswordActivatedForm' => 'your new password is activated !<br/>To change it, use the following form:<br/>',

		'actionLinkModalInstructions' => 'please identify yourself to continue',

		// profile page
		'realName' => 'real name',
		'livesIn' => 'lives in',
		'lastLoggedIn' => 'last visit',
		'userAlbum' => 'album',
		'userBlog' => 'blog',

		'privatemessageshome' => 'messages',
		'toUser' => 'recipient',
		'userProfileUpdated' => 'Profile updated ! You are being %s redirected %s',

		// newstuff
		'newStuffFor' => 'New things to see for %s',

		// suggestions
		'userActionSuggestion_header' => 'in the meantime...',
		'userActionSuggestion_Location_link' => 'Indicate your location on a map',
		'userActionSuggestion_Location_text' => '%s so that you get a better chance to meet local riders!',

		'userActionSuggestion_Avatar_link' => 'add one now!',
		'userActionSuggestion_Avatar_text' => 'Your profile doesn\'t seem to have a picture yet, %s',

		'userActionSuggestion_NewPhoto_link' => 'care to do so now?',
		'userActionSuggestion_NewPhoto_text' => 'You haven\'t posted any picture in a little while, %s',

		'userActionSuggestion_FirstPhoto_link' => 'send one now!',
		'userActionSuggestion_FirstPhoto_text' => 'Wow, you haven\'t posted a single picture yet, don\'t wait, %s',
);

// EMAILS
// Creation Confirmation - HTML Version
$user['creationConfirmationEmail_Html'] = <<<EMAIL
<p>Hi %s !</p>

<p>Your account on %s is registered, all you need now is to activate it !</p>

<p>Click on the following link to finish the process: <a href='%s'>[activate my account]</a></p>

<p>Just in case, we want to remind you the %s you provided: %s</p>

<p>See ya soon on %s !</p>

<p>The webmaster</p>
EMAIL;

// Creation Confirmation - Text Version
$user['creationConfirmationEmail_Txt'] = <<<EMAIL
Hi %s !

Your account on %s is registered, all you need now is to activate it !

Open the following link to finish the process: %s

Just in case, we want to remind you the % you provided: %s

See ya soon on %s !

The webmaster
EMAIL;


// Lost password - HTML Version
$user['lostPasswordEmail_Html'] = <<<EMAIL
<p>Hi %s !</p>

<p>
    We have received a request to generate a new password for you.<br/>
    If you did not request a new password, you can ignore this email completely, and we apologize for annoying you.
</p>

<p>
    However, if you did request a new password, then you need to click on the following link to activate it:
    <a href='%s'>[activate my new password]</a>
</p>

<p>After you click this link, your password will be changed to: <strong>%s</strong></p>

<p> <a href="{$contactUrl}">Let us know</a> if you have any trouble with this procedure.</p>

<p>The webmaster</p>
EMAIL;

$user['lostPasswordEmail_Txt'] = <<<EMAIL
Hi %s !

We have received a request to generate a new password for you.
If you did not request a new password, you can ignore this email completely, and we apologize for annoying you.

However, if you did request a new password, then you need to click on the following link to activate it: %s

After you click this link, your password will be changed to: %s

Let us know if you have any trouble with this procedure: {$contactUrl}

The webmaster
EMAIL;
