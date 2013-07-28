<?php
class Forum_Topic extends Data
{
    const ITEM_TYPE = 'topic';

    protected $_itemType = 'topic';

    protected $_name = Constants_TableNames::TOPIC;

    protected $_rowClass = 'Forum_Topic_Row';

    /**
     * @var array
     */
    protected $_referenceMap    = array(
        'Forum' => array(
            'columns'           => 'forumId',
            'refTableClass'     => 'Forum',
            'refColumns'        => 'id'
        ),
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