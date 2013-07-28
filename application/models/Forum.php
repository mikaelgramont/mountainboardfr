<?php
class Forum extends Data
{
    const ITEM_TYPE = 'forum';

    protected $_itemType = 'forum';

    protected $_name = Constants_TableNames::FORUM;

    protected $_rowClass = 'Forum_Row';

    const CAT_GENERAL = 'forum général';
    const CAT_ASSOCIATIONS = 'forums des associations';
    
    const PUBLIC_FORUM = 'public';
    const PRIVATE_FORUM = 'private';

    public static $categories = array(
        0 => null,
        1 => self::CAT_GENERAL,
        2 => self::CAT_ASSOCIATIONS,
    );
    
    protected $_referenceMap    = array(
        'LastPoster' => array(
            'columns'           => 'lastPoster',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'LastEditor' => array(
            'columns'           => 'lastEditor',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Submitter' => array(
            'columns'           => 'submitter',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
    );    
}