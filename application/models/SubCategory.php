<?php
/**
 * SubCategory class
 */
class SubCategory
{
    const NONE = 0;
    const INDEX = 1;

    const PORTFOLIO = 2;
    const FRANCE = 3;
    const EVENTS = 4;
    const TRICKS = 5;

    const DOSSIERS = 6;
    const NEWS = 7;
    const TESTS = 8;

    const FORUMS = 9;
    const PHOTOS = 10;
    const VIDEOS = 11;
    const DPT = 12;
    const COUNTRIES = 13;
    const USERS = 14;
    const SPOTS = 15;
    const BLOGS = 16;

    const CREATENEWS = 17;
    const CREATEDOSSIERS = 18;
    const CREATETESTS = 19;
	const CREATEEVENT = 20;

    const USERUPDATE = 21;
    const USERMYPROFILE = 22;
    const LOGOUT = 23;
    const USERREGISTER = 24;
    const LOGINPAGE = 25;
    const PRIVATEMESSAGES = 26;
    const NEWSTUFF = 27;

    public static $names = array(
        self::NONE => 'none',
        self::INDEX => 'index',

        self::FRANCE => 'france',
        self::PORTFOLIO => 'portfolio',
        self::EVENTS => 'events',
        self::TRICKS => 'tricks',

        self::DOSSIERS => 'dossiers',
        self::NEWS => 'news',
        self::TESTS => 'tests',

        self::FORUMS => 'forums',
        self::PHOTOS => 'photos',
        self::VIDEOS => 'videos',
        self::DPT => 'dpt',
        self::COUNTRIES => 'countries',
        self::USERS => 'users',
        self::SPOTS => 'spots',
        self::BLOGS => 'blogs',

        self::CREATENEWS => 'createnews',
        self::CREATEDOSSIERS => 'createdossiers',
        self::CREATETESTS => 'createtests',
        self::CREATEEVENT => 'createevent',

        self::PRIVATEMESSAGES => 'privatemessageshome',
        self::NEWSTUFF => 'newstuff',
        self::USERMYPROFILE => 'usermyprofile',
        self::LOGOUT => 'logout',
        self::USERREGISTER => 'userregister',
        self::LOGINPAGE => 'loginpage',
    );
}