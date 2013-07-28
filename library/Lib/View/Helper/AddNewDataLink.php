<?php
class Lib_View_Helper_AddNewDataLink extends Zend_View_Helper_Abstract
{
    public function addNewDataLink($dataType, $parentId = null)
    {
        $logger = Globals::getLogger();

    	$dataType = ucfirst($dataType);
        $table = new $dataType;
        $data = $table->fetchNew();

        $user = $this->view->user;

		$prepend = $prependClass = $append = $class = $id = '';
		$title = ucfirst($this->view->translate('add'.$dataType.'Link'));
		switch($dataType){
        	case 'Forum_Topic':
        		if(is_null($parentId)){
					$logger->error('No parentId given for link to new forum topic');
					return '';
        		}
        		$data->forumId = $parentId;
        		$destinationUrl = $data->getCreateLink();
        		break;
        	case 'Forum_Post':
        		$topicTable = new Forum_Topic();
        		$topic = $topicTable->find($parentId)->current();
        		if(empty($topic)){
        			$logger->error('Could not instantiate topic '.$parentId);
        			return '';
        		}
        		$destinationUrl = $topic->getLastPost()->getLink();
				$prependClass = ' forum_post';
        		break;
        	default:
				$destinationUrl = $data->getCreateLink();
        		break;
        }

        if($user->isLoggedIn() && !$data->isCreatableBy($user, $this->view->acl)){
			return '';
		}

		$prepend = "<div id=\"register\" class=\"actionLinkContainer$prependClass\">";
		$append = "</div>".PHP_EOL;


		$return = $this->view->actionLink(
			$prepend,
			$append,
			$user,
			array(
				'url' => $destinationUrl,
				'title' => $title,
			),
			$class
		);
		return $return;
    }

	public function oldaddNewDataLink($dataType, $parentId = null)
    {
        $dataType = ucfirst($dataType);
        $table = new $dataType;
        $data = $table->fetchNew();

        switch($dataType){
        	case 'Forum_Topic':
        		$data->forumId = $parentId;
        		break;
        	case 'Forum_Post':
        		$topicTable = new Forum_Topic();
        		$topic = $topicTable->find($parentId)->current();
        		$destinationUrl = urlencode($topic->getLastPost()->getLink());
        		$url = Globals::getRouter()->assemble(array('url' => $destinationUrl), 'savedestinationforredirect', true);
		        $stringName = 'add'.$dataType.'Link';
		        $string  = ucfirst($this->view->translate('loginInOrderTo'));
		        $string .= ' '.$this->view->translate($stringName);
        		return "<div id=\"register\" class=\"actionLinkContainer forum_post\"><a class=\"loginLink\" href=\"$url\">$string</a></div>".PHP_EOL;
        		break;
        }

        if($data->isCreatableBy($this->view->user, $this->view->acl)){
            // Creation authorized
            $content = $this->_getAddLink($data, $dataType);
        } else {
            if(!$this->view->user->isLoggedIn()){
                // Creation not authorized because user is logged out
                $content = $this->_getLoggedOutAddLink($data, $dataType);
            } elseif($data->getCreationResourceId() == Lib_Acl::WRITER_RESOURCE){
                // Creation not authorized because user is not a writer/admin
                $content = '';
            }
        }

        return $content;
    }

    protected function _getAddLink(Data_Row $data, $dataType)
    {
        $url = $data->getCreateLink();
        $stringName = 'add'.$dataType.'Link';
        $string = ucfirst($this->view->translate($stringName));
		$class = 'add'.ucfirst($dataType);
        return "<div class=\"$class actionLinkContainer\"><a href=\"$url\">$string</a></div>".PHP_EOL;
    }

    protected function _getLoggedOutAddLink(Data_Row $data, $dataType)
    {
        $class = strtolower($dataType);

        $prepend = "<div id=\"register\" class=\"$class actionLinkContainer\">".PHP_EOL;
		$append = '	</div>'.PHP_EOL;
        $stringName = 'add'.$dataType.'Link';
        $string  = ucfirst($this->view->translate('loginInOrderTo'));
        $string .= ' '.$this->view->translate($stringName);

		$content = $this->view->actionLink($prepend, $append, $this->view->user, $data->getCreateRoute(), $string, array('dataType' => $dataType), "dataLink $dataType");
		return $content;

    	$destinationUrl = urlencode($data->getCreateLink());

    	$url = Globals::getRouter()->assemble(array('url' => $destinationUrl), 'savedestinationforredirect', true);
        return "<div id=\"register\" class=\"$class actionLinkContainer\"><a class=\"loginLink\" href=\"$url\">$string</a></div>".PHP_EOL;
    }
}