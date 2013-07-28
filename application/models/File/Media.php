<?php
abstract class File_Media extends File
{
    /**
     * Width of the media
     *
     * @var integer
     */
	protected $_width;
    /**
     * Height of the media
     *
     * @var integer
     */
	protected $_height;
    /**
     * Type of the media
     *
     * @var string
     */
	protected $_type;
    /**
     * Sub-type of the media
     *
     * @var string
     */
	protected $_subType;
    /**
     * Mime type of the media
     *
     * @var string
     */
	protected $_mimeType = null;

    public function __construct($fullPath)
    {
        parent::__construct($fullPath);
        $this->_checkValidity();
    }

    protected function _setMimeType($val)
    {
       $this->_mimeType = $val;
    }

    public function getMimeType() {return $this->_mimeType;}
    public function getWidth() {return $this->_width;}
    public function getHeight() {return $this->_height;}
    public function getType() {return $this->_type;}
    public function getSubType() {return $this->_subType;}

    /**
     * Performs checks that are specific to media files
     */
    abstract protected function _checkValidity();

}