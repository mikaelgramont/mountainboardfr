<?php
class Lib_View_Helper_Header extends Zend_View_Helper_Abstract
{
    protected $_acl;
    protected $_user;
    protected $_currentCategoryContent;
    protected $_currentUrl;

    protected $_content;

    public function __construct(Zend_View_Abstract $view, Lib_Acl $acl, User_Row $user = null, $currentCategoryId = null, $currentSubCategoryId = null, $config = null, $userOptions = array())
    {
    	$this->setView($view);
    	$this->_acl = $acl;
        $this->_user = $user;
    }

    public function header($currentCategoryId = null, $currentSubCategoryId = null, $config = null, $userOptions = array())
    {
        if(empty($currentCategoryId) && Zend_Registry::isRegistered('Category')){
            $currentCategoryId = Category::$names[Zend_Registry::get('Category')];
        }
        if(empty($currentSubCategoryId) && Zend_Registry::isRegistered('SubCategory') && array_key_exists(Zend_Registry::get('SubCategory'), SubCategory::$names)){
            $currentSubCategoryId = SubCategory::$names[Zend_Registry::get('SubCategory')];
        }

   		$menu = $this->menu($currentCategoryId, $currentSubCategoryId, $config, $userOptions);
   		$searchForm = (string)$this->view->searchForm;
		$class = $this->getEmptyCategoryContentClass();
		$imgPath = $this->view->baseUrl . '/' . IMAGES_PATH;
		$alt = $title = $this->view->translate('logo');
		$languageBox = $this->_getLanguageBox($imgPath);
		$slides = $this->view->useHeaderSlideshow ? $this->view->homePageSlides() : '';
		$logoUrl = $this->view->cdnHelper->url($imgPath. 'logo.png');
    		$content = <<<HTML
<header$class>
	<div id="headerContent">
		$languageBox
		$slides
	    <div class="title">
	        <a id="logo" href="{$this->view->baseUrl}/"><img src="{$logoUrl}" id="logoImage" alt="$alt" title="$title"/></a>
	    </div>
$searchForm
	    <div id="topMenu">
$menu
		</div>
	</div>
</header>
HTML;

        $defaultOptions = array(
            'menuId' => 'catMenu',
        );
        $options = array_merge($defaultOptions, $userOptions);
    	$this->view->getHelper('homePageSlides')->getScripts();
    	return $content;
    }

    /**
     * Render a menu of categories and subcategories,
     * depending on the current user and current route
     *
     * @param Lib_Acl $acl
     * @param User_Row $user
     * @param string $currentCategoryId
     * @param Zend_Config $config
     * @param array $userOptions
     * @return string
     */
    public function menu($currentCategoryId, $currentSubCategoryId, $config = null, $userOptions = array())
    {
        $this->_currentUrl = $_SERVER['REQUEST_URI'];

        if(empty($config)){
            $config = Globals::getMenuConfig();
        }

        $defaultOptions = array(
            'menuId' => 'catMenu',
            'currentCategoryMenuId' => 'currentCategoryMenu',
            'menuClass' => null,
            'currentCategoryMenuClass' => null,
            'activeCategoryClass' => 'activeCat',
            'inactiveCategoryClass' => 'inactiveCat',
            'activeSubCategoryClass' => 'activeSubCat',
            'inactiveSubCategoryClass' => 'inactiveSubCat',
        );
        $options = array_merge($defaultOptions, $userOptions);

        try {
	        /*
	         * Main menu
	         */
	        $menuClass = empty($options['menuClass']) ? '' : " class='{$options['menuClass']}'";
	        $content  = '<nav role="navigation" class="clearfix">';
	        $content .= "<ul id='{$options['menuId']}'$menuClass>".PHP_EOL;
	        foreach($config->categories->category as $category){
	            $content .= $this->_getCategoryContent($category, $currentCategoryId, $currentSubCategoryId, $options);
	        }
	        $content .= '</ul>'.PHP_EOL;
	        $content .= '</nav>'.PHP_EOL;
	        
	        /*
	         * Current category menu
	         */
	        if(!empty($this->_currentCategoryContent)){
	            $currentCategoryMenuClass = empty($options['currentCategoryMenuClass']) ? '' : " class='{$options['currentCategoryMenuClass']}'";
	            $content .= "<ul id='{$options['currentCategoryMenuId']}'$currentCategoryMenuClass>".PHP_EOL;
	            $content .= $this->_currentCategoryContent.PHP_EOL;
	            $content .= '</ul>'.PHP_EOL;
	        }
        } catch (Exception $e) {
        	$message = "An error occured while generating the main menu";
        	Globals::getLogger()->error($message.": ".$e->getMessage().PHP_EOL.$e->getTraceAsString(), Zend_Log::ERR );
        	Zend_Controller_Front::getInstance()->throwExceptions(true);
        	Zend_Layout::disableLayout();
        	throw new Lib_Exception_Menu($message);
        }

        $this->view->jQuery()->addOnLoad($this->_getJavascript($options['menuId']));
        return $content;
    }

    public function getEmptyCategoryContentClass()
    {
		$class = '';
    	if(empty($this->_currentCategoryContent) && $this->view->useHeaderSlideshow){
			$class = ' class="noCategoryContent"';
		}
		return $class;
    }

    /**
     * Return the content of a category
     *
     * @param Zend_Config $category
     * @param string $currentCategoryId
     * @param array $options
     * @return string
     */
    protected function _getCategoryContent($category, $currentCategoryId, $currentSubCategoryId, $options)
    {
        if($category->resource){
            if(!$this->_acl->isAllowed($this->_user, $category->resource)){
                return '';
            }
        }

        if(!isset($category->subCategories)){
            return '';
        }

        $content = '';


        if($category->id == 'account' && !in_array($this->_user->status, array(User::STATUS_BANNED, User::STATUS_GUEST))){
            // Display username instead of category name
            $title = ucfirst($this->_user->getTitle());
        } else {
            $title = ucfirst($this->view->translate($category->route));
        }

        if($category->id == $currentCategoryId){
            // Current category
            $catClass = " class='category {$options['activeCategoryClass']}'";
            $catString = $this->view->routeLink($category->route, $title, array()).PHP_EOL;
            $content .= "<li$catClass>".PHP_EOL;
            $content .= "        ".$catString . PHP_EOL;
            $content .= "</li>";
            if($category->subCategories){
                $this->_currentCategoryContent = $this->_getSubCategoriesContent($category, $currentCategoryId, $currentSubCategoryId, $category->subCategories, $options);
            }
        } else {
            // Other categories
            $catClass = " class='category {$options['inactiveCategoryClass']}'";
        	$url = $this->view->cdnHelper->url($this->view->baseUrl . '/' . IMAGES_PATH.'menuItem.gif');

        	$title .= "<img src=\"$url\" alt=\"\" class=\"menuItemImage\"/>";
            $catString = $this->view->routeLink($category->route, $title, array()).PHP_EOL;
            $content .= "<li$catClass>".PHP_EOL;
            $content .= "        ".$catString . PHP_EOL;
            if($category->subCategories){
                $content .= $this->_getSubCategoriesContent($category, $currentCategoryId, $currentSubCategoryId, $category->subCategories, $options);
            }
            $content .= "    </li>";
        }

        return $content;
    }

    /**
     * Return the content of all subcategories in a category
     *
     * @param Zend_Config $category
     * @param string $currentCategoryId
     * @param Zend_Config $subCategories
     * @param array $options
     * @return string
     */
    protected function _getSubCategoriesContent($category, $currentCategoryId, $currentSubCategoryId, $subCategories, $options)
    {
        $content = '';
        foreach($subCategories as $element){
            // Current category opening tag is taken care of in the main function
            if($category->id != $currentCategoryId){
                $content .= "        <ul>".PHP_EOL;
            }

            if($element->route){
                // This is a subcategory
                $content .= $this->_getSingleSubcategoryContent($currentSubCategoryId, $element, $options);

            } else {
                // This is a list of subcategories
                $content .= $this->_getSubcategoryListContent($currentSubCategoryId, $element, $options);
            }

            // Current category closing tag is taken care of in the main function
            if($category->id != $currentCategoryId){
                $content .= "        </ul>".PHP_EOL;
            }
        }
        return $content;
    }

    /**
     * Return the content of a single subcategory
     *
     * @param Zend_Config $subCategory
     * @param array $options
     * @return string
     */
    protected function _getSingleSubcategoryContent($currentSubCategoryId, $subCategory, $options)
    {
        if($subCategory->resource){
            if(!$this->_acl->isAllowed($this->_user, $subCategory->resource)){
                return '';
            }
        }

        $content = '';
        $title = isset($subCategory->title) ? ucfirst($subCategory->title) : null;

        $params = array();
        $subCategoryId = '';
        foreach($subCategory as $name=>$attribute){
            if($name == 'title'){
                $title = ucfirst($attribute);
                continue;
            }
            if($name == 'route'){
                continue;
            }
            if($name == 'id'){
            	$subCategoryId = $attribute;
                continue;
            }
            $params[$name] = $attribute;
        }

        if($subCategoryId == $currentSubCategoryId){
            $subCatClass = " class='{$options['activeSubCategoryClass']}'";
        } else{
            $subCatClass = " class='{$options['inactiveSubCategoryClass']}'";
        }
        $subCatString = $this->view->routeLink($subCategory->route, $title, $params);

        $content .= "            <li$subCatClass>".PHP_EOL;
        $content .= "                ".$subCatString.PHP_EOL;
        $content .= "            </li>".PHP_EOL;
        return $content;
    }

    /**
     * Return the content of a list of subcategories
     *
     * @param Zend_Config $subCategoryList
     * @param array $options
     * @return string
     */
    protected function _getSubcategoryListContent($currentSubCategoryId, $subCategoryList, $options)
    {
        $content = '';
        foreach($subCategoryList as $element){
            $content .= $this->_getSingleSubcategoryContent($currentSubCategoryId, $element, $options);
        }
        return $content;
    }

    /**
     * Return the javascript used to make the menu work
     *
     * @param string $menuId
     * @return string
     */
    protected function _getJavascript($menuId)
    {
        $js = '
$("#'.$menuId.' li").click(function(e){
    if(!$(e.target).parent().is("li.inactiveSubCat")){
		$(this).addClass("active").children("ul").toggle();
		return false;
	}
}).hover(
	function(){},
	function(){$(this).removeClass("active").children("ul").hide()}
);';
        return $js;
    }

    protected function _getLanguageBox($imgPath)
    {
    	$currentLang = Zend_Registry::get('Zend_Locale');
    	$supportedLanguages = explode(',', GLOBAL_SUPPORTED_LANG);

 		$return = '		<div id="languageBox">'.PHP_EOL;

 		foreach($supportedLanguages as $lang){
 			$url = Globals::getRouter()->assemble(array('lang' => $lang), 'switchlanguage', true);
 			$class = ($currentLang == $lang) ? ' class="currentLanguage"' : '';
 			$img = $this->view->cdnHelper->url("{$imgPath}flag_$lang.png");
 			$return .= "			<a href=\"$url\"$class><img src=\"{$img}\" width=\"16\" height=\"16\" alt=\"$lang\" /></a>".PHP_EOL;
 		}
 		$return .= '		</div>'.PHP_EOL;
 		return $return;
    }
}
