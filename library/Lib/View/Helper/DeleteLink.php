<?php
class Lib_View_Helper_DeleteLink extends Zend_View_Helper_Abstract
{
    /**
     * Builds the delete link for an item
     *
     * @param Data_Row $data
     * @return string
     */
    public function deleteLink(Data_Row $data)
    {
        $viewVars = $this->view->getVars();
        $baseUrl = $viewVars['baseUrl'].'/';
		$url = $this->view->cdnHelper->url($baseUrl.IMAGES_PATH.'actions/delete.png');

        $altTitle = ucfirst($this->view->translate('delete'));
        $content  = ' <a class="deleteLink" href="'.$data->getDeleteLink().'">';
        $content .= ' <img src="'.$url.'" class="inset" alt="'.$altTitle.'" title="'.$altTitle.'"/>';
        $content .= '</a>'.PHP_EOL;

        return $content;
    }
}