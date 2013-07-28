<?php
class Search_Engine_Sphinx
{
	protected $_sphinx;

	protected $_options = array();

	public function __construct()
	{
		$this->_sphinx = new SphinxClient();
		$this->_sphinx->SetServer("localhost",3312);
		$this->_sphinx->SetMatchMode(SPH_MATCH_BOOLEAN);
		//$this->_sphinx->SetRankingMode(SPH_RANK_WORDCOUNT);
		$this->_sphinx->SetLimits(0, 10);
	}

	public function setFilter($attribute, $values, $exclude=false)
	{
		$this->_sphinx->setFilter($attribute, $values, $exclude)
	}

	public function query($term, $index)
	{
		$results = $this->_sphinx->Query($term, $index);
		return $results;
	}

	public function getLastError()
	{
		$error = $this->_sphinx->GetLastError();
		return $error;
	}

	public function buildExcerpts($docs, $index, $words, $options = array())
	{
		$excerpts = $this->_sphinx->BuildExcerpts($docs, $index, $words, $options);
		return $excerpts;
	}
}