<?php
$data = array(
    'exists' => 'this element already exists !',
    'doesNotExist' => 'this element does not exist !',

    'dptExists' => 'this region already exists!',
    'dptDoesNotExist' => 'this region does not exist !',
	'albumTypeNotAllowed' => 'album type not allowed',

    User::INPUT_USERNAME => 'username',
    'title' => 'title',
    'content' => 'content',
    'username' => 'username',
    'description' => 'description',
    'submitter' => 'submitted by',
    'postedOn' => 'posted on',
    'posted' => 'posted',
    'lastEditor' => 'edited by',
    'lastEditionDate' => 'last edited',
    'author' => 'author',
    'organiser' => 'organiser',
    'authorName' => 'authorName',
    'photoBy' => 'taken by',
    'videoBy' => 'filmed by',
    'writtenBy' => 'written by',
    'createdBy' => 'created by',
    'valid' => 'valid',
    'invalid' => 'invalid',
	'clickToValidate' => 'click to validate',
	'clickToInvalidate' => 'click to invalidate',
	'status' => 'status',
    'tags' => 'tags',
    'skipAutoFields' => 'edit auto. fields',
    'dataCouldNotBeSaved' => 'an error occured and data could not be saved',
    'dataSaved' => 'data was saved, you will be redirected to it shortly... or you can click this',
    'difficulty' => 'difficulty',
    'comment' => 'comment',
	'deleteError' => 'an error occured and this item could not be deleted',
	'shownOnce' => 'shown once',
	'shownNTimes' => 'shown %s times',
    'organisedBy' => 'organised by',

	'tone' 			=> 'tone',
    'toneNeutral' 	=> 'neutral',
    'toneJoke' 		=> 'joke',
    'toneBritish' 	=> 'british humor',
    'toneLameJoke' 	=> 'lame joke',
    'tonePeace' 	=> 'peace, man !',
    'toneHappy' 	=> 'happy',
    'toneGotcha' 	=> 'gotcha !',
    'toneUpset' 	=> 'upset',
    'toneSad' 		=> 'sad',

    'postTitle'		=> 'this message',

    'dpt' => 'region',
    'dptlist' => 'in France',
    'dptClickOut1' => 'you clicked outside the specified region !',
    'dptClickOut2' => 'do you still want to use these coordinates ?',
    'showStreetView' => 'Streetview mode',
    'hideStreetView' => 'normal mode',
    'contentInDptFr' => 'le mountainboard %s%s',
    'contentInDpt' => 'mountainboarding in %s',

    'trickQuestion' => 'trick question',

	// COMMENTS
		'commentSavingError' => 'an error occured and your comment could not be saved',

    // SPOTS
        'spotType' => 'spot type',
        'spotTypeFreeride' => 'freeride',
        'spotTypeDownhill' => 'downhill',
        'spotTypeUrban' => 'urban',
        'spotTypeSkatepark' => 'skatepark',
        'spotTypeCenter' => 'mountainboard center',
        'spotTypeDirtMTB' => 'mountainbike trail',
        'spotTypeDirtBMX' => 'BMX trail',
        'spotTypeBoarderX' => 'boarderX',
        'spotTypeOther' => 'other',

        'groundType' => 'ground type',
        'groundTypeGrass' => 'grass',
        'groundTypeForest' => 'forest',
        'groundTypeDirt' => 'dirt',
        'groundTypeAsphalt' => 'asphalt',
        'groundTypeGravel' => 'gravel',
        'groundTypeWood' => 'wood',
        'groundTypeMetal' => 'metal',
        'groundTypeConcrete' => 'concrete',
        'groundTypeOther' => 'other',

    	'addLocationToThisSpot' => 'do you know where this is? place it on the map!',

    // TESTS
    	'category' => 'category',
    	Test_Category::BOARDS => 'board review',
    	Test_Category::ACCESSORIES => 'accessory review',
    	Test_Category::PADS => 'pads and protections review',
    	Test_Category::MISC => 'misc.',

    // EVENTS
    	'eventType' => 'event type',
    	'compLevel_national' => 'national competition',
    	'compLevel_worldSeries' => 'world championships',
    	'compLevel_european' => 'european mountainboard tour',
    	'compContent' => 'type of riding',
    	'compContent_boardercross' => 'boardercross',
    	'compContent_slopestyle' => 'slopestyle',
    	'compContent_slalom' => 'slalom',
    	'compContent_bigair' => 'bigair',
    	'compContent_downhill' => 'downhill',
    	'nextEvent' => 'next event',
    	'nextEvents' => 'next events',

    // LOCATIONS
        'longitude' => 'longitude',
        'latitude' => 'latitude',
        'zoom' => 'zoom',
        'yaw' => 'yaw',
        'pitch' => 'pitch',
        'mapType' => 'map type',
        Location::MAPTYPENORMAL => 'normal',
        Location::MAPTYPESATELLITE => 'satellite',
        Location::MAPTYPEHYBRID => 'hybrid',
        Location::MAPTYPEPHYSICAL => 'physical',
        Location::MAPTYPEEARTH => 'earth',
        Location::MAPTYPESTREETVIEW => 'streetview',
        'showMap' => 'show map',
        'hideMap' => 'hide map',
        'moreInThisRegion' => 'more in this region',

    // ADDLINKS
        'addSpotLink' => 'add a new spot',
        'addEventLink' => 'add a new event',
        'addTrickLink' => 'add a new trick',
        'addDossierLink' => 'add a new dossier',
        'addTestLink' => 'add a new review',
        'addNewsLink' => 'add a news article',
        'addForum_TopicLink' => 'new topic',
        'addForum_PostLink' => 'reply to this topic',
        'addCommentLink' => 'leave a comment',

    // ADDTITLES
        'addSpotTitle' => 'add a new spot',
        'addTrickTitle' => 'add a new trick',
        'addDossierTitle' => 'add a new dossier',
        'addTestTitle' => 'add a new review',
        'addNewsTitle' => 'add a news article',

    // EDITLINKS
        'editSpotTitle' => 'edit a spot',
        'editTrickTitle' => 'edit a trick',
        'editDossierTitle' => 'edit a dossier',
        'editTestTitle' => 'edit a review',
        'editNewsTitle' => 'edit a news article',
        'editForum_TopicTitle' => 'edit a topic',
        'editForum_PostTitle' => 'edit a post',
        'editCommentTitle' => 'edit a comment',
        'editAlbumTitle' => 'edit an album',

	// BACKTO LINKS
		'backTo' => 'back to %s',

	// DELETE CONFIRMATIONS
    	'deleteConfirmationTitle' => 'deleting is irreversible!',
        'deleteConfirm' => 'are you sure you want to delete %s ?',
        'redirectAfterDeletion' => 'delete done ! You are being %s redirected %s',

    // PRIVATE MESSAGES
    	'messageWriteSuccessful' => 'message successfully sent. Back to %s?',
    	'messageWriteError' => 'the message could not be sent. Sorry!!',
        'replyToMessageFrom' => 'reply to the message from',
        'messageFrom' => 'message from %s',

	// UPLOADS
		'uploadButtonText' => 'upload files',
        'uploadSuccessfullyUploaded' => 'Successfully uploaded file',
        'uploadFileDescGeneral' => 'Image Files (*.jpg, *.jpeg, *.png, *.gif), Office Documents (*.xls, *.doc, *.ppt), PDF (*.pdf), Flash (*.swf)',
        'uploadReplaceMsg' => 'do you want want to replace',

    //ITEMS
		'itemSing_'.Constants_DataTypes::ALBUM 			=> 'album',
		'itemSing_'.Constants_DataTypes::BLOGPOST 		=> 'blog post',
		'itemSing_'.Constants_DataTypes::COMMENT 		=> 'comment',
		'itemSing_'.Constants_DataTypes::DATA 			=> 'data',
		'itemSing_'.Constants_DataTypes::DOSSIER 		=> 'dossier',
		'itemSing_'.Constants_DataTypes::DPT 			=> 'region',
		'itemSing_'.Constants_DataTypes::EVENT 			=> 'event',
		'itemSing_'.Constants_DataTypes::FORUM 			=> 'forum',
        'itemSing_'.Constants_DataTypes::MEDIAALBUM 	=> 'album',
		'itemSing_'.Constants_DataTypes::NEWS 			=> 'news',
		'itemSing_'.Constants_DataTypes::PHOTO 			=> 'photo',
		'itemSing_'.Constants_DataTypes::FORUMPOST 		=> 'forum message',
		'itemSing_'.Constants_DataTypes::PRIVATEMESSAGE => 'private message',
		'itemSing_'.Constants_DataTypes::FORUMTOPIC 	=> 'forum topic',
		'itemSing_'.Constants_DataTypes::TEST 			=> 'review',
        'itemSing_'.Constants_DataTypes::TRICK 			=> 'trick',
		'itemSing_'.Constants_DataTypes::SPOT 			=> 'spot',
		'itemSing_'.Constants_DataTypes::USER 			=> 'rider',
		'itemSing_'.Constants_DataTypes::VIDEO 			=> 'video',

        'itemPlur_'.Constants_DataTypes::ALBUM 			=> 'albums',
        'itemPlur_'.Constants_DataTypes::BLOGPOST		=> 'blog posts',
		'itemPlur_'.Constants_DataTypes::COMMENT 		=> 'comments',
		'itemPlur_'.Constants_DataTypes::DATA 			=> 'data',
		'itemPlur_'.Constants_DataTypes::DOSSIER 		=> 'dossiers',
		'itemPlur_'.Constants_DataTypes::DPT 			=> 'regions',
		'itemPlur_'.Constants_DataTypes::EVENT 			=> 'events',
        'itemPlur_'.Constants_DataTypes::FORUM 			=> 'forums',
        'itemPlur_'.Constants_DataTypes::MEDIAALBUM 	=> 'albums',
		'itemPlur_'.Constants_DataTypes::NEWS 			=> 'news',
		'itemPlur_'.Constants_DataTypes::PHOTO 			=> 'photos',
		'itemPlur_'.Constants_DataTypes::FORUMPOST 		=> 'forum messages',
		'itemPlur_'.Constants_DataTypes::PRIVATEMESSAGE => 'private messages',
		'itemPlur_'.Constants_DataTypes::FORUMTOPIC 	=> 'forum topics',
		'itemPlur_'.Constants_DataTypes::TEST 			=> 'reviews',
        'itemPlur_'.Constants_DataTypes::TRICK 			=> 'tricks',
		'itemPlur_'.Constants_DataTypes::SPOT 			=> 'spots',
		'itemPlur_'.Constants_DataTypes::USER 			=> 'riders',
		'itemPlur_'.Constants_DataTypes::VIDEO 			=> 'videos',

	// NEW ITEMS
		'newItemSing_'.Constants_DataTypes::ALBUM 			=> 'new album',
		'newItemSing_'.Constants_DataTypes::BLOGPOST 		=> 'new blog post',
        'newItemSing_'.Constants_DataTypes::COMMENT 		=> 'new comment',
		'newItemSing_'.Constants_DataTypes::DATA 			=> 'new data',
		'newItemSing_'.Constants_DataTypes::DOSSIER 		=> 'new dossier',
		'newItemSing_'.Constants_DataTypes::DPT 			=> 'new region',
		'newItemSing_'.Constants_DataTypes::EVENT 			=> 'new event',
		'newItemSing_'.Constants_DataTypes::FORUM 			=> 'new forum',
        'newItemSing_'.Constants_DataTypes::MEDIAALBUM 		=> 'new album',
		'newItemSing_'.Constants_DataTypes::NEWS 			=> 'news',
		'newItemSing_'.Constants_DataTypes::PHOTO 			=> 'new photo',
		'newItemSing_'.Constants_DataTypes::FORUMPOST 		=> 'new forum message',
		'newItemSing_'.Constants_DataTypes::PRIVATEMESSAGE 	=> 'new private message',
		'newItemSing_'.Constants_DataTypes::FORUMTOPIC 		=> 'new forum topic',
		'newItemSing_'.Constants_DataTypes::TEST 			=> 'new review',
        'newItemSing_'.Constants_DataTypes::TRICK 			=> 'new trick',
		'newItemSing_'.Constants_DataTypes::SPOT 			=> 'new spot',
		'newItemSing_'.Constants_DataTypes::USER 			=> 'new user',
		'newItemSing_'.Constants_DataTypes::VIDEO 			=> 'new video',

        'newItemPlur_'.Constants_DataTypes::ALBUM 			=> 'new albums',
        'newItemPlur_'.Constants_DataTypes::BLOGPOST		=> 'new blog posts',
        'newItemPlur_'.Constants_DataTypes::COMMENT 		=> 'new comments',
		'newItemPlur_'.Constants_DataTypes::DATA 			=> 'new data',
		'newItemPlur_'.Constants_DataTypes::DOSSIER 		=> 'new dossiers',
		'newItemPlur_'.Constants_DataTypes::DPT 			=> 'new regions',
		'newItemPlur_'.Constants_DataTypes::EVENT 			=> 'new events',
		'newItemSing_'.Constants_DataTypes::FORUM 			=> 'new forums',
        'newItemPlur_'.Constants_DataTypes::MEDIAALBUM 		=> 'new albums',
		'newItemPlur_'.Constants_DataTypes::NEWS 			=> 'news',
		'newItemPlur_'.Constants_DataTypes::PHOTO 			=> 'new photos',
		'newItemPlur_'.Constants_DataTypes::FORUMPOST 		=> 'new forum messages',
		'newItemPlur_'.Constants_DataTypes::PRIVATEMESSAGE 	=> 'new private messages',
		'newItemPlur_'.Constants_DataTypes::FORUMTOPIC 		=> 'new forum topics',
		'newItemPlur_'.Constants_DataTypes::TEST 			=> 'new reviews',
        'newItemPlur_'.Constants_DataTypes::TRICK 			=> 'new tricks',
		'newItemPlur_'.Constants_DataTypes::SPOT 			=> 'new spots',
		'newItemPlur_'.Constants_DataTypes::USER 			=> 'new users',
		'newItemPlur_'.Constants_DataTypes::VIDEO 			=> 'new videos',

	// RANDOM ADDITIONAL CONTENT
		'random_'.Constants_DataTypes::ALBUM 			=> 'random album',
		'random_'.Constants_DataTypes::BLOGPOST 		=> 'random blog post',
        'random_'.Constants_DataTypes::COMMENT 			=> 'random comment',
		'random_'.Constants_DataTypes::DATA 			=> 'random data',
		'random_'.Constants_DataTypes::DOSSIER 			=> 'random dossier',
		'random_'.Constants_DataTypes::DPT 				=> 'random region',
		'random_'.Constants_DataTypes::EVENT 			=> 'random event',
		'random_'.Constants_DataTypes::MEDIAALBUM 		=> 'random album',
		'random_'.Constants_DataTypes::NEWS 			=> 'random news',
		'random_'.Constants_DataTypes::PHOTO 			=> 'random photo',
		'random_'.Constants_DataTypes::FORUMPOST 		=> 'random forum message',
		'random_'.Constants_DataTypes::PRIVATEMESSAGE 	=> 'random private message',
		'random_'.Constants_DataTypes::FORUMTOPIC 		=> 'random forum topic',
		'random_'.Constants_DataTypes::TEST 			=> 'random review',
		'random_'.Constants_DataTypes::TRICK 			=> 'random trick',
        'random_'.Constants_DataTypes::SPOT 			=> 'random spot',
		'random_'.Constants_DataTypes::USER 			=> 'random user',
		'random_'.Constants_DataTypes::VIDEO 			=> 'random video',

	// NEW STUFF
        'showStuffPosted' => 'show stuff posted',
        'notificationNewItemsSince' => 'new stuff posted %s (%s)',
        'overLastMonth' => 'over the last month',
        'overLastWeek' => 'over the last week',
        'overLastDay' => 'over the last day',
        'sinceLastVisit' => 'since my last visit',
        'notificationNewItemsFromUntil' => 'stuff posted between %s and %s',

        'notificationNewMetaDataAlso' => 'also check out',
		'notificationNewMetaData' => 'check out this new stuff',

        'noNewStuff' => 'no new stuff yet !',

        'backToHomePage' => 'back to my page',
        'updateNotifications' => 'update my preferences',
        'notificationEdition' => 'new stuff notifications - edition',
        'notificationUpdateSuccessful' => 'update successful',
        'notificationUpdateFailed' => 'an error occured while saving your notification preferences. Please try again later !',

	// HINTS
		'titleHint' => 'enter a title.',
		'descriptionHint' => 'explain what this is about.',
		'contentHint' => 'enter the content.',
		'photoHint' => 'select a photo from your computer (supported formats: JPG, PNG)',
		'videoHint' => 'paste here the code for this video (supported sites: Vimeo, YouTube, Dailymotion)',
        'trickHint' => 'type the name of a trick... if there is one!',
		'spotHint' => 'type the name of a spot if you know it.',
		'ridersHint' => 'type the name of the rider(s) if you know them, press TAB to switch to the next one.',
		'tagsHint' => 'type keywords describing this page, press TAB to start another word.',
		'userNameHint' => 'type the name of that person.',
		'pickYourUserNameHint' => 'pick a (unique) username.',
		'enterYourUserNameHint' => 'enter your username.',
        'dptHint' => 'pick the name of that region.',
        'countryHint' => 'pick the name of that country.',
        'difficultyHint' => 'pick a difficulty level (1 = easy, 5 = difficult).',
		'spotTypeHint' => 'pick the type of spot.',
		'spotGroundTypeHint' => 'pick the type of ground for this spot.',
        'trickQuestionHint'=> 'answer the question',

	// LIST TITLES
		Constants_DataTypes::BLOG.'ListTitle' => 'mountainboard blogs',
        Constants_DataTypes::DOSSIER.'ListTitle' => 'dossiers',
		Constants_DataTypes::DPT.'ListTitle' => 'french départements',
		Constants_DataTypes::EVENT.'ListTitle' => 'mountainboarding events',
		Constants_DataTypes::NEWS.'ListTitle' => 'archives',
		Constants_DataTypes::TEST.'ListTitle' => 'mountainboard and gear reviews',
		Constants_DataTypes::TRICK.'ListTitle' => 'mountainboard tricks',
		Constants_DataTypes::SPOT.'ListTitle' => 'mountainboard spots',

		// LIST DESCRIPTIONS
		Constants_DataTypes::BLOG.'ListDescription' => 'everyone can have their mountainboarding blog here. This is the list.',
		Constants_DataTypes::DOSSIER.'ListDescription' => 'here\'s the list of all the dossiers we put together.',
		Constants_DataTypes::DPT.'ListDescription' => 'France is made up of 100 \'départements\'. Pick one and see what each has to offer to a mountainboarder.',
		Constants_DataTypes::EVENT.'ListDescription' => 'all the events from our database are listed here.',
		Constants_DataTypes::NEWS.'ListDescription' => 'if you\'re interested in older mountainboarding news, you should be happy with this. These articles go back to 2002!',
		Constants_DataTypes::TEST.'ListDescription' => 'listed below are all our reviews for mountainboards, pads and accessories',
		Constants_DataTypes::TRICK.'ListDescription' => 'all the tricks in the book! Or at least we hope so. If you know one that\'s missing, feel free to add it!',
		Constants_DataTypes::SPOT.'ListDescription' => 'all the spots in France that have been reported so far. Feel free to add more!',

	// COUNTRIES
		'gotoDptSubmit' => 'Go to that region',
		'franceDesc' => 'it\'s not that easy to find great spots to ride in France, just yet. If you\'re looking for riders or spots nearby, this is where it\'s at.',
		'countryTextDefault' => 'having a hard time finding other riders, or cool spots nearby?. We may have the answer here!',
		'locationString' => 'location',
		'regionTabMap' => 'map',
		'regionTabList' => 'list',
		'regionTabDpt' => 'regions',
	);