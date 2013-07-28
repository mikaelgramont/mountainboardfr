<?php
class ErrorController extends Lib_Controller_Action
{
    public function init()
    {
        parent::init();
		$this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Server Error');
        Zend_Registry::set('Category', Category::NONE);
    }

    public function indexAction()
    {

    }

    public function exceptionAction()
    {
        $view = 'exception';
        $errors = $this->_getParam('error_handler');
        $e = $errors->exception;
        $logMessage  = "Type: ".$errors->type.' - '.get_class($e).PHP_EOL;
        $logMessage .= "Code: ".$e->getCode().PHP_EOL;
        $logMessage .= "Message: ".$e->getMessage().PHP_EOL.$e->getTraceAsString();

        $is404 = false;

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                $is404 = true;
                break;
            default:
            	$class = get_class($e);
            	switch($class){
            		case 'Lib_Exception_NotFound':
            			$is404 = true;
            			break;
            	}
                break;
        }

        if($is404){
            /**
             * 404 error -- controller or action not found: bad route
             * Check old urls before giving up and replying with a 404
             */
        	$currentUrl = $this->_getCurrentUrl();
        	$urlConversions = Import::getOldUrls('http://' . GLOBAL_DOMAIN_FULL_AND_SUB_OLD);
			if(array_key_exists($currentUrl, $urlConversions)){
				$redirect = $urlConversions[$currentUrl];
				$logMessage = "Old url '$currentUrl' was rerouted to '$redirect'".PHP_EOL;
				Globals::getLogger()->urlRemapping($logMessage);

				$this->_response->setRedirect($redirect, 301)->sendResponse();
				exit();
			}

            $view = '404';
            $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
            $logMessage = "Error 404 - Url does not exist";
	        if(!$this->_isKnown404Type($currentUrl)){
	            try{
		            Globals::getLogger()->notFound($logMessage);
		        } catch(Zend_Log_Exception $e2 ) {
		            // Zend_Log_Exceptions cannot be logged, so they must display
		            // a different message so we can identify what happened
		            $this->view->message = ucfirst(Globals::getTranslate()->_('logFailure')).'.';
		            $view = 'exception';
		        }
	        }

        } else {
            // Standard case: any exception that was not caused by a bad route
            $this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Server Error');
            if(!DEBUG){
                if($e instanceof Zend_Db_Exception){
                    $view = 'database';
                } else {
                    $view = 'standarderror';
                }
            } else {
                $this->view->message = $logMessage;
                if($e instanceof Zend_Db_Exception){
                    $view = 'database';
                } else {
                    $view = 'exception';
                }
            }
	        try{
	            Globals::getLogger()->error($logMessage);
	        } catch(Zend_Log_Exception $e2 ) {
	            // Zend_Log_Exceptions cannot be logged, so they must display
	            // a different message so we can identify what happened
	            $this->view->message = ucfirst(Globals::getTranslate()->_('logFailure')).'.';
	            $view = 'exception';
	        }
        }

        $this->getResponse()->clearBody();
        $this->render($view, 'default');
    }

    public function notfoundAction()
    {
        $logMessage = "Displayed 404 page after redirect - see referer";
    	Globals::getLogger()->notFound($logMessage);
    	$this->render('404', 'default');
    }

    public function maintenanceAction()
    {
    }

    public function menuerrorAction()
    {
    	Zend_Layout::getMvcInstance()->disableLayout();
    }

	public function othererrorAction()
	{
        $this->getResponse()->setRawHeader('HTTP/1.1 500 Internal Server Error');
		$logMessage = "Displayed othererror page after redirect - see referer";
    	Globals::getLogger()->othererror($logMessage);
    	$this->render('500', 'default');
	}

	private function _getCurrentUrl($stripParams = true)
	{
		$url = 'http';
		if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
			$url .=  's';
		}
		$url .=  '://';
		if($_SERVER['SERVER_PORT'] != '80'){
			$url .=  $_SERVER['HTTP_HOST'].':'.$_SERVER['SERVER_PORT'];
		} else {
			$url .=  $_SERVER['HTTP_HOST'];
		}

		if(!empty($_SERVER['REQUEST_URI'])){
			$url .=  $_SERVER['REQUEST_URI'];
		}

		if($stripParams){
			$parts = explode('?', $url);
			$url = $parts[0];
		}

		return $url;
	}

	private function _isKnown404Type($currentUrl)
	{
		$knownParts = array(
			'http://www.mountainboard.fr/sendpmsg',
			'http://www.mountainboard.fr/annonce',
			'http://www.mountainboard.fr/topic',
			'http://www.mountainboard.fr/blog',
			'http://www.mountainboard.fr/membres',
			'http://www.mountainboard.fr/assotopic',
			'http://www.mountainboard.fr/newtopic',
			'http://www.mountainboard.fr/editpost',
			'http://www.mountainboard.fr/forum',
			'http://www.mountainboard.fr/contact.php',
		);
		foreach($knownParts as $part){
			if(strpos($currentUrl, $part) !== false){
				return true;
			}
		}
	}
}