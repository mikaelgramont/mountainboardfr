<?php
class Search_Log extends Zend_Db_Table_Abstract
{
	protected $_name = Constants_TableNames::SEARCHLOGS;

	/**

CREATE TABLE `searches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `userId` int(11) NOT NULL,
  `ip` int(39) NOT NULL,
  `hostname` varchar(256) NOT NULL,
  `sessionId` varchar(32)  NOT NULL,
  `searchTerm` varchar(512) DEFAULT NULL,
  `results` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;


	 */
}