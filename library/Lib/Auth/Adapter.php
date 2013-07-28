<?php
class Lib_Auth_Adapter extends Zend_Auth_Adapter_DbTable
{
	public function __construct($db, $identity, $password)
	{
        parent::__construct($db);

		$authorizedLevels = implode(', ',array(
        	"'".User::STATUS_MEMBER."'",
        	"'".User::STATUS_EDITOR."'",
        	"'".User::STATUS_WRITER."'",
        	"'".User::STATUS_ADMIN."'",
        ));

        $this->setTableName(Constants_TableNames::USER)
             ->setIdentityColumn(User::COLUMN_USERNAME)
             ->setCredentialColumn(User::COLUMN_PASSWORD);
        $this->setIdentity($identity)->setCredential($password);
		$this->setCredentialTreatment("MD5(?) AND ".User::COLUMN_STATUS." IN ({$authorizedLevels})");
	}

}