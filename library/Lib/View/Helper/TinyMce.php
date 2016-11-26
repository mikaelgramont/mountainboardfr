<?php
class Lib_View_Helper_TinyMce extends Zend_View_Helper_FormTextarea
{
	public function TinyMce($id, $value = null, array $params = array(), $attribs = array())
    {
    	$scriptUrl = $this->view->asset()->script('tinymce/tiny_mce.js');
        // $scriptUrl = $this->view->cdnHelper->jsUrl('/'.SCRIPTS_PATH.'tinymce/tiny_mc/e.js');
        $lang = Zend_Registry::get('Zend_Locale');

        $fileBrowserUrl = Globals::getRouter()->assemble(array(
        	'type' => 'placeholder',
        	'path' => ''
        ), 'filebrowser');

        $css = $this->view->asset()->css('style.full.min.css');

		$sessionUploadsNamespace = new Zend_Session_Namespace(UPLOADS_NAMESPACE);
        $fileBrowserUrl .= $sessionUploadsNamespace->folder;

        $code = in_array(Globals::getUser()->getRoleId(), array(User::STATUS_EDITOR, User::STATUS_ADMIN))  ? 'code,|,' : '';

        // The label is currently associated to an element that is going to be replaced, let's fix that:
        $newFor = $id.'_ifr';

    	$js = <<<JS

tinyMCE.init({
	mode:'textareas',
	editor_deselector : "mceNoEditor",
	script_url : '$scriptUrl',
	language: '$lang',
	body_class: 'richTextContent',
	theme : "advanced",
	theme_advanced_buttons1 : "{$code}cut,copy,paste,pasteword,|,bullist,numlist,|,undo,redo,|,link,unlink,image,cleanup,|,forecolor,backcolor,bold,italic,formatselect,fontselect,fontsizeselect,emotions",
	theme_advanced_buttons2 : "",
	theme_advanced_buttons3 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	plugins: "emotions",
	content_css: '$css',
	file_browser_callback : function myFileBrowser (field_name, url, type, win) {
		var cmsUrl = '$fileBrowserUrl';
		cmsUrl = cmsUrl.replace("placeholder", type);

		tinyMCE.activeEditor.windowManager.open({
			file : cmsUrl,
			title : 'File Browser',
			width : 420,  // Your dimensions may differ - toy around with them!
			height : 400,
			resizable : "yes",
			inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
			close_previous : "no"},{
				window : win,
				input : field_name
			});
		return false;
	}
});
JS;

    	$js2 = <<<JS2
$("label[for='$id']").attr('for', '$newFor');
JS2;
		$this->view->jQuery()->addOnLoad($js);
		$this->view->jQuery()->addOnLoad($js2);
		$this->view->JQuery()->addJavascriptFile($scriptUrl);

		$return = parent::formTextarea($id, $value, $params, $attribs);
		return $return;
    }
}
