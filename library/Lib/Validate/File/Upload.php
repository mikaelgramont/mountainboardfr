<?php
class Lib_Validate_File_Upload extends Zend_Validate_File_Upload
{

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::INI_SIZE       => "fileUploadIniSize",
        self::FORM_SIZE      => "fileUploadFormSize",
        self::PARTIAL        => "fileUploadPartial",
        self::NO_FILE        => "fileUploadNoFile",
        self::NO_TMP_DIR     => "fileUploadNoTmpDir",
        self::CANT_WRITE     => "fileUploadCantWrite",
        self::EXTENSION      => "fileUploadExtension",
        self::ATTACK         => "fileUploadAttack",
        self::FILE_NOT_FOUND => "fileUploadFileNotFound",
        self::UNKNOWN        => "fileUploadUnknown"
    );
}