<?php
class News extends Article
{
    const ITEM_TYPE = 'news';

    protected $_itemType = 'news';

    protected $_name = Constants_TableNames::NEWS;

    protected $_rowClass = 'News_Row';

    protected $_referenceMap    = array(
       'LastEditor' => array(
            'columns'           => 'last_editor',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Submitter' => array(
            'columns'           => 'submitter',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Author' => array(
            'columns'           => 'author',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Dpt' => array(
            'columns'           => 'dpt',
            'refTableClass'     => 'Dpt',
            'refColumns'        => 'id'
        ),
        'Spot' => array(
            'columns'           => 'spot',
            'refTableClass'     => 'Spot',
            'refColumns'        => 'id'
        ),
    );
}