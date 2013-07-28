<?php
class Media_Album_PhotoMain_Row extends Media_Album_Simple_Row
{
    /**
     * Default category
     *
     * @var int
     */
    protected $_category = Category::COMMUNITY;

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::PHOTOS;

	public function getAmountPerPage()
	{
	    return PHOTOS_PER_PAGE;
	}
}