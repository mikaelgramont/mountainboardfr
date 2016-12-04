<?php
interface VideoThumbnailFetcherInterface
{
	public function getVideoInfo($id);
	public function getThumbnailInfo($id, $size);
}