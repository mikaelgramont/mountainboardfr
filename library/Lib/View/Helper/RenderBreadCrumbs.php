<?php
/**
 * This class can render a BreadCrumbs object
 *
 */
class Lib_View_Helper_RenderBreadCrumbs extends Zend_View_Helper_Abstract
{
    /**
     * Render a BreadCrumb object. The separator defined in the BreadCrumb
     * object can be overridden
     *
     * @param BreadCrumbs $breadCrumbs
     * @param string $separator
     * @return string
     */
    public function renderBreadCrumbs(BreadCrumbs $breadCrumbs, string $separator = null)
    {
        if($separator === null){
            $separator = $breadCrumbs->getSeparator();
        }

        $outArray = array();
        foreach($breadCrumbs->getSteps() as $step){
            $url = Globals::getRouter()->assemble(array(), $step[0], true);
            $outArray[] = "<a href='".$url."'>{$step[1]}</a>";
        }

        $outString = implode($separator, $outArray);
        return $outString;
    }
}