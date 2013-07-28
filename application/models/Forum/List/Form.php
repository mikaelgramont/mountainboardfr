<?php
class Forum_List_Form extends Lib_Form_GoTo
{
	public function __construct($forumList,  $selected)
	{
		parent::__construct(
			Globals::getRouter()->assemble(array(), 'gotoforum', true),
    		$forumList,
    		array()
		);
		$this->setAttrib('id', 'goToForum');
		$this->getElement('goToParameter')->setValue($selected);
	}	
}