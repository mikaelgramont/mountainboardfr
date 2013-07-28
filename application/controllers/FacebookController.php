<?php
/*
{
  ["id"] => string(9) "570099317"
  ["name"] => string(14) "Mikael Gramont"
  ["first_name"] => string(6) "Mikael"
  ["last_name"] => string(7) "Gramont"
  ["link"] => string(38) "http://www.facebook.com/mikael.gramont"
  ["hometown"] => array(2) {
    ["id"] => string(15) "110445852316970"
    ["name"] => string(16) "Toulouse, France"
  }
  ["location"] => array(2) {
    ["id"] => string(15) "108363292521622"
    ["name"] => string(19) "Oakland, California"
  }
  ["gender"] => string(4) "male"
  ["email"] => string(18) "mgramont@gmail.com"
  ["timezone"] => int(-8)
  ["locale"] => string(5) "en_US"
  ["verified"] => bool(true)
  ["updated_time"] => string(24) "2011-01-17T17:18:40+0000"
}
 */


class FacebookController extends Lib_Controller_Action
{
	/**
	 * Default callback handler for oauth
	 */
	public function oauthAction()
	{
		$params = $this->_request->getParams();
		if(!isset($params['code'])){
			throw new Lib_Exception_Facebook('Missigng code parameter', Lib_Exception_Facebook::NO_CODE_ACCESS_UNAUTHORIZED);
		}

		try{
			$oauth = new Facebook_Oauth();
			$client = new Zend_Http_Client();
			$data = $oauth->retrieveData($client, $oauth->retrieveAccessToken($client, $params['code']));

			switch($params['type']){
				case 'login':
				case 'register':
				default:
					$this->_loginRegister($data);
					break;
			}

			$this->_redirect($oauth->getDestination());
		} catch(Lib_Exception_Facebook $e) {
			Globals::getLogger()->facebookOauth($e->getMessage().PHP_EOL.$e->getTraceAsString());
			$this->view->errorCode = $e->getCode();
		}
	}

	protected function _loginRegister($data)
	{
		if(!isset($data['email'])){
			throw new Lib_Exception_Facebook('Email was not provided, it is mandatory for login and user creation', Lib_Exception_Facebook::FACEBOOK_DATA_WRONG_OR_INCOMPLETE);
		}

		$fbTable = new Facebook_User();
		$userTable = new User();
		$currentUser = Globals::getUser();

		if($existingUser = $userTable->findByEmail($data['email'])){
			if($currentUser->isLoggedIn()){
				if($existingUser->getId() != $currentUser->getId()){
					throw new Lib_Exception_Facebook(
						'Logged-in user ('.$currentUser->getId().') '.
						'connected to another user\'s facebook account ('.$existingUser->getId().')',
						Lib_Exception_Facebook::USER_CONNECTED_TO_OTHER_USERS_FB_ACCOUNT
					);
				}
			} else {
				$this->_logUserIn($existingUser);
			}
		} else {
			// new email in the database
			$fbUser = $fbTable->fetchNew();
			if($currentUser->isLoggedIn()){
				$fbUser->userId = $currentUser->getId();
			} else {
				$user = $this->_createUser($data, $userTable, $fbUser);
				$this->_logUserIn($user);
				$fbUser->userId = $user->getId();
			}

			$fbUser->site = CURRENT_SITE; // @see Constants_Sites for allowed values
			$fbUser->fbUserId = $data['id'];
			$fbUser->rawData = Zend_Json::encode($data);
			$fbUser->save();
		}
	}

	protected function _createUser($data, User $userTable)
	{
		$user = $userTable->fetchNew();

		if(!isset($data['name'])){
			throw new Lib_Exception_Facebook('Name not found in fb data', Lib_Exception_Facebook::FACEBOOK_DATA_WRONG_OR_INCOMPLETE);
		}

   		$validator = new Lib_Validate_Username(Lib_Validate_Data::MUST_NOT_EXIST, $userTable, User::COLUMN_USERNAME, FALSE, FALSE);
		if(!$validator->isValid($data['name'])){
			throw new Lib_Exception_Facebook('Name cannot be used', Lib_Exception_Facebook::NAME_INVALID_OR_UNAVAILABLE);
		} else {
			$user->{User::COLUMN_USERNAME} = $data['name'];
		}

		$dataMapping = array(
			'email' => 'email',
			'firstName' => 'first_name',
			'lastName' => 'last_name',
		);

		foreach($dataMapping as $to => $from){
			if(!isset($data[$from])){
				continue;
			}
			$user->$to = $data[$from];
		}

		if(isset($data['gender'])){
			if($data['gender'] != 'male'){
				$user->gender = Lib_Form_Element_Gender::FEMALE;
			} else {
				$user->gender = Lib_Form_Element_Gender::MALE;
			}
		}
		if(isset($data['location']['name'])){
			$parts = explode(', ', $data['location']['name']);
			if(count($parts) == 2){
				$user->city = $parts[0];
				$user->country = $parts[1];
			}
		}

		$user->date = date('Y-m-d H:i:s');
		$user->status = User::STATUS_MEMBER;
		$user->lang = Globals::getTranslate()->getLocale();
		$user->lastLogin = date('Y-m-d H:i:s');
		$user->save();

		return $user;
	}

	protected function _logUserIn(User_Row $user)
	{
        $userData = new stdClass();
        $userData->{User::COLUMN_USERID} = $user->{User::COLUMN_USERID};
        $userData->sessionId = session_id();
        $userData->lastLogin = $user->lastLogin = date('Y-m-d H:i:s');
        Zend_Auth::getInstance()->getStorage()->write($userData);

        $user->save();
	}

}