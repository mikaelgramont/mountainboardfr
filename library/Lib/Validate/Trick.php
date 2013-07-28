<?php
class Lib_Validate_Trick extends Lib_Validate_Data
{
    /**
     * @var array
     */
    protected $_messageTemplates = array(
        Lib_Validate_Data::DOES_NOT_EXIST => "trickDoesNotExist",
        Lib_Validate_Data::EXISTS => "trickExists"
    );
}