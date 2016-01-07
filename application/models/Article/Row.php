<?php
abstract class Article_Row extends Document_Row implements Data_Row_ArticleInterface
{
    /**
     * String that appears in urls
     *
     * @var string
     */
    protected $_routeDataType = 'article';

    /**
     * Name of the class of form used to edit this object
     *
     * @var string
     */
    protected $_formClass = 'Article_Form';

    /**
     * Indicates whether the content is found in another table (true)
     * or directly here (false)
     *
     * @var boolean
     */
    protected $_isContentTranslated = true;

    /**
     * Name of the layouts used to display this item
     *
     * @var string
     */
    protected $_layouts = array(
    	Data::ACTION_LIST => 'two-columns',
    	Data::ACTION_DISPLAY => 'two-columns',
    );

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

    public function getContentFromCdn($cdnHelper, $lang)
    {
    	$cache = $this->getCache();
    	$cacheId = $this->getCacheIdForContentDisplay($lang);
		$cachedContent = $cache->load($cacheId);

	    if($cachedContent === false){
			$cachedContent = $cdnHelper->replace($this->getContent());
			$this->getTable()->saveDataInCache($cache, $cachedContent, $cacheId);
	    }
	    return $cachedContent;
    }

    /**
     * Returns the resource for creating a new instance of this object
     *
     * @return string
     */
    public function getCreationResourceId()
    {
        $string = Lib_Acl::WRITER_RESOURCE;
        if($this->id){
            $string .= '_'.$this->submitter;
        }
        return $string;
    }

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
     * Delete tag, item, and translated text entries for this data.
     *
     * @return void
     */
    protected function _postDelete()
	{
		$itemType = $this->_table->getItemType();

		$tagsTable = new Tag();
		$tagsTable->delete("itemType = '$itemType' AND itemId = $this->id");

		$itemTable = new Item();
		$itemTable->delete("itemType = '$itemType' AND itemId = $this->id");

		$table = new Data_TranslatedText();
		$where = "id = $this->id AND itemType='$itemType' and type IN('".Data_Form_Element::TITLE."', '".Data_Form_Element::DESCRIPTION."', '".Data_Form_Element::CONTENT."')";
		$table->delete($where);

		Globals::getLogger()->log("Deleted data '$this->id' of type '$itemType'", Zend_Log::INFO);

        // Album deletion
        Media_Album::deleteAggregateAlbumFor($this);
	}

	/**
	 * Returns a string representing the content.
	 * Used for search exceprt generation.
	 */
	public function getFlatContent()
	{
		$return = ucfirst($this->getTitle());
		$return .= ' '.$this->getDescription();
		$return .= ' '.$this->getContent();
		$return .= ' '.implode(' ', $this->getTags());
		return $return;
	}

	public function getThumbnail()
	{
		$thumb = $this->getFolderPath() .'/thumb.jpg';
		return $thumb;
	}

	public function getCacheIdForContentDisplay($lang)
	{
		return 'articleContentFromCdn_'.$this->getItemType().'_'.$this->getId().'_'.$lang;
	}

	protected function _getCacheIdsForClear()
	{
		$table = new Item();
		$cacheIds = parent::_getCacheIdsForClear();
		$cacheIds[] =  $table->getArticlesCacheId();
		$cachedIds[] = $this->getCacheIdForContentDisplay();
		return $cacheIds;
	}

}
