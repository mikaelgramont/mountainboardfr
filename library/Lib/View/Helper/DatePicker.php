<?php
class Lib_View_Helper_DatePicker extends Zend_View_Helper_FormElement
{
    public function datePicker($id, $value = null, array $params = array(), $attribs = array())
    {
        return $this->view->datePicker($id, $value, $params, $attribs);
    }
}