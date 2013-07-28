<?php
class Lib_View_Helper_ForumPostList extends Zend_View_Helper_Abstract
{
    /**
     * Render a list of posts in a topic
     *
     * @param array $posts
     * @throws Lib_Exception_Forum
     * @return string
     */
    public function forumPostList(Forum_Topic_Row $topic, $posts, $page, $lastLogin = null, $userParams = array())
    {
        $params = $this->_getDefaultParameters();
        $params = array_merge($params, $userParams);

        if(!count($posts)){
        	throw new Lib_Exception_Forum("topic $topic->id has no posts");
        }

        $firstSubmitterId = $topic->getSubmitter()->getId();

        $content = $this->view->postsList($posts, $firstSubmitterId, $params, $lastLogin, Lib_View_Helper_PostsList::TYPE_FORUMPOSTS );
        $content .= '<div class="clear"></div>'.PHP_EOL;
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
        );
        return $params;
    }
}