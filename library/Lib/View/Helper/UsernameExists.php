<?php
/**
 * View helper meant to be used in register forms, to get a quick
 * feedback of the availability of a username
 *
 */
class Lib_View_Helper_UsernameExists extends Zend_View_Helper_FormText
{
    /**
     * Set view and enable jQuery Core and UI libraries
     *
     * @param  Zend_View_Interface $view
     * @return ZendX_JQuery_View_Helper_Widget
     */
    public function setView(Zend_View_Interface $view)
    {
        parent::setView($view);
        $this->jquery = $this->view->jQuery();
        $this->jquery->enable()
                     ->uiEnable();
        return $this;
    }

    public function usernameExists($name, $value = null, $attribs = null)
    {
        $return = parent::formText($name, $value, $attribs);

        $errorMessage = Globals::getTranslate()->_('usernameExists');

        $id = $name.'Error';
        $js = <<<JS

    $.checkUsername = new Object();
    $.checkUsername.cache = [];
    $.checkUsername.elementName = '%s';

    $.checkUsername.updateDisplay = function(){
        var toBeChecked = $("#" + $.checkUsername.elementName).get(0).value.toLowerCase();
        if(typeof($.checkUsername.cache[toBeChecked]) === "undefined" ||
           $.checkUsername.cache[toBeChecked] === 0){
           $("#{$id}").css('display', 'none').html("");
        } else {
            $("#{$id}").css('display', 'normal').html("$errorMessage").fadeIn(500);
        }
    };

    $.checkUsername.updateCache = function(data, status){
        if(status != "success"){
            return;
        }

        $.each(data, function(i, item){
            $.checkUsername.cache[item.name] = item.exists;
        });

    };

    $.checkUsername.wasChecked = function(toBeChecked){
        if(typeof($.checkUsername.cache[toBeChecked]) !== "undefined"){
            return true;
        }

        return false;
    };

    $.checkUsername.check = function() {
        var toBeChecked = $("#" + $.checkUsername.elementName).get(0).value.toLowerCase();

        //if(typeof($.checkUsername.cache[toBeChecked]) === "undefined"){

        if(!$.checkUsername.wasChecked(toBeChecked)){
            $.get("%s", {userN: toBeChecked}, function(data, status){
                    $.checkUsername.updateCache(data, status);
                    $.checkUsername.updateDisplay();},
                'json');
        } else {
            $.checkUsername.updateDisplay();
        }
    }


    $("#" + $.checkUsername.elementName).keypress(function(e){
        setTimeout("$.checkUsername.check()", 100);
    });
    $.checkUsername.check();
JS;
        $route = Globals::getRouter()->assemble(array('format' => 'html'), "isusernameavailable", true);
        $js = sprintf($js, $name, $route, $name);
        $this->jquery->addOnLoad($js);


        //$this->jquery->addJavascriptFile('/js/username.js');

        return $return;
    }
}