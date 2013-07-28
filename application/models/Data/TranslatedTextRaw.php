<?php
class Data_TranslatedTextRaw extends Zend_Db_Table
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
	public static function getTranslatedText($id, $itemType, $lang, $type)
	{
		$table = new self();

		if(empty($id) && !$exceptionOnNull){
			return null;
		}

		$where = "id = $id AND itemType='$itemType' and type='$type' and lang='$lang'";
		$textRowset = $table->fetchAll($where);
		if(count($textRowset) == 0){
			return null;
		}
		
		return $textRowset[0];
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