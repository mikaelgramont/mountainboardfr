<?php
class ModalContent
{
    protected $_items = array();
    
    public function __construct($initItems)
    {
        $this->_items = $initItems;
    }
    
    public function addItem($key, $html)
    {
        $this->_items[$key] = $html;
    }
    
    public function getItems()
    {
        return $this->_items;
    }
}