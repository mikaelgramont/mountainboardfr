<?php
class Lib_Form_Element_Video extends Zend_Form_Element_Textarea
{
	/**
	 * The video element being edited
	 *
	 * @var Media_Item_Video_Row
	 */
	protected $_video;

	public $helper = 'videoFormElement';
	
	public function __construct(Media_Item_Video_Row $video, $required = true, $spec = 'video', $label = 'video', $options = null)
	{
		$this->_video = $video;
		parent::__construct($spec, $options);
        $this->setLabel(ucfirst(Globals::getTranslate()->_($label)))
             ->setRequired($required)
             ->addPrefixPath('Lib_Validate', 'Lib/Validate', 'Validate')
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('Video')
             ->addValidator('NotEmpty')
             ->addValidator('Video');
	}	
	
	/**
	 * The media element is not saved directly in database.
	 * Hence there is no value to fetch. We don't use the $value
	 * parameter which is actually always null.
	 *
	 * @param null $value
	 * @return string
	 */
	public function getValueFromDatabase($value = null)
	{
		$return = $this->_video->getProviderCode();
		return $return;
	}

	public function getHint()
	{
		return 'videoHint';
	}
}