<?php
class Lib_View_Helper_ModalContentRenderer extends Zend_View_Helper_Abstract
{
    protected $_content;
    
    public function modalContentRenderer(ModalContent $content)
    {
        $this->content = $content;
        
        $return = '';
        foreach($this->content->getItems() as $id => $html) {
            $return .=
                "<div id=\"modalContent-$id\" class=\"modalContentItem\">".
                "$html</div>".PHP_EOL;
        }
        return $return;
    }
}