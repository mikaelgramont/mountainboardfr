<?php
class Trick_Row extends Data_Row implements Data_Row_AlbumInterface
{
    /**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = 'trick';

    /**
     * Name of the route used to construct urls
     *
     * @var string
     */
    protected $_route = 'displaytrick';

    /**
     * Name of the route used to construct edition urls
     *
     * @var string
     */
    protected $_editRoute = 'edittrick';

    /**
     * Name of the route used to construct creation urls
     *
     * @var string
     */
    protected $_createRoute = 'createtrick';

    /**
     * Name of the route used to construct delete urls
     *
     * @var string
     */
    protected $_deleteRoute = 'deletetrick';

    /**
     * Name of the route used to construct list urls
     *
     * @var string
     */
    protected $_listRoute = 'listtricks';

    /**
     * Foreign key name
     *
     * @var string
     */
    protected $_foreignKeyName = 'trick';

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Trick_Form';

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
    protected $_subCategory = SubCategory::TRICKS;

    /**
     * Whether or not we should create an album when this
     * item is saved
     *
     * @var Boolean
     */
    protected $_createAlbumOnSave = true;

    public function getTrickTip()
    {
        return $this->trickTip;
    }

    public function getAlbum()
    {
		$album = Media_Album_Factory::buildAggregateItemAlbum($this->getItemType(), $this->id);
		return $album;
    }

	/**
	 * No folders for tricks
	 */
    public function getFolderPath(){}

	/**
	 * Returns a string representing the content.
	 * Used for search exceprt generation.
	 */
	public function getFlatContent()
	{
		$return = ucfirst($this->getTitle()).' '.$this->getDescription().' '.$this->getTrickTip();
		$return .= ' '.implode(' ', $this->getTags());
		return $return;
	}
}