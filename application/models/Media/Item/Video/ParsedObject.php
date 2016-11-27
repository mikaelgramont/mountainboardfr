<?php
class Media_Item_Video_ParsedObject
{
	public function __construct($type, $id, $width = null, $height = null) {
		$this->_type = $type;
		$this->_id = $id;
		$this->_height = $height ? $height : $this->_getDefaultHeight($type);
		$this->_width = $width ? $width : $this->_getDefaultWidth($type);
	}
	
	public function getType()
	{
		return $this->_type; 		
	}
	
	public function getId()
	{
		return $this->_id;
	}
	
	public function getHeight()
	{
		return $this->_height;
	}
	
	public function getWidth()
	{
		return $this->_width;
	}
	
	private function _getDefaultHeight($type) {
		switch ($type) {
			default:
			case Media_Item_Video::SUBTYPE_YOUTUBE:
				return 315;
				break;
			case Media_Item_Video::SUBTYPE_VIMEO:
				return 360;
				break;
			case Media_Item_Video::SUBTYPE_DAILYMOTION:
				return 270;
				break;
		}
	}

	private function _getDefaultWidth($type) {
		switch ($type) {
			default:
			case Media_Item_Video::SUBTYPE_YOUTUBE:
				return 560;
				break;
			case Media_Item_Video::SUBTYPE_VIMEO:
				return 640;
				break;
			case Media_Item_Video::SUBTYPE_DAILYMOTION:
				return 480;
				break;
		}
	}
}