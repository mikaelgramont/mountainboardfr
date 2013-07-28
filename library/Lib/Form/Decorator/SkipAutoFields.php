<?php
/**
 * Generates inline script to hide auto fields
 *
 */
class Lib_Form_Decorator_SkipAutoFields extends Zend_Form_Decorator_Abstract
{
    public function render($content)
    {
        $id = $this->getElement()->getId();
        // The loop added here looks for errors in the auto fields fieldset
        $js = <<<JS

$('#$id').click(function(){
    var container = $(this).parent().parent().get(0);
    var spans = container.getElementsByTagName("span");
    var hasErrors = false;
    for(var i =0; i < spans.length; i++){
    	if(spans[i].className.indexOf('errors') == -1){
    		continue;
    	}
    	if(spans[i].innerHTML.length > 0){
    		hasErrors = true;
    	}
    }
    
	if(this.checked || hasErrors){
        $(this).parent().siblings().show();
    } else {
        $(this).parent().siblings().hide();
    }
});
$('#$id').click().attr('checked', false);
JS;

        $this->getElement()->getView()->JQuery()->addOnLoad($js);
        return $content;
    }
}