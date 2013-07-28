<?php
class Lib_Validate_Spot extends Lib_Validate_Data
{
    /**
     * @var array
     */
    protected $_messageTemplates = array(
        Lib_Validate_Data::DOES_NOT_EXIST => "spotDoesNotExist",
        Lib_Validate_Data::EXISTS => "spotExists"
    );
}