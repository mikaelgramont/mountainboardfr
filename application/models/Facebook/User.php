<?php
class Facebook_User extends Cache_Object
{
	protected $_name = Constants_TableNames::FACEBOOK_USER;

    protected $_referenceMap    = array(
        'User' => array(
            'columns'           => 'userId',
            'refTableClass'     => 'User',
            'refColumns'        => User::COLUMN_USERID
        ),
	);

	public function getUserWithFacebookId()
	{
		$user = $this->findParentRow('User');
		return $user;
	}
}