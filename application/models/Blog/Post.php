<?php
class Blog_Post extends Data
{
    const ITEM_TYPE = 'blogpost';

    protected $_itemType = 'blogPost';

    protected $_name = Constants_TableNames::BLOGPOST;

    protected $_rowClass = 'Blog_Post_Row';

    /**
     * @var array
     */
    protected $_referenceMap    = array(
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
        'Blog' => array(
            'columns'           => 'blogId',
            'refTableClass'     => 'Blog',
            'refColumns'        => 'id'
        )
    );
}