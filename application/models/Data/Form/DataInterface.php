<?php
interface Data_Form_DataInterface
{
    public function getTitle();
    public function getDate();
    public function getSubmitter();
    public function getDescription();
    public function getLastEditionDate();
    public function getLastEditor();
    public function getSubmit();
    public function getStatus();
    public function getTags();
}