<?php
class HeaderContent
{
    protected $_title = '';
    protected $_titleClass = '';
    
    protected $_headerLeftActions = '';
    protected $_headerRightActions = '';
    
    protected $_subheaderLeftActions = '';
    protected $_subheaderRightActions = '';
    
    public function __construct($title)
    {
        $this->_title = $title;
    }
    
    public function getTitle()
    {
        return $this->_title;
    }
    
    public function setTitleClass($titleClass)
    {
        $this->_titleClass = $titleClass;
    }
    
    public function getTitleClass()
    {
        return $this->_titleClass;
    }
    
    public function setHeaderActions($left, $right = null)
    {
        $this->_headerLeftActions = $left;
        if (!is_null($right)) {
            $this->_headerRightActions = $right;
        }
    }
    
    public function hasHeaderActions()
    {
        return !empty($this->_headerLeftActions) &&
            !empty($this->_headerRightActions);
    }
    
    public function getHeaderLeftActions()
    {
        return $this->_headerLeftActions;
    }
    
    public function getHeaderRightActions()
    {
        return $this->_headerRightActions;
    }
    
    public function getSubHeaderLeftActions()
    {
        return $this->_subHeaderLeftActions;
    }
    
    public function getSubHeaderRightActions()
    {
        return $this->_subHeaderRightActions;
    }
    
    public function setSubHeaderActions($left, $right = null)
    {
        $this->_subHeaderLeftActions = $left;
        if (!is_null($right)) {
            $this->_subHeaderRightActions = $right;
        }
    }
    
    public function hasSubHeaderActions()
    {
        return !empty($this->_subHeaderLeftActions) &&
            !empty($this->_subHeaderRightActions);
    }
    
    public function shouldRenderDropDownOnMobile()
    {
        return $this->hasHeaderActions() || $this->hasSubHeaderActions();
    }
}