<?php
class Dossier_Category
{
	const FREESTYLE = 'dossierCategoryFreestyle';
	const FREERIDE = 'dossierCategoryFreeride';
	const GEAR = 'dossierCategoryGear';
	const BUILD = 'dossierCategoryBuild';
	const MISC = 'dossierCategoryMisc';

	public static $available = array(
		1 => self::FREESTYLE,
		3 => self::FREERIDE,
		8 => self::BUILD,
		9 => self::GEAR,
		10 => self::MISC,
	);
}