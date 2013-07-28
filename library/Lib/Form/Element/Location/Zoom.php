<?php
class Lib_Form_Element_Location_Zoom extends Zend_Form_Element_Select
{
    public $name = 'zoom';

    public function __construct($options = null)
    {
        parent::__construct($this->name, $options);

        $this->setLabel(ucfirst(Globals::getTranslate()->_('zoom')))
             ->addPrefixPath('Lib_Filter', 'Lib/Filter', 'Filter')
             ->addFilter('HTMLPurifier')
             ->setMultiOptions(array(
                0 => '0',
                1 => '1',
                2 => '2',
                3 => '3',
                4 => '4',
                5 => '5',
                6 => '6',
                7 => '7',
                8 => '8',
                9 => '9',
               10 => '10',
               11 => '11',
               12 => '12',
               13 => '13',
               14 => '14',
               15 => '15',
               16 => '16',
               17 => '17',
               18 => '18',
               19 => '19',
               20 => '20',
		));
    }
}