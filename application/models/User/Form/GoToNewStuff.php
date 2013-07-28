<?php
class User_Form_GoToNewStuff extends Lib_Form_GoTo
{
	public function __construct($selected)
	{
		parent::__construct(
			Globals::getRouter()->assemble(array(), 'gotonewstuff', true),
    		array(
    			'lastVisit' => 'sinceLastVisit',
    			'lastDay' => 'overLastDay',
    			'lastWeek' => 'overLastWeek',
    			'lastMonth' => 'overLastMonth',
    		),
    		array()
		);
		$this->setAttrib('id', 'goToNewStuff');
		$this->getElement('goToParameter')->setValue($selected);
	}	
}