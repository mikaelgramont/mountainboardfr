<?php
class Item_View extends Zend_Db_Table_Abstract
{
    protected $_name = Constants_TableNames::ITEM_VIEW;

	/**
	 * Item type
	 *
	 * @var unknown_type
	 */
	protected $_itemType = 'itemview';
}