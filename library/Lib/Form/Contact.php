<?php
class Lib_Form_Contact extends Lib_Form
{
    public function __construct(User_Row $from, $options = null)
	{
        parent::__construct();

        $message = new Lib_Form_Element_Textarea('message');
        $message->setHint('contactMessageHint')
        		->setLabel(ucfirst($this->_translator->translate('message')))
        		->setRequired(true)
        		->addValidator('notEmpty');
        
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(ucfirst(Globals::getTranslate()->_('sendMessage')));

        $this->addElements(array($message, $submit));
	}
}