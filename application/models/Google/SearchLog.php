<?php
class Google_SearchLog extends Zend_Db_Table_Abstract
{
	protected $_name = Constants_TableNames::GOOGLE_SEARCHLOGS;

	/**

CREATE TABLE `googlesearchlogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `userId` int(11) NOT NULL,
  `searchTerm` varchar(512) DEFAULT NULL,
  `rank` tinyint(3) unsigned DEFAULT 0,
  `siteUrl` varchar(512) DEFAULT NULL,
  `resultsUrl` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

	 */
}