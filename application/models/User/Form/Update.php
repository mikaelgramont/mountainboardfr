<?php
class User_Form_Update extends Lib_Form
{
    /**
     * Is user confirmed yet ?
     *
     * @var boolean
     */
    protected $_pending;
    /**
     * Did user pick OpenId as an authentication method ?
     *
     * @var boolean
     */
    protected $_useOpenId;
    /**
     * Flag that indicates whether OpenId can be changed by the user
     *
     * @var boolean
     */
    protected $_openIdIsEditable = false;

    /**
     * User being edited
     * @var User_Row
     */
    protected $_user;
    
    /**
     * Constructor
     *
     * @param boolean $pending
     * @param boolean $useOpenId
     * @param booleanarray $options
     */
    public function __construct(User_Row $user, $pending = false, $useOpenId = false, $options = null)
    {
        $this->_pending = $pending;
        $this->_useOpenId = $useOpenId;
		$this->_user = $user;
        
        parent::__construct($options);

        if($this->_pending){
            $action = Globals::getRouter()->assemble(array(), 'userpending', true);
        } else {
            $action = Globals::getRouter()->assemble(array(), 'userupdate', true);
        }

        $this->setAttrib('accept-charset', APP_PAGE_ENCODING);
        $this->setMethod('POST')
             ->setAction($action)
             ->setName('loginForm');

        $htmlPurifier = new Lib_Filter_HTMLPurifier();

        // These elements are only accessible after account confirmation
        if(!$this->_pending){
            $languages = $this->_translator->getList();
            $lang = new Zend_Form_Element_Select('lang');
            $lang->setLabel(ucfirst(Globals::getTranslate()->_('language')))
                 ->addFilter($htmlPurifier)
                 ->setMultiOptions($languages);

			if(!$useOpenId){
                $passwordOld = new Lib_Form_Element_Password_Old();
                $password = new Lib_Form_Element_Password();
                $passwordConfirm = new Lib_Form_Element_Password_Confirm(false, $this, $password->getName());
            } elseif($this->_openIdIsEditable){
                $identity = new Lib_Form_Element_OpenId(false, true);
            }
            $email = new Lib_Form_Element_Email(true, true);
        }

        $firstName = new Zend_Form_Element_Text('firstName');
        $firstName->setLabel(ucfirst(Globals::getTranslate()->_('firstName')))
                  ->addFilter($htmlPurifier);

        $lastName = new Zend_Form_Element_Text('lastName');
        $lastName->setLabel(ucfirst(Globals::getTranslate()->_('lastName')))
                 ->addFilter($htmlPurifier);

		$gender = new Lib_Form_Element_Gender();

        $locale = Zend_Registry::get('Zend_Locale');
        switch($locale){
            case 'fr':
                $format = 'dd/mm/YYYY';
                break;
            default:
                $format = 'mm-dd-YYYY';
                break;
        }

        $dateValidator = new Zend_Validate_Date($format, $locale);
        $birthDate = new Lib_Form_Element_Date('birthDate',array());
        $birthDate->setLabel(ucfirst(Globals::getTranslate()->_('birthDate')))
                  ->setOptions(array('yearRange' => date('Y') - 80 . ':'. date('Y')))
                  ->addValidator($dateValidator);

        $site = new Zend_Form_Element_Text('site');
        $site->setLabel(ucfirst(Globals::getTranslate()->_('site')))
             ->addFilter($htmlPurifier);

        $occupation = new Zend_Form_Element_Text('occupation');
        $occupation->setLabel(ucfirst(Globals::getTranslate()->_('occupation')))
                   ->addFilter($htmlPurifier);

        $longitude = $this->getLongitude();
        $latitude = $this->getLatitude();
        $zoom = $this->getZoom();
        $mapType = $this->getMapType();
        $yaw = $this->getYaw();
        $pitch = $this->getPitch();
        
        $rideType = new Lib_Form_Element_RideType();

        $level = new Lib_Form_Element_Level();

        $gear = new Zend_Form_Element_Text('gear');
        $gear->setLabel(ucfirst(Globals::getTranslate()->_('gear')))
             ->addFilter($htmlPurifier);

        $otherSports = new Zend_Form_Element_Text('otherSports');
        $otherSports->setLabel(ucfirst(Globals::getTranslate()->_('otherSports')))
                    ->addFilter($htmlPurifier);

		$avatarUrl = new Zend_Form_Element_Text('avatarUrl');
        $avatarUrl->setLabel(ucfirst(Globals::getTranslate()->_('avatarUrl')))
             ->addFilter($htmlPurifier);

		$avatarFile = new Lib_Form_Element_File('avatarFile', false, null, null, null, Media_Item_Photo::SUBTYPE_JPG);
        $avatarFile->setLabel(ucfirst(Globals::getTranslate()->_('avatarFile')));

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(ucfirst(Globals::getTranslate()->_('doUpdateProfile')));

        if(!$this->_pending){
            $this->addElement($lang);

            $authMembers = array();
            if(!$this->_useOpenId){
                $authMembers[] = $passwordOld;
                $authMembers[] = $password;
                $authMembers[] = $passwordConfirm;
            } elseif($this->_openIdIsEditable) {
                $authMembers[] = $identity;
            }
            $authMembers[] = $email;

            $this->addElements($authMembers);
        }

        $this->addElements(array(
            $firstName,
            $lastName,
            $gender,
            $birthDate,
            $site,
            $occupation,
            $longitude,
            $latitude,
            $latitude,
            $zoom,
            $mapType,
            $yaw,
            $pitch,
            $rideType,
            $level,
            $gear,
            $otherSports,
            $avatarUrl,
            $avatarFile,
        ));

        if(!$this->_pending){
            $authGroupMembers = array();
            $authGroupMembers[] = $lang->getId();
            if(!$this->_useOpenId){
                $authGroupMembers[] = $passwordOld->getId();
                $authGroupMembers[] = $password->getId();
                $authGroupMembers[] = $passwordConfirm->getId();
            } elseif($this->_openIdIsEditable){
                $authGroupMembers[] = $identity->getId();
            }
            $authGroupMembers[] = $email->getId();

            $this->addDisplayGroup($authGroupMembers, 'passwordGroup', array('disableLoadDefaultDecorators' => false));
        }

        $this->addDisplayGroup(array('firstName', 'lastName', 'gender', 'birthDate',  'site', 'occupation', 'avatarUrl', 'avatarFile'), 'personGroup');

        $this->addDisplayGroup(array('longitude', 'latitude', 'zoom', 'mapType', 'yaw', 'pitch'), 'locationGroup');
        
        $this->addDisplayGroup(array('rideType', 'level', 'gear', 'otherSports'), 'rideGroup');

        $this->addElements(array($submit));
        
        
    }
    
    protected function _setOwnDecorators()
    {
        $this->clearDecorators();
        $this->addDecorator('FormElements')
             ->addDecorator('JsForm')
             ->addDecorator('JsValidation')
             ->addDecorator('JsHint')
             ->addDecorator('JsMap');
             
        $this->setDisplayGroupDecorators(array(
            'FormElements',
            'Fieldset'
        ));

        $this->setElementDecorators(array(
            array('AjaxValidation'),
            array('ViewHelper'),
            array('CustomErrors'),
            array('Hint'),
            array('Description'),
            array('Label', array('separator'=>' ', 'class' => 'form-element-label')),
            array('HtmlTag', array('tag' => 'p', 'class'=>'element-group')),
        ));
    }
    
    /**
     * This method allows to check passwords as well as other
     * elements.
     *
     * @param array $data
     * @return boolean
     */
    public function isUpdateValid($data, $user)
    {
        $valid = parent::isValid($data);

        $hasErrors = false;
        if(!empty($data[User::INPUT_PASSWORD_OLD])){
            /**
             * Something was typed in the old password field
             */
            if($user->{User::COLUMN_PASSWORD} != md5($data[User::INPUT_PASSWORD_OLD])){
                /**
                 * Old password is incorrect, stop right here
                 */
                $this->getElement(User::INPUT_PASSWORD_OLD)->clearErrorMessages()->addError('wrongPassword');
                $hasErrors = true;
            } else {
                /**
                 * Old password is correct, check for updates in new password fields
                 */
                if($data[User::INPUT_PASSWORD_CONFIRM] !== $data[User::INPUT_PASSWORD]){
                    $this->getElement(User::INPUT_PASSWORD)->clearErrorMessages();
                    $this->getElement(User::INPUT_PASSWORD_CONFIRM)->clearErrorMessages()
                                                                   ->addError(Zend_Validate_Identical::NOT_SAME);
                    $hasErrors = true;
                }
            }
        } else {
            /**
             * Nothing was typed in the old password field
             */
            if(!empty($data[User::INPUT_PASSWORD_CONFIRM]) || !empty($data[User::INPUT_PASSWORD])){
                /**
                 * New passwords were given, but we need the old one !
                 */
                $this->getElement(User::INPUT_PASSWORD_OLD)->clearErrorMessages()->addError('isEmpty');
                $hasErrors = true;
            }
        }

        $valid = $valid && !$hasErrors;
        return $valid;
    }

    /**
     * Call special formatting functions before populating
     * form with data form database
     *
     * @param array $data
     */
    public function populateFromDatabaseData(array $data)
    {
        $formattedData = $data;
		$elements = $this->getElements();
		$location = $this->_user->getLocation();
		
        foreach($elements as $name => $element){
            if(method_exists($element, 'getValueFromDatabase')){
            	$rawValue = isset($data[$name]) ? $data[$name] : null;
                $formattedData[$name] = $element->getValueFromDatabase($rawValue);
            }
	        
            // Location elements
	        if(in_array($name, array('longitude','latitude','zoom','mapType','yaw','pitch'))){
            	$value = $location ? $location->$name : null;
                $formattedData[$name] = $value;
	        }
        }
        
        
        $this->populate($formattedData);
    }
        
    /**
     * Call special formatting function before storing data
     * into database
     *
     * @param array $data
     * @return array
     */
    public function getFormattedValuesForDatabase()
    {
        $formattedData = parent::getFormattedValuesForDatabase();
		if(!empty($formattedData['avatarUrl'])){
			$formattedData['avatar'] = $formattedData['avatarUrl'];
		}

		unset($formattedData['avatarUrl']);
		unset($formattedData['avatarFile']);

        return $formattedData;
    }

    /**
     * Factory for longitude element
     *
     * @return Lib_Form_Element_Location_Angle_Longitude
     */
    public static function getLongitude()
    {
        $element = new Lib_Form_Element_Location_Angle_Longitude();
        return $element;
    }

    /**
     * Factory for latitude element
     *
     * @return Lib_Form_Element_Location_Angle_Latitude
     */
    public static function getLatitude()
    {
        $element = new Lib_Form_Element_Location_Angle_Latitude();
        return $element;
    }

    /**
     * Factory for zoom element
     *
     * @return Lib_Form_Element_Location_Zoom
     */
    public static function getZoom()
    {
        $element = new Lib_Form_Element_Location_Zoom();
        return $element;
    }

    /**
     * Factory for map type element
     *
     * @return Lib_Form_Element_Location_MapType
     */
    public static function getMapType()
    {
        $element = new Lib_Form_Element_Location_MapType();
        return $element;
    }

    /**
     * Factory for yaw element
     *
     * @return Lib_Form_Element_Location_Angle_Yaw
     */
    public static function getYaw()
    {
        $element = new Lib_Form_Element_Location_Angle_Yaw();
        return $element;
    }

    /**
     * Factory for pitch element
     *
     * @return Lib_Form_Element_Location_Angle_Pitch
     */
    public static function getPitch()
    {
        $element = new Lib_Form_Element_Location_Angle_Pitch();
        return $element;
    }
    
    public function getData()
    {
        return $this->_object;
    }    
}