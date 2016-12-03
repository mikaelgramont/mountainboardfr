<?php
class Lib_View_Helper_PostsList extends Zend_View_Helper_Abstract
{
	const TYPE_COMMENTS = 'comments';
	const TYPE_FORUMPOSTS = 'forumPosts';
	const TYPE_PRIVATEMESSAGES = 'privateMessages';
	
	/**
	 * Renders a list of comments or forum posts
	 *
	 * @param array $posts
	 * @param integer $firstSubmitterId
	 * @param array $userParams
	 * @param string $type
	 * @return string
	 */
	public function postsList($posts, $firstSubmitterId = 0, $userParams = array(), $lastLogin = null, $type = null)
	{
		if($type == self::TYPE_FORUMPOSTS){
			$params = $this->_getForumPostsParameters();
			$useTone = true;
			$skipFirstLinks = true;
		} elseif($type == self::TYPE_PRIVATEMESSAGES){
			$params = $this->_getPrivateMessageParameters();
			$useTone = false;
			$skipFirstLinks = false;
		} else {
			$type = self::TYPE_COMMENTS;
			$params = $this->_getCommentsParameters();
			$useTone = true;
			$skipFirstLinks = false;
		}
        $params = array_merge($params, $userParams);

		$content = '<ul id="postsList">'.PHP_EOL;
		foreach($posts as $index => $post){
			$submitter = $post->getSubmitter();
			if(empty($submitter)){
				// If a user was deleted from the database, we just use guest instead
				$userTable = new User();
				$submitter = $userTable->find(0)->current();
			}
			$isFirstSubmitter = ($submitter->getId() == $firstSubmitterId);

			$info = '';

			if($useTone){
				$tone = $post->getTone();
				if(!empty($tone) && isset(Data::$tones[$tone])){
					$info .= '<span class="tone">'.ucfirst($this->view->translate('tone')) . ': '. $this->view->translate(Data::$tones[$tone]).'</span> - '.PHP_EOL;
				}
			}

			$statusString = $editLink = $deleteLink = $edition = '';
            if($index > 0 || !$skipFirstLinks){
	            if($post->isEditableBy($this->view->user, $this->view->acl)){
	                $statusString = $this->view->itemStatus($post, true);
	                $editLink = $this->view->editLink($post);
	            }
            	if($post->isDeletableBy($this->view->user, $this->view->acl)){
	                $deleteLink = $this->view->deleteLink($post);
            	}
            }
	        if($post->lastEditor){
            	$lastEditor = $post->getLastEditor();
				$lastEditionDate = $post->getLastEditionDate();
				$edition = ' - '. $this->view->translate('lastEditor').' '.$this->view->userLink($lastEditor). ' '. $this->view->translate('dateOn') .' '. $lastEditionDate;
			}
			if($type == self::TYPE_PRIVATEMESSAGES && $submitter->getId() == $this->view->user->getId()){
				$toUser = $post->getRecipient();
				$info .= $this->view->translate('for').' '.$this->view->userLink($toUser).', '.PHP_EOL;
			}
			
			if($post->date == '1970-01-01 00:00:00'){
				$date = $this->view->translate('aLongTimeAgo');
				$info .= $this->view->translate('posted') .'... ';
			} else {
				$date = $post->getDate();
				$info .= $this->view->translate('postedOn') .' ';
			}
			$info .= $date.$edition;
			$info .= $statusString.$editLink.$deleteLink;

            $new = $lastLogin && $post->date > $lastLogin;
            
            if($type == self::TYPE_PRIVATEMESSAGES){
            	$currentContent = $post->getContent();
            	if($type != self::TYPE_PRIVATEMESSAGES || $submitter->getId() != $this->view->user->getId()){
            		$currentContent .= PHP_EOL.'<div class="privateMessageReplyLinkContainer">'.$this->view->routeLink('privatemessagesreply', null, array('name' => $post->getCleanTitle(), 'id' => $post->getId())).'</div>'.PHP_EOL;
            	}
            } else {
            	$currentContent = $post->getContent();
            }
            
            $content .= $this->_postTemplate(
				$isFirstSubmitter,
				$index,
				$post->id,
				$params['idPrefix'],
				$params['class'],
				$params['anchorPrefix'],
				$currentContent,
				$this->view->profilePic($submitter),
				$info,
				$new
			);
		}
		$content .= '</ul>'.PHP_EOL;
		return $content;
	}

	protected function _getForumPostsParameters()
	{
		$return = array(
			'idPrefix' => 'forumPost',
			'class' => '',
			'anchorPrefix' => 'forumPostId'
		);

		return $return;
	}

	protected function _getCommentsParameters()
	{
		$return = array(
			'idPrefix' => 'comment',
			'class' => '',
			'anchorPrefix' => 'commentId'
		);

		return $return;
	}

	protected function _getPrivateMessageParameters()
	{
		$return = array(
			'idPrefix' => 'privateMessage',
			'class' => '',
			'anchorPrefix' => 'messageId'
		);

		return $return;		
	}
	
	protected function _postTemplate($isFirstSubmitter, $index, $postId, $idPrefix, $class, $anchorPrefix, $content, $avatar, $info, $new = false)
	{
		if($isFirstSubmitter){
			$class .= ' topicSubmitter';
		}
		
		if(!empty($class)){
			$class = " class=\"$class\"";
		}

		$postInfoClass = $new ? ' new' : '';
		$content = ucfirst($content);
		$info = ucfirst($info);

		$anchor = "<a name=\"$anchorPrefix$postId\"></a>";

		$postInfo = empty($info) ? '' : "<div class=\"postInfo$postInfoClass\">$info</div>";

		$template = <<<HTML
<li id="$idPrefix$index"$class>$anchor
    <div class="posterInfo">
        $avatar
    </div>
	<div class="postWrapper">
    	$postInfo
    	<div class="postContent">$content</div>
    </div>
</li>
HTML;
		return $template;
	}
}