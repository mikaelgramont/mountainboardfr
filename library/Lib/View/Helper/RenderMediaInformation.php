<?php
class Lib_View_Helper_RenderMediaInformation extends Zend_View_Helper_Abstract
{
	public function renderMediaInformation(Media_Item_Row $data)
    {
        $date = $data->getDate();
        $submitter = $data->getSubmitter();
        if(empty($submitter)){
            throw new Lib_Exception("Data_Row $data->id has no submitter");
        }

        $reflection = new ReflectionClass($data);

        if($reflection->implementsInterface('Data_Row_DocumentInterface') && $data->hasAuthor()){
        	if($data instanceof Media_Item_Photo_Row){
        		$createdBy = $this->view->translate('photoBy');
        	} elseif ($data instanceof Media_Item_Video_Row) {
        		$createdBy = $this->view->translate('videoBy');
        	} else {
        		$createdBy = $this->view->translate('createdBy');
        	}
        	
        	$author = $data->getAuthor();
        	if($author instanceof User_Row){
        		$authorLink = ', '.$createdBy.' ' . $this->view->userLink($author, 'author');
        	} else {
        		$authorLink = ', '.$createdBy.' ' . $author['name'];
        	}
        } else {
            $authorLink = '';
        }

        $album = $data->getAlbum();
        
        $content  = ' (';
        $content .= $this->view->translate('postedBy'). ' '.$this->view->userLink($submitter);
		$content .= ' '.$this->view->translate('mediaInAlbum')." '".$this->view->itemLink($album, 'album dataLink', 'contents')."'";        
        $content .= ' '.$this->view->translate('dateOn').' '.$date.$authorLink.')';

        return '<p class="mediaInformation deemphasized-text">'.$content.'</p>'.PHP_EOL;
    }
}