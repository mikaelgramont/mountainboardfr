<?php
class Test extends Article
{
    const ITEM_TYPE = 'test';

    protected $_itemType = 'test';

    protected $_name = Constants_TableNames::TEST;

    protected $_rowClass = 'Test_Row';
}