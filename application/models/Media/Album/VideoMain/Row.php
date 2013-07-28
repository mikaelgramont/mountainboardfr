<?php
class Media_Album_VideoMain_Row extends Media_Album_Simple_Row
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
    protected $_subCategory = SubCategory::VIDEOS;

    public function getAmountPerPage()
	{
	    return VIDEOS_PER_PAGE;
	}
}