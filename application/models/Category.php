<?php
/**
 * Category class
 */
class Category
{
    const NONE = 0;
    const START = 1;
    const COMMUNITY = 2;
    const ARTICLES = 3;
    const ACCOUNT = 4;
    const EDITION = 5;
    const ADMIN = 6;
    
    public static $names = array(
        self::NONE => 'none',
        self::START => 'start',
        self::COMMUNITY => 'community',
        self::ARTICLES => 'articles',
        self::ACCOUNT => 'account',
        self::EDITION => 'edition',
        self::ADMIN => 'admin',
	);
}