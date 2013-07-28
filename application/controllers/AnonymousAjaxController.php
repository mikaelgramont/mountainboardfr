<?php
/**
 * Anonymous ajax
 * This class is not part of the Zend MVC. Called directly by the ajax bootstrap file.
 *
 */
class AnonymousAjaxController
{
    public function __construct()
    {
    	$this->_sendJsonHeader();

    	$this->view = new Zend_View;

    	$cdnHelper = new Lib_View_Helper_Cdn($this->view);
		if(APPLICATION_ENV == 'development' || !USE_CDN){
			$cdnHelper->setDevMode();
		}
        $cdnHelper->setCdnUrl(CDN_URL);
        $cdnHelper->setCssCdnUrl(CSS_CDN_URL);
        $cdnHelper->setJsCdnUrl(JS_CDN_URL);
        $cdnHelper->setSiteUrl(APP_URL);
		$this->view->cdnHelper = $cdnHelper;
    }

    /**
     * Source of data for autocomplete fields
     * Search into users
     */
    public function getpersonAction()
    {
        $return = array();
        $name = isset($_GET['q']) ? $_GET['q'] : null;

        $table = new User();

        if(!$name){
            $where = User::COLUMN_STATUS .' >= '.User::STATUS_MEMBER;
        } else {
            $user = strtolower($name);
            $where  = $table->getAdapter()->quoteInto('LOWER(`'.User::COLUMN_USERNAME.'`) LIKE ?', "%{$user}%");
            $where .= " AND ". User::COLUMN_STATUS ." IN ('".implode("', '", array(User::STATUS_MEMBER, User::STATUS_WRITER, User::STATUS_EDITOR, User::STATUS_ADMIN))."')";
        }

        $return['list'] = $table->fetchAll($where);
        return $return;
    }

    /**
     * Search for a spot
     */
    public function getspotAction()
    {
        $return = $this->_getdataAction('spot');
        return $return;
    }

    /**
     * Search for a trick
     */
    public function gettrickAction()
    {
        $return = $this->_getdataAction('trick');
        return $return;
    }

    /**
     * Search for a country
     */
    public function getcountryAction()
    {
        $return = array();
        $name = isset($_GET['q']) ? $_GET['q'] : null;
        $table = new Country();

        $title = strtolower($name);
        $where = $table->getAdapter()->quoteInto('LOWER(`title`) LIKE ?', "%{$title}%");
        $where .= $table->getAdapter()->quoteInto(' OR `id` LIKE ?', "%{$title}%");
        $where .= " AND status = '" . Data::VALID ."'";

        $return['list'] = $table->fetchAll($where);
        return $return;
    }

    /**
     * Search for a dpt
     */
    public function getdptAction()
    {
        $return = array();
        $name = isset($_GET['q']) ? $_GET['q'] : null;
        $country = isset($_GET['country']) ? $_GET['country'] : null;
        $table = new Dpt();

        $title = strtolower($name);
        $where = $table->getAdapter()->quoteInto('(LOWER(`title`) LIKE ?', "%{$title}%");
        $where .= $table->getAdapter()->quoteInto(' OR `id` LIKE ?)', "%{$title}%");
        if($country){
        	$where .= $table->getAdapter()->quoteInto(' AND `country` = ?', $country);
        }
        $where .= " AND status = '" . Data::VALID ."'";

        $return['list'] = $table->fetchAll($where);
        return $return;
    }

    /**
     * Search for a media album
     */
    public function getalbumAction()
    {
        $return = array();
        $name = isset($_GET['q']) ? $_GET['q'] : null;

        $db = Globals::getMainDatabase();

        $textTable = Constants_TableNames::TRANSLATEDTEXT;
        $albumTable = Constants_TableNames::ALBUM;
        $and = $db->quoteInto('LOWER(t.text) LIKE ?', "%{$name}%")." AND status = '" . Data::VALID ."'";

        $sql = <<<SQL
SELECT t.text AS title FROM $textTable t
JOIN $albumTable a
ON t.id = a.id
WHERE t.itemType = 'mediaalbum'
AND t.type = 'title' AND $and
SQL;

    	$stmt = $db->query($sql);
    	$return['list'] = $stmt->fetchAll();
        return $return;
    }

    /**
     * Get autocompletion data
     *
     * @param string $type
     * @return array
     */
    protected function _getdataAction($type)
    {
        $return = array();
        $name = isset($_GET['q']) ? $_GET['q'] : null;
		$and = '';

        switch($type){
            default:
                return array();
                break;
            case 'spot':
            case 'trick':
                break;
        }

        $table = new Data_TranslatedText();
        $itemType = strtolower($type);
		$title = strtolower($name);

        $where = $table->getAdapter()->quoteInto("itemType = '" . $itemType ."' AND type='title' AND LOWER(`text`) LIKE ?", "%{$title}%");

        $return['list'] = $table->fetchAll($where);
        return $return;
    }

    /**
     * Check availability of a username
     *
     * @return array
     */
    public function isusernameavailableAction()
    {
        $username = new Lib_Form_Element_Username(null, false, false, true);

        $name = '';
        foreach($_GET as $key => $value){
            if(in_array($key, array(User::INPUT_USERNAME, 'submitter', 'author', 'lastEditor'))){
                $name = $value;
                break;
            }
        }
        $results = array(
            'status' => $username->isValid($name),
            'messageNames' => $username->getErrors()
        );
        return $results;
    }

    /**
     * Check whether a username exists
     *
     * @return array
     */
    public function doesuserexistAction()
    {
        $name = '';
        foreach($_GET as $key => $value){
            if(in_array($key, array(User::INPUT_USERNAME, 'submitter', 'author', 'lastEditor'))){
                $name = $value;
                break;
            }
        }
        $username = new Lib_Form_Element_Person($key, true);

        $results = array(
            'status' => $username->isValid($name),
            'messageNames' => $username->getErrors()
        );
        return $results;
    }

    /**
     * Check availability of an email
     *
     * @return array
     */
    public function isemailavailableandvalidAction()
    {
        $email = new Lib_Form_Element_Email(true, true);
        $email->setTranslator(Globals::getTranslate());

        $results = array(
            'status' => $email->isValid($_GET[User::INPUT_EMAIL]),
            'messageNames' => $email->getErrors()
        );
        return $results;
    }

    /**
     * Check availability of an openid
     *
     * @return array
     */
    public function isopenidavailableAction()
    {
        return array();

    	$openId = new Lib_Form_Element_OpenId(false, true);

        $results = array(
            'status' => $openId->isValid($_GET[User::INPUT_OPENID_IDENTITY]),
            'messageNames' => $openId->getErrors()
        );
        return $results;
    }

	/**
	 * Takes bounds in, and retrieve all items found in there
	 * optionally, filter data to only get certain types of items.
	 */
    public function getitemsinboundsAction()
	{
		Zend_Session::start(array(
			'cookie_domain' => GLOBAL_DOMAIN_FULL
		));

		Zend_Registry::set('Zend_Locale', 'fr');
		$return = array();

		if(!isset($_GET['b'])){
			return $return;
		}

		$loggedIn = isset($_SESSION['Zend_Auth']['storage']->userId)
					&& $_SESSION['Zend_Auth']['storage']->userId > 0;

		// bounds format: b=swLon,swLat,neLon,neLat
		$bounds = explode(',', $_GET['b']);
		$filter = isset($_GET['f']) ? $_GET['f'] : null;


		$this->view->addHelperPath('../library/Lib/View/Helper/', 'Lib_View_Helper');
		Zend_Registry::set('Zend_Translate', Globals::getTranslate());

		try{
			$result = Item::getItemsInBoundsAsJson($this->view, $bounds, $filter, $loggedIn);
		} catch(Exception $e) {
			$result = array();
		}
		return $result;
	}

	protected function _sendJsonHeader()
	{
		//header("Content-Type: application/json");
	}
}