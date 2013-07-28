<?php
class Test_Row extends Article_Row
{
    /**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = 'test';

    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'displaytest';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'edittest';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'createtest';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deletetest';

    /**
     * Name of the route used to construct list urls
     *
     * @var string
     */
    protected $_listRoute = 'listtest';

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Test_Form';

    /**
     * Name of the class of form used to edit the rest of the attributes
     * of this object before it is activated
     *
     * @var string
     */
    protected $_subForm2Class = 'Test_Form_SubForm2';

    /**
     * Default category
     *
     * @var int
     */
    protected $_category = Category::ARTICLES;

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::TESTS;

    /**
     * Default creation category
     *
     * @var int
     */
    protected $_creationCategory = Category::EDITION;

    /**
     * Default creation subcategory
     *
     * @var int
     */
    protected $_creationSubCategory = SubCategory::CREATETESTS;

	/**
	 * Returns the whole path for the folder associated
	 * to this object
	 */
    public function getFolderPath()
	{
		$path = CONTENT_DIRECTORY_TESTS . $this->getFolderName();
		return $path;
	}
}