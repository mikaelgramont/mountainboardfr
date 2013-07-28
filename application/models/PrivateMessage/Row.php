<?php
class PrivateMessage_Row extends Data_Row
{
    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'displayprivatemessage';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deleteprivatemessage';
    
	/**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = Constants_DataTypes::PRIVATEMESSAGE;

	/**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'PrivateMessage_Form';

/**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'privatemessagesnew';

    /**
     * Default category
     *
     * @var int
     */
    protected $_category = Category::ACCOUNT;

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::PRIVATEMESSAGES;

    /**
     * Indicates whether the title is found in another table (true)
     * or directly here (false)
     *
     * @var boolean
     */
    protected $_isTitleTranslated = false;

    /**
     * Indicates whether the description is found in another table (true)
     * or directly here (false)
     *
     * @var boolean
     */
    protected $_isDescriptionTranslated = false;

	/**
	 * No public notification for private messages
	 *
	 * @var boolean
	 */
    protected $_defaultNotification = false;

	public function isReadableBy(User_Row $user, Lib_Acl $acl)
	{
		if($this->submitter == $user->getId()){
			return true;
		}
		
		if($this->toUser != $user->{User::COLUMN_USERID}){
			return false;
		}
		return true;
	}

	/**
	 * Private messages cannot be edited
	 *
	 * @param User_Row $user
	 * @param Lib_Acl $acl
	 * @return boolean
	 */
    public function isEditableBy(User_Row $user, Lib_Acl $acl)
	{
		return false;
	}

	public function isDeletableBy(User_Row $user, Lib_Acl $acl)
	{
		$status = ($this->toUser == $user->{User::COLUMN_USERID});
		return $status;
	}

	/**
	 * No folders for private messages
	 */
    public function getFolderPath(){}

	public function getRecipient()
	{
		$table = new User();
		$recipient = $table->find($this->toUser)->current();
		return $recipient;
	}
    
    public function getTitle()
    {
       	return 'message';
    }

    public function getDescription()
    {
       	throw new Lib_Exception("Private messages have no description.");
    }
    
    /**
     * Returns the content
     *
     * @return string
     */
    public function getContent()
    {
       	$content = $this->content;
        return $content;
    }    
}