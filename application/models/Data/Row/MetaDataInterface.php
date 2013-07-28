<?php
/**
 * Interface that indicates a given Data_Row is attached to another one.
 * Ex: a Comment_Row implements this interface because it reprents
 * metadata for say a News_Row
 *
 */
interface Data_Row_MetaDataInterface
{
    public function getParentItemfromDatabase();
}