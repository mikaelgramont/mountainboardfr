<?php
class Lib_Form_Element_Difficulty extends Zend_Form_Element_Select
{
    public $name = 'difficulty';

    public function __construct($options = null)
    {
        parent::__construct($this->name, $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('difficulty')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->setMultiOptions(array(
                1 => '1',
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5',
        ));
    }

	public function getHint()
	{
		return 'difficultyHint';
	}
}