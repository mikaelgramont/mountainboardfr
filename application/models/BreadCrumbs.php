<?php
/**
 * BreadCrumbs class
 * Represents list of navigation iterations
 */
class BreadCrumbs
{
    /**
     * Array of steps
     *
     * @var array
     */
    private $_steps = array();

    /**
     * Default separator between steps (used for rendering)
     *
     * @var string
     */
    private $_separator = ' ';

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Setter for separator
     *
     * @param string $separator
     * @return BreadCrumbs
     */
    public function setSeparator(string $separator)
    {
        $this->_separator = $separator;
        return $this;
    }

    /**
     * Getter for separator
     *
     * @return string
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

    /**
     * Getter for steps
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->_steps;
    }

    /**
     * Reset steps
     * @return BreadCrumbs
     */
    public function reset()
    {
        $this->_steps = array();
        return $this;
    }

    /**
     * Add a step before all others
     *
     * @param string $route
     * @param string $title
     * @return BreadCrumbs
     */
    public function prepend($route, $title)
    {
        $this->_checkStep($route, $title);
        array_unshift($this->_steps, array($route, $title));
        return $this;
    }

    /**
     * Add a step after all others
     *
     * @param string $route
     * @param string $title
     * @return BreadCrumbs
     */
    public function append($route, $title)
    {
        $this->_checkStep($route, $title);
        $this->_steps[] = array($route, $title);
        return $this;
    }

    /**
     * Perform checks on a step
     *
     * @param string $route
     * @param string $title
     * @throws Lib_Exception
     */
    private function _checkStep($route, $title)
    {
        if(!Globals::getRouter()->hasRoute($route)){
            throw new Lib_Exception('Breadcrumb route does not exist: '.$route);
        }

        if(empty($title)){
            throw new Lib_Exception('Empty title for BreadCrumbs step:'.$route);
        }
    }
}