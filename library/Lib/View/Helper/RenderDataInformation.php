<?php
class Lib_View_Helper_RenderDataInformation extends Zend_View_Helper_Abstract
{
    /**
     * Render submit information (submitter, author, date)
     *
     * @param array $items
     * @return string
     */
    public function renderDataInformation(Data_Row $data, $class = null, $tag = 'p')
    {
        $date = $data->getDate();
        $submitter = $data->getSubmitter();
        if(empty($submitter)){
            throw new Lib_Exception("Data_Row $data->id has no submitter");
        }
        
        if(empty($class)){
        	$class = "postInformation";
        }

        $reflection = new ReflectionClass($data);

        if($reflection->implementsInterface('Data_Row_DocumentInterface') && $data->hasAuthor()){
        	$author = $data->getAuthor();
        	if($author instanceof User_Row){
            	$authorLink = ', '.$this->view->translate('writtenBy').' ' . $this->view->userLink($author, 'author');
        	} else {
        		$authorLink = ', '.$this->view->translate('writtenBy').' ' . $author['name'];
        	}
        } else {
            $authorLink = '';
        }

        $content  = ' (';
        if($data instanceof Test_Row){
        	$content .= '<span class="testCategory">'.ucfirst($this->view->translate('category')) . ': '.$this->view->translate(Test_Category::$available[$data->category]).'</span> - ';
        }
        $content .= $this->view->translate('postedBy'). ' '.$this->view->userLink($submitter);
        $content .= ' '.$this->view->translate('dateOn').' '.$date.$authorLink.')';


        return '<'.$tag.' class="'.$class.'">'.$content."</$tag>".PHP_EOL;
    }
    
	public function renderMediaInformation(Media_Item_Row $data, Album_Row $album)
    {
        $date = $data->getDate();
        $submitter = $data->getSubmitter();
        if(empty($submitter)){
            throw new Lib_Exception("Data_Row $data->id has no submitter");
        }

        $reflection = new ReflectionClass($data);

        if($reflection->implementsInterface('Data_Row_DocumentInterface') && $data->hasAuthor()){
        	$author = $data->getAuthor();
        	if($author instanceof User_Row){
            	$authorLink = ', '.$this->view->translate('author').': ' . $this->view->userLink($author, 'author');
        	} else {
        		$authorLink = ', '.$this->view->translate('author').': ' . $author['name'];
        	}
        } else {
            $authorLink = '';
        }

        $content  = ' (';
        $content .= $this->view->translate('postedBy'). ' '.$this->view->userLink($submitter);
		$content .= ' '.$this->view->translate('mediaInAlbum')." '".$this->view->itemLink($album, 'album dataLink', 'contents')."'";        
        $content .= ' '.$this->view->translate('dateOn').' '.$date.$authorLink.')';
	
        $content = str_replace('<p>', '', $content);
        $content = str_replace('</p>', '', $content);
        return '<p class="postInformation">'.$content.'</p>'.PHP_EOL;
    }    
}