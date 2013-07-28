<?php
class Blog_Post_Row extends Data_Row
{
    /**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = 'blogpost';

    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'blogpost';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'editblogpost';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'createblogpost';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deleteblogpost';

	protected $_onePassSubmit = true;

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Blog_Post_Form';

    /**
     * Indicates whether the content is found in another table (true)
     * or directly here (false)
     *
     * @var boolean
     */
    protected $_isContentTranslated = true;

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::BLOGS;

	/**
	 * Determines whether a field is translated or not
	 *
	 * @param string $columnName
	 * @return boolean
	 */
	protected function _isTranslated($columnName)
	{
		$isTranslated = false ||
			($columnName == Data_Form_Element::TITLE && $this->_isTitleTranslated) ||
			($columnName == Data_Form_Element::DESCRIPTION && $this->_isDescriptionTranslated) ||
			($columnName == Data_Form_Element::CONTENT && $this->_isContentTranslated);
		return $isTranslated;
	}

    /**
     * Returns the content of this article
     *
     * @return string
     */
    public function getContent()
    {
    	$content = $this->content;
        return $content;
    }

	public function getFolderPath()
	{
		throw new Lib_Exception("Blog posts have no folders and must not be asked for a folder path");
	}

	public function getBlog()
	{
		$blog = $this->findParentRow('Blog');
		return $blog;
	}

	/**
	 * Returns a string representing the content.
	 * Used for search exceprt generation.
	 */
	public function getFlatContent()
	{
		$return  = $this->getTitle();
		$return .= ' '.$this->getContent();
		return $return;
	}
}