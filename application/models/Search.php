<?php
class Search
{
	/***
	 * Separate indexes for all item types, that include comments tags and locations when applicable.
	 * Separate index just for comments
	 * Separate index just for tags
	 */

	protected $_engine;

	const DEFAULT_SEARCH = '*';

	const INDEX_FORUMS = 'forumsIndex';
	const INDEX_EVENTS = 'eventsIndex';
	const INDEX_SPOTS = 'spotsIndex';
	const INDEX_TRICKS = 'tricksIndex';
	const INDEX_TESTS = 'testsIndex';
	const INDEX_NEWS = 'newsIndex';
	const INDEX_DOSSIERS = 'dossiersIndex';
	const INDEX_PHOTOS = 'photosIndex';
	const INDEX_VIDEOS = 'videosIndex';

	const INDEX_COMMENTS = 'commentsIndex';

	const INDEX_TAGS = 'tagsIndex';

	const INDEX_LOCATIONS = 'locationsIndex';

	/**
	 * Parameters used when running queries
	 * @var array
	 */
	protected $_params = array();

	/**
	 * The name of
	 * @var unknown_type
	 */
	protected $_currentSearchType;

	/**
	 * Lists which indexes (array values) must be searched when performing a search
	 * on a given filter (array keys)
	 * @var array
	 */
	protected $_searchTypes = array(
		self::DEFAULT_SEARCH	=> array(self::INDEX_FORUMS, self::INDEX_EVENTS, self::INDEX_SPOTS, self::INDEX_TRICKS, self::INDEX_TESTS, self::INDEX_NEWS, self::INDEX_DOSSIERS, self::INDEX_PHOTOS, self::INDEX_VIDEOS),
		Forum::ITEM_TYPE 		=> array(self::INDEX_FORUMS),
		Event::ITEM_TYPE 		=> array(self::INDEX_EVENTS),
		Spot::ITEM_TYPE 		=> array(self::INDEX_SPOTS),
		Trick::ITEM_TYPE 		=> array(self::INDEX_TRICKS),
		Test::ITEM_TYPE 		=> array(self::INDEX_TESTS),
		News::ITEM_TYPE 		=> array(self::INDEX_NEWS),
		Dossier::ITEM_TYPE 		=> array(self::INDEX_DOSSIERS),
		Media_Item::TYPE_PHOTO	=> array(self::INDEX_PHOTOS),
		Media_Item::TYPE_VIDEO	=> array(self::INDEX_VIDEOS),
		Comment::ITEM_TYPE 		=> array(self::INDEX_COMMENTS),
		Tag::ITEM_TYPE 			=> array(self::INDEX_TAGS),
		Location::ITEM_TYPE 	=> array(self::INDEX_LOCATIONS),
	);

	public function __construct($params = array())
	{
		$this->_setParams($params);
		$this->_setupEngine();
		$this->_currentSearchType = self::DEFAULT_SEARCH;
	}

	protected function _setParams(array $customParams)
	{
		$params = array_merge(
			array(
				'offset' => 0,
				'amount' => 10,
				''
			),
			$customParams
		);
		$this->_params = $params;

	}

	protected function _setupEngine()
	{
		$this->_engine = new SphinxClient();
		$this->_engine->SetServer(SPHINX_HOST, SPHINX_PORT);
		$this->_engine->SetMatchMode(SPH_MATCH_BOOLEAN);
		$this->_engine->SetMatchMode(SPH_MATCH_EXTENDED2);
		$this->_engine->SetRankingMode(SPH_RANK_WORDCOUNT);
		$this->_engine->SetLimits(0, 10);
	}

	public function execute($terms, $cache)
	{
		$cacheId = 'search_'.str_replace('-', '_', Utils::cleanString($terms));

		if(ALLOW_CACHE){
			$results = $cache->load($cacheId);
			if($results !== false){
				return $results;
			}
		}

		foreach($this->_getSearchIndexes() as $index){
			$this->_engine->AddQuery($terms, $index);
		}

		$results = $this->_engine->RunQueries();
		if ($results === false || $results === null) {
			throw new Lib_Exception_Sphinx('Search failure: '.$this->_engine->getLastError());
		}

		list($sortedObjects, $objectStrings, $searchInfo, $results) = $this->_parseResults($results);

		// the value of $index doesn't seem to make a difference
		$excerpts = $this->buildExcerpts($objectStrings, $index, $terms);

		$this->log($terms, $results);

		$results = array(
			$sortedObjects,
			$excerpts,
			$searchInfo
		);

		if(ALLOW_CACHE){
        	$cache->save($results, $cacheId);
        }

		return $results;
	}

	protected function _getSearchIndexes()
	{
		$indexes = $this->_searchTypes[$this->_currentSearchType];
		return $indexes;
	}

	protected function _parseResults($results)
	{
		$combinedResults = array();
		$time = $totalFound = 0;


		foreach($results as $result){
			if(!isset($result['matches']) || count($result['matches']) == 0) {
				continue;
			}

			foreach($result['matches'] as $id => $info){
				if(!isset($combinedResults[$id]) || $combinedResults[$id] < $info['weight']){
					$combinedResults[$id] = $info['weight'];
				}
			}

			if($time < $result['time']){
				$time = $result['time'];
			}
			$totalFound += $result['total_found'];
		}
		arsort($combinedResults);
		$objects = $this->_getObjects($combinedResults);
		$sortedObjects = $objectStrings = array();
		foreach($combinedResults as $id => $weight){
			if(!isset($objects[$id])){
				Globals::getLogger()->search("Object $id was not found");
				continue;
			}

			$sortedObjects[] = $objects[$id];
			$objectStrings[] = strip_tags($objects[$id]->getFlatContent());
		}

		return array(
			$sortedObjects,
			$objectStrings,
			array(
				'time' => $time,
				'totalFound' => $totalFound,
			),
			$combinedResults
		);
	}

	protected function _getObjects($results)
	{
		$return = array();
		if(empty($results)){
			return $return;
		}

		$ids = array_keys($results);
		$table = new Item();
		$items = $table->fetchAll("id IN (".implode(',', $ids).")");
		foreach($items as $item){
			$return[$item['id']] = Data::factory($item['itemId'], $item['itemType'], false);
		}

		return $return;
	}

	public function buildExcerpts($docs, $index, $words, $customOptions = array())
	{
		$defaultOptions = array(
			'before_match' => '<span class="searchMatch">',
			'after_match' => '</span>'
		);
		$options = array_merge($defaultOptions, $customOptions);

		$excerpts = $this->_engine->buildExcerpts($docs, $index, $words, $options);
		return $excerpts;
	}

	public function getAllowedTypes()
	{
		return array(
			Forum::ITEM_TYPE,
			Event::ITEM_TYPE,
			Spot::ITEM_TYPE,
			Trick::ITEM_TYPE,
			Test::ITEM_TYPE,
			News::ITEM_TYPE,
			Dossier::ITEM_TYPE,
			Comment::ITEM_TYPE,
			Tag::ITEM_TYPE,
			Location::ITEM_TYPE,
		);
	}

	public function setSimpleForm()
	{
		$this->_form = new Search_Form_Simple($this);
	}

	public function setAdvancedForm()
	{
		$this->_form = new Search_Form_Advanced($this);
	}

	public function getForm()
	{
		return $this->_form;
	}

	public function prepare($params)
	{

	}

	public function log($terms, $results)
	{
		/**
		 * @todo log search and result info
		 * session_id, ip, hostname, datetime, terms, found, returned, time
		 */
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'local';
		$hostname = Utils::getHost($ip);

		$table = new Search_Log();
		$log = $table->fetchNew();
		$log->date = date('Y-m-d H:i:s');
		$log->userId = Globals::getUser()->getId();
		$log->sessionId = session_id();
		$log->ip = $ip;
		$log->hostname = $hostname;
		$log->searchTerm = $terms;
		$log->results = implode(',', array_keys($results));
		$log->save();
	}
}
