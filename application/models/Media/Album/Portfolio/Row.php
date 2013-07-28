<?php
class Media_Album_Portfolio_Row extends Media_Album_Simple_Row
{
    /**
     * Default category
     *
     * @var int
     */
    protected $_category = Category::START;

    /**
     * Default subcategory
     *
     * @var int
     */
    protected $_subCategory = SubCategory::PORTFOLIO;
}