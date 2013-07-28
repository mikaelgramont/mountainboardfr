<?php
class Data_Utils
{
    public static function getList(User_Row $user, Lib_Acl $acl, $dataType = null, $where = null, $itemsPerPage = DEFAULT_ITEMS_PER_PAGE, $orderByDate = true)
    {
        if(empty($dataType)){
            throw new Lib_Exception_NotFound("No data type given");
        }

        if($orderByDate){
            $order = "date DESC";
        }

        switch($dataType){
            case 'archives':
                Zend_Registry::set('Category', Category::ARTICLES);
                $itemsPerPage = ARCHIVES_PER_PAGE;
                $table = new News();
                break;
            case 'comment':
                $itemsPerPage = COMMENTS_PER_PAGE;
                $table = new Comment();
                $order = "date ASC";
                break;
            case 'dossier':
                Zend_Registry::set('Category', Category::ARTICLES);
                $itemsPerPage = DOSSIERS_PER_PAGE;
                $table = new Dossier();
                break;
            case 'forum':
                $itemsPerPage = 0;
                $table = new Forum();
                $order = "";
                break;
            case 'news':
                $itemsPerPage = NEWS_PER_PAGE;
                $table = new News();
                break;
            case 'spot':
                $itemsPerPage = SPOTS_PER_PAGE;
                $table = new Spot();
                $order = "id DESC";
                break;
            case 'test':
                $itemsPerPage = TESTS_PER_PAGE;
                $table = new Test();
                $order = "id DESC";
                break;
            case 'trick':
                $itemsPerPage = TRICKS_PER_PAGE;
                $table = new Trick();
                $order = "id DESC";
                break;
            case 'event':
                $itemsPerPage = EVENTS_PER_PAGE;
                $table = new Event();
                $order = "startDate DESC";
                if(empty($where)){
                	$where = "startDate > NOW()";
                }
                break;
        }

        if(!isset($table)){
            throw new Lib_Exception("Unknown data type: '$dataType'");
        }

        $seeInvalidItems = $acl->isAllowed($user, Lib_Acl::PUBLIC_EDIT_RESOURCE);

        $select = $table->select();
        if(!$seeInvalidItems){
            $select->where("status = '".Data::VALID."'");
        }
        if(!empty($where)){
            $select->where($where);
        }

        $select->order($order);

        $return = array(
            'itemsPerPage' => $itemsPerPage,
            'select' => $select
        );

        return $return;
    }

    public static function getChildrenList(User_Row $user, Lib_Acl $acl, $dataType = null, Data_Row $parent, $order = null)
    {
        if(empty($dataType)){
            throw new Lib_Exception_NotFound("No data type given");
        }
        if(empty($parent)){
            throw new Lib_Exception_NotFound("No parent given");
        }

        if(empty($order)){
            $order = "date ASC";
        }

        switch($dataType){
            case 'Forum_Topic':
                $itemsPerPage = TOPICS_PER_PAGE;
                $table = new Forum_Topic();
                $where = $table->getAdapter()->quoteInto('forumId = ?', $parent->id);
                break;
            case 'Forum_Post':
                $itemsPerPage = POSTS_PER_PAGE;
                $table = new Forum_Post();
                $where = $table->getAdapter()->quoteInto('topicId = ?', $parent->id);
                break;
        }

        if(!isset($table)){
            throw new Lib_Exception("Unknown data type");
        }

        $seeInvalidItems = $acl->isAllowed($user, Lib_Acl::PUBLIC_EDIT_RESOURCE);

        $select = $table->select();
        if(!$seeInvalidItems){
            $select->where("status = '".Data::VALID."'");
        }
        if(!empty($where)){
            $select->where($where);
        }

        $select->order($order);

        $return = array(
            'itemsPerPage' => $itemsPerPage,
            'select' => $select
        );

        return $return;
    }
}