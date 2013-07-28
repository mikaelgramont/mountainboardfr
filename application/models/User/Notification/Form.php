<?php
class User_Notification_Form extends Lib_Form
{
	protected $_dataTypes = array();

    /**
     * Call special formatting functions before populating
     * form with data form database
     *
     * @param array $data
     */
    public function populateFromDatabaseData(array $data)
    {
    	$formattedData = array();

		$elements = $this->getElements();
        foreach($elements as $itemType => $element){
        	$formattedData[$itemType] = User_Notification::DO_NOT_NOTIFY;

        	foreach($data as $notification){
	        	if($notification->itemType != $itemType){
	        		continue;
	        	}
        		$formattedData[$itemType] = $notification->notify;
        	}
        }
        $this->populate($formattedData);
    }

	public function __construct(array $dataTypes, $options = null, $csrfProtection = false)
	{
		$this->_dataTypes = $dataTypes;

		parent::__construct($options, $csrfProtection);
        $this->setName('userNotifications');

		$elements = array();
        foreach($this->_dataTypes as $dataType => $notify){
        	$radio = new Zend_Form_Element_Radio($dataType);
        	$radio->setLabel(ucfirst(Globals::getTranslate()->_('itemPlur_'.$dataType)))
        		  ->setSeparator('')
             	  ->setMultiOptions(
             	  		array(
             	  			array(
             	  				'key' => 1,
             	  				'value' => 'yes'
             	  			),
             	  			array(
             	  				'key' => 0,
             	  				'value' => 'no'
             	  			),
             	  		));
			$elements[$dataType] = $radio;
        }

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel(ucfirst(Globals::getTranslate()->_('updateNotifications')));
        $elements[] = $submit;

        $this->addElements($elements);
	}
}