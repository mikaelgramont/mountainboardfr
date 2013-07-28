<?php
class Data_TranslatedText extends Cache_Object
{
	protected $_name = Constants_TableNames::TRANSLATEDTEXT;

	/**
	 * Item type
	 *
	 * @var unknown_type
	 */
	protected $_itemType = 'translatedtext';	
	
	/**
	 * Returns the best translation possible for a given text
	 *
	 * @param int $id
	 * @param string $itemType
	 * @param string $lang
	 * @param string $type
	 * @param boolean $exceptionOnNull
	 * @return string
	 */
	public static function getTranslatedText($id, $itemType, $lang, $type, $exceptionOnNull = true)
	{
		$table = new self();

		if(empty($id) && !$exceptionOnNull){
			return null;
		}

		$where = "id = $id AND itemType='$itemType' and type='$type'";
		$textRowset = $table->fetchAll($where);
		if(empty($textRowset)){
			if($exceptionOnNull){
				throw new Lib_Exception("Could not find any translated text '".$type."' for id='$id', itemType='$itemType', lang='$lang', type='$type'");
			} else {
				return null;
			}
		}

		$textRow = self::_getTextFromRowset($lang, $textRowset);
		if(!empty($textRow)){
			return $textRow;
		}

		if($lang == GLOBAL_LANG_DEFAULT){
			/**
			 * The default language was not found.
			 * We pick the first language available.
			 */
			$textRow = $textRowset->rewind()->current();
			return $textRow;
		}

		/**
		 * A non-default language was not found.
		 * Let's try to find the text in the default language.
		 */
		$textRow = self::_getTextFromRowset(GLOBAL_LANG_DEFAULT, $textRowset);
		if(!empty($textRow)){
			return $textRow;
		}

		// Nothing has worked, no text was found.
		if($exceptionOnNull){
			throw new Lib_Exception("Could not find translated text '".$type."' for id='$id', itemType='$itemType', lang='$lang', type='$type'");
		}

		return null;
	}
	
	/**
	 * Returns the best translation possible for a given text
	 *
	 * @param int $id
	 * @param string $itemType
	 * @param boolean $exceptionOnNull
	 * @return string
	 */
	public static function getAllTranslatedTexts($id, $itemType)
	{
		$table = new self();

		$where = "id = $id AND itemType='$itemType'";
		$textRowset = $table->fetchAll($where);

		return $textRowset;
	}

	/**
	 * Returns the desired language for a text within a groupe
	 *
	 * @param string $lang
	 * @param Zend_Db_Table_Rowset_Abstract $textRowset
	 * @return Zend_Db_Table_Row_Abstract
	 */
	private static function _getTextFromRowset($lang, $textRowset)
	{
		foreach($textRowset as $textRow){
			if($textRow->lang == $lang){
				return $textRow;
			}
		}
		return null;
	}
}