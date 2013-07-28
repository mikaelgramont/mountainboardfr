<?php
class Media_Item extends Data
{
    const TYPE_PHOTO = 'photo';
    const TYPE_VIDEO = 'video';

    public static $itemTypes = array(
        0 => self::TYPE_PHOTO,
        1 => self::TYPE_VIDEO,
    );

	public static $allowedThumbnailSubTypes = array(
    	Media_Item_Photo::SUBTYPE_JPG,
    	Media_Item_Photo::SUBTYPE_PNG,
    	Media_Item_Photo::SUBTYPE_GIF,
    	Media_Item_Photo::SUBTYPE_FLICKR,
		Media_Item_Photo::SUBTYPE_VIMEO_THUMBNAIL,
    	Media_Item_Photo::SUBTYPE_DAILYMOTION_THUMBNAIL,
    	Media_Item_Photo::SUBTYPE_YOUTUBE_THUMBNAIL,
    );

    protected $_itemType = 'media_Item';

	protected $_name = Constants_TableNames::MEDIA;

	/**
     * Name of the class representing a row
     *
     * @var string
     */
    protected $_rowClass = 'Media_Item_Row';

    /**
     * @var array
     */
    protected $_referenceMap    = array(
        'Album' => array(
            'columns'           => 'albumId',
            'refTableClass'     => 'Media_Album',
            'refColumns'        => 'id'
        ),
        'Spot' => array(
            'columns'           => 'spot',
            'refTableClass'     => 'Spot',
            'refColumns'        => 'id'
        ),
        'Trick' => array(
            'columns'           => 'trick',
            'refTableClass'     => 'Trick',
            'refColumns'        => 'id'
        ),
        'LastEditor' => array(
            'columns'           => 'lastEditor',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Location' => array(
            'columns'           => 'location',
            'refTableClass'     => 'Location',
            'refColumns'        => 'id'
        ),
        'Submitter' => array(
            'columns'           => 'submitter',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
        'Author' => array(
            'columns'           => 'author',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        )
    );
}