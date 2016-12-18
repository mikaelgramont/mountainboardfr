<?php
class Lib_View_Helper_CardHeader extends Zend_View_Helper_Abstract
{
    protected $_hasRenderedJS = false;
    
    public function cardHeader(HeaderContent $content)
    {
        $button = $content->shouldRenderDropDownOnMobile() ? $this->_getDropDownButton() : '';
        $topClass= $content->hasSubHeaderActions() ? "hasBothCards" : "";
        
        $html = <<<HTML
        <div class="cardWrapper $topClass">
        	<div class="card headerCard subHeaderCardTop">
        		
        		<div class="cardSectionLeft">
        			<h1 class="headerCardTitle {$content->getTitleClass()}">{$content->getTitle()}</h1>
        			{$content->getHeaderLeftActions()}
        		</div>
        		
        		<div class="cardSectionRight">
        			{$content->getHeaderRightActions()}
        			{$button}
        		</div>
        	</div>
HTML;
        if ($content->hasSubHeaderActions()) {
            $html .= <<<HTML
            	<div class="card headerCard subHeaderCardBottom">
            		<div class="cardSectionLeft">
            			{$content->getSubHeaderLeftActions()}
            		</div>
            		
            		<div class="cardSectionRight">
            			{$content->getSubHeaderRightActions()}
            		</div>
            	</div>
            
            </div>
HTML;
        }
        return $html;
    }
    
    protected function _getDropDownButton()
    {
        $title = "Actions menu";
        $html = <<<HTML
        			<button class="headerCardMenuAnchor actionLinkContainer" title="{$title}">
        			    <svg version="1.1" viewBox="0 0 128 32" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        			        <circle color="#fff" cx="16" cy="16" r="16"></circle>
        			        <circle cx="64" cy="16" r="16"></circle>
        			        <circle cx="112" cy="16" r="16"></circle>
        				</svg>
        			</button>		
HTML;
        			
        $this->_maybeAddJS();
        
        return $html;
    }
    
    protected function _maybeAddJS()
    {
		if ($this->_hasRenderedJS) {
		    return;
		}
		$this->_hasRenderedJS = true;
        
        $js = <<<JS
        
Lib.Event.listenOnClass('click', 'headerCardMenuAnchor', Lib.onHeaderCardMenuClick, Lib);
JS;
        
        $this->view->jQuery()->addOnLoad($js);        
        
    }
}
