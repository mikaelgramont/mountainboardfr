<?php
class Lib_View_Helper_ForumsList extends Zend_View_Helper_Abstract
{
	/**
	 * Builds the list of forums
	 */
	public function forumsList(Zend_Db_Table_Rowset $forums, Forum_List_Form $form, $lastLogin = null)
	{
		$category = null;
		$content = '<div class="actionLinkContainer" id="forumListMain">'.ucfirst($this->view->translate('goToForum')).' '.$form.'</div>'.PHP_EOL;
		$content .= '<ul id="forumsList">'.PHP_EOL;
	 	
		foreach($forums as $index => $forum){
			if($forum->category != $category){
				if($category !== null){
					$content .= '        </ul>'.PHP_EOL;
					$content .= '    </li>'.PHP_EOL;
				}
				$category = $forum->category;
				$content .= '    <li class="category">'.PHP_EOL;
				$content .= '    <h2>'.ucfirst(Forum::$categories[$category]).'</h2>'.PHP_EOL;
				$content .= '        <ul>'.PHP_EOL;
			}
			
			$class = "forum";
			if($lastLogin && $forum->lastPostDate > $lastLogin){
            	$class .= ' new';
            }			
			$content .= "            <li class=\"$class\">".PHP_EOL;
    		$content .= '                <h3 class="title"><a href="'.$forum->getLink().'">'.$forum->getTitle().' - <span class="description">'.stripslashes($forum->getDescription()).'</span></a></h3>'.PHP_EOL;
    		$content .= '				 <p class="topicsCount">'.$this->_getTopicsCount($forum).'</p>'.PHP_EOL;
    		$content .= '				 <p class="lastPostInfo">'.$this->_getLastPostInfo($forum).'</p>'.PHP_EOL;
    		$content .= '            </li>'.PHP_EOL;
		}
		
		if($index){
			$content .= '        </ul>'.PHP_EOL;
			$content .= '    </li>'.PHP_EOL;
		} else {
			$content .= '<li>'.$this->view->translate('noForum').'</li>';
		}
		
		$content .= '</ul>'.PHP_EOL;
		return $content;
	}
	
	protected function _getLastPostInfo(Forum_Row $forum)
	{
    	$lastPostInfo = '';
    	$lastPoster = $forum->getLastPoster();
    	$lastPostDate = $forum->getLastPostDate();
    	if($lastPoster){
        	$lastPostInfo .= '                '.ucfirst($this->view->translate('lastPostedBy')).' ';
        	$lastPostInfo .= $this->view->userLink($lastPoster).PHP_EOL;
    	}
    	if($forum->lastPostDate != '0000-00-00 00:00:00'){
    		$lastPostInfo .= '                '.$this->view->translate('dateOn').' ';
    		$lastPostInfo .= $lastPostDate.PHP_EOL;
		}
		return $lastPostInfo;		
	}
	
	protected function _getTopicsCount(Forum_Row $forum)
	{
		$topics = count($forum->getTopics());
		
		if(empty($topics)){
			return ucfirst($this->view->translate('forumNoTopic'));
		}
		
		if($topics > 1){
			return $topics.' '.$this->view->translate('forumSeveralTopics'); 
		}
		
		return $this->view->translate('forumOneTopic'); 
	}
	
}