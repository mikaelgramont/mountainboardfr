<?php
class Blog extends Data
{
    const ITEM_TYPE = 'blog';

    protected $_itemType = 'blog';

    protected $_name = Constants_TableNames::BLOG;

	protected $_rowClass = 'Blog_Row';

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
	);
	
	/**
	 * Returns blogs with at least one post
	 *
	 * @return Zend_Db_Select
	 */
	public function getActiveBlogSelect()
	{
		$select = $this->select();
		$select->from(array('b' => Constants_TableNames::BLOG));
		$select->join(array('p' => Constants_TableNames::BLOGPOST), 'b.id = p.blogId', array());
		$select->group('b.id');
		return $select;
	}
}