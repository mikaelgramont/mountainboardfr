<?php
class Lib_Controller_Helper_GoogleSearchLogger extends Zend_Controller_Action_Helper_Abstract
{
	public function direct()
	{
		return $this;
	}

	public function log($referer, User_Row $user)
	{
		$referString = parse_url($referer, PHP_URL_QUERY);
		parse_str($referString, $vars);
		if(!isset($vars['q']) ||!isset($vars['cd']) ||!isset($vars['url'])){
			$msg = "Could not find all referer variables for google search results info: ".$referString;
			Globals::getLogger()->searchLogError($msg);
			return;
		}

		$searchTerm = $vars['q'];
		$rank = $vars['cd'];
		$siteUrl = $vars['url'];

		try{
			$table = new Google_SearchLog();
			$row = $table->fetchNew();

			$row->date = date('Y-m-d H:i:s');
			$row->userId = $user->getId();
			$row->searchTerm = $searchTerm;
			$row->rank = $rank;
			$row->siteUrl = $siteUrl;
			$row->resultsUrl = 'http://www.google.com/search?q='.urlencode($searchTerm);
			$row->save();
		} catch (Exception $e) {
			$msg = "Could not log google search results info: ".$e->getMessage();
			Globals::getLogger()->searchLogError($msg);
		}
	}
}