<?php
class Lib_View_Helper_UserList extends Zend_View_Helper_Abstract
{
    /**
     * Render a list of users
     *
     * @param array $users
     * @return string
     */
    public function userList($users, $elementId = 'users', $elementClass = '', $userClass = '')
    {
        if(!count($users)){
            // No users: nothing to be rendered
            $content = "<div id='$elementId'>".PHP_EOL;
            $content .= "</div>".PHP_EOL;
            return $content;
        }

        if($elementClass){
            $elementClass = " class='$elementClass'";
        }
        if($userClass){
            $userClass = " class='$userClass'";
        }

        $content = "<ul id='{$elementId}'$elementClass>".PHP_EOL;
        foreach($users as $user){
            $content .= "<li$userClass>".PHP_EOL;
            $content .= '   '.$this->view->userLink($user).PHP_EOL;
            $content .= "</li>".PHP_EOL;
        }
        $content .= '</ul>'.PHP_EOL;
        return $content;
    }
}