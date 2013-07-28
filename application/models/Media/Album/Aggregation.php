<?php
class Media_Album_Aggregation extends Zend_Db_Table_Abstract
{
	const KEYNAME_USER = 'user';

	protected $_name = Constants_TableNames::AGGREGATION;

	protected $_rowClass = 'Media_Album_Aggregation_Row';
}