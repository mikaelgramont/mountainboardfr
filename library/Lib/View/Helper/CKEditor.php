<?php
class Lib_View_Helper_CKEditor extends Zend_View_Helper_FormTextarea
{
	public function CKEditor($id, $value = null, array $params = array(), $attribs = array())
    {
    	if(substr(CKEDITOR_VERSION, 0 , 1) < 3){
        	/**
        	 * FCKEDITOR 2.X
        	 */
        	$this->view->JQuery()->addJavascriptFile($this->view->baseUrl.CKEDITOR2_PATH);
        	$this->view->headLink()->prependStylesheet($this->view->baseUrl.'/'.CSS_PATH.'ckeditor2.css');

        	$ckEditorBasePath = CKEDITOR2_BASEPATH;
        	$ckEditorInstanceName = 'FCKeditor'.ucfirst($id);
        	$js = <<<JS
	$(window).load(function(){
		var $ckEditorInstanceName = new FCKeditor('$id') ;
		$ckEditorInstanceName.BasePath = '$ckEditorBasePath' ;
		$ckEditorInstanceName.ReplaceTextarea() ;
	});
JS;
			$this->view->jQuery()->addOnLoad($js);
        } else {
        	/**
        	 * CKEDITOR 3
        	 */
        	if(array_key_exists('advanced', $params) && $params['advanced']){
        		$config = $this->_getAdvancedConfig();
        		unset($params['advanced']);
        	} else {
        		$config = $this->_getNormalConfig();
        	}
        	$this->view->JQuery()->addJavascriptFile(CKEDITOR3_PATH);
        	$this->view->headLink()->prependStylesheet($this->view->baseUrl.'/'.CSS_PATH.'ckeditor3.css');
        	$js = PHP_EOL."var {$id}Editor = CKEDITOR.replace('$id', ".$config.");".PHP_EOL;
        	$this->view->jQuery()->addOnLoad($js);
        }

        return parent::formTextarea($id, $value, $params, $attribs);
    }

    /**
     * Returns configuration for advanced editor
     * @return string
     */
    protected function _getAdvancedConfig()
    {
    	$baseConfig = $this->_getBaseConfig();
    	$return = <<<CONFIG

{
$baseConfig
   	toolbar : [
		['Source','Templates','Cut','Copy','Paste','PasteText','PasteFromWord'],
		['Undo','Redo','-','RemoveFormat'],
		['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
		['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		['Link','Unlink','Image','Flash','Table','Smiley','SpecialChar'],
		'/',['Styles','Format','Font','FontSize','TextColor','Bold','Italic','Underline','ShowBlocks']
	]
}
CONFIG;
    	return $return;
    }

    /**
     * Returns configuration for normal editor
     * @return string
     */
    protected function _getNormalConfig()
    {
    	$baseConfig = $this->_getBaseConfig();
    	$return = <<<CONFIG

{
$baseConfig
	toolbar : [
       	['Source','RemoveFormat','BulletedList'],
       	['Link','Unlink','Image','Flash','Smiley'],
       	['Styles','FontSize','TextColor','Bold','Italic','Underline','ShowBlocks']
    ]
}
CONFIG;
    	return $return;
    }

    /**
     * Returns base of configuration for all types of editor
     * @return string
     */
    protected function _getBaseConfig()
    {
		$css = '../../'.CSS_PATH.'main.css';
    	$return = <<<CONFIG
	customConfig : '',
	docType : '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
	height : '40em',
	image_browseServer : false,
	contentsCss : '{$css}',
CONFIG;
		return $return;
    }
}