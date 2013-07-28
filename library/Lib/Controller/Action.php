<?php
class Lib_Controller_Action extends Zend_Controller_Action
{
    /**
     * Acl object
     *
     * @var Lib_Acl
     */
    protected $_acl;
    /**
     * Current User object
     *
     * @var User_Row
     */
    protected $_user;
    /**
     * Acl rule for controller-wide access restrictions
     * @var array
     */
    protected $_aclControllerRule = array();
    /**
     * Array of acl data for each action that needs access restriction
     * @var array
     */
    protected $_aclActionRules = array();

	/**
	 * Whether or not to generate additional content for display
	 *
	 * @var boolean
	 */
    protected $_useAdditionalContent = false;

	/**
	 * Whether or not to generate the slideshow
	 *
	 * @var boolean
	 */
    protected $_useHeaderSlideshow = false;

    public function init()
    {
        parent::init();

        // USER
        $this->view->user = $this->_user = Globals::getUser();

		// SEARCH RANK
        if (isset($_SERVER["HTTP_REFERER"]) && strpos($_SERVER["HTTP_REFERER"],"google")) {
			$this->_helper->googleSearchLogger()->log($_SERVER["HTTP_REFERER"], $this->_user);
		}

        // ACL
        $this->view->acl = $this->_acl = Globals::getAcl();
        if(!empty($this->_aclControllerRule)){
            $this->_acl->checkControllerRule($this->_aclControllerRule, $this->_request);
        }
        if(!empty($this->_aclActionRules)){
            $this->_acl->checkActionAccess($this->_aclActionRules, $this->_request);
        }

        // CATEGORY AND SUBCATEGORY
        Zend_Registry::set('Category', Category::START);
        Zend_Registry::set('SubCategory', SubCategory::NONE);

        // TRANSLATE
        Zend_Registry::set('Zend_Translate', Globals::getTranslate());

        // CONFIGURATION
        $this->view->addHelperPath('../library/Lib/View/Helper/', 'Lib_View_Helper');
        $this->view->baseUrl = $baseUrl = $this->_request->getBaseUrl();
        $this->view->headTitle()->setSeparator(APP_VIEW_TITLE_SEPARATOR);

        $this->view->headMeta('text/html; charset='.APP_PAGE_ENCODING, 'Content-Type', 'name', array(), Zend_View_Helper_Placeholder_Container_Abstract::SET );
        $this->view->headMeta(APP_PAGE_LANG, 'Content-Language', 'name', array(), Zend_View_Helper_Placeholder_Container_Abstract::SET );

        $this->view->keywords = Constants::getDefaultKeywords();
        $this->view->setEncoding(APP_PAGE_ENCODING);
        // this is to add a class to content divs to allow user-submitted content to be styled
        $this->view->richTextContent = false;

        $cdnHelper = new Lib_View_Helper_Cdn($this->view);
		if(APPLICATION_ENV == 'development' || !USE_CDN){
			$cdnHelper->setDevMode();
		}
        $cdnHelper->setCdnUrl(CDN_URL);
        $cdnHelper->setCssCdnUrl(CSS_CDN_URL);
        $cdnHelper->setJsCdnUrl(JS_CDN_URL);
        $cdnHelper->setSiteUrl(APP_URL);
		$this->view->cdnHelper = $cdnHelper;

		$this->view->favicon = $cdnHelper->url($baseUrl.'/'.IMAGES_PATH.'favicon.ico');

        $this->_helper->layout->setLayout(APP_DEFAULT_LAYOUT);

        // JQUERY CONFIGURATION
        ZendX_JQuery::enableView($this->view);
        $jQueryHelper = $this->view->jQuery();
        $jQueryHelper->useCdn(JQUERY_USE_CDN);
        $jQueryHelper->useUiCdn(JQUERY_USE_UI_CDN);
        $jQueryHelper->setRenderMode(ZendX_JQuery::RENDER_ALL & ~ZendX_JQuery::RENDER_STYLESHEETS );
        $jQueryHelper->enable();

        if(!JQUERY_USE_CDN){
            $jQueryHelper->setLocalPath($baseUrl . '/' .JQUERY_LOCAL_PATH);
        } else {
            $jQueryHelper->setCdnVersion(JQUERY_VERSION);
        }
        if(!JQUERY_USE_UI_CDN){
            $jQueryHelper->setUiLocalPath($baseUrl . '/' .JQUERYUI_LOCAL_PATH);
        } else {
            $jQueryHelper->setUiCdnVersion(JQUERYUI_VERSION);
        }

        header('Content-Type: text/html; charset=UTF-8');

        if($this->_request->getControllerName() != 'Search'){
			$this->view->searchForm = new Search_Form_Simple();
        } else {
        	$this->view->searchForm = null;
        }
    }

    public function postDispatch()
    {
    	if(!isset($this->view->additionalContentItems) && $this->_useAdditionalContent){
    		$this->view->additionalContentItems = $this->_helper->getAdditionalContentItems($this->_user, $this->_acl);
    	}

    	$this->view->useHeaderSlideshow = $this->_useHeaderSlideshow;

    	$lang = Zend_Registry::get('Zend_Locale');
    	if(empty($lang)){
    		$lang = GLOBAL_LANG_DEFAULT;
    	}
    	$this->view->pageLang = $lang;
        $this->view->headMeta($lang, 'Content-Language', 'name', array(), Zend_View_Helper_Placeholder_Container_Abstract::SET );

    	parent::postDispatch();
    }
}