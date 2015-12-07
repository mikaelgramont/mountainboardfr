<?php
class Lib_View_Helper_PrepareCss extends Zend_View_Helper_Abstract
{
	/**
	 * Builds the list of CSS files to render, depending on the environment
	 * @param boolean $production
	 */
	public function prepareCss()
	{
		return $this;
	}

	public function main($production = true)
	{
		if($production){
			$this->view->headLink()->appendStylesheet($this->view->asset()->css('style.full.css'));
		} else {
			$this->view->headLink()->appendStylesheet($this->view->baseUrl().'/'.CSS_PATH.'main.css');
			$this->view->headLink()->appendStylesheet($this->view->baseUrl().'/'.CSS_PATH.'jquery-ui.css');
			$this->view->headLink()->appendStylesheet($this->view->baseUrl().'/'.CSS_PATH.'jquery-ui-custom.css');
		}
	}

	public function ieCss($production = true)
	{
		if($production){
			$return = $this->view->asset()->css('ie.min.css');
		} else {
			$return = $this->view->baseUrl().'/'.CSS_PATH.'ie.css';
		}
		return $return;
	}
}