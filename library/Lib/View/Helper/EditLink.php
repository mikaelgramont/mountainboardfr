<?php
class Lib_View_Helper_EditLink extends Zend_View_Helper_Abstract
{
    /**
     * Builds the edit link for an item
     *
     * @param Data_Row $data
     * @return string
     */
    public function editLink(Data_Row $data, $text = null)
    {
        $viewVars = $this->view->getVars();
        $baseUrl = $viewVars['baseUrl'].'/';

        $editAltTitle = ucfirst($this->view->translate('edit'));
        $content  = ' <a class="editLink" href="'.$data->getEditLink().'">';
        $img = $this->view->cdnHelper->url($baseUrl.IMAGES_PATH.'actions/edit.gif');
        $content .= ' <img src="'.$img.'" class="inset" alt="'.$editAltTitle.'" title="'.$editAltTitle.'"/>';
        if($text){
        	$content .= ucfirst($this->view->translate($text));
        }
        $content .= '</a>'.PHP_EOL;

        return $content;
    }
}