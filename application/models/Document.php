<?php
abstract class Document extends Data
{
    protected $_rowClass = 'Document_Row';

    protected $_type = 'document';

    public static $documentClasses = array(
        News::ITEM_TYPE,
        Dossier::ITEM_TYPE,
    );

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
        'Author' => array(
            'columns'           => 'author',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        )
    );
}