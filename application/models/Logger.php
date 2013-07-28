<?php
class Logger extends Zend_Log
{
    protected $_userId;
    protected $_formatter;
    
    /**
     * Class constructor.  Create a new logger
     *
     * @param Zend_Log_Writer_Abstract|null  $writer  default writer
     */
    public function __construct($userId)
    {
        $this->_userId = $userId;
    	
		$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'local';

		$this->setEventItem('timestamp', date('Y-m-d H:i:s'));
		$this->setEventItem(User::COLUMN_USERID, $this->_userId);
		$this->setEventItem('url', Utils::getCompleteUrl());
		$this->setEventItem('referer', array_key_exists('HTTP_REFERER', $_SERVER) ? $_SERVER['HTTP_REFERER'] : '');
		$this->setEventItem('ip', $ip);
		$this->setEventItem('hostname', Utils::getHost($ip));
        
        $this->_formatter = new Zend_Log_Formatter_Simple(
        	'%timestamp% [%priorityName% (%priority%)] / '.User::COLUMN_USERID.': \'%'.User::COLUMN_USERID.'%\' / IP: %ip% / HOSTNAME: %hostname%'.PHP_EOL."\tURL: %url%".PHP_EOL."\tREFERER: %referer%".PHP_EOL."\t%message%" . PHP_EOL.PHP_EOL.PHP_EOL
        );
		
        $writer = new Zend_Log_Writer_Stream($this->_getCommonLogFile());
        $writer->setFormatter($this->_formatter);
        $this->_writers['log'] = $writer;
    }
	
    /**
     * Undefined method handler allows a shortcut:
     *   $log->priorityName('message')
     *     instead of
     *   $log->log('message', Zend_Log::PRIORITY_NAME)
     *
     * @param  string  $method  priority name
     * @param  string  $params  message to log
     * @return void
     * @throws Zend_Log_Exception
     */
    public function __call($method, $params)
    {
        $eventType = strtolower($method);
		switch (count($params)) {
			case 0:
				/** @see Zend_Log_Exception */
                require_once 'Zend/Log/Exception.php';
                throw new Zend_Log_Exception('Missing log message');
            case 1:
            	$message = array_shift($params);
                $extras = null;
                break;
            default:
            	$message = array_shift($params);
                $extras  = array_shift($params);
                break;
		}
        $this->_log($message, $eventType, $extras);
    }
    
    public function log($message, $priority = null, $extras = null)
    {
    	$this->_log($message, 'log');
    }
    
    protected function _log($message, $eventType, $extras = null)
    {
        if(!array_key_exists($eventType, $this->_writers)){
	    	$writer = new Zend_Log_Writer_Stream($this->_getEventLogFile($eventType));
	        $writer->setFormatter($this->_formatter);
	        $this->_writers[$eventType] = $writer;
        }
    	
        $event = $this->_prepareEvent($message, $eventType);
        $this->_writers[$eventType]->write($event);
        $this->_writers['log']->write($event);
    }
    
    protected function _prepareEvent($message, $eventType)
    {
        // pack into event required by filters and writers
        $event = array_merge(array('timestamp'    => date('c'),
                                    'message'      => $message,
                                    'priority'     => $eventType,
                                    'priorityName' => $eventType),
                              $this->_extras);

        // Check to see if any extra information was passed
        if (!empty($extras)) {
            $info = array();
            if (is_array($extras)) {
                foreach ($extras as $key => $value) {
                    if (is_string($key)) {
                        $event[$key] = $value;
                    } else {
                        $info[] = $value;
                    }
                }
            } else {
                $info = $extras;
            }
            if (!empty($info)) {
                $event['info'] = $info;
            }
        }
		
        return $event;    	
    }
    
    protected function _getCommonLogFile()
    {
        $file = APP_DEBUGDIR . date('Y-m-d') . '.log';
        return $file;
    }

    protected function _getEventLogFile($eventType)
    {
        $file = APP_DEBUGDIR . date('Y-m-d') .'-'.$eventType.'.log';
        return $file;
    }
}
