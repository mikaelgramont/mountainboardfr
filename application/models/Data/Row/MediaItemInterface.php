<?php
interface Data_Row_MediaItemInterface
{
	public function getMediaType();
	public function getMediaSubType();
	public function getURI();
	public function getWidth();
	public function getHeight();
	public function getSize();
	
	public function getThumbnailSubType();
	public function getThumbnailURI();
	public function getThumbnailWidth();
	public function getThumbnailHeight();
}