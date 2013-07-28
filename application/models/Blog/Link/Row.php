<?php
class Blog_Link_Row extends Data_Row
{
    /**
     * No dedicated page for a link
     *
     * @var string
     */
    protected $_routeDataType = '';

	/**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Blog_Link_Form';

	public function getFolderPath()
	{
		throw new Lib_Exception("Blog posts have no folders and must not be asked for a folder path");
	}
}