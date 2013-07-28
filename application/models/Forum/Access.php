<?php
class Forum_Access extends Zend_Db_Table_Abstract
{
    // An access of value AUTHORIZED means that user can read and post
	const READ = 1;
    const POST = 2;
    const MODERATE = 3;

	protected $_name = Constants_TableNames::FORUM_ACCESS;

    protected $_referenceMap    = array(
        'User' => array(
            'columns'           => User::COLUMN_USERID,
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
    );
}