<?php
class Lib_View_Helper_ItemStatus extends Zend_View_Helper_Abstract
{
    const VALID_IMAGE = 'valid.png';
    const INVALID_IMAGE = 'invalid.gif';

    /**
     * Render a list of items (thumbnail, title, etc.)
     *
     * @param array $items
     * @return string
     */
    public function itemStatus(Data_Row $data, $showValid = false)
    {
        $viewVars = $this->view->getVars();
        $baseUrl = $viewVars['baseUrl'].'/';

        if(!$showValid){
        	return '';
		}

		$isEditable = $data->isEditableBy($this->view->user, $this->view->acl);
		$itemType = $data->getItemType();

        if(($data->status == Data::VALID)){
            // Valid string
            $link = Globals::getRouter()->assemble(array('dataType' => $itemType, 'id' => $data->id), 'invalidatedata', true);
            $statusAltTitle = ucfirst($this->view->translate('valid')) . '. ' .ucfirst($this->view->translate('clickToInvalidate')) .'.';
            $url = $this->view->cdnHelper->url($baseUrl.IMAGES_PATH.'status/'.self::VALID_IMAGE);
            $content = ' <a href="'. $link .'"><img src="'.$url.'" class="inset" alt="'.$statusAltTitle.'" title="'.$statusAltTitle.'"/></a>';

        } else {
            // Invalid string
            $link = Globals::getRouter()->assemble(array('dataType' => $itemType, 'id' => $data->id), 'validatedata', true);
            $statusAltTitle = ucfirst($this->view->translate('invalid')) . '. ' .ucfirst($this->view->translate('clickToValidate')) .'.';
            $url = $this->view->cdnHelper->url($baseUrl.IMAGES_PATH.'status/'.self::INVALID_IMAGE);
            $content = ' <a href="'. $link .'"><img src="'.$url.'" class="inset" alt="'.$statusAltTitle.'" title="'.$statusAltTitle.'"/>';
        }

        return $content;
    }
}