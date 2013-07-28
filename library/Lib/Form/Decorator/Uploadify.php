<?php
class Lib_Form_Decorator_Uploadify extends Zend_Form_Decorator_Form
{
	protected $_debug = false;

	/**
	 * Adds an uploadify element to the current form
	 *
	 * @param string $content
	 * @return string
	 */
	public function render($content)
	{
		$baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
		$options = $this->_getDefaultOptions($baseUrl);
        $options = array_merge($options, $this->_options);
        $sessionId = Zend_Session::getId();

        $sessionUploadsNamespace = new Zend_Session_Namespace(UPLOADS_NAMESPACE);
        $sessionUploadsNamespace->folder = $options['uploadFolder'];

        $translate = Globals::getTranslate();
        $buttonText = ucfirst($translate->translate($options['buttonText']));
        $successText = ucfirst($translate->translate('uploadSuccessfullyUploaded'));
        $replaceMsg = ucfirst($translate->translate('uploadReplaceMsg'));

		/**
		 * Available callback functions:
		 * 		onInit: function(){alert('init');},
		 * 		onSelectOnce: function(){alert('onSelectOnce');},
		 * 		onOpen: function(){alert('onOpen');},
		 * 		onProgress: function(){alert('onProgress');},
		 */
		$js = <<<JS

$("#{$options['elementId']}").uploadify({
	'buttonText'    : '$buttonText',
	'replaceMsg'    : '$replaceMsg',
	'script'        : '{$options['uploadRoute']}',
	'checkScript'	: '{$options['checkRoute']}',
	//'folder'        : '{$options['uploadFolder']}',
	'uploader'      : '{$options['uploader']}',
	'cancelImg'     : '{$options['cancelImg']}',
	'queueID'     	: '{$options['queueID']}',
	'auto'			: {$options['auto']},
	'multi'     	: {$options['multi']},
	'fileDesc'     	: '{$options['fileDesc']}',
	'fileExt'     	: '{$options['fileExt']}',
	'scriptData'	: {'PHPSESSID': '{$sessionId}'},
	'displayData'	: 'speed',

	onComplete: function (evt, queueID, fileObj, response, data) {
		alert("$successText: '" + response + "'");
	},
	onAllComplete: function(){
		$("#{$options['elementId']}").uploadifyClearQueue();
	},
	onError: function (a, b, c, d) {
		$("#{$options['elementId']}").uploadifyClearQueue();
		if (d.status == 404){
			alert('Could not find upload script. Use a relative path.');
		} else if (d.type === "HTTP") {
			alert('error '+d.type+": "+d.info);
		} else if (d.type === "File Size") {
			alert(c.name+' '+d.type+' Limit: '+Math.round(d.sizeLimit/1024)+'KB');
		} else {
			alert('error '+d.type+": "+d.text);
		}
	}
});
JS;
if($options['auto'] == 'false'){
	$js .= <<<JS

	$("#{$options['parentElementId']}").append('<input type="button" id="filesButton" value="send"/>');
	$("#filesButton").click(function(){
		$("#{$options['elementId']}").uploadifyUpload();
	});
JS;
}

		$this->getElement()->getView()->JQuery()->addOnLoad($js);
		$view = $this->getElement()->getView();
		if(SWFOBJECT_USE_CDN){
			$swfObject = 'http://ajax.googleapis.com/ajax/libs/swfobject/'.SWFOBJECT_VERSION.'/swfobject.js';
			$view->JQuery()->addJavascriptFile($swfObject);
		} else {
			$view->JQuery()->addJavascriptFile($view->asset()->script(SWFOBJECT_LOCAL_PATH));

		}

		$view->JQuery()->addJavascriptFile($view->asset()->script(UPLOADIFY_PATH));

		$uploadifyContent = '';
        /**
		 * For debug purposes, use the following line. Also set the 'elementName' option to 'Filedata'
		 */
		if($this->_debug){
			$uploadifyContent .= "<form enctype=\"multipart/form-data\" method=\"post\" action=\"/upload-content/\"><input type=\"file\" name=\"{$options['elementName']}\" id=\"{$options['elementId']}\" /><input type=\"submit\"/></form>".PHP_EOL;
		} else {
			$uploadifyContent .= "<p id=\"{$options['parentElementId']}\"><span id=\"{$options['elementId']}\">&nbsp;</span></p>".PHP_EOL;
		}
        $content = $uploadifyContent . $content;
        return $content;
	}

	/**
	 * Returns default options for uploadify script
	 *
	 * @param string $baseUrl
	 * @return array
	 */
	protected function _getDefaultOptions($baseUrl)
	{
		$uploadifyDir = SCRIPTS_PATH.UPLOADIFY_DIR;

		$options =  array(
			'buttonText' 		=> 'uploadButtonText',
			'buttonImg' 		=> $baseUrl . '/' . IMAGES_PATH . 'uploadify.jpg',
			'parentElementId' 	=> 'uploadifyParent',
			'elementId'			=> 'uploadify',
			'fileDesc'			=> Globals::getTranslate()->translate('uploadFileDescGeneral'),
			'fileExt'			=> '*.jpg; *.jpeg; *.png; *.gif; *.xls; *.doc; *.ppt; *pdf; *.swf',
			'elementName' 		=> 'uploadify',
			'uploadRoute' 		=> $baseUrl.'/upload-content/',
			'checkRoute' 		=> $baseUrl.'/check-content/',
			'uploadFolder' 		=> '/' .CONTENT_DIRECTORY,
			'uploader' 			=> $baseUrl . '/' . $uploadifyDir . 'uploadify.swf',
			'cancelImg' 		=> $baseUrl . '/' .$uploadifyDir . 'cancel.png',
			'queueID'			=> 'fileQueueToto',
			'auto'				=> 'true',
			'multi'				=> 'true',
		);

		if($this->_debug){
			$options['elementName'] = 'Filedata';
		}

		return $options;
	}
}