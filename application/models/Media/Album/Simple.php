<?php
/**
 * Represents an album which gets all its items from the same source,
 * and they are all of the same type.
 */
class Media_Album_Simple extends Media_Album 
{
    /**
     * Name of the class representing a row
     *
     * @var string
     */
    protected $_rowClass = 'Media_Album_Simple_Row';
}