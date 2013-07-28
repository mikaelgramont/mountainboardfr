<?php
class Media_Item_Photo_Form extends Media_Item_Form
{
	protected $_useRemoteFile = false;

	/**
	 * Constructor
	 *
	 * @param Data_Row $object
	 * @param User_Row $user
	 * @param Lib_Acl $acl
	 * @param array $options
	 */
	public function __construct(Data_Row $object, User_Row $user, Lib_Acl $acl, $options = null)
	{
		if($object->id){
			$this->_mediaRequired = false;
		}

		if(isset($options['useRemoteFile']) && $options['useRemoteFile']) {
			$this->_useRemoteFile = true;
		}

		parent::__construct($object, $user, $acl, $options);
		$this->setAction($this->_action);
	}

	/**
     * Factory for the media element
     *
     * @param boolean $required
     * @return Lib_Form_Element_File
     *
     */
    public function getMedia($required = true)
    {
        if($this->_useRemoteFile) {
			$element = new Lib_Form_Element_RemoteFile('media', $required);
			$element->setLabel(ucfirst(Globals::getTranslate()->_('photo')));
			/**
			 * @todo: add a url validator
			 */

        } else {

	    	$element = new Lib_Form_Element_File('media', $required, APP_MEDIA_DIR, GLOBAL_UPLOAD_MAXSIZE_PHOTO, array('accept' => Media_Item_Photo::getAllowedMimeTypes()));
	        $element->setHint('photoHint');

	        /**
	         * setValueDisabled is set to true, in order to be able to use a filter
	         * to manage the renaming
	         * @see http://www.thomasweidner.com/flatpress/2009/04/17/recieving-files-with-zend_form_element_file/
	         */
	        $element->setLabel(ucfirst(Globals::getTranslate()->_('photo')))
	        		->setValueDisabled(true);
        }


		return $element;
    }

	public function getAdditionalJs()
	{
		$this->getView()->JQuery()->addJavascriptFile($this->getView()->asset()->script('libFacebookUpload.js'));


		$params = array();

		$translator = Globals::getTranslate();
		$translations = array(
			'loading' => 'defaultLoadingMessage',

			'pageTitle' => 'facebookUploadPageTitle',
			'pickAFacebookAlbum' => 'facebookUploadPickAlbum',
			'pickAnotherOne' => 'facebookUploadPickOtherAlbum',

			'albumSingular' => 'itemSing_'.Constants_DataTypes::ALBUM,
			'albumPlural' => 'itemPlur_'.Constants_DataTypes::ALBUM,
			'photoSingular' => 'itemSing_'.Constants_DataTypes::PHOTO,
			'photoPlural' => 'itemSing_'.Constants_DataTypes::PHOTO,
		);

		foreach($translations as $k => $v){
			$params[$k] = $translator->_($v);
		}

		$params['imgPath'] = IMAGES_PATH;

		$json = Zend_Json::encode($params);
		$js = <<<JS

Lib.FacebookUpload.start($json);

JS;
		return $js;
	}

}