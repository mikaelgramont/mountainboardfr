<?php
class PrivateMessage extends Data
{
    const ITEM_TYPE = 'privatemessage';

    protected $_itemType = 'privatemessage';

    protected $_name = Constants_TableNames::PRIVATEMESSAGES;

    protected $_rowClass = 'PrivateMessage_Row';
}