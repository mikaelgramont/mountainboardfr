<?php
class Lib_CountryCrawler
{
	/**
	 * Site to parse
	 * @var string
	 */
	public $site = 'http://www.statoids.com/';
	/**
	 * Url of all country pages (could be crawled, but this is simpler)
	 * @var array
	 */
	public $countryPages = array('a','b','c','df','g','hj','kl','m','no','pr','s','tu','vz');	
	
	protected $_skipCountryList = array(
		'Åland Islands', 'Anguilla', 'Antarctica', 'Aruba',
		'British Indian Ocean Territory', 'Bouvet Island',
		'Bosnia and Herzegovina', 'Cook Islands', 'Comoros',
		'Cocos (Keeling) Islands', 'Christmas Island',
		'Cayman Islands', 'Bahamas', 'Falkland Islands',
		'Faroe Islands', 'French Guiana', 'French Polynesia', 'French Southern Territories',
		'Guernsey', 'Guam', 'Gibraltar', 'Heard and McDonald Islands', 'Hong Kong', 'Jersey',
		'Isle of Man', 'Kiribati', 'Latvia', 'Malta', 'Marshall Islands', 'Mayotte',
		'Norfolk Island', 'Niue', 'New Caledonia', 'Netherlands Antilles', 'Nepal',
		'Papua New Guinea', 'Pitcairn', 'Qatar', 'Svalbard and Jan Mayen Islands',
		'Saint Pierre and Miquelon', 'Saint Martin', 'Saint Helena', 'Saint Barthélemy',
		'Vatican City', 'Virgin Islands, British', 'Virgin Islands, U.S.',
		'Wallis and Futuna Islands', 'Western Sahara', 'Yemen','Tonga',
		'Trinidad and Tobago', 'Tokelau', 'Tuvalu', 'Uruguay', 'Uzbekistan', 'Zambia', 
		
		'United States Minor Outlying Islands', 'Vanuatu', 'Zimbabwe','Vietnam',

		// To fix:
		
		// To be handcoded:
		'Guadeloupe', 'Martinique','Monaco', 'Puerto Rico','Reunion',
	
		/*
		'Italy', 'Jamaica', 'Kazakhstan',
		'Korea, South', 'Kuwait', 'Lithuania', 'Martinique', 'Monaco', 'Poland',
		'Puerto Rico', 'Reunion', 'Switzerland', 'Spain', 'Slovenia', 'Singapore',
		'Serbia', 'United States of America'
		*/
		//'United States of America'
	);
	
	protected $_debug = true;
	
	protected $_debugCountryList = array(
		
		'United Kingdom'
	);
	
	/**
	 * Cache front end to store and retrive content
	 * @var $_cacheObject
	 */
	protected $_cacheObject;
	
	public function __construct($cacheObject)
	{
		$this->_cacheObject = $cacheObject;
	}
	
	/**
	 * Returns the list of all countries with their provinces
	 * @return array
	 */
	public function getList()
	{
		$countries = array();
		Globals::getLogger()->debug('start');
		foreach($this->countryPages as $index => $countryPage){
			$url = "l{$countryPage}.html";
			$newCountries = $this->_extractCountries($url);
			
			foreach($newCountries as $newCountry => $newCountryUrl){
				if($this->_debug && !in_array($newCountry, $this->_debugCountryList)){
					continue;
				}
				
				if($newCountry == 'United Kingdom'){
					$provinces = $this->_getUkProvinces();
				} else {
					$provinces = $this->_getProvinces($newCountryUrl);	
				}				
				$countries[$newCountry] = $provinces;
			}

		}
		return $countries;
	}
	
	/**
	 * Returns an array of countries (with their provinces)
	 * from the given url
	 * @param string $url
	 * @return array
	 */
	protected function _extractCountries($url)
	{
		$countries = array();
		
		$html = $this->_load($url);
		$dom = new SimpleHtmlDom();
		$dom->load(utf8_encode($html));
		$rows = $dom->find('table.cy tr');
		$count = count($rows);
		
		foreach($rows as $index => $row){
			if($index == 0 || $index == $count - 1){
				continue;
			}
			$cells = $row->find('td');
			$countryName = $cells[0]->plaintext;
			
			if(in_array($countryName, $this->_skipCountryList)){
				continue;
			}
			
			$links = $cells[1]->find('a');
			$url = $links[0]->href;
			$countries[$countryName] = $url;
		}
		return $countries;
	}
	
	/**
	 * Returns a list of provinces with corresponding fips
	 * for a given country page.
	 * @param string $countryUrl
	 * @return array
	 */
	protected function _getProvinces($countryUrl)
	{
		$foundNameColumn = $foundFipsColumn = false;
		$provinces = array();
		
		$html = $this->_load($countryUrl);
		$dom = new SimpleHtmlDom();
		$dom->load(utf8_encode($html));
		$rows = $dom->find('table.st tr');
		
		$count = count($rows);
		foreach($rows as $index => $row){
			
			if($index == 0){
				$cells = $row->find('th');	
				$columnCount = count($cells);
				// Find the index of columns for name and fips
				foreach($cells as $columnIndex => $cell){
					$text = $cell->plaintext;
					switch(trim(strtolower($text))){
						case 'name':
						case 'province':
						case 'county':
						case 'district':
						case 'parish':
						case 'state':
						case 'claim':
						case 'municipality':
						case 'governorate':
						case 'governorates':
						case 'department':
						case 'region':
						case 'regions':
						case 'arrondissement':
						case 'sheading':
						case 'prefecture':
						case 'island group':
						case 'commune':
						case 'atoll':
						case 'island region':
						case 'subject':
						case 'island council':
						case 'emirate':
						case 'territory':
						case 'kingdom':
						case 'division':
						case 'states':
						case 'rayon':
						case 'new hangeul':
						case 'short name':
						case 'voivodship':
						case 'canton':
						case 'community':
							if(!$foundNameColumn){
								$nameColumnIndex = $columnIndex;
								$foundNameColumn = true;
							}
							break;
						case 'fips':
						case 'iso':
							if(!$foundFipsColumn){
								$fipsColumnIndex = $columnIndex;
								$foundFipsColumn = true;
							}
							break;
					}
				}
				continue;
			}
			
			if($index >= $count - 2){
				break;
			}
			
			$cells = $row->find('td');
			$comments = $row->find('comment');
			$l = count($comments);
			for($i = 0; $i < $l; $i++){
				$comments[$i] = null;
			}			
			
			
			if(count($cells) != $columnCount){
				continue;
			}
			
			if(!$foundNameColumn || !array_key_exists($nameColumnIndex, $cells)){
				echo('no name column - '.$countryUrl.' '.$index.' ');
				continue;
			}			
			$name = $cells[$nameColumnIndex]->plaintext ? trim($cells[$nameColumnIndex]->plaintext) : null;
			
			if(!$foundFipsColumn || !array_key_exists($fipsColumnIndex, $cells)){
				echo('no fips column - '.$countryUrl.' '.$index.' ');
				continue;
			}
			$fips = $cells[$fipsColumnIndex]->plaintext ? trim($cells[$fipsColumnIndex]->plaintext) : null;
			
			if(empty($name) || empty($fips) || $fips == '&nbsp;'){
				// Do not keep provinces without a name or a fips
				continue;
			}
			$provinces[$name] = ''; //$fips;
		}
		return $provinces;
	}
	
	/**
	 * Loads the content of a url, and caches it
	 * @param string $url
	 */
	protected function _load($url)
	{
		$cacheId = str_replace('.', '_', $url);
		
		if($html = $this->_cacheObject->load($cacheId)){
			return $html;
		}
		
		$client = new Zend_Http_Client();
		$fullUrl = $this->site.$url;
		$client->setUri($fullUrl);
		$response = $client->request(Zend_Http_Client::GET);
		$html = $response->getBody();
		
		$this->_cacheObject->save($html, $cacheId);
		return $html;
	}
	
	protected function _getUkProvinces()
	{
		$list = array (
		    'Aberdeen' => '',
		    'Aberdeenshire' => '',
		    'Anglesey' => '',
		    'Angus' => '',
		    'Antrim' => '',
		    'Ards' => '',
		    'Argyll and Bute' => '',
		    'Armagh' => '',
		    'Ballymena' => '',
		    'Ballymoney' => '',
		    'Banbridge' => '',
		    'Barnsley' => '',
		    'Bath and North East Somerset' => '',
		    'Bedford' => '',
		    'Belfast' => '',
		    'Birmingham' => '',
		    'Blackburn with Darwen' => '',
		    'Blackpool' => '',
		    'Blaenau Gwent' => '',
		    'Bolton' => '',
		    'Bournemouth' => '',
		    'Bracknell Forest' => '',
		    'Bradford' => '',
		    'Bridgend' => '',
		    'Brighton and Hove' => '',
		    'Bristol' => '',
		    'Buckinghamshire' => '',
		    'Bury' => '',
		    'Caerphilly' => '',
		    'Calderdale' => '',
		    'Cambridgeshire' => '',
		    'Cardiff' => '',
		    'Carmarthenshire' => '',
		    'Carrickfergus' => '',
		    'Castlereagh' => '',
		    'Central Bedfordshire' => '',
		    'Ceredigion' => '',
		    'Cheshire East' => '',
		    'Cheshire West and Chester' => '',
		    'Clackmannanshire' => '',
		    'Coleraine' => '',
		    'Conwy' => '',
		    'Cookstown' => '',
		    'Cornwall' => '',
		    'Coventry' => '',
		    'Craigavon' => '',
		    'Cumbria' => '',
		    'Darlington' => '',
		    'Denbighshire' => '',
		    'Derby' => '',
		    'Derbyshire' => '',
		    'Derry' => '',
		    'Devon' => '',
		    'Doncaster' => '',
		    'Dorset' => '',
		    'Down' => '',
		    'Dudley' => '',
		    'Dumfries and Galloway' => '',
		    'Dundee' => '',
		    'Dungannon' => '',
		    'Durham' => '',
		    'East Ayrshire' => '',
		    'East Dunbartonshire' => '',
		    'East Lothian' => '',
		    'East Renfrewshire' => '',
		    'East Riding of Yorkshire' => '',
		    'East Sussex' => '',
		    'Edinburgh' => '',
		    'Eilean Siar' => '',
		    'Essex' => '',
		    'Falkirk' => '',
		    'Fermanagh' => '',
		    'Fife' => '',
		    'Flintshire' => '',
		    'Gateshead' => '',
		    'Glasgow' => '',
		    'Gloucestershire' => '',
		    'Greater London' => '',
		    'Gwynedd' => '',
		    'Halton' => '',
		    'Hampshire' => '',
		    'Hartlepool' => '',
		    'Herefordshire' => '',
		    'Hertfordshire' => '',
		    'Highland' => '',
		    'Inverclyde' => '',
		    'Isle of Wight' => '',
		    'Isles of Scilly' => '',
		    'Kent' => '',
		    'Kingston upon Hull' => '',
		    'Kirklees' => '',
		    'Knowsley' => '',
		    'Lancashire' => '',
		    'Larne' => '',
		    'Leeds' => '',
		    'Leicester' => '',
		    'Leicestershire' => '',
		    'Limavady' => '',
		    'Lincolnshire' => '',
		    'Lisburn' => '',
		    'Liverpool' => '',
		    'Luton' => '',
		    'Magherafelt' => '',
		    'Manchester' => '',
		    'Medway' => '',
		    'Merthyr Tydfil' => '',
		    'Middlesbrough' => '',
		    'Midlothian' => '',
		    'Milton Keynes' => '',
		    'Monmouthshire' => '',
		    'Moray' => '',
		    'Moyle' => '',
		    'Neath Port Talbot' => '',
		    'Newcastle upon Tyne' => '',
		    'Newport' => '',
		    'Newry and Mourne' => '',
		    'Newtownabbey' => '',
		    'Norfolk' => '',
		    'Northamptonshire' => '',
		    'North Ayrshire' => '',
		    'North Down' => '',
		    'North Lanarkshire' => '',
		    'North Lincolnshire' => '',
		    'North Somerset' => '',
		    'North Tyneside' => '',
		    'Northumberland' => '',
		    'North Yorkshire' => '',
		    'Nottingham' => '',
		    'Nottinghamshire' => '',
		    'Oldham' => '',
		    'Omagh' => '',
		    'Orkney Islands' => '',
		    'Oxfordshire' => '',
		    'Pembrokeshire' => '',
		    'Perthshire and Kinross' => '',
		    'Peterborough' => '',
		    'Plymouth' => '',
		    'Poole' => '',
		    'Portsmouth' => '',
		    'Powys' => '',
		    'Reading' => '',
		    'Redcar and Cleveland' => '',
		    'Renfrewshire' => '',
		    'Rhondda, Cynon, Taff' => '',
		    'Rochdale' => '',
		    'Rotherham' => '',
		    'Rutland' => '',
		    'Saint Helens' => '',
		    'Salford' => '',
		    'Sandwell' => '',
		    'Scottish Borders' => '',
		    'Sefton' => '',
		    'Sheffield' => '',
		    'Shetland Islands' => '',
		    'Shropshire' => '',
		    'Slough' => '',
		    'Solihull' => '',
		    'Somerset' => '',
		    'Southampton' => '',
		    'South Ayrshire' => '',
		    'Southend-on-Sea' => '',
		    'South Gloucestershire' => '',
		    'South Lanarkshire' => '',
		    'South Tyneside' => '',
		    'Staffordshire' => '',
		    'Stirling' => '',
		    'Stockport' => '',
		    'Stockton-on-Tees' => '',
		    'Stoke-on-Trent' => '',
		    'Strabane' => '',
		    'Suffolk' => '',
		    'Sunderland' => '',
		    'Surrey' => '',
		    'Swansea' => '',
		    'Swindon' => '',
		    'Tameside' => '',
		    'Telford and Wrekin' => '',
		    'Thurrock' => '',
		    'Torbay' => '',
		    'Torfaen' => '',
		    'Trafford' => '',
		    'Vale of Glamorgan' => '',
		    'Wakefield' => '',
		    'Walsall' => '',
		    'Warrington' => '',
		    'Warwickshire' => '',
		    'West Berkshire' => '',
		    'West Dunbartonshire' => '',
		    'West Lothian' => '',
		    'West Sussex' => '',
		    'Wigan' => '',
		    'Wiltshire' => '',
		    'Windsor and Maidenhead' => '',
		    'Wirral' => '',
		    'Wokingham' => '',
		    'Wolverhampton' => '',
		    'Worcestershire' => '',
		    'Wrexham' => '',
		    'York' => '',
		  );
		return $list;
	}
}
