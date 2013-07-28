<?php
class Lib_View_Helper_RenderComments extends Zend_View_Helper_Abstract
{
    /**
     * Array of comments
     *
     * @var array
     */
    protected $_comments;

    protected $_data;

    /**
     * Determines what to display
     *
     * @param array $comments
     * @param Data_Row $data
     * @return string
     */
    public function renderComments($comments, Data_Row $data, $lastLogin = null)
    {
    	$this->_comments = $comments;
        $this->_data = $data;

        if($this->view->acl->isAllowed($this->view->user, new Zend_Acl_Resource(Lib_Acl::WRITER_RESOURCE))){
            // Editors or admins
            $displayComments = true;
            $editComments = true;
            $postComment = true;
            $loginLink = false;
        } elseif($this->view->acl->isAllowed($this->view->user, new Zend_Acl_Resource(Lib_Acl::REGISTERED_RESOURCE))){
            // Registered users
            $displayComments = true;
            $editComments = false;
            $postComment = true;
            $loginLink = false;
        } elseif(LOGGEDOUT_USERS_SEE_COMMENTS){
            // Logged out users + they can see comments
            $displayComments = true;
            $editComments = false;
            $postComment = false;
            $loginLink = true;
        } else {
            // Logged out users + they cannot see comments
            $displayComments = false;
            $editComments = false;
            $postComment = false;
            $loginLink = true;
        }

        $content = $this->_getContent($displayComments, $editComments, $postComment, $loginLink, $lastLogin);
        return $content;
    }

    /**
     * Builds what to display: comments, new comment form, link to login, etc.
     *
     * @param boolean $displayComments
     * @param boolean $editComments
     * @param boolean $postComment
     * @param boolean $loginLink
     * @return string
     */
    protected function _getContent($displayComments, $editComments, $postComment, $loginLink, $lastLogin = null)
    {
        $content = '';
        if($editComments){
            $content .= $this->_displayComments($lastLogin);
        } elseif($displayComments){
            $content .= $this->_displayComments($lastLogin).PHP_EOL;
        }

        if($postComment){
            $table = new Comment();
            $comment = $table->fetchNew();
            // Object being commented:
            $comment->parentItem = $this->_data;
            $content .= '<div id="postComment">'.PHP_EOL;

            $form = $comment->getForm($this->view->user, $this->view->acl);

            $content .= $form->__toString().PHP_EOL;
            $content .= '</div>'.PHP_EOL;
        }

        if($loginLink && !$this->view->isPrefetch){
        	$prepend = '<div id="registerForComment" class="actionLinkContainer">';
        	$append = '</div>'.PHP_EOL;
        	$url = $this->view->url().'#postComments';
        	$content .= $this->view->actionLink($prepend, $append, $this->view->user, array('url' => $url, 'title' => $this->view->translate('addCommentLink')), 'dataLink comment');
        }

        return $content;
    }

    /**
     * Renders comments
     *
     * @return string
     */
    protected function _displayComments($lastLogin = null)
    {
        $params = array(
        );

        $contentSubmitterId = $this->_data->getSubmitter()->getId();
        $content  = '<a name="postComments" />'.PHP_EOL;
        $content .= $this->view->postsList($this->_comments, $contentSubmitterId, $params, $lastLogin, Lib_View_Helper_PostsList::TYPE_COMMENTS );
        return $content;
	}
}