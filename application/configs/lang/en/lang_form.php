<?php
$form = array(
    // Zend_Validate messages
    Zend_Validate_Alnum::NOT_ALNUM => 'may only contain letters or numbers',
    Zend_Validate_Alnum::STRING_EMPTY => 'may not be empty',
    Zend_Validate_Alpha::NOT_ALPHA => 'may only contain letters',
    Zend_Validate_Alpha::STRING_EMPTY => 'may not be empty',
    Zend_Validate_Between::NOT_BETWEEN => "'%value%' is not between '%min%' and '%max%', inclusively",
    Zend_Validate_Between::NOT_BETWEEN_STRICT => "'%value%' is not strictly between '%min%' and '%max%'",
    Zend_Validate_Ccnum::LENGTH => 'must contain between 13 and 19 digits',
    Zend_Validate_Ccnum::CHECKSUM => 'Luhn algorithm failed',
    Zend_Validate_Date::NOT_YYYY_MM_DD => 'is not of the format YYYY-MM-DD',
    Zend_Validate_Date::INVALID => 'is not a valid date',
    Zend_Validate_Date::FALSEFORMAT => 'does not fit given date format',
    Zend_Validate_Digits::NOT_DIGITS => 'may only contain numbers',
    Zend_Validate_Digits::STRING_EMPTY => 'may not be empty',
    Zend_Validate_EmailAddress::INVALID => 'is not a valid email address',
    Zend_Validate_EmailAddress::INVALID_HOSTNAME => 'invalid domain name',
    Zend_Validate_EmailAddress::INVALID_MX_RECORD =>"'%hostname%' does not appear to have a valid MX record for the email address '%value%'",
    Zend_Validate_EmailAddress::DOT_ATOM => 'does not fit dot-atom format',
    Zend_Validate_EmailAddress::QUOTED_STRING => 'does not fit quoted-string format',
    Zend_Validate_EmailAddress::INVALID_LOCAL_PART => "'%localPart%' is not a valid username",
    Zend_Validate_Float::NOT_FLOAT => 'must be a decimal number',
    Zend_Validate_GreaterThan::NOT_GREATER => "must be greater than '%min'",
    Zend_Validate_Hex::NOT_HEX => 'may only contain hexadecimal characters',
    Zend_Validate_Hostname::IP_ADDRESS_NOT_ALLOWED => "'%value% is an IP, not a hostname'",
    Zend_Validate_Hostname::UNKNOWN_TLD => 'unknown TLD',
    Zend_Validate_Hostname::INVALID_DASH => '"-" is not allowed',
    Zend_Validate_Hostname::INVALID_HOSTNAME_SCHEMA => 'invalid hostname',
    Zend_Validate_Hostname::UNDECIPHERABLE_TLD => 'undecipherable TLD',
    Zend_Validate_Hostname::INVALID_HOSTNAME => 'invalid hostname',
    Zend_Validate_Hostname::INVALID_LOCAL_NAME => 'invalid local network name',
    Zend_Validate_Hostname::LOCAL_NAME_NOT_ALLOWED => 'local netowrk name not allowed',
    Zend_Validate_Identical::NOT_SAME => 'these fields must be the same',
    Zend_Validate_Identical::MISSING_TOKEN => 'missing element for comparison',
    Lib_Validate_CSRF::NOT_SAME_2 => 'action not authorized, please reload the page',
    Lib_Validate_CSRF::MISSING_TOKEN_2 => 'authorization not found, please reload the page',
    Zend_Validate_InArray::NOT_IN_ARRAY => 'must be in the list',
    Zend_Validate_Int::NOT_INT => 'must be an integer',
    Zend_Validate_Ip::NOT_IP_ADDRESS => 'must be a valid IP address',
    Zend_Validate_LessThan::NOT_LESS => "'must be lower than '%max%'",
    Zend_Validate_NotEmpty::IS_EMPTY => 'may not be empty',
    Zend_Validate_Regex::NOT_MATCH => 'bad format',
    Zend_Validate_StringLength::TOO_SHORT => 'too short',
    Zend_Validate_StringLength::TOO_LONG => 'too long',
    Lib_Validate_IdenticalTo::MISSING_REFERENCE => 'missing reference',
    Lib_Validate_IdenticalTo::NOT_SAME => 'these fields must be the same',
	Lib_Validate_File_Upload::INI_SIZE       => "File too big",
	Lib_Validate_File_Upload::FORM_SIZE      => "File too big",
	Lib_Validate_File_Upload::PARTIAL        => "File was only partially uploaded",
	Lib_Validate_File_Upload::NO_FILE        => "The file was not uploaded",
	Lib_Validate_File_Upload::NO_TMP_DIR     => "No temporary directory was found for the uploaded file",
	Lib_Validate_File_Upload::CANT_WRITE     => "The file can't be written",
	Lib_Validate_File_Upload::EXTENSION      => "The extension returned an error while uploading the file",
	Lib_Validate_File_Upload::ATTACK         => "The file was illegaly uploaded, possible attack",
	Lib_Validate_File_Upload::FILE_NOT_FOUND => "The file was not found",
	Lib_Validate_File_Upload::UNKNOWN        => "Unknown error while uploading the file",
	Zend_Validate_File_Extension::FALSE_EXTENSION => 'unallowed file extension',
	Zend_Validate_File_Extension::NOT_FOUND => 'no file extension',
	Zend_Validate_File_MimeType::FALSE_TYPE => 'bad file type',
	Lib_Validate_Video::NOT_VALID 			=> 'the code for this video is invalid',
	Lib_Validate_Username::SELF_UNALLOWED	=> 'you cannot pick your own name',
	Lib_Validate_TrickQuestion::WRONGANSWER => 'wrong answer!',
	Lib_Validate_LocationRequired::LOCATION_REQUIRED => 'please place it on the map',
	'searchSubmit' => 'search',
);