<?php
class Lib_View_Helper_ForumTopicList extends Zend_View_Helper_Abstract
{
    /**
     * Render a list of forum topics
     *
     * @param array $topics
     * @return string
     */
    public function forumTopicList($topics, $lastLogin, $userParams = array())
    {
        $params = $this->_getDefaultParameters();
        $params = array_merge($params, $userParams);

        if(!count($topics)){
            // No topics: nothing to be rendered
            $content = "<div id='{$params['containerId']}'>".PHP_EOL;
            $content .= "</div>".PHP_EOL;
            return $content;
        }

        if($params['containerClass']){
            $params['containerClass'] = " class='{$params['containerClass']}'";
        }
        


        $content = "<ul id='{$params['containerId']}'{$params['containerClass']}>".PHP_EOL;
        foreach($topics as $topic){
            $statusString = $editLink = $deleteLink = '';

            if($topic->isEditableBy($this->view->user, $this->view->acl)){
                $statusString = $this->view->itemStatus($topic);
                $editLink = $this->view->editLink($topic);
            }
            if($topic->isDeletableBy($this->view->user, $this->view->acl)){
            	if(count($topic->getPosts()) == 1){
            		$deleteLink = $this->view->deleteLink($topic);
            	}
            }
            

            if($params['useAnchor']){
                $href = "<a name=\"{$params['anchorPrefix']}{$topic->id}\"></a>";
            } else {
                $href = "";
            }
            
	        $topicClasses = array();
	        if($params['topicClass']){
	            $topicClasses[] = $params['topicClass'];
	        }
            
	        if($lastLogin && $topic->lastPostDate > $lastLogin){
            	$topicClasses[] = 'new';
            }
            
	        if(count($topicClasses)){
            	$classes = ' class="'.implode(' ', $topicClasses).'"';
            } else {
            	$classes = '';
            }
            
            $content .= "<li$classes>$href".PHP_EOL;
            if($params['link']){
                $content .= "   <a href='".$topic->getLink()."'>".ucfirst($topic->getTitle())."</a>$statusString$editLink$deleteLink".PHP_EOL;
            } else {
                $content .= "   ".ucfirst($topic->getTitle())."$statusString$editLink".PHP_EOL;
            }

            if($params['showPostCountInfo']){
                $content .= '   '.$this->_getPostsCount($topic, $params['postCountInfoClass']).PHP_EOL;
            }

            if($params['showLastPostInfo']){
                $content .= '   '.$this->_getLastPostInformation($topic, $params['lastPostInfoClass']).PHP_EOL;
            }

            $content .= "</li>".PHP_EOL;
        }
        $content .= '</ul>'.PHP_EOL;
        return $content;
    }

    /**
     * Returns a list of default parameters used for rendering
     * (classes, id's)
     *
     * @return array
     */
    protected function _getDefaultParameters()
    {
        $params = array(
            'link' => true,

            'containerId' => 'topics',
            'containerClass' => '',

            'topicClass' => '',

            'showLastPostInfo' => true,
            'lastPostInfoClass' => '',

			'showPostCountInfo' => true,
			'postCountInfoClass' => '',

            'useAnchor' => false,
            'anchorPrefix' => '',

        );
        return $params;
    }

     /**
     * Returns information about who last posted to this topic
     *
     * @param Forum_Topic_Row $topic
     * @param string $lastPostInfoClass
     * @return string
     */
    protected function _getLastPostInformation(Data_Row $topic, $lastPostInfoClass = null)
    {
        if($lastPostInfoClass){
            $lastPostInfoClass = " class='{$lastPostInfoClass}'";
        }

        $date = $topic->getDate();
        $lastPostdate = $topic->getLastPostDate();
        
        if(empty($topic->submitter)){
            throw new Lib_Exception("Forum_Topic_Row $topic->id has no submitter");
        }
        $submitter = $topic->getSubmitter();
        if(empty($topic->lastPoster)){
            throw new Lib_Exception("Forum_Topic_Row $topic->id has no lastPoster");
        }
        $lastPoster = $topic->getLastPoster();

        $content  = "       <p{$lastPostInfoClass}>";
        $content .= ' ('.$this->view->translate('topicStartedBy'). ' '.$this->view->userLink($submitter);
        $content .= ' '.$this->view->translate('dateOn').' '.$date;
        if($lastPostdate != $date){
        	$content .= ', '.$this->view->translate('lastPostedBy'). ' '.$this->view->userLink($lastPoster);
        	$content .= ' '.$this->view->translate('dateOn').' '.$lastPostdate;
        }
        $content .= ')'.PHP_EOL;
        $content .= '</p>'.PHP_EOL;
        return $content;
    }

	protected function _getPostsCount(Forum_Topic_Row $topic)
	{
		$posts = count($topic->getPosts()) - 1;
		switch($posts){
			case 0:
				$return = $this->view->translate('topicNoPost');
				break;
			case 1:
				$return = $this->view->translate('topicOnePost');
				break;
			default:
				$return = $posts.' '.$this->view->translate('topicSeveralPosts');
				break;
		}
		$return = '<p>'.$return.'</p>'.PHP_EOL;
		return $return;
	}
}