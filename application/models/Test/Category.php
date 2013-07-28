<?php
class Test_Category
{
	const BOARDS = 'testCategoryBoards';	
	const ACCESSORIES = 'testCategoryAccessories';	
	const PADS = 'testCategoryPads';	
	const MISC = 'testCategoryMisc';

	public static $available = array(
		1 => self::BOARDS,
		2 => self::ACCESSORIES,
		3 => self::PADS,
		4 => self::MISC,
	);
}