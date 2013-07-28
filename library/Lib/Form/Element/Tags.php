<?php
class Lib_Form_Element_Tags extends Zend_Form_Element_Text
{
    protected $_defaultName = 'tags';

    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'Tags';    

	/**
     * Does the element represent an array?
     * @var bool
     */
    protected $_isArray = true;	

	public function getHint()
	{
		return 'tagsHint';
	}
}