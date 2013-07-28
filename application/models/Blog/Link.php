<?php
class Blog_Link extends Data
{
    const ITEM_TYPE = 'bloglink';

    protected $_itemType = 'blogLink';

    protected $_name = Constants_TableNames::BLOGLINK;

    protected $_rowClass = 'Blog_Link_Row';
}