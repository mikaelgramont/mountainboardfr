<?php
class Lib_View_Helper_MyDatePicker extends ZendX_JQuery_View_Helper_DatePicker
{
    public function myDatePicker($id, $value = null, array $params = array(), array $attribs = array())
    {
        $locale = Zend_Registry::get('Zend_Locale');
        $this->jquery->addOnLoad("$.datepicker.setDefaults($.datepicker.regional['{$locale}'])");
        $params['dateFormat'] = Lib_Date::getDateFormat();
        return parent::datePicker($id, $value, $params, $attribs);
    }
}