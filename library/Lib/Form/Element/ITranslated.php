<?php
/**
 * Form elements that implement this interface correspond to
 * data item columns which can be translated to a number of
 * languages
 *
 */
interface Lib_Form_Element_ITranslated
{
	public function getTranslatedText($id, $itemType, $lang, $type, $exceptionOnNull);
	public function saveTranslatedText($id, $itemType, $lang, $type, $text);
}