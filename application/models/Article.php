<?php
abstract class Article extends Document
{
    protected $_rowClass = 'Article_Row';

    public static $articleClasses = array(
        News::ITEM_TYPE,
        Dossier::ITEM_TYPE,
        Test::ITEM_TYPE,
        Event::ITEM_TYPE,
    );
}