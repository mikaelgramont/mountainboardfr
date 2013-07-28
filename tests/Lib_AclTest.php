<?php
require_once('ApplicationTest.php');

class Lib_AclTest extends ApplicationTest
{
	public function testAdmin()
	{
		$admin = $this->_getAdmin();
		$acl = new Lib_Acl($admin);
		
		// Admin can do everything with everyone's data
		$dummysPicture = $this->_getDummysPicture();
		$this->assertTrue($dummysPicture->isReadableBy($admin, $acl));
		$this->assertTrue($dummysPicture->isEditableBy($admin, $acl));
		$this->assertTrue($dummysPicture->isCreatableBy($admin, $acl));
		$this->assertTrue($dummysPicture->isDeletableBy($admin, $acl));
		
		$dummysInvalidPicture = $this->_getDummysInvalidPicture();
		$this->assertTrue($dummysInvalidPicture->isReadableBy($admin, $acl));
		$this->assertTrue($dummysInvalidPicture->isEditableBy($admin, $acl));
		$this->assertTrue($dummysInvalidPicture->isDeletableBy($admin, $acl));
		
		// Admin can add articles
		$dossier = $this->_getNewDossier();
		$this->assertTrue($dossier->isCreatableBy($admin, $acl));
		
		// Admin cannot read private messages not for him or from him 
		$message = $this->_getDummysPrivateMessageToWriter();
		$reply = $this->_getWritersResponseToDummy();
		$this->assertFalse($message->isReadableBy($admin, $acl));
		$this->assertFalse($reply->isReadableBy($admin, $acl));
	}
	
	public function testEditor()
	{
		$editor = $this->_getEditor();
		$acl = new Lib_Acl($editor);
		
		$dummysPicture = $this->_getDummysPicture();
		
		// Admin can do everything with everyone's data
		$this->assertTrue($dummysPicture->isReadableBy($editor, $acl));
		$this->assertTrue($dummysPicture->isEditableBy($editor, $acl));
		$this->assertTrue($dummysPicture->isCreatableBy($editor, $acl));
		$this->assertTrue($dummysPicture->isDeletableBy($editor, $acl));
		
		$dummysInvalidPicture = $this->_getDummysInvalidPicture();
		$this->assertTrue($dummysInvalidPicture->isReadableBy($editor, $acl));
		$this->assertTrue($dummysInvalidPicture->isEditableBy($editor, $acl));
		$this->assertTrue($dummysInvalidPicture->isDeletableBy($editor, $acl));
		
		// Editor can add articles
		$dossier = $this->_getNewDossier();
		$this->assertTrue($dossier->isCreatableBy($editor, $acl));
		
		// Editor cannot read private messages not for him or from him 
		$message = $this->_getDummysPrivateMessageToWriter();
		$reply = $this->_getWritersResponseToDummy();
		
		$this->assertFalse($message->isReadableBy($editor, $acl));
		$this->assertFalse($reply->isReadableBy($editor, $acl));
	}
	
	public function testWriter()
	{
		$writer = $this->_getWriter();
		$acl = new Lib_Acl($writer);
		
		$dummysPicture = $this->_getDummysPicture();
		
		// Writer can't do more than a regular member about other people's stuff
		$this->assertTrue($dummysPicture->isReadableBy($writer, $acl));
		$this->assertFalse($dummysPicture->isEditableBy($writer, $acl));
		$this->assertTrue($dummysPicture->isCreatableBy($writer, $acl));
		$this->assertFalse($dummysPicture->isDeletableBy($writer, $acl));
		
		$dummysInvalidPicture = $this->_getDummysInvalidPicture();
		$this->assertFalse($dummysInvalidPicture->isReadableBy($writer, $acl));
		$this->assertFalse($dummysInvalidPicture->isEditableBy($writer, $acl));
		$this->assertFalse($dummysInvalidPicture->isDeletableBy($writer, $acl));
		
		// Writer can add articles
		$dossier = $this->_getNewDossier();
		$this->assertTrue($dossier->isCreatableBy($writer, $acl));
		
		// Writer can read private messages for him or from him 
		$message = $this->_getDummysPrivateMessageToWriter();
		$reply = $this->_getWritersResponseToDummy();
		
		$this->assertTrue($message->isReadableBy($writer, $acl));
		$this->assertTrue($reply->isReadableBy($writer, $acl));
	}
	
	public function testDummy()
	{
		$dummy = $this->_getDummy();
		$acl = new Lib_Acl($dummy);

		$dummysPicture = $this->_getDummysPicture();
		$this->assertEquals(Data::VALID, $dummysPicture->status);
		
		// Dummy can do everything with his pictures
		$this->assertTrue($dummysPicture->isReadableBy($dummy, $acl));
		$this->assertTrue($dummysPicture->isEditableBy($dummy, $acl));
		$this->assertTrue($dummysPicture->isCreatableBy($dummy, $acl));
		$this->assertTrue($dummysPicture->isDeletableBy($dummy, $acl));

		$dummysInvalidPicture = $this->_getDummysInvalidPicture();
		$this->assertEquals(Data::INVALID, $dummysInvalidPicture->status);
		
		$this->assertTrue($dummysInvalidPicture->isReadableBy($dummy, $acl));
		$this->assertTrue($dummysInvalidPicture->isEditableBy($dummy, $acl));
		$this->assertTrue($dummysInvalidPicture->isDeletableBy($dummy, $acl));

		// Dummy cannot add articles
		$dossier = $this->_getNewDossier();
		$this->assertTrue($dossier->isCreatableBy($dummy, $acl));
		
		// Dummy can read his message to Writer, and the reply too 
		$message = $this->_getDummysPrivateMessageToWriter();
		$reply = $this->_getWritersResponseToDummy();
		
		$this->assertTrue($message->isReadableBy($dummy, $acl));
		$this->assertTrue($reply->isReadableBy($dummy, $acl));
	}
	
	public function testMember()
	{
		$member = $this->_getMember();
		$acl = new Lib_Acl($member);
		$dummysPicture = $this->_getDummysPicture();
		
		// Member can only read dummy's picture
		$this->assertTrue($dummysPicture->isReadableBy($member, $acl));
		$this->assertFalse($dummysPicture->isEditableBy($member, $acl));
		$this->assertFalse($dummysPicture->isDeletableBy($member, $acl));
		
		// Member can not do anything with dummy's invalid picture
		$dummysInvalidPicture = $this->_getDummysInvalidPicture();
		$this->assertFalse($dummysInvalidPicture->isReadableBy($member, $acl));
		$this->assertFalse($dummysInvalidPicture->isEditableBy($member, $acl));
		$this->assertFalse($dummysInvalidPicture->isDeletableBy($member, $acl));

		// Member cannot read private messages not for him or from him 
		$message = $this->_getDummysPrivateMessageToWriter();
		$reply = $this->_getWritersResponseToDummy();
		
		$this->assertFalse($message->isReadableBy($member, $acl));
		$this->assertFalse($reply->isReadableBy($member, $acl));
	}
	
	public function testGuest()
	{
		$guest = $this->_getGuest();
		$acl = new Lib_Acl($guest);
		$dummysPicture = $this->_getDummysPicture();
		
		// Guest can only read dummy's picture
		$this->assertTrue($dummysPicture->isReadableBy($guest, $acl));
		$this->assertFalse($dummysPicture->isEditableBy($guest, $acl));
		$this->assertFalse($dummysPicture->isDeletableBy($guest, $acl));
		
		// Guest can not do add pictures
		$this->assertFalse($dummysPicture->isCreatableBy($guest, $acl));
		
		// Guest can not do anything with dummy's invalid picture
		$dummysInvalidPicture = $this->_getDummysInvalidPicture();
		$this->assertFalse($dummysInvalidPicture->isReadableBy($guest, $acl));
		$this->assertFalse($dummysInvalidPicture->isEditableBy($guest, $acl));
		$this->assertFalse($dummysInvalidPicture->isDeletableBy($guest, $acl));

		// Guest cannot add articles
		$dossier = $this->_getNewDossier();
		$this->assertFalse($dossier->isCreatableBy($guest, $acl));
		
		// Guest cannot read private messages not for him or from him 
		$message = $this->_getDummysPrivateMessageToWriter();
		$reply = $this->_getWritersResponseToDummy();
		
		$this->assertFalse($message->isReadableBy($guest, $acl));
		$this->assertFalse($reply->isReadableBy($guest, $acl));
	}
	
	
	public function testBanned()
	{
		// Banned users can't do anything
		$banned = $this->_getBanned();
		$acl = new Lib_Acl($banned);
		$dummysPicture = $this->_getDummysPicture();
		
		$this->assertFalse($dummysPicture->isReadableBy($banned, $acl));
		$this->assertFalse($dummysPicture->isEditableBy($banned, $acl));
		$this->assertFalse($dummysPicture->isDeletableBy($banned, $acl));
		$this->assertFalse($dummysPicture->isCreatableBy($banned, $acl));

		$dummysInvalidPicture = $this->_getDummysInvalidPicture();
		$this->assertFalse($dummysInvalidPicture->isReadableBy($banned, $acl));
		$this->assertFalse($dummysInvalidPicture->isEditableBy($banned, $acl));
		$this->assertFalse($dummysInvalidPicture->isDeletableBy($banned, $acl));

		$dossier = $this->_getNewDossier();
		$this->assertFalse($dossier->isCreatableBy($banned, $acl));
		
		$message = $this->_getDummysPrivateMessageToWriter();
		$reply = $this->_getWritersResponseToDummy();
		
		$this->assertFalse($message->isReadableBy($banned, $acl));
		$this->assertFalse($reply->isReadableBy($banned, $acl));
	}		
		
	public function testPending()
	{
		// Pending users aren't much better than guests
		$pending = $this->_getPending();
		$acl = new Lib_Acl($pending);
		$dummysPicture = $this->_getDummysPicture();
		
		$this->assertTrue($dummysPicture->isReadableBy($pending, $acl));
		$this->assertFalse($dummysPicture->isEditableBy($pending, $acl));
		$this->assertFalse($dummysPicture->isDeletableBy($pending, $acl));
		$this->assertFalse($dummysPicture->isCreatableBy($pending, $acl));

		$dummysInvalidPicture = $this->_getDummysInvalidPicture();
		$this->assertFalse($dummysInvalidPicture->isReadableBy($pending, $acl));
		$this->assertFalse($dummysInvalidPicture->isEditableBy($pending, $acl));
		$this->assertFalse($dummysInvalidPicture->isDeletableBy($pending, $acl));

		$dossier = $this->_getNewDossier();
		$this->assertFalse($dossier->isCreatableBy($pending, $acl));
		
		$message = $this->_getDummysPrivateMessageToWriter();
		$reply = $this->_getWritersResponseToDummy();
		
		$this->assertFalse($message->isReadableBy($pending, $acl));
		$this->assertFalse($reply->isReadableBy($pending, $acl));
		
		$this->assertTrue($acl->isAllowed($pending->getRoleId(), Lib_Acl::PENDING_RESOURCE));
	}

	public function testForumReadAccess()
	{
		$forumDao = new Forum();
		$privateForum = $forumDao->find(32)->current();
		
		$dummy = $this->_getDummy();
		$dummyAcl = new Lib_Acl($dummy);
		$this->assertTrue($privateForum->isReadableBy($dummy, $dummyAcl));
		$this->assertFalse($privateForum->checkTopicPostAcces($dummy));
		
		$member = $this->_getMember();
		$memberAcl = new Lib_Acl($member);
		$this->assertFalse($privateForum->isReadableBy($member, $memberAcl));
		$this->assertFalse($privateForum->checkTopicPostAcces($member));
	}
	
	public function testForumPostAccess()
	{
		$forumDao = new Forum();
		$privateForum = $forumDao->find(33)->current();
		
		$dummy = $this->_getDummy();
		$dummyAcl = new Lib_Acl($dummy);
		$this->assertTrue($privateForum->checkTopicPostAcces($dummy));
		
		$member = $this->_getMember();
		$memberAcl = new Lib_Acl($member);
		$this->assertFalse($privateForum->checkTopicPostAcces($member));
	}
	
	public function testForumModerateAccess()
	{
		$forumDao = new Forum();
		$forum = $forumDao->find(34)->current();
		$moderators = $forum->getModerators();
		$moderatorIds = array();
		foreach($moderators as $user){
			$moderatorIds[] = $user->getId();
		}
		
		$dummy = $this->_getDummy();
		$dummyAcl = new Lib_Acl($dummy);
		
		$this->assertContains($dummy->getId(), $moderatorIds);
		
		$member = $this->_getMember();
		$memberAcl = new Lib_Acl($member);
		$this->assertFalse(in_array($member->getId(), $moderatorIds));
	}
	
	/**
	 * User fixtures
	 */
	private function _getAdmin()
	{
		$dao = new User();
		$user = $dao->find(4)->current();
		return $user;
	}
	
	private function _getEditor()
	{
		$dao = new User();
		$user = $dao->find(5)->current();
		return $user;
	}
	
	private function _getWriter()
	{
		$dao = new User();
		$user = $dao->find(6)->current();
		return $user;
	}
	
	private function _getBanned()
	{
		$dao = new User();
		$user = $dao->find(3)->current();
		return $user;
	}
	
	private function _getDummy()
	{
		$dao = new User();
		$user = $dao->find(1)->current();
		return $user;
	}
	
	private function _getMember()
	{
		$dao = new User();
		$user = $dao->find(7)->current();
		return $user;
	}
	
	private function _getGuest()
	{
		$dao = new User_Guest();
		$user = $dao->find(0)->current();
		return $user;
	}

	private function _getPending()
	{
		$dao = new User();
		$user = $dao->find(8)->current();
		return $user;
	}
	
	/**
	 * Data fixtures
	 */

	/**
	 * @return Media_Item_Photo
	 */
	private function _getDummysPicture()
	{
		$dao = new Media_Item_Photo();
		$data = $dao->find(1)->current();
		return $data;
	}
	
	/**
	 * @return Media_Item_Photo
	 */
	private function _getDummysInvalidPicture()
	{
		$dao = new Media_Item_Photo();
		$data = $dao->find(2)->current();
		return $data;
	}
	
	private function _getDummysPrivateMessageToWriter()
	{
		$dao = new PrivateMessage();
		$data = $dao->find(1)->current();
		return $data;
	}	
	
	private function _getWritersResponseToDummy()
	{
		$dao = new PrivateMessage();
		$data = $dao->find(2)->current();
		return $data;
	}	
	
	private function _getNewDossier()
	{
		$dao = new Dossier();
		$dossier = $dao->fetchNew();
		return $dossier;
	}
}