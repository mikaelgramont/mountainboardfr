<?php
class Dossier extends Article
{
    const ITEM_TYPE = 'dossier';

    protected $_itemType = 'dossier';

    protected $_name = Constants_TableNames::DOSSIER;

    protected $_rowClass = 'Dossier_Row';
}