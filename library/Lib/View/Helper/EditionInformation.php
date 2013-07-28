<?php
class Lib_View_Helper_EditionInformation extends Zend_View_Helper_Abstract
{
    /**
     * Render submit information (submitter, author, date)
     *
     * @param array $items
     * @return string
     */
    public function editionInformation(Data_Row $data, $editionInfoClass = null)
    {
        $info = '';

        $lastEditionDate = $data->getLastEditionDate();
        $lastEditor = $data->getLastEditor();
        if(empty($lastEditionDate) && empty($lastEditor)){
            return $info;
        }

        $info .= ' ('.$this->view->translate('edited');
        $elements = array();
        if(!empty($lastEditor)){
            $elements[] = ' '.$this->view->translate('by'). ' '.$this->view->userLink($lastEditor);
        }
        if(!empty($lastEditionDate)){
            $elements[] = ' '.$this->view->translate('dateOn').' '.$lastEditionDate;
        }

        $info .= implode(', ', $elements);
        $info .= ')';

        if($editionInfoClass){
            $editionInfoClass = " class='{$editionInfoClass}'";
        }
        $content = "       <p{$editionInfoClass}>$info</p>".PHP_EOL;
        return $content;

    }
}