<?php
class Comment extends Data
{
    protected $_name = Constants_TableNames::COMMENT;

    protected $_rowClass = 'Comment_Row';

    const ITEM_TYPE = 'comment';

    protected $_itemType = 'comment';
}