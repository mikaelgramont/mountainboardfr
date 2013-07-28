<?php
class Forum_Post extends Data
{
    const ITEM_TYPE = 'post';

    protected $_itemType = 'post';

    protected $_name = Constants_TableNames::POST;

    protected $_rowClass = 'Forum_Post_Row';

	/**
     * @var array
     */
    protected $_referenceMap    = array(
        'Topic' => array(
            'columns'           => 'topicId',
            'refTableClass'     => 'Forum_Topic',
            'refColumns'        => 'id'
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