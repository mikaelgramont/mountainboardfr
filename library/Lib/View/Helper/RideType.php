<?php
class Lib_View_Helper_RideType extends Zend_View_Helper_Abstract
{
	public function rideType(User_Row $user)
	{
		$rideTypes = Lib_Form_Element_RideType::$rideTypes;
		$rideType = $this->view->collapsedValues($user->rideType, $rideTypes);
		return $rideType;
	}
}