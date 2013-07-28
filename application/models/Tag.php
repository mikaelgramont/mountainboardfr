<?php
class Tag extends Cache_Object
{
	/**
	 * This is used only by search. Tags are not regular items.
	 * @var string
	 */
	const ITEM_TYPE = 'tag';

	protected $_name = Constants_TableNames::TAG;

    /**
     * Clean an array of tags
     *
     * @param array $arrTags
     * @return array
     */
    public static function cleanTags(array $arrTags)
    {
        $return = array();
        foreach($arrTags as $tag){
            $return[] = self::cleanTag($tag);
        }
        return $return;
    }

    /**
     * Clean one tag
     *
     * @param string $tag
     * @return string
     */
    public static function cleanTag($tag)
    {
        $return = Utils::cleanStringForTag($tag);
        return $return;
    }
}