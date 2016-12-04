<?php
class TempController extends Lib_Controller_Action
{
    public function init()
    {
    	parent::init();
    	if($this->_user->getId() != 1){
    		die();
    	}
    }

    public function genmorephotothumbsAction()
    {
    	set_time_limit(300);
    	
    	$table = new Media_Item_Photo();
    	$items = $table->fetchAll('mediaType = "photo"');
    	$photos = 0;
    	$errors = array();
    	foreach ($items as $item) {
			$photos++;
			list($status, $error) = $this->_processPhoto($item);
    		if ($status != true) {
				$errors[] = $error;    					
    		}
    	}
    	echo sprintf("<div>Photos: %s</div>", $photos);
    	echo sprintf("<div>Errors: <pre>%s</pre></div>", print_r($errors, true));
    	exit();
    }
    
    public function genmorevideothumbsAction()
    {
    	set_time_limit(300);
    	 
    	$table = new Media_Item_Video();
    	$where = 'mediaType = "video"';
    	$items = $table->fetchAll($where, null);
    	$videos = 0;
    	$errors = array();
    	foreach ($items as $item) {
			$videos++;
    		list($status, $error) = $this->_processVideo($item);
    		if ($status != true) {
    			$errors[] = $error;
    		}
    	}
    	echo sprintf("<div>Videos: %s</div>", $videos);
    	echo sprintf("<div>Errors: <pre>%s</pre></div>", print_r($errors, true));
    	exit();
    }
    
    protected function _processPhoto(Media_Item_Row $item)
    {
    	return array(true, 'already done');
    	try {
    		$original = new File_Photo(APP_MEDIA_DIR.'/'.$item->getURI());
    		$item->createAllThumbnailsFromOriginal($original);
    	} catch (Exception $e) {
    		return array(false, $e->getMessage());
    	}
    	return array(true, null);
    }
    
    protected function _processVideo(Media_Item_Row $item)
    {
    	//return array(false, 'not ready');
    	
    	try {
    		$targetName = Utils::cleanStringForFilename(
    			$item->title) . '_' . uniqid();
    		$tempFile = APP_MEDIA_DIR_RAW.DIRECTORY_SEPARATOR.'videothumbs'.
    				DIRECTORY_SEPARATOR.$targetName;
    		
    		$client = new Zend_Http_Client();
    		switch($item->mediaSubType) {
    			case Media_Item_Video::SUBTYPE_YOUTUBE:
    				$apiClient = new Google_Api_Youtube(GOOGLE_APIS_KEY, $client);
    				break;
    			case Media_Item_Video::SUBTYPE_DAILYMOTION:
    				$apiClient = new Dailymotion_Api($client);
    				break;
    			case Media_Item_Video::SUBTYPE_VIMEO:
    				$apiClient = new Vimeo_Api(VIMEO_TOKEN, $client);
    				break;
    			default:
    				throw new Lib_Exception_Media("Unsupported video provider: '".
      					$item->mediaSubType."'");
    				break;
    		}
   			$info = $apiClient->getThumbnailInfo($item->uri,
   				Media_Item_Row::SIZE_MEDIUM);
   			file_put_contents($tempFile, file_get_contents($info['thumbnailUri']));
   			
    		$original = new File_Photo($tempFile);
    		$original->renameAfterSubType();    		
    		$thumbs = $item->createAllThumbnailsFromOriginal($original);
    		$original->delete();
    		
    		$smallThumb = $thumbs[Media_Item_Row::SIZE_SMALL]; 
    		$item->thumbnailUri = $smallThumb->getName();
    		$item->thumbnailWidth = $smallThumb->getWidth();
    		$item->thumbnailHeight = $smallThumb->getHeight();
    		$item->save();
		} catch (Exception $e) {
    		return array(false, sprintf(
    			"Video %s:\n%s".PHP_EOL, $item->getId(), $e->getMessage()
    		));
    	}
    	return array(true, null);
    }
    
    public function updateLocationsAction()
	{
		/**
		 * @todo:
		 * - get all locations
		 * - save them
		 */
		$count = 0;
		$table = new Location();
		$locations = $table->fetchAll();
		foreach ($locations as $location) {
			if($location instanceof Country_Row || $location instanceof Dpt_Row){
				continue;
			}
			try{
				$location->save();
			} catch(Exception $e) {
				continue;
			}
			$count++;
		}

		$this->view->count = $count;
	}

    public function logAction()
	{
		$logger = new Logger($this->_user->getId());
	$logger->notFound('test');
	}

	public function savedptlocationsAction()
    {
        $table = new Location();
        if($table->find(1)->current()){
            die("table de locations non vide");
        }

        $dptFocalPoints = array (
            "67" => "48.599187, 7.586570",
            "68" => "47.865798, 7.222762",
            "24" => "45.142728, 0.703240",
            "33" => "44.883881, -0.474218",
            "40" => "44.009922, -0.698237",
            "47" => "44.369254, 0.468752",
            "64" => "43.187408, -0.881594",
            "03" => "46.367877, 3.141527",
            "15" => "45.049891, 2.717207",
            "43" => "45.085981, 3.786243",
            "63" => "45.771938, 3.186703",
            "14" => "49.092978, -0.356423",
            "50" => "49.093557, -1.343196",
            "61" => "48.576332, 0.057986",
            "21" => "47.465594, 4.792314",
            "58" => "47.119579, 3.538374",
            "71" => "46.655721, 4.544038",
            "89" => "47.855299, 3.594307",
            "22" => "48.458409, -2.787477",
            "29" => "48.232757, -4.264126",
            "35" => "48.172063, -1.652918",
            "56" => "47.744269, -2.884961",
            "18" => "47.024891, 2.426636",
            "28" => "48.447422, 1.374955",
            "36" => "46.812255, 1.536098",
            "37" => "47.223410, 0.709267",
            "41" => "47.659764, 1.414145",
            "45" => "47.913833, 2.320001",
            "08" => "49.698015, 4.709221",
            "10" => "48.320135, 4.124092",
            "51" => "48.961390, 4.218025",
            "52" => "48.132939, 5.259089",
            "20" => "42.056269, 9.150663",
            "25" => "47.066857, 6.380444",
            "39" => "46.782985, 5.729471",
            "70" => "47.638395, 6.096119",
            "90" => "47.624825, 6.950104",
            "27" => "49.075729, 1.049821",
            "76" => "49.661630, 0.928599",
            "75" => "48.858853, 2.347005",
            "77" => "48.618909, 2.975640",
            "78" => "48.761996, 1.837898",
            "91" => "48.530290, 2.250067",
            "92" => "48.840203, 2.241248",
            "93" => "48.910792, 2.445818",
            "94" => "48.774569, 2.461979",
            "95" => "49.071363, 2.101589",
            "11" => "43.054657, 2.464672",
            "30" => "43.960103, 4.053731",
            "34" => "43.592608, 3.366918",
            "48" => "44.542683, 3.490079",
            "66" => "42.625705, 2.450215",
            "19" => "45.343267, 1.877855",
            "23" => "46.059506, 1.992035",
            "87" => "45.919213, 1.270488",
            "54" => "48.956121, 6.274396",
            "55" => "49.013088, 5.371281",
            "57" => "49.020719, 6.765730",
            "88" => "48.163513, 6.295955",
            "09" => "42.943940, 1.501116",
            "12" => "44.315875, 2.645563",
            "31" => "43.305402, 1.244674",
            "32" => "43.695479, 0.460501",
            "46" => "44.624894, 1.596306",
            "65" => "43.143377, 0.159571",
            "81" => "43.791885, 2.234733",
            "82" => "44.080753, 1.368274",
            "59" => "50.528903, 3.149132",
            "62" => "50.513221, 2.371636",
            "44" => "47.348001, -1.740869",
            "49" => "47.389406, -0.559479",
            "53" => "48.150766, -0.644357",
            "72" => "48.026716, 0.234294",
            "85" => "46.675889, -1.469182",
            "02" => "49.453628, 3.607794",
            "60" => "49.412233, 2.427612",
            "80" => "49.971149, 2.292245",
            "16" => "45.664696, 0.242009",
            "17" => "45.730391, -0.779816",
            "79" => "46.539156, -0.341337",
            "86" => "46.612096, 0.554250",
            "04" => "44.164191, 6.232933",
            "05" => "44.656398, 6.248195",
            "06" => "43.920701, 7.177142",
            "13" => "43.542134, 5.020405",
            "83" => "43.395516, 6.294573",
            "84" => "44.045319, 5.202928",
            "01" => "46.065375, 5.448974",
            "07" => "44.815342, 4.373790",
            "26" => "44.729651, 5.238637",
            "38" => "45.289696, 5.550697",
            "42" => "45.753789, 4.224511",
            "69" => "45.880356, 4.702795",
            "73" => "45.494388, 6.403594",
            "74" => "46.045179, 6.424750",
        );

        ksort($dptFocalPoints);
        foreach($dptFocalPoints as $dpt => $position){
            $dpt = (int) $dpt;
            $parts = explode(', ', $position);

            $table->insert(array(
                'id' => $dpt,
                'latitude' => $parts[0],
                'longitude' => $parts[1]
            ));
            echo $dpt.'<br/>';
        }
        die();
    }

    public function editdptkmlAction()
    {
        $path = 'kml/in/';
        $result = array();
        $dir = new DirectoryIterator($path);
        foreach($dir as $file){
            $fileName = $file->getFilename();
            $sizeBefore = $file->getSize();
            if($file->isDot() || $file->isDir()){
                continue;
            }

            $result[$fileName] = array(
                'sizeBefore' => $sizeBefore,
                'sizeAfter' => filesize ($this->_reduceKMLPrecision2($fileName)),
            );
        }

        $this->view->result = $result;
    }

    public function urlconversionAction()
    {
    	$i = 0;
    	$urlConversions = Import::getOldUrls('http://www.mountainboard.fr');
    	foreach($urlConversions as $k => $v){
    		echo "$k: $v<br/>\n";
    		$i++;
    		if($i> 5){
    			break;
    		}
    	}
    	die();
    }

    /**
     * Remove long/lat points
     *
     * @param unknown_type $fileName
     * @param unknown_type $ratioDivider
     * @param unknown_type $dirIn
     * @param unknown_type $dirOut
     * @return unknown
     */
    protected function _reduceKMLPrecision1($fileName, $ratioDivider = 3, $dirIn = 'kml/in/', $dirOut = 'kml/out/')
    {
        $xml = new DOMDocument();
        $xml->load($dirIn . $fileName);
        $coordinates = $xml->getElementsByTagName('coordinates')->item(0)->nodeValue;

        $allParts = explode(' ', $coordinates);
        $keptParts = array();
        for($i = 0; $i < count($allParts); $i++){
            if($i%$ratioDivider){
                continue;
            }
            $keptParts[] = $allParts[$i];
        }

        $newCoordinates = implode(' ', $keptParts);
        $xml->getElementsByTagName('coordinates')->item(0)->nodeValue = $newCoordinates;

        $xml->save($dirOut . $fileName);
        return $dirOut . $fileName;
    }

    /**
     * Reduce long/lat precision
     *
     * @param unknown_type $fileName
     * @param unknown_type $roundPrecision
     * @param unknown_type $dirIn
     * @param unknown_type $dirOut
     * @return unknown
     */
    protected function _reduceKMLPrecision2($fileName, $roundPrecision = 5, $dirIn = 'kml/in/', $dirOut = 'kml/out/')
    {
        $xml = new DOMDocument();
        $xml->load($dirIn . $fileName);
        $coordinates = $xml->getElementsByTagName('coordinates')->item(0)->nodeValue;

        $allParts = explode(' ', $coordinates);
        $keptParts = array();
        for($i = 0; $i < count($allParts); $i++){
            $coordinateParts = explode(',', $allParts[$i]);
            $coordinateParts[0] = round($coordinateParts[0], $roundPrecision);
            $coordinateParts[1] = round($coordinateParts[1], $roundPrecision);
            $keptParts[] = implode(',', $coordinateParts);
        }

        $newCoordinates = implode(' ', $keptParts);
        $xml->getElementsByTagName('coordinates')->item(0)->nodeValue = $newCoordinates;

        $xml->save($dirOut . $fileName);
        return $dirOut . $fileName;
    }

    public function savedptsimpletitleAction()
    {

        $table = new Dpt();
        $db = $table->getAdapter();

        $dptList = $table->fetchAll();
        foreach($dptList as $dpt){
            $simpleTitle = strtolower(str_replace('-', '', Utils::cleanString($dpt->title)));
            $sql = "UPDATE dpt SET simpleTitle = '$simpleTitle' WHERE id = $dpt->id";
            $db->query($sql);
        }
        die('ok');
    }

	public function asciiAction()
	{
		$text = $this->getRequest()->getParam('text', 'RIDBL');
      	$figlet = new Zend_Text_Figlet();
      	die($figlet->render($text));
	}

	public function phpinfoAction()
	{
		phpinfo();
		die();
	}

	public function apcAction()
	{
		require_once('apc.php');
		die();
	}

	public function memcacheAction()
	{
		require_once('memcache.php');
		die();
	}

	public function translateAction()
	{
		ini_set('max_execution_time', 3600);
		$destinationLanguage = 'en';

		$gt = new Google_Translate();
		$ttTable = new Data_TranslatedTextRaw();
		$count = array();

		foreach( array('Dossier', 'News', 'Test', 'Trick', 'Spot') as $dataType){
			Zend_Registry::set('Zend_Locale', 'fr');
			$table = new $dataType();
			$members = $table->fetchAll();
			$count[$dataType] = 0;

			foreach($members as $member){
				$increment = 1;
				/*
				$descriptionFr = Data_TranslatedTextRaw::getTranslatedText($member->id, $dataType, 'fr', 'description');
				$translation = $gt->translate($descriptionFr->text, $destinationLanguage, "fr");
				if($translation){
					$descriptionEn = Data_TranslatedTextRaw::getTranslatedText($member->id, $dataType, 'en', 'description');
					if(!$descriptionEn){
						$descriptionEn = $ttTable->fetchNew();
						$descriptionEn->id = $descriptionFr->id;
						$descriptionEn->itemType = $descriptionFr->itemType;
						$descriptionEn->lang = $destinationLanguage;
						$descriptionEn->type = 'description';
					}

					$descriptionEn->text = $translation;
					$descriptionEn->save();
				} else {
					$increment = 0;
				}
				*/
				if(in_array($dataType, array('Dossier', 'News', 'Test'))){
					$contentFr = Data_TranslatedTextRaw::getTranslatedText($member->id, $dataType, 'fr', 'content');
					$translation = $gt->translate($contentFr->text, "en", "fr");
					if($translation){
						$contentEn = Data_TranslatedTextRaw::getTranslatedText($member->id, $dataType, $destinationLanguage, 'content');
						if(!$contentEn){
							$contentEn = $ttTable->fetchNew();
							$contentEn->id = $contentFr->id;
							$contentEn->itemType = $contentFr->itemType;
							$contentEn->lang = $destinationLanguage;
							$contentEn->type = 'content';
						}
						$message = "<p class=\"translationWarning\">Word of warning: this page was automatically translated from English to French, using Google Translate.<br/> This is likely going to sound a little funny, but hopefully you'll get the idea. If you're interested in a better translation, drop us a note in the comments.</p>\n";
						$contentEn->text = $message.$translation;
						$contentEn->save();
					} else {
						$increment = 0;
					}
				}

				$count[$dataType] += $increment;
			}
		}
		$this->view->count = $count;
		return;
	}

	public function spritesAction()
	{

	}

	public function countriesCrawlAction()
	{
		ini_set('max_execution_time', 300);

		$crawler = new Lib_CountryCrawler(Globals::getGlobalCache());
		$list = $crawler->getList();

		$countryTable = new Country();
		$dptTable = new Dpt();

		foreach($list as $countryName => $provinces){
			if($countryName == 'France'){
				continue;
			}

			$countryRow = $countryTable->fetchNew();
			$countryRow->title = $countryName;
			$countryRow->simpleTitle = Utils::cleanStringForUrl($countryName);
			$countryRow->status = Data::VALID;
			if(in_array($countryName, array('France', 'Belgium'))){
				$countryRow->lang = 'fr';
			} else {
				$countryRow->lang = 'en';
			}
			$countryRow->submitter = $this->_user->getId();
			$countryRow->date = date('Y-m-d H:i:s');
			$countryRow->save();

			foreach($provinces as $provinceName => $code){
				$provinceRow = $dptTable->fetchNew();

				$provinceRow->title = Utils::cleanStringForUrl($provinceName);
				$provinceRow->simpleTitle = Utils::cleanStringForUrl($provinceName); // PB HERE: non-ascii characters must be mapped correctly
				$provinceRow->prefix = '';
				$provinceRow->status = Data::VALID;
				$provinceRow->code = $code;
				$provinceRow->country = $countryRow->getId();
				$provinceRow->submitter = $this->_user->getId();
				$provinceRow->date = date('Y-m-d H:i:s');
				$provinceRow->save();
			}
		}

		$this->view->list = $list;
	}

	public function notifySpotSubmittersAction()
	{
		die('done');
		$userContent = array();

		$table = new Spot();
		$spots = $table->fetchAll();
		foreach($spots as $spot){
			if($spot->hasLocation()){
				continue;
			}

			if(!isset($userContent[$spot->submitter])){
				$userContent[$spot->submitter] = array(
					'user' => $spot->getSubmitter(),
					'spots' => array()
				);
			}

			$userContent[$spot->submitter]['spots'][] = $spot;
		}

		$messages = array();

		$table = new PrivateMessage();
		foreach($userContent as $userId => $content){
			$message = $table->fetchNew();
			$message->submitter = $this->_user->{User::COLUMN_USERID};
			$message->toUser = $userId;
			$message->status = Data::VALID;
			$message->title = 'Mais ou se trouvent donc tes spots??!!';
			$message->content = $this->_getMessageContent($content['user'], $content['spots']);
			$id = $message->save();

			$messages[$id] = $message->content;
		}

		$this->view->messages = $messages;
	}

	protected function _getMessageContent(User_Row $user, $spots)
	{
		$spotsList = '';
		foreach($spots as $spot){
			$spotsList .= '<li>'.$this->view->itemLink($spot).'</li>'.PHP_EOL;
		}
		$name = $user->getTitle();

		$html = <<<HTML
		<p>Salut {$name}!</p>
		<p>Tu as peut etre vu la <a href="/france">nouvelle page</a> avec les spots sur une carte de France? On a besoin de toi pour marquer tes spots dessus!<br/>
		Si tu peux prendre 5 minutes pour les placer sur la carte, ca permettra aux petits nouveaux de trouver facilement des coins sympas pour rider!</p>
		Voila la liste des spots que tu as postes:
		<ul>
{$spotsList}
		</ul>
		<br/>
		Merci!
		<br/>
		Mikael
HTML;
		return $html;
	}

	public function convertZipToDptAction()
	{
		die('done');
		$table = new User();
		$users = $table->fetchAll("zip IS NOT NULL");
		$out = '';
		foreach($users as $user){
			if(empty($user->zip)){
				continue;
			}

			if($user->zip > 1000){
				$user->dpt = floor($user->zip / 1000);
			} else {
				$user->dpt = $user->zip;
			}

			$out .= $user->zip . ' -> '.$user->dpt.'<br/>';
			$user->save();
		}

		die($out);

	}

	public function searchAction()
	{

	}

	public function fboauthAction()
	{
		$oauth = new Facebook_Oauth(array(
			'scope' => array('email','read_stream'),
			'destination' => APP_URL.'/envoyer/photo',
		));
		$this->view->link = $oauth->buildInitialUrl();
	}
}
