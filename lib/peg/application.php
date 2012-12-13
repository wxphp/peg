<?php
/*
 * @author Jefferson González
 * 
 * @license 
 * This file is part of PEG check the license file for information.
 * 
*/

namespace Peg;

/**
 * Holds global options and objects.
 */
class Application
{
	/**
	 * Reference to the global parser.
	 * @var \Peg\CommandLine\Parser
	 */
	private static $parser;
	
	// Disable constructor
	private function __construct(){}
	
	/**
	 * Retreieve the skeleton path from PEG_SKELETON_PATH or throws
	 * an exception if not exists.
	 * @return string
	 * @throws Exception
	 */
	public static function GetSkeletonPath()
	{
		if(file_exists(PEG_SKELETON_PATH))
			return PEG_SKELETON_PATH;
		
		throw new Exception("Skeleton path not found.");
	}

	/**
	 * Gets the global parser.
	 * @return \Peg\CommandLine\Parser
	 */
	public static function GetParser()
	{
		return self::$parser;
	}
	
	/**
	 * Sets the global parser.
	 * @param \Peg\CommandLine\Parser $parser
	 */
	public static function SetParser(\Peg\CommandLine\Parser $parser)
	{
		self::$parser = $parser;
	}
	
	/**
	 * Gets the current working directory.
	 * @return string
	 */
	public static function GetCwd()
	{
		return $_SERVER["PWD"];
	}

	/**
	 * Check if the current directory is of a valid extension.
	 * @return boolean
	 */
	public static function ValidExtension()
	{
		$dir = self::GetCwd();
		
		if(
			// Class templates
			file_exists($dir . "/templates/class/constructor.php") &&
			file_exists($dir . "/templates/class/get.php") &&
			file_exists($dir . "/templates/class/header.php") &&
			file_exists($dir . "/templates/class/method.php") &&
			file_exists($dir . "/templates/class/source.php") &&
			file_exists($dir . "/templates/class/virtual_method.php") &&
			file_exists($dir . "/templates/class/constructor.php") &&
				
			// Config templates
			file_exists($dir . "/templates/config/config.m4") &&
			file_exists($dir . "/templates/config/config.w32") &&
				
			// Function template
			file_exists($dir . "/templates/function/function.php") &&
				
			// Source templates
			file_exists($dir . "/templates/source/common.h") &&
			file_exists($dir . "/templates/source/extension.cpp") &&
			file_exists($dir . "/templates/source/functions.cpp") &&
			file_exists($dir . "/templates/source/functions.h") &&
			file_exists($dir . "/templates/source/object_types.h") &&
			file_exists($dir . "/templates/source/php_extension.h") &&
			file_exists($dir . "/templates/source/references.cpp") &&
			file_exists($dir . "/templates/source/references.h") &&
				
			// Peg configuration file
			file_exists($dir . "/peg.conf")
		)
		{
			return true;
		}
		
		return false;
	}
}

?>
