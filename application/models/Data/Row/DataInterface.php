<?php
interface Data_Row_DataInterface
{
    public function getLink();
    public function getEditLink();
    public function getCreateLink();
    public function getTitle();
    public function getCleanTitle();
    public function getDescription();
    public function getDate($useTime = true);
    public function getSubmitter();
    public function getLastEditor();
    public function getLastEditionDate();
    public function getTags();
    public function getCategory();
    public function getSubCategory();
    public function getForm(User_Row $user, Lib_Acl $acl, $options = null);
	public function getLayout();
}