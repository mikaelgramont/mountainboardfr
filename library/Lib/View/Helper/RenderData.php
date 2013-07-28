<?php
class Lib_View_Helper_RenderData extends Zend_View_Helper_Abstract
{
    protected $_additionalData;

    /**
     * Renders a document
     *
     * @param Document_Row $document
     * @return string
     * @throws Lib_Exception
     */
    public function renderData(Data_Row $data, array $additionalData, $hasMap = false, Media_Album_Row $album = null)
    {
        if(empty($data)){
            throw new Lib_Exception('No document given for rendering');
        }

        if(!array_key_exists('comments', $additionalData)){
            $additionalData['comments'] = array();
        }
        $this->_additionalData = $additionalData;

        $rowClass = get_class($data);

        // Dpt/Country
        if($rowClass == 'Dpt_Row' || $rowClass == 'Country_Row'){
        	$this->view->richTextContent = false;
            $content = $this->renderRegion($data, $album, $additionalData['items'], $additionalData['spots']);
            return $content;
        }

        // Spots
        if($rowClass == 'Spot_Row'){
            $content = $this->renderSpot($data, $album, $hasMap);
            return $content;
        }

        // Tricks
        if($rowClass == 'Trick_Row'){
            $content = $this->renderTrick($data, $album);
            return $content;
        }

        // Blog Posts
        if($rowClass == 'Blog_Post_Row'){
            $content = $this->renderBlogPost($data);
            return $content;
        }

        // PrivateMessage
        if($rowClass == 'PrivateMessage_Row'){
            $content = $this->renderPrivateMessage($data);
            return $content;
        }

        // Events
        if($rowClass == 'Event_Row'){
            $content = $this->view->renderData_Event($data);
            return $content;
        }

        // Articles
        $tableClass = explode('_', $rowClass);
        $tableClass = strtolower($tableClass[0]);
        if(in_array($tableClass, Article::$articleClasses)){
            $content = $this->renderArticle($data, $album, $hasMap);
            return $content;
        }

        throw new Lib_Exception("No renderer for data of type $rowClass");
    }

    /**
     * Renders an article
     *
     * @param Article_Row $article
     * @return string
     * @throws Lib_Exception
     */
    public function renderArticle(Article_Row $article, Media_Album_Row $album = null, $hasMap = false)
    {
        $class = get_class($article);
        switch($class){
            case 'Dossier_Row':
            	break;

            case 'Test_Row':
            	// Add popup script for old tests
            	if($article->id <= MOST_RECENT_OLDSTYLE_TEST){
            		$path = '/' . $article->getFolderPath() . '/';
            		$js = <<<SCRIPT
var popup_pic = function (image,width, height){
	var url = '$path' + image;
	width = width + 90;
	height = height + 90;
	var params = 'width=' + width + ',height=' + height;
	window.open(url,'_blank',params);
};
SCRIPT;
					$this->view->JQuery()->addJavascript($js);
            	}
            	break;

            case 'News_Row':
            	// Add popup script for old news
            	if($article->id <= MOST_RECENT_OLDSTYLE_NEWS){
            		$path = '/' . $article->getFolderPath() . '/';
            		$js = <<<SCRIPT
var popup_pic = function (image,width, height){
	var url = '$path' + image;
	width = width + 90;
	height = height + 90;
	var params = 'width=' + width + ',height=' + height;
	window.open(url,'_blank',params);
};
SCRIPT;
					$this->view->JQuery()->addJavascript($js);
            	}
                break;

            default:
                throw new Lib_Exception("Unknown document type: $class");
                break;
        }

        $content = '';
        $content .= '<h1 class="articleTitle">'.ucfirst($article->getTitle()).'</h1>'.PHP_EOL;
        if(($article->isEditableBy($this->view->user, $this->view->acl))){
            $content .= $this->view->itemStatus($article, true);
            $content .= $this->view->editLink($article);
        }
        if(($article->isDeletableBy($this->view->user, $this->view->acl))){
            $content .= $this->view->deleteLink($article);
        }
        $content .= $this->view->renderDataInformation($article).PHP_EOL;
        $content .= $this->view->shareButtons()->all(APP_URL.$this->view->url(), 'horizontal').PHP_EOL;
        $content .= $this->view->renderTags($article->getTags());

        $content .= '<p class="description">'.$article->getDescription().'</p>'.PHP_EOL;
        $content .= '<div class="clear"></div>'.PHP_EOL;

        $content .= $article->getContentFromCdn($this->view->cdnHelper).PHP_EOL;
        if($hasMap){
            $content .= $this->_getMap($article);
        }

        if(!empty($album)){
			$content .= $this->view->itemLink($album);
			$content .= $this->view->albumPreview($album);
        }

        return $content;
    }

    /**
     * Renders a dpt
     *
     * @param Dpt_Row|Country_Row $region
     * @return string
     * @throws Lib_Exception
     */
    public function renderRegion($region, Media_Album_Row $album = null, $items = array(), $spotsWithoutLocation = array())
    {
        $string = ucfirst($this->view->translate('createspot'));
		$bounds = $region->getBounds();
        $content = $addSpot = $dptTab = '';

        if($region instanceof Dpt_Row){
        	$title = ucfirst($region->getTitle(). ', '.$region->getCountry()->getTitle());

        	$spotTable = new Spot();
	        $spot = $spotTable->fetchNew();
        	if($spot->isCreatableBy($this->view->user, $this->view->acl)){
				$addSpot  = '<div class="actionLinkContainer addSpot dptPage">'.PHP_EOL;
	        	$addSpot .= $this->view->routeLink('createdatafordpt', $string, array('dataType' => 'spot', 'dpt' => $region->id));
	        	$addSpot .= '</div>'.PHP_EOL;
	        }

	        $regionIntro = $this->_renderDptContent($region);
	        $bottomContent = '';
		} else {
			$title = ucfirst($region->getTitle());
			list($regionIntro, $bottomContent) = $this->_renderCountryContent($region);
		}

        $content .= '<h1 class="pageTitle">'.ucfirst($title).'</h1>'.PHP_EOL;
        $content .= $regionIntro.PHP_EOL;
        $content .= $addSpot;

		//$content .= $this->view->itemList($this->_additionalData['spots']);
        $params = array(
			'showMarker' => false,
			'mapZoneClass' => 'regionMapZone',
			'width' => '51.5em',
			'height' => '30em',
			'bounds' => $bounds,
			'items' => $items
		);

		if($region instanceof Country_Row){
			$dptTabTitle = ucfirst($this->view->translate('regionTabDpt'));
			$dptTab = "				<li><a href=\"#dptList\">{$dptTabTitle}</a></li>".PHP_EOL;
		}

		$mapTabTitle = ucfirst($this->view->translate('regionTabMap'));
		$listTabTitle = ucfirst($this->view->translate('regionTabList'));
		$content .= <<<HTML
		<div id="regionTabs">
			<ul>
				<li><a href="#mapZone">{$mapTabTitle}</a></li>
				<li><a href="#mapItemsList">{$listTabTitle}</a></li>
{$dptTab}			</ul>

HTML;

		$content .= $this->_getMap($region, $params);
        $content .= $this->_renderMapItemsTable($items, $spotsWithoutLocation, $this->view->user, $this->view->acl);

		if(!empty($album)){
			$content .= $this->view->itemLink($album);
			$content .= $this->view->albumPreview($album);
        }

        $content .= $bottomContent.PHP_EOL;
		$content .= '	</div>';

        return $content;
        //http://htmlcoderhelper.com/problems-with-google-maps-api-v3-jquery-ui-tabs/
    }

	/**
     * Renders a spot
     *
     * @param Spot_Row $spot
     * @return string
     * @throws Lib_Exception
     */
    public function renderSpot(Spot_Row $spot, Media_Album_Row $album = null, $hasMap = false)
    {
        $content = '';
        $content .= '	<div class="spotTitle">		<h1>'.ucfirst($spot->getTitle());
        if(($spot->isEditableBy($this->view->user, $this->view->acl))){
            $content .= $this->view->itemStatus($spot);
            $content .= $this->view->editLink($spot);
        }
        if(($spot->isDeletableBy($this->view->user, $this->view->acl))){
            $content .= $this->view->deleteLink($spot);
        }
        $content .= '		</h1>'.PHP_EOL;
        $content .= $this->view->renderDataInformation($spot, 'spotInformation');
        $content .= '	</div>'.PHP_EOL;
        $content .= '	<div class="spotMetadata">'.PHP_EOL;
        $content .= '		<div class="spotInfo">'.PHP_EOL;

       	if($locationString = $this->view->renderLocationInfo($spot)){
       		$content .= '			<p class="location">'. ucfirst($this->view->translate('locationString')) .': '. $locationString . '</p>'.PHP_EOL;
       	}

        $content .= '			<p class="typeInfo">';
        $content .= ucfirst($this->view->translate('spotType')) . ': ' .$spot->getSpotType(). ' - '. ucfirst($this->view->translate('groundType')) . ': ' .$spot->getGroundType();
        $content .= '			</p>'.PHP_EOL;
        $content .= '			<h2>'.$spot->getDescription().'</h2>'.PHP_EOL;
        $content .= $this->view->renderTags($spot->getTags());
        $content .= '		</div>'.PHP_EOL;
        $content .= '		<div class="spotAlbum">'.PHP_EOL;
        if(!empty($album)){
			$content .= $this->view->albumPreview($album);
        }
        $content .= '		</div>'.PHP_EOL;
        if($hasMap){
            $content .= $this->_getMap($spot);
        }
        $content .= '	</div>'.PHP_EOL;
        return $content;
    }

    /**
     * Renders a trick
     *
     * @param Trick_Row $trick
     * @return string
     * @throws Lib_Exception
     */
    public function renderTrick(Trick_Row $trick, Media_Album_Row $album = null)
    {
        $content = '';
        $content .= '	<div class="trickTitle">		<h1>'.ucfirst($trick->getTitle());
        if(($trick->isEditableBy($this->view->user, $this->view->acl))){
            $content .= $this->view->itemStatus($trick);
            $content .= $this->view->editLink($trick);
        }
        if(($trick->isDeletableBy($this->view->user, $this->view->acl))){
            $content .= $this->view->deleteLink($trick);
        }
        $content .= '		</h1>'.PHP_EOL;
        $content .= $this->view->renderDataInformation($trick, 'trickInformation');
        $content .= '	</div>'.PHP_EOL;
        $content .= '	<div class="trickMetadata">'.PHP_EOL;
        $content .= '		<div class="trickInfo">'.PHP_EOL;
        $content .= '			<h2 class="trickDescription">'.$trick->getDescription().'		</h2>'.PHP_EOL;
        $tip = $trick->getTrickTip();
        if($tip){
        	$content .= '			<h2 class="trickTip">'.$tip.'</h2>'.PHP_EOL;
        }
        $content .= $this->view->renderTags($trick->getTags());
        $content .= '		</div>'.PHP_EOL;

        if(!empty($album)){
			$content .= '	<div class="trickAlbum">'.PHP_EOL;
			$content .= $this->view->albumPreview($album);
			$content .= '	</div>'.PHP_EOL;
        }
        $content .= '	</div>'.PHP_EOL;

        return $content;
    }

    public function renderBlogPost(Blog_Post_Row $blogPost)
    {
        $blog = $blogPost->getBlog();
        $content = $this->view->itemLink($blog);
        $content .= '<h1>'.ucfirst($blogPost->getTitle());
        if(($blogPost->isEditableBy($this->view->user, $this->view->acl))){
            $content .= $this->view->itemStatus($blogPost);
            $content .= $this->view->editLink($blogPost);
        }
        if(($blogPost->isDeletableBy($this->view->user, $this->view->acl))){
            $content .= $this->view->deleteLink($blogPost);
        }
        $content .= '</h1>'.PHP_EOL;
        $content .= $this->view->renderDataInformation($blogPost);
        $content .= $this->view->renderTags($blogPost->getTags());
        $content .= '<div>'.$blogPost->getContent().'</div>';

        return $content;
    }

	/**
	 * Renders a private message
	 *
	 * @param PrivateMessage_Row $message
	 */
    public function renderPrivateMessage(PrivateMessage_Row $message)
	{
		if(!$message->read){
			// Mark private message as read
			$message->read = 1;
			$message->save(true);
		}

		$content = $this->view->privateMessageButtons('message');
		$content .= '<h2>'.$message->getTitle().'</h2>'.PHP_EOL;
		$content .= $this->view->renderDataInformation($message);
		$content .= '<p>'.$message->getDescription().'</p>'.PHP_EOL;
		$content .= '<p>'.$this->view->routeLink('privatemessagesreply', null, array('name' => $message->getTitle(), 'id' => $message->getId())).'</p>'.PHP_EOL;
		return $content;
	}

    /**
     * Builds the static map zone
     *
     * @param Data_Row $data
     * @return string
     */
    protected function _getMap(Data_Row $data, $userParams = array())
    {
    	if(!APP_GOOGLE_MAPS_ACTIVE){
    		return '';
    	}

        $defaultParams = array(
        	'lang' => $this->view->user->lang,
        	'width' => '57em',
        	'height' => '20em',
        	'mapZoneId' => 'mapZone',
        	'mapElementId' => 'mapElement',
        	'mapZoneClass' => '',
        );
        $params = array_merge($defaultParams, $userParams);

		$class = empty($params['mapZoneClass']) ? '' : ' class="'.$params['mapZoneClass'].'"';

        $content = '<div id="'.$params['mapZoneId'].'"'.$class.'>'.PHP_EOL;

        if($data instanceof Dpt_Row || $data instanceof Country_Row){
	        $this->view->googleMaps()->regionDisplay($params);
	        $content .= '<a name="mapAnchor" id="mapAnchor"></a>'.PHP_EOL;
	        $content .= '   <div style="width:'.$params['width'].';height:'.$params['height'].';" id="'.$params['mapElementId'].'"></div>'.PHP_EOL;
        } else {
	        $location = $data->getLocation();
	        if(empty($location)){
	            /**
	             * @todo: addLocation button
	             */
	            $js = '';
	        } else {

	        	$this->view->googleMaps()->displayItem($location, $params);
	            $content .= '   <div style="width:'.$params['width'].';height:'.$params['height'].';" id="'.$params['mapElementId'].'">'.PHP_EOL.
	            			'		<img src="'.$this->view->staticGoogleMap($location).'" alt="" />'.PHP_EOL.
	            			'	</div>'.PHP_EOL;

	            $coordinates = ucfirst($this->view->translate('coordinates'));
	            $content .= <<<GEO
<div class="geo">{$coordinates}:
 <span class="latitude">{$location->latitude}</span>,
 <span class="longitude">{$location->longitude}</span>
</div>
GEO;
	        	$content .= '<a href="http://maps.google.com/maps?q='.urlencode(ucfirst($data->getTitle())).'@'.$location->latitude.','.$location->longitude.'">'.ucfirst($this->view->translate('viewOnGoogleMaps')).'</a>'.PHP_EOL;
	        }
		}


        $content .= '</div>'.PHP_EOL;
		return $content;
    }

    protected function _renderMapItemsTable($items, $spotsWithoutLocation, User_Row $user, Lib_Acl $acl)
    {
    	$itemTypes = array();

    	$type = ucfirst($this->view->translate('type'));
		$name = ucfirst($this->view->translate('name'));
		$info = ucfirst($this->view->translate('info'));

    	$html = <<<HTML
<div class="pager" id="pager">
    <form>
        <span class="tableNav first">First</span>
        <span class="tableNav prev">Prev</span>
        <input type="text" class="pagedisplay"/>
        <span class="tableNav next">Next</span>
        <span class="tableNav last">Last</span>
        <select class="pagesize">
            <option value="10" selected="selected">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="40">40</option>
        </select>
    </form>
</div>
HTML;

$html = <<<HTML
<div id="mapItemsList">
	<table class="mapItems">
		<thead>
			<tr>
				<th>{$type}</th>
				<th>{$name}</th>
				<th>{$info}</th>
			</tr>
		</thead>
		<tbody>
HTML;
		$spotsWithLocationCount = 0;
		foreach($items as $item){
			$html .= $this->_renderItem($item, $itemTypes, $user, $acl);
			if($item instanceof Spot_Row){
				$spotsWithLocationCount++;
			}
		}

		foreach($spotsWithoutLocation as $spot){
			$html .= $this->_renderSpotWithoutLocation($spot, $itemTypes, $user, $acl);
		}

		$html .= <<<HTML
		</tbody>
	</table>
</div>
HTML;

		if(empty($itemTypes)){
			// No items to display
			return '';
		} else {
			$list = '	<ul id="mapItemsSummary">'.PHP_EOL;
			foreach($itemTypes as $itemType => $count){

				if($count == 1){
					$translatedItemType = ucfirst($this->view->translate('itemSing_'.$itemType));
				} else {
					$translatedItemType = ucfirst($this->view->translate('itemPlur_'.$itemType));
				}
				if($itemType == 'spot'){
					$count = $spotsWithLocationCount;
				}

				$list .= "<li class=\"$itemType\"><span class=\"count\">$count</span> <span class=\"itemType\">$translatedItemType</span></li>".PHP_EOL;
			}
			$list .= '	</ul>'.PHP_EOL;
			$html = $list.$html;
		}
    	return $html;
    }

    public static function getItemInfoForDisplay(Zend_View $view, $item, $loggedIn = false)
    {
		if($item instanceof Spot_Row){
			$info = ucfirst($view->translate('spotType')) . ': ' .$item->getSpotType(). ', '. $view->translate('groundType') . ': ' .$item->getGroundType();
			$link = $view->itemLink($item);
			$title = $item->getTitle();
		} elseif($item instanceof User_Row){
			$info = ucfirst($view->translate('rideType')) .': '.$view->rideType($item);
			$title = $item->getTitle();
			if($loggedIn){
				// Show actual details
				$link = $view->userLink($item);
			} else {
				// Show an anonymous user, with no link
				$link = $view->userLink($item, 'user', true);
				$title = ucfirst($view->translate('anonymousUserName'));
			}

		} elseif($item instanceof Media_Item_Row){
			$info = ucfirst($view->translate('mediaInAlbum')) .' '.$view->itemLink($item->getAlbum());
			$link = $view->mediaThumbnail($item, true);
			$title = $item->getTitle();
		} elseif($item instanceof Country_Row){
			$info = $link = $title = null;
		} else {
			throw new Lib_Exception("Not supported:".get_class($item));
		}

		return array($info, $link, $title);
    }

    protected function _renderItem($item, &$itemTypes, User_Row $user)
    {
    	$loc  = ucfirst($this->view->translate('location'));

    	list($info, $link, $title) = self::getItemInfoForDisplay($this->view, $item);

		$itemType = $item->getItemType();
		if(!isset($itemTypes[$itemType])){
			$itemTypes[$itemType] = 0;
		}
		$itemTypes[$itemType]++;

		$itemId = $item->getId();
		$rawItemType = $item->getItemType();
		$itemType = ucfirst($this->view->translate('itemSing_'.$rawItemType));
		$location = $item->getLocation();
		$latParts = explode('.', $location->latitude);
		$latDec = $latParts[1] / 1000000;
		$lonParts = explode('.', $location->longitude);
		$lonDec = $lonParts[1] / 1000000;

		$lat = ucfirst($this->view->translate($location->latitude > 0 ? 'northAbbr':'southAbbr'));
		$lon = ucfirst($this->view->translate($location->longitude > 0 ? 'eastAbbr':'westAbbr'));
		$html = <<<HTML

			<tr id="{$rawItemType}{$itemId}" class="{$rawItemType}">
				<td class="itemType">{$itemType}</td>
				<td class="itemLink">{$link}</td>
				<td class="itemInfo">
					{$info}
					<dl class="position">
						<dt>{$loc}</dt>
						<dd class="geo">
							<abbr class="latitude" title="{$location->latitude}">{$lat} {$latParts[0]}° {$latDec}</abbr>
							<abbr class="longitude" title="{$location->longitude}">{$lon} {$lonParts[0]}° {$lonDec}</abbr>
						</dd>
					</dl>
				</td>
			</tr>
HTML;
		return $html;
    }

    protected function _renderSpotWithoutLocation($item, &$itemTypes, User_Row $user, Lib_Acl $acl)
    {
    	$loc  = ucfirst($this->view->translate('location'));
		$info = ucfirst($this->view->translate('spotType')) . ': ' .$item->getSpotType(). ', '. $this->view->translate('groundType') . ': ' .$item->getGroundType();
		$link = $this->view->itemLink($item);

		if($item->isEditableBy($user, $acl)){
			$info .= '<br/>'.$this->view->editLink($item, 'addLocationToThisSpot');
		}

		if(!($item instanceof Spot_Row)){
			throw new Lib_Exception("Not supported item without a location:".get_class($item));
		}

		$itemType = $item->getItemType();
		if(!isset($itemTypes[$itemType])){
			$itemTypes[$itemType] = 0;
		}
		$itemTypes[$itemType]++;

		$itemId = $item->getId();
		$rawItemType = $item->getItemType();
		$itemType = ucfirst($this->view->translate('itemSing_'.$rawItemType));

		$html = <<<HTML

		<tr id="item{$itemId}" class="{$rawItemType}">
			<td class="itemType">{$itemType}</td>
			<td class="itemLink">{$link}</td>
			<td class="itemInfo">
				{$info}
			</td>
		</tr>
HTML;
		return $html;
    }

	protected function _renderDptContent($dpt)
	{
		/**
		 * @todo: add generic text to explain what's in the page
		 * link to go to the country page
		 */
		$content = '';
		return $content;
	}

	protected function _renderCountryContent(Country_Row $country)
	{
		/**
		 * @todo:
		 * add funny text to talk about this country, and explain what's in the page
		 * add goto form
		 *
		 * in bottom, return a table of links to all the dpt in that country
		 */
    	$redirect = false;
        $countryId = $country->getId();

    	$gotoDptForm = new Dpt_Form_GoTo($countryId);
        $data = $_POST;
    	if($data){
            $gotoDptForm->populate($data);
            $redirect = $gotoDptForm->isValid($data);
        }

        if($redirect){
            $dpt = $gotoDptForm->getElement('dpt')->getDpt($data['dpt']);
            $helper = new Lib_Controller_Helper_RedirectToRoute();
            $helper->direct('displaydpt',array(
				'name' => $dpt->getCleanTitle(),
				'id' => $dpt->id,
            ));
        }

        $table = new Dpt();
        $where = $this->view->acl->isAllowed($this->view->user, Lib_Acl::ADMIN_RESOURCE) ? '1' : "status = '".Data::VALID."'";
		$where .= $table->getAdapter()->quoteInto(" AND country = ?", $countryId);
		$items = $table->fetchAll($where);

		$countryText = ucfirst($this->view->translate(Country::getTextForPage($countryId)));
        $topContent = <<<HTML
        <div id="country">
			<div id="countryDescription">{$countryText}</div>
			<div id="countryNavigation">{$gotoDptForm}</div>
		</div>
HTML;

        $bottomContent = '';
        if($items){
        	$bottomContent = '<ul id="dptList">'.PHP_EOL;
        	foreach($items as $item){
        		if($this->view->user->lang == 'fr'){
        			$title = ucfirst(sprintf($this->view->translate('contentInDptFr'), $item->prefix, $item->getTitle()));
        		} else {
        			$title = ucfirst(sprintf($this->view->translate('contentInDpt'), $item->getTitle()));
        		}

        		$bottomContent .= '<li>'.$this->view->itemLink($item, null, null, $title).'</li>'.PHP_EOL;
        	}
        	$bottomContent .= '</ul>'.PHP_EOL;
        	$bottomContent .= '<div class="clear"></div>'.PHP_EOL;
        }

        $content = array($topContent, $bottomContent);
		return $content;
	}
}

