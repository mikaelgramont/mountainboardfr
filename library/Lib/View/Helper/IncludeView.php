<?php
/**
 * This helper allows a new view to be included directly
 * No action is involved, only rendering.
 * Thus all view variables are copied from main view
 *
 */
class Lib_View_Helper_IncludeView extends Zend_View_Helper_Abstract
{
    public function includeView($path)
    {
        $view = clone($this->view);
        $content = $view->render($path);
        unset($view);
        return $content;
    }
}