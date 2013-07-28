<?php
interface Data_Row_LocationInterface
{
    public function hasLocation();
    public function getLocation();
    public function setLocation(Location_Row $location);
}