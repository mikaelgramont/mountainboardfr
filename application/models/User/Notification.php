<?php
class User_Notification extends Zend_Db_Table_Abstract
{
	const NOTIFY 			= 1;
	const DO_NOT_NOTIFY 	= 0;

	const MEDIUM_HOMEPAGE 	= 'homePage';
	const MEDIUM_EMAIL		= 'email';
	const MEDIUM_TWITTER 	= 'twitter';
	const MEDIUM_FACEBOOK 	= 'facebook';

	protected $_rowClass = 'User_Notification_Row';

	protected $_name = Constants_TableNames::USER_NOTIFICATIONS;

    protected $_referenceMap    = array(
        'User' => array(
            'columns'           => 'userId',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
    );

    public static $mediums = array(
    	self::MEDIUM_HOMEPAGE,
    	self::MEDIUM_EMAIL,
    	self::MEDIUM_TWITTER,
    	self::MEDIUM_FACEBOOK
    );

    public static $available = array(
		Constants_DataTypes::BLOGPOST		=> self::NOTIFY,
		Constants_DataTypes::COMMENT		=> self::NOTIFY,
		Constants_DataTypes::DOSSIER		=> self::NOTIFY,
		Constants_DataTypes::MEDIAALBUM		=> self::DO_NOT_NOTIFY,
		Constants_DataTypes::NEWS			=> self::NOTIFY,
		Constants_DataTypes::PHOTO			=> self::NOTIFY,
		Constants_DataTypes::FORUMTOPIC		=> self::NOTIFY,
		Constants_DataTypes::PRIVATEMESSAGE	=> self::NOTIFY,
		Constants_DataTypes::FORUMPOST		=> self::NOTIFY,
		Constants_DataTypes::TEST			=> self::NOTIFY,
		Constants_DataTypes::TRICK			=> self::NOTIFY,
		Constants_DataTypes::SPOT			=> self::NOTIFY,
		Constants_DataTypes::USER			=> self::DO_NOT_NOTIFY,
		Constants_DataTypes::VIDEO			=> self::NOTIFY,
    );

}