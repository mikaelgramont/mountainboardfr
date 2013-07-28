<?php
class Comment_Row extends Data_Row implements Data_Row_MetaDataInterface
{
    /**
     * Object being commented
     *
     * @var Data_Row
     */
    public $parentItem;

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'postcomment';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'editcomment';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deletecomment';

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Comment_Form';

    /**
     * Returns the url of the parent, with an anchor to this comment
     *
     * @return string
     */
    public function getLink()
    {
        $parentItem = $this->getParentItemfromDatabase();
        $link = $parentItem->getLink();
        $link .= "#commentId".$this->id;
        return $link;
    }

    /**
     * Returns the url for the submission of a new object
     *
     * @return string
     */
    public function getCreateLink()
    {
        if(empty($this->parentItem)){
            throw new Lib_Exception('No parent defined for comment');
        }
        $link = Globals::getRouter()->assemble(array('dataType' => $this->parentItem->getItemType(), 'id' => $this->parentItem->id), $this->_createRoute, true);
        return $link;
    }

    /**
     * Returns the url for the edition page of the current object
     *
     * @return string
     */
    public function getEditLink()
    {
        if(empty($this->id)){
            return $this->getCreateLink();
        }

        $params = array(
            'id' => $this->id,
        );
        $link = Globals::getRouter()->assemble($params, $this->_editRoute, true);
        return $link;
    }

    /**
     * Returns the url for deleting an object
     *
     * @return string
     */
    public function getDeleteLink()
    {
        $params = array(
            'id' => $this->id
        );
        $link = Globals::getRouter()->assemble($params, $this->_deleteRoute, true);
        return $link;
    }

	/**
	 * Returns the url that the user will be redirected to
     * upon deletion of this object
	 *
	 * @return string
	 */
	protected function _getDeleteRedirectUrl($params, User_Row $user)
	{
		$parentRow = $this->getParentItemfromDatabase();
		$parentData = Data::factory($parentRow->id, $parentRow->getItemType());
		return $parentData->getLink();
	}

    /**
     * Returns the parent item
     * @return Data_Row
     * @throws Lib_Exception
     */
    public function getParentItemfromDatabase()
    {
        $dataTableName = Data::mapDataType($this->parentType);


        $dataTable = new $dataTableName();

        $parentItem = $this->parentItem = $dataTable->find($this->parentId)->current();
        return $parentItem;
    }

    public function getParentItem()
    {
        $parentRow = $this->getParentItemfromDatabase();

        $itemTable = new Item();
        $select = $itemTable->select();
        $select->where('itemId = '.$parentRow->id)
               ->where('itemType = "'.$parentRow->getItemType().'"');


        $parentItem = $itemTable->fetchRow($select);
        return $parentItem;
    }

	/**
	 * No folders for comments
	 */
    public function getFolderPath(){}

	/**
	 * Comments are not translated
	 *
	 * @param string $columnName
	 * @return boolean
	 */
	protected function _isTranslated($columnName)
	{
		return false;
	}

    public function getTone()
    {
    	return $this->tone;
    }

    /**
     * Returns the title, from this table or another one
     *
     * @return string
     */
    public function getTitle()
    {
       	return $this->id;
    }

    /**
     * Returns the description, from this table or another one
     *
     * @return string
     */
    public function getDescription()
    {
       	throw new Lib_Exception("Comments cannot be asked for their description");
    }

    /**
     * Returns the content of this object
     *
     * @return string
     */
    public function getContent()
    {
    	$content = $this->content;
        return $content;
    }
}